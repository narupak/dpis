<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
/*
0HG_ID VARCHAR(10) NOT NULL,	
1HG_CODE VARCHAR(10) NOT NULL,	
2HG_NAME VARCHAR(100) NOT NULL,
3HG_SEQ_NO INTEGER2 NULL,
4HG_ACTIVE SINGLE NOT NULL,
5UPDATE_USER INTEGER2 NOT NULL,
6UPDATE_DATE VARCHAR(19) NOT NULL,		
*/
	$table="PER_HOLIDAY_GROUP";
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
	
	if($command=="REORDER"){
		foreach($ARR_ORDER as $tmp_HG_ID => $tmp_HG_SEQ_NO){
			$cmd = " update $table set HG_SEQ_NO=$tmp_HG_SEQ_NO where HG_ID='$tmp_HG_ID' ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "<hr>".$cmd;
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับกลุ่มวันหยุด [$HG_CODE : $HG_NAME]");
	} // end if

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
//		echo "$setflagshow";
		$cmd = " update $table set HG_ACTIVE = 0 where HG_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$cmd = " update $table set HG_ACTIVE = 1 where HG_ID in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}
	
	if($command == "ADD" && trim($HG_CODE)){		
		$cmd = " select HG_CODE, HG_NAME from $table where HG_CODE='". trim($HG_CODE) ."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " select max(HG_ID) as max_id,max(HG_SEQ_NO) as max_seq_no from $table ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$HG_ID = $data[max_id] + 1;		$HG_SEQ_NO = $data[max_seq_no] + 1;
			if($HG_SEQ_NO==''){
					//Access มันเป็น ชนิด Number ใส่เป็น เครื่องหมาย '' ไม่ได้ จะ Error data type
					if($DPISDB=="odbc"){
						$HG_SEQ_NO=0;
					}else if($DPISDB=="oci8" || $DPISDB=="mysql"){
						$HG_SEQ_NO="''";
					}
				}			
				$cmd = " insert into $table (HG_ID,HG_CODE, HG_NAME, HG_SEQ_NO, HG_ACTIVE, UPDATE_USER,UPDATE_DATE) values (".$HG_ID.",'".$HG_CODE."', '".$HG_NAME."', ".$HG_SEQ_NO.", ".$HG_ACTIVE.",$SESS_USERID,'$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
				//echo "$cmd<br>";
				//$db_dpis->show_error();

				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $menu_detail เพิ่มข้อมูล [".trim($HG_CODE)." : ".$HG_NAME."]");
				$success_sql = "เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]เรียบร้อยแล้ว"; //กำหนดในส่วนของ php
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[HG_CODE]." ".$data[HG_NAME]."]";
		} // endif
	}

	if($command == "UPDATE"  && trim($HG_ID)){
		$cmd = " update $table set HG_CODE='".$HG_CODE."', HG_NAME='".$HG_NAME."', HG_ACTIVE=".$HG_ACTIVE.",UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' where HG_ID='".$HG_ID."' ";										
		//echo $cmd;				//เพิ่มฟิล์ด  HG_ID         
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($HG_CODE)." : ".$HG_NAME."]");
	}
	
	$yr_perholiday = ($yr_perholiday)? $yr_perholiday : substr($$arr_fields[0], 4);
	if($command == "DELETE" && trim($HG_ID)){
		$cmd = " select HG_CODE,HG_NAME from $table where HG_ID='".$HG_ID."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$HG_NAME= $data[HG_NAME];
		$HG_CODE= $data[HG_CODE];
		
		$cmd = " delete from $table where HG_ID='".$HG_ID."' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($HG_CODE)." : ".$HG_NAME."]");
	}
	
	if($UPD){
		$cmd = " select $arr_fields[0], $arr_fields[1], $arr_fields[2],$arr_fields[3],$arr_fields[4], UPDATE_USER, UPDATE_DATE from $table where trim($arr_fields[0])='".trim($$arr_fields[0])."' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$$arr_fields[0] = $data[$arr_fields[0]];
		$$arr_fields[1] = $data[$arr_fields[1]];
		$$arr_fields[2] = $data[$arr_fields[2]];
		$$arr_fields[3] = $data[$arr_fields[3]];
		$$arr_fields[4] = $data[$arr_fields[4]];
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
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if


	//  function YEAR of  PER_HOLIDAY
	function list_year_perholiday ($name, $val) {
		global $DPISDB, $db_list, $NOW_YEAR, $PLUS_TH_YEAR, $yr_perholiday;
		$val = ($val)? $val : $NOW_YEAR;
		$yr_perholiday = ($yr_perholiday)? $yr_perholiday : $val;
		//echo $yr_perholiday;
		if($DPISDB=="oci8") $cmd = "SELECT DISTINCT SUBSTR(HG_DATE,1, 4) AS HG_YEAR FROM PER_HOLIDAY ORDER BY SUBSTR(HG_DATE,1, 4) DESC";
		elseif($DPISDB=="odbc")  $cmd = "SELECT DISTINCT LEFT(HG_date, 4) AS HG_YEAR FROM PER_HOLIDAY ORDER BY LEFT(HG_date, 4)";
		elseif($DPISDB=="mysql") $cmd = "SELECT DISTINCT LEFT(HG_date, 4) AS HG_YEAR FROM PER_HOLIDAY ORDER BY LEFT(HG_date, 4)";
		$count_year = $db_list->send_cmd($cmd);
		
		echo "<select name=\"$name\" class=\"selectbox\" onchange=\"form1.submit();\">";
		while ($data_list = $db_list->get_array()) {
		
			$sel = ($data_list['HG_YEAR'] == $val)? "selected" : "";
			echo "<option value=".$data_list['HG_YEAR']." ".$sel.">".($data_list['HG_YEAR'] + $PLUS_TH_YEAR)."</option>";
	}
		 
		if(!$count_year) echo "<option value=\"$NOW_YEAR\" selected>".($NOW_YEAR + $PLUS_TH_YEAR)."</option>";
		echo "</select>";
		return $val;
	}	//  end function

	
?>