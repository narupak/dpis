<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

	$table="PER_SPECIAL_HOLIDAY";
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
		foreach($ARR_ORDER as $HG_ID => $HOLS_SEQ_NO){
		if($HOLS_SEQ_NO=="") { $cmd = " update $table set HOLS_SEQ_NO='' where HG_ID='$HG_ID' "; }
		else { $cmd = " update $table set HOLS_SEQ_NO=$HOLS_SEQ_NO where HG_ID='$HG_ID' ";  }
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
//echo "<hr>".$cmd;
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับปฏิทินวันหยุดพิเศษ [$HOLS_DATE : $HOLS_NAME]");
	} // end if

/*******
	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
//		echo "$setflagshow";
		$cmd = " update $table set $arr_fields[2] = 0 where $arr_fields[0] in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$cmd = " update $table set $arr_fields[2] = 1 where $arr_fields[0] in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}
********/	
	
	if($command == "ADD" && trim($$arr_fields[1])){		
		$HOLS_DATE =  save_date($HOLS_DATE);
	
		$cmd = " select $arr_fields[0], $arr_fields[1] from $table where $arr_fields[1]='". trim($$arr_fields[1]) ."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
		if($HOLS_SEQ_NO==''){
				//Access มันเป็น ชนิด Number ใส่เป็น เครื่องหมาย '' ไม่ได้ จะ Error data type
				if($DPISDB=="odbc"){
					$HOLS_SEQ_NO=0;
				}else if($DPISDB=="oci8" || $DPISDB=="mysql"){
					$HOLS_SEQ_NO="''";
				}
			}
			$cmd = " insert into $table (HG_ID,HOLS_DATE,HOLS_NAME,HOLS_SEQ_NO,UPDATE_USER,UPDATE_DATE) values (".trim($HG_ID).", '".$HOLS_DATE."','".$HOLS_NAME."',$HOLS_SEQ_NO , $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
			//echo $cmd;
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($HG_ID)." : ".$HOLS_NAME."]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".trim($HG_ID)." : ".$HOLS_NAME."]";
		} // endif
	}

	if($command == "UPDATE" && trim($$arr_fields[0]) && trim($$arr_fields[1])){
		$HOLS_DATE =  save_date($HOLS_DATE);
	
		$cmd = " update $table set $arr_fields[1]='".$$arr_fields[1]."',$arr_fields[2]='".$$arr_fields[2]."' ,UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' where $arr_fields[0]=".$$arr_fields[0]." ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($$arr_fields[1])." : ".$$arr_fields[2]."]");
	}
	
	$yr_perholiday = ($yr_perholiday)? $yr_perholiday : substr($$arr_fields[1], 4);
	if($command == "DELETE" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[1] from $table where $arr_fields[0]=".$$arr_fields[0]." ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$HOLS_DATE = show_date_format($data[HOLS_DATE], 1);
		
		$cmd = " delete from $table where $arr_fields[0]=".$$arr_fields[0]." ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
	}
	
	if($UPD){
		$cmd = " select $arr_fields[0], $arr_fields[1] , $arr_fields[2], UPDATE_USER, UPDATE_DATE from $table where trim($arr_fields[0])=".trim($$arr_fields[0])." ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$$arr_fields[0] = $data[$arr_fields[0]];
		$$arr_fields[2] = $data[$arr_fields[2]];

		$HOLS_DATE = show_date_format($data[HOLS_DATE], 1);
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
//		$$arr_fields[2] = 1;
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
	


	//  function YEAR of  $table
	function list_year_perholiday ($name, $val) {
		global $DPISDB, $db_dpis, $NOW_YEAR, $PLUS_TH_YEAR, $yr_perholiday;
		$val = ($val)? $val : $NOW_YEAR;
		$yr_perholiday = ($yr_perholiday)? $yr_perholiday : $val;
		//echo $yr_perholiday;
		if($DPISDB=="oci8") $cmd = "SELECT DISTINCT SUBSTR(HOLS_DATE,1, 4) AS HOLS_YEAR FROM $table ORDER BY SUBSTR(HOLS_DATE,1, 4) DESC";
		elseif($DPISDB=="odbc")  $cmd = "SELECT DISTINCT LEFT(HOLS_date, 4) AS HOLS_YEAR FROM $table ORDER BY LEFT(HOLS_date, 4)";
		elseif($DPISDB=="mysql") $cmd = "SELECT DISTINCT LEFT(HOLS_date, 4) AS HOLS_YEAR FROM $table ORDER BY LEFT(HOLS_date, 4)";
		$count_year = $db_dpis->send_cmd($cmd);
		
		echo "<select name=\"$name\" class=\"selectbox\" onchange=\"form1.submit();\">";
		while ($data_list = $db_dpis->get_array()) {
		
			$sel = ($data_list['HOLS_YEAR'] == $val)? "selected" : "";
			echo "<option value=".$data_list['HOLS_YEAR']." ".$sel.">".($data_list['HOLS_YEAR'] + $PLUS_TH_YEAR)."</option>";
	}
		 
		if(!$count_year) echo "<option value=\"$NOW_YEAR\" selected>".($NOW_YEAR + $PLUS_TH_YEAR)."</option>";
		echo "</select>";
		return $val;
	}	//  end function
	
	
?>