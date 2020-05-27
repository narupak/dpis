<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
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
		foreach($ARR_ORDER as $HOL_DATE => $HOL_SEQ_NO){
		$HOL_DATE2 =  save_date($HOL_DATE);
		
		if($HOL_SEQ_NO=="") { $cmd = " update PER_HOLIDAY set HOL_SEQ_NO='' where HOL_DATE='$HOL_DATE2' "; }
		else { $cmd = " update PER_HOLIDAY set HOL_SEQ_NO=$HOL_SEQ_NO where HOL_DATE='$HOL_DATE2' ";  }
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "<hr>".$cmd;
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับปฏิทินวันหยุด [$HOL_DATE : $HOL_NAME]");
	} // end if

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
	
	if($command == "ADD" && trim($$arr_fields[0])){		
		$HOL_DATE =  save_date($HOL_DATE);
		
		
		$chkPerId = 0;
		$cmd = " select  ex.PER_ID from PER_ABSENT ex
					left join PER_ABSENTTYPE at on(at.AB_CODE=ex.AB_CODE)
					WHERE '$HOL_DATE'  BETWEEN ex.ABS_STARTDATE and ex.ABS_ENDDATE AND at.AB_COUNT=2 AND ex.CANCEL_FLAG=0 ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate > 0){
				$chkPerId++;
		}
		
		$cmd = " select  ex.PER_ID from PER_ABSENTHIS ex
					left join PER_ABSENTTYPE at on(at.AB_CODE=ex.AB_CODE)
				   WHERE '$HOL_DATE'  BETWEEN ex.ABS_STARTDATE and ex.ABS_ENDDATE AND at.AB_COUNT=2 ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate > 0){
				$chkPerId++;
		}

		$OpenDialog=0; 
		if($chkPerId>0){
			$HHOL_DATE=$HOL_DATE;
			$HHOL_NAME=$$arr_fields[1];
			$OpenDialog=1;  
			
		}else{
	
			$cmd = " select $arr_fields[0], $arr_fields[1] from $table where $arr_fields[0]='". trim($$arr_fields[0]) ."' ";
			$count_duplicate = $db_dpis->send_cmd($cmd);
			if($count_duplicate <= 0){
			if($HOL_SEQ_NO==''){
					//Access มันเป็น ชนิด Number ใส่เป็น เครื่องหมาย '' ไม่ได้ จะ Error data type
					if($DPISDB=="odbc"){
						$HOL_SEQ_NO=0;
					}else if($DPISDB=="oci8" || $DPISDB=="mysql"){
						$HOL_SEQ_NO="''";
					}
				}	
				$cmd = " insert into $table (". implode(", ", $arr_fields) .") values ('".trim($$arr_fields[0])."', '".$$arr_fields[1]."', $SESS_USERID, '$UPDATE_DATE',$HOL_SEQ_NO) ";
				$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
				//echo $cmd;
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
			}else{
				$data = $db_dpis->get_array();			
				$err_text = "รหัสข้อมูลซ้ำ [".show_date($data[$arr_fields[0]], $DATE_FORMAT)." ".$data[$arr_fields[1]]."]";
				$HOL_DATE =  show_date($HOL_DATE, $DATE_FORMAT);
			} // endif
		
		}
		
	}

	if($command == "UPDATE" && trim($$arr_fields[0])){
		$HOL_DATE =  save_date($HOL_DATE);
	
		$cmd = " update $table set $arr_fields[1]='".$$arr_fields[1]."', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
	}
	
	$yr_perholiday = ($yr_perholiday)? $yr_perholiday : substr($$arr_fields[0], 4);
	if($command == "DELETE" && trim($$arr_fields[0])){
		$HOL_DATE =  save_date($HOL_DATE);
		$cmd = " select $arr_fields[1] from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		//เช็คที่ใบลา
		$chkPerId = 0;
		$cmd = " select  ex.PER_ID from PER_ABSENT ex
					left join PER_ABSENTTYPE at on(at.AB_CODE=ex.AB_CODE)
					WHERE '$HOL_DATE'  BETWEEN ex.ABS_STARTDATE and ex.ABS_ENDDATE AND at.AB_COUNT=2 AND ex.CANCEL_FLAG=0 ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate > 0){
				$chkPerId++;
		}
		//เช็คที่ ประวัติการลา
		$cmd = " select  ex.PER_ID from PER_ABSENTHIS ex
					left join PER_ABSENTTYPE at on(at.AB_CODE=ex.AB_CODE)
				   WHERE '$HOL_DATE'  BETWEEN ex.ABS_STARTDATE and ex.ABS_ENDDATE AND at.AB_COUNT=2 ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate > 0){
				$chkPerId++;
		}
		$OpenDialog_plus=0; 
		if($chkPerId>0){
			$HHOL_DATE=$HOL_DATE;
			$HHOL_NAME=$$arr_fields[1];
			$OpenDialog_plus=1;  
			
		}else{
		
		$cmd = " delete from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
		}	
	}
	
	if($UPD){
		$HOL_DATE =  save_date($HOL_DATE);

		$cmd = " select $arr_fields[0], $arr_fields[1], UPDATE_USER, UPDATE_DATE from $table where trim($arr_fields[0])='".trim($$arr_fields[0])."' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$HOL_DATE = show_date_format($data[HOL_DATE], 1);
		$$arr_fields[1] = $data[$arr_fields[1]];
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
	


	//  function YEAR of  PER_HOLIDAY
	function list_year_perholiday ($name, $val) {
		global $DPISDB, $db_list, $NOW_YEAR, $PLUS_TH_YEAR, $yr_perholiday;
		$val = ($val)? $val : $NOW_YEAR;
		$yr_perholiday = ($yr_perholiday)? $yr_perholiday : $val;
		//echo $yr_perholiday;
		if($DPISDB=="oci8") $cmd = " SELECT * FROM (
                                            SELECT  to_char(MAX(SUBSTR(HOL_DATE,1, 4))+1) AS HOL_YEAR FROM PER_HOLIDAY
                                            UNION all
                                            SELECT DISTINCT SUBSTR(HOL_DATE,1, 4) AS HOL_YEAR FROM PER_HOLIDAY 
                                            )ORDER BY HOL_YEAR DESC ";//เดิม$cmd = "SELECT DISTINCT SUBSTR(HOL_DATE,1, 4) AS HOL_YEAR FROM PER_HOLIDAY ORDER BY SUBSTR(HOL_DATE,1, 4) DESC";
		elseif($DPISDB=="odbc")  $cmd = "SELECT DISTINCT LEFT(hol_date, 4) AS HOL_YEAR FROM PER_HOLIDAY ORDER BY LEFT(hol_date, 4)";
		elseif($DPISDB=="mysql") $cmd = "SELECT DISTINCT LEFT(hol_date, 4) AS HOL_YEAR FROM PER_HOLIDAY ORDER BY LEFT(hol_date, 4)";
		$count_year = $db_list->send_cmd($cmd);
		
		echo "<select name=\"$name\" class=\"selectbox\" onchange=\"form1.submit();\">";
		while ($data_list = $db_list->get_array()) {
			$sel = ($data_list['HOL_YEAR'] == $val)? "selected" : "";
			echo "<option value=".$data_list['HOL_YEAR']." ".$sel.">".($data_list['HOL_YEAR'] + $PLUS_TH_YEAR)."</option>";
                }		 
		if(!$count_year) echo "<option value=\"$NOW_YEAR\" selected>".($NOW_YEAR + $PLUS_TH_YEAR)."</option>";
		echo "</select>";
		return $val;
	}	//  end function
	
	
?>