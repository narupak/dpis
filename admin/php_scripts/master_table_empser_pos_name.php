<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	function list_kpi_competence () {
	global $db_list, $DPISDB, $EP_CP_CODE, $ISCS_FLAG, $CTRL_TYPE;
//		if ($ISCS_FLAG==1 || $CTRL_TYPE==2 || $CTRL_TYPE==3) $where = "and DEPARTMENT_ID=2937";
		$cmd = "	select		DISTINCT CP_CODE, CP_NAME 
									from		PER_COMPETENCE
									WHERE CP_MODEL=3 and CP_ACTIVE=1 $where
									order by CP_CODE, CP_NAME";
		$db_list->send_cmd($cmd);
		//$db_list->show_error();	
		if($EP_CP_CODE)	$arr_setcompetence =  explode("|", $EP_CP_CODE);
		//print_r($arr_setcompetence);
		while ($data_list = $db_list->get_array()) {
			//$data_list = array_change_key_case($data_list, CASE_LOWER);
			$cpcode = trim($data_list[CP_CODE]);
			$cpname= trim($data_list[CP_NAME]);
			$sel = (in_array($cpcode,$arr_setcompetence))?"checked":"";
			echo"<input type='checkbox' id='list_competence_id$cpcode' name='list_competence_id[]' value='$cpcode' $sel>&nbsp;$cpname<br>";
		}
		return $val;
	//echo "<pre>";		
	//print_r($data_list);
	//echo "</pre>";	
	}

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
		foreach($ARR_ORDER as $EP_CODE => $EP_SEQ_NO){
			if($EP_SEQ_NO=="") { $cmd = " update PER_EMPSER_POS_NAME set EP_SEQ_NO='' where EP_CODE='$EP_CODE' "; }
		else {	$cmd = " update PER_EMPSER_POS_NAME set EP_SEQ_NO=$EP_SEQ_NO where EP_CODE='$EP_CODE' "; }
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "<hr>".$cmd;
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับชื่อตำแหน่งพนักงานราชการ [$EP_CODE : $EP_NAME]");
	} // end if

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
//echo "$setflagshow<br>";
		$cmd = " update $table set $arr_fields[4] = 0 where $arr_fields[0] in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error(); echo "<br>";
		$cmd = " update $table set $arr_fields[4] = 1 where $arr_fields[0] in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error(); echo "<br>";
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}
	
	$setcompetence = "";
	if($list_competence_id)	$setcompetence =  implode("|", $list_competence_id);
	//echo "$setcompetence // ";
	
	if($command == "ADD" && trim($$arr_fields[0])){
                /*เดิม*///if($menu_detail == "ชื่อตำแหน่งพนักงานราชการ >") 	{$$arr_fields[2] = $GROUP_WORK;}
                /*Release 5.1.0.6 Begin*/
                $condition = "";
                if(strtoupper($table)=="PER_EMPSER_POS_NAME"){$condition = " AND $arr_fields[2] = '".$$arr_fields[2]."' ";}
		$cmd = " select $arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[4]  
                         from $table where ($arr_fields[0]='". trim($$arr_fields[0]) ."' or $arr_fields[1]='". trim($$arr_fields[1]) ."') ".$condition;
                $count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
		if($EP_SEQ_NO==''){
				//Access มันเป็น ชนิด Number ใส่เป็น เครื่องหมาย '' ไม่ได้ จะ Error data type
				if($DPISDB=="odbc"){
					$EP_SEQ_NO=0;
				}else if($DPISDB=="oci8" || $DPISDB=="mysql"){
					$EP_SEQ_NO="''";
				}
			}	
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") values ('".trim($$arr_fields[0])."', '".$$arr_fields[1]."', '".$$arr_fields[2]."', ".$$arr_fields[3].", ".$$arr_fields[4].", 
							$SESS_USERID, '$UPDATE_DATE', $EP_SEQ_NO, '".$$arr_fields[8]."','$setcompetence') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $menu_detail เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]." : ".$$arr_fields[3]."]");
		$success_sql = "เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]เรียบร้อยแล้ว"; //กำหนดในส่วนของ php
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสหรือชื่อซ้ำ [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($$arr_fields[0])){
            /*Release 5.1.0.6 Begin*/
            $isDup ="FALSE";
            if(strtoupper($table)=="PER_EMPSER_POS_NAME"){
              $cmd = "SELECT EP_CODE,EP_NAME FROM PER_EMPSER_POS_NAME 
                      WHERE EP_NAME='".$$arr_fields[1]."'  
                         AND LEVEL_NO = '".$$arr_fields[2]."'  
                         AND EP_CODE NOT IN ('".$$arr_fields[0]."')";
              $count_duplicate = $db_dpis->send_cmd($cmd);
              if($count_duplicate > 0){
                  $isDup ="TRUE";
              }
            }
            /*Release 5.1.0.6 End*/
            if($isDup=="FALSE"){
                $cmd = " update $table set 
						$arr_fields[1]='".$$arr_fields[1]."', 
						$arr_fields[2]='".$$arr_fields[2]."', 
						$arr_fields[3]=".$$arr_fields[3].", 
						$arr_fields[4]=".$$arr_fields[4].", 
						$arr_fields[8]='".$$arr_fields[8]."', 
						EP_CP_CODE = '$setcompetence',  
						UPDATE_USER=$SESS_USERID, 
						UPDATE_DATE='$UPDATE_DATE' 
						where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]." : ".$$arr_fields[3]."]");
            }else{
                $data = $db_dpis->get_array();			
		$err_text = "รหัสหรือชื่อซ้ำ [".$data[EP_CODE]." ".$data[EP_NAME]."] ";
                $UPD=1;
                $arr_fields[0]=$arr_fields[0];
            }
		
	}
	
	if($command == "DELETE" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[1] from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		
		$cmd = " delete from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]." : ".$$arr_fields[3]."]");
	}
	
	if($UPD){
		$cmd = " select $arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[4], $arr_fields[8] , EP_CP_CODE, UPDATE_USER, UPDATE_DATE  
						from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		$$arr_fields[2] = $data[$arr_fields[2]];
		$$arr_fields[3] = $data[$arr_fields[3]];
		$$arr_fields[4] = $data[$arr_fields[4]];
		$$arr_fields[8] = $data[$arr_fields[8]];
		$EP_CP_CODE = $data[EP_CP_CODE];
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
		$$arr_fields[4] = 1;
		$$arr_fields[8] = "";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>