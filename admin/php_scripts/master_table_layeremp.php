<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	$cmd = " select * from $table ";
	$db_dpis->send_cmd($cmd);
	$field_list = $db_dpis->list_fields($table);
//	echo "<pre>";		print_r($field_list);		echo "</pre>";
	unset($arr_fields);
	if($DPISDB=="mysql"){
		for($i=0; $i<count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	} else {
		for($i=1; $i<=count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	}
//	echo (implode(", ", $arr_fields));
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
/*	if($command=="REORDER"){
		foreach($ARR_ORDER as $PG_CODE => $LAYERE_SEQ_NO){
		if($LAYERE_SEQ_NO=="") { $cmd = " update PER_LAYEREMP set LAYERE_SEQ_NO='' where PG_CODE='$PG_CODE' "; }
		else { $cmd = " update PER_LAYEREMP set LAYERE_SEQ_NO=$LAYERE_SEQ_NO where PG_CODE='$PG_CODE' ";  }
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "<hr>".$cmd;
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับคำบัญชีอัตราเงินเดือนลูกจ้าง [$PG_CODE : $PG_NAME]");
	} // end if */

	if ($command == "COPY" || $command == "COPY2") {
		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 34.5, 19410, 843.9, 120.55, 1, $SESS_USERID, '$UPDATE_DATE', 68) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 35, 19720, 857.4, 122.5, 1, $SESS_USERID, '$UPDATE_DATE', 69) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 35.5, 20040, 871.3, 124.5, 1, $SESS_USERID, '$UPDATE_DATE', 70) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 36, 20360, 885.2, 126.45, 1, $SESS_USERID, '$UPDATE_DATE', 71) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 36.5, 20680, 899.15, 128.45, 1, $SESS_USERID, '$UPDATE_DATE', 72) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('1000', 37, 21010, 913.5, 130.5, 1, $SESS_USERID, '$UPDATE_DATE', 73) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 27.5, 23710, 1030.85, 147.25, 1, $SESS_USERID, '$UPDATE_DATE', 154) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 28, 24080, 1046.95, 149.55, 1, $SESS_USERID, '$UPDATE_DATE', 155) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 28.5, 24450, 1063.05, 151.85, 1, $SESS_USERID, '$UPDATE_DATE', 156) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 29, 24850, 1080.45, 154.35, 1, $SESS_USERID, '$UPDATE_DATE', 157) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 29.5, 25250, 1097.85, 156.85, 1, $SESS_USERID, '$UPDATE_DATE', 158) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('2000', 30, 25670, 1116.10, 159.45, 1, $SESS_USERID, '$UPDATE_DATE', 159) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 34.5, 38440, 1671.30, 238.75, 1, $SESS_USERID, '$UPDATE_DATE', 268) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 35, 39050, 1697.85, 242.55, 1, $SESS_USERID, '$UPDATE_DATE', 269) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 35.5, 39680, 1725.20, 246.45, 1, $SESS_USERID, '$UPDATE_DATE', 270) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 36, 40310, 1752.60, 250.35, 1, $SESS_USERID, '$UPDATE_DATE', 271) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 36.5, 40960, 1780.85, 254.40, 1, $SESS_USERID, '$UPDATE_DATE', 272) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('3000', 37, 41610, 1809.15, 258.45, 1, $SESS_USERID, '$UPDATE_DATE', 273) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 26, 68680, 2986.10, 426.60, 1, $SESS_USERID, '$UPDATE_DATE', 351) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 26.5, 69800, 3034.80, 433.55, 1, $SESS_USERID, '$UPDATE_DATE', 352) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 27, 70920, 3083.50, 440.50, 1, $SESS_USERID, '$UPDATE_DATE', 353) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 27.5, 72050, 3132.60, 447.50, 1, $SESS_USERID, '$UPDATE_DATE', 354) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 28, 73180, 3181.75, 454.55, 1, $SESS_USERID, '$UPDATE_DATE', 355) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " INSERT INTO PER_LAYEREMP_NEW (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, 
						  LAYERE_ACTIVE, UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO)
		                  VALUES ('4000', 28.5, 74310, 3230.85, 461.55, 1, $SESS_USERID, '$UPDATE_DATE', 356) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " select PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, LAYERE_SEQ_NO from PER_LAYEREMP_NEW ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		while($data = $db_dpis->get_array()){
			$data = array_change_key_case($data, CASE_LOWER);
			$pg_code = trim($data[pg_code]);
			$layere_no = $data[layere_no];
			$layere_salary = $data[layere_salary];
			$layere_day = $data[layere_day];
			$layere_hour = $data[layere_hour];
			$layere_active = $data[layere_active];
			$layere_seq_no = $data[layere_seq_no];

			$cmd = " select LAYERE_SALARY from PER_LAYEREMP 
					 where PG_CODE='$pg_code' and LAYERE_NO=$layere_no ";
			$count_data = $db_dpis2->send_cmd($cmd);
			if ($count_data)
				$cmd = " update PER_LAYEREMP set LAYERE_SALARY = $layere_salary, LAYERE_DAY = $layere_day, LAYERE_HOUR = 
						 $layere_hour, LAYERE_ACTIVE = $layere_active, LAYERE_SEQ_NO = $layere_seq_no
						 where PG_CODE='$pg_code' and LAYERE_NO=$layere_no ";
			else
				$cmd = " insert into PER_LAYEREMP (PG_CODE, LAYERE_NO, LAYERE_SALARY, LAYERE_DAY, LAYERE_HOUR, LAYERE_ACTIVE, 
						 UPDATE_USER, UPDATE_DATE, LAYERE_SEQ_NO) 
						 values ('$pg_code', $layere_no, $layere_salary, $layere_day, $layere_hour, $layere_active, 
						 $SESS_USERID, '$UPDATE_DATE', $layere_seq_no) ";
			$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();

		} // end while  

		if ($command == "COPY") {
			$cmd = " delete from PER_LAYEREMP where PG_CODE='1000' and LAYERE_NO in (34.5, 35, 35.5, 36, 36.5, 37) ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();
			
			$cmd = " delete from PER_LAYEREMP where PG_CODE='2000' and LAYERE_NO in (27.5, 28, 28.5, 29, 29.5, 30) ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();
			
			$cmd = " delete from PER_LAYEREMP where PG_CODE='3000' and LAYERE_NO in (34.5, 35, 35.5, 36, 36.5, 37) ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();
			
			$cmd = " delete from PER_LAYEREMP where PG_CODE='4000' and LAYERE_NO in (26, 26.5, 27, 27.5, 28, 28.5) ";
			$db_dpis->send_cmd($cmd);
	//		$db_dpis->show_error();
			
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับอัตราค่าจ้าง ๒๒ มีนาคม ๒๕๕๖");
		} elseif ($command == "COPY2") {
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ปรับอัตราค่าจ้าง ๒๙ พฤษภาคม ๒๕๕๘");
		}
	}

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",", $list_show_id);
//		echo "$setflagshow<br>";
		if($DPISDB=="oci8") $cmd = " update $table set $arr_fields[5] = 0 where concat(trim($arr_fields[0]), concat('|', trim($arr_fields[1]))) in (".stripslashes($current_list).") ";
		elseif($DPISDB=="odbc") $cmd = " update $table set $arr_fields[5] = 0 where trim($arr_fields[0]) + '|' + trim($arr_fields[1]) in (".stripslashes($current_list).") ";
		elseif($DPISDB=="mysql") $cmd = " update $table set $arr_fields[5] = 0 where trim($arr_fields[0]) + '|' + trim($arr_fields[1]) in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); echo "<br>-------------------<br>";
		if($DPISDB=="oci8") $cmd = " update $table set $arr_fields[5] = 1 where concat(trim($arr_fields[0]), concat('|', trim($arr_fields[1]))) in (".stripslashes($setflagshow).") ";
		elseif($DPISDB=="odbc") $cmd = " update $table set $arr_fields[5] = 1 where trim($arr_fields[0]) + '|' + trim($arr_fields[1]) in (".stripslashes($setflagshow).") ";
		elseif($DPISDB=="mysql") $cmd = " update $table set $arr_fields[5] = 1 where trim($arr_fields[0]) + '|' + trim($arr_fields[1]) in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error(); echo "<br>";
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}
	
	if($command == "ADD" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3] from $table where $arr_fields[0]='".$$arr_fields[0]."' and $arr_fields[1]='".$$arr_fields[1]."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
		if($LAYERE_SEQ_NO==''){
				//Access มันเป็น ชนิด Number ใส่เป็น เครื่องหมาย '' ไม่ได้ จะ Error data type
				if($DPISDB=="odbc"){
					$LAYERE_SEQ_NO=0;
				}else if($DPISDB=="oci8" || $DPISDB=="mysql"){
					$LAYERE_SEQ_NO="''";
				}
			}	
		
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") values ('".trim($$arr_fields[0])."', '".$$arr_fields[1]."', '".$$arr_fields[2]."', '".$$arr_fields[3]."', '".$$arr_fields[4]."', '".$$arr_fields[5]."', $SESS_USERID, '$UPDATE_DATE',$LAYERE_SEQ_NO) ";
			$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $menu_detail เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]."]");
			$success_sql = "เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]."]เรียบร้อยแล้ว"; //กำหนดในส่วนของ php
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลขั้นซ้ำ [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]." ".$data[$arr_fields[2]]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($$arr_fields[0])){
		$cmd = " update $table set $arr_fields[2]='".$$arr_fields[2]."', $arr_fields[3]=".$$arr_fields[3].", $arr_fields[4]=".$$arr_fields[4].",  UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' where $arr_fields[0]='".$$arr_fields[0]."' and $arr_fields[1]='".$$arr_fields[1]."' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]."]");
	}
	
	if($command == "DELETE" && trim($$arr_fields[0]) && trim($$arr_fields[1])){
		$cmd = " select $arr_fields[2] from $table where $arr_fields[0]='".$$arr_fields[0]."' and $arr_fields[1]='".$$arr_fields[1]."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[2] = $data[$arr_fields[2]];
		
		$cmd = " delete from $table where $arr_fields[0]='".$$arr_fields[0]."' and $arr_fields[1]='".$$arr_fields[1]."' ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]."]");
	}
	
	if($UPD){
		$cmd = " select $arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[4], $arr_fields[5], UPDATE_USER, UPDATE_DATE 
				 from $table 
				 where $arr_fields[0]='".$$arr_fields[0]."' and $arr_fields[1] = ".$$arr_fields[1]." ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		//$db_dpis->show_error();
		$$arr_fields[0] = trim($data[$arr_fields[0]]);
		$$arr_fields[1] = trim($data[$arr_fields[1]]);
		$$arr_fields[2] = $data[$arr_fields[2]];
		$$arr_fields[3] = $data[$arr_fields[3]];
		$$arr_fields[4] = $data[$arr_fields[4]];		
		$$arr_fields[5] = $data[$arr_fields[5]];		
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$$arr_fields[0] = "";
		$$arr_fields[1] = "";
		$$arr_fields[2] = "";
		$$arr_fields[3] = "";
		$$arr_fields[4] = "";
		$$arr_fields[5] = 1;				
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>