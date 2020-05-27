<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
        
        
        /*ขยาย fld INV_DETAIL เพื่อให้รองรับการคีข้อความยาวๆ จาก 200 เป็น 2000*/
        $db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        $cmdModify = "select column_name, data_type ,data_length
                    from user_tab_columns
                    where table_name = 'PER_MOVMENT'
                    and column_name = 'MOV_NAME'";
        $db_dpis2->send_cmd($cmdModify);
        $dataModify = $db_dpis2->get_array_array();
        $data_length = $dataModify[2];
        if($data_length==100){
            $cmdModify = "ALTER TABLE PER_MOVMENT MODIFY MOV_NAME VARCHAR2(255)";
            $db_dpis2->send_cmd($cmdModify);
            $db_dpis2->send_cmd("COMMIT");
            
            $cmdChk = "select count(*) AS CNT from PER_MOVMENT where MOV_CODE='10190' ";
            $db_dpis2->send_cmd($cmdChk);
            $dataChk = $db_dpis2->get_array_array();
            $data_Chk = $dataChk[0];
            if($data_Chk==0){
                $cmdInsert = "insert into PER_MOVMENT (MOV_CODE, MOV_NAME, MOV_TYPE, MOV_ACTIVE, UPDATE_USER, UPDATE_DATE,  MOV_SUB_TYPE) values ('10190', 'บรรจุทายาทของข้าราชการพลเรือนสามัญที่เสียชีวิตทุพพลภาพหรือพิการจนต้องออกจากราชการอันเนื่องมาจากการปฏิบัติหน้าที่ราชการหรือการปฏิบัติตนเป็นพลเมืองดีเข้ารับราชการเป็นข้าราชการพลเรือนสามัญ', 3, 1, 1, sysdate, 1)";
                $db_dpis2->send_cmd($cmdInsert);
                $db_dpis2->send_cmd("COMMIT");
            }
            
        }
        
        /**/
        
        
	
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
	if ($MOV_SEQ_NO) $MOV_SEQ_NO = "NULL";
	if (!$MOV_SUB_TYPE) $MOV_SUB_TYPE = "NULL";
	$MOV_CODE = trim($MOV_CODE);
	
	if($command=="REORDER"){
		foreach($ARR_ORDER as $MOV_CODE => $MOV_SEQ_NO){
			if($MOV_SEQ_NO=="") { $cmd = " update PER_MOVMENT set MOV_SEQ_NO='' where trim(MOV_CODE)=trim('$MOV_CODE') "; }
			else { $cmd = " update PER_MOVMENT set MOV_SEQ_NO=$MOV_SEQ_NO where trim(MOV_CODE)=trim('$MOV_CODE') ";  }
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับประเภทความเคลื่อนไหว [$MOV_CODE : $MOV_NAME]");
	} // end if

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",", $list_show_id);
//echo "$setflagshow<br>";
		$cmd = " update $table set $arr_fields[3] = 0 where $arr_fields[0] in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error(); echo "<br>";
		$cmd = " update $table set $arr_fields[3] = 1 where $arr_fields[0] in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error(); echo "<br>";

		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}
	if($MM_CODE) $$arr_fields[0] = $MM_CODE;
	if($command == "ADD" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[0], $arr_fields[1], $arr_fields[2], $arr_fields[3] from $table where trim($arr_fields[0])='". trim($$arr_fields[0]) ."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
		if($MOV_SEQ_NO==''){
				//Access มันเป็น ชนิด Number ใส่เป็น เครื่องหมาย '' ไม่ได้ จะ Error data type
				if($DPISDB=="odbc"){
					$MOV_SEQ_NO=0;
				}else if($DPISDB=="oci8" || $DPISDB=="mysql"){
					$MOV_SEQ_NO="''";
				}
			}	
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") values ('".trim($$arr_fields[0])."', '".$$arr_fields[1]."', ".$$arr_fields[2].", ".$$arr_fields[3].", 
							$SESS_USERID, '$UPDATE_DATE',$MOV_SEQ_NO, $MOV_SUB_TYPE, '".$$arr_fields[8]."') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $menu_detail เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
			$success_sql = "เพิ่มข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]เรียบร้อยแล้ว"; //กำหนดในส่วนของ php
			
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]." ".$data[$arr_fields[2]]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($$arr_fields[0])){
		$cmd = " update $table set 
						$arr_fields[1]='".$$arr_fields[1]."', 
						$arr_fields[2]=".$$arr_fields[2].", 
						$arr_fields[3]=".$$arr_fields[3].",  
						$arr_fields[8]='".$$arr_fields[8]."', 
						UPDATE_USER=$SESS_USERID, 
						UPDATE_DATE='$UPDATE_DATE', 
						MOV_SUB_TYPE=$MOV_SUB_TYPE 
						where $arr_fields[0]='".trim($$arr_fields[0])."' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]."]");
	}
	
	if($command == "DELETE" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[1] from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		
		$cmd = " delete from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($$arr_fields[0])." : ".$$arr_fields[1]." : ".$$arr_fields[2]."]");
	}
	
	if($UPD){
		$cmd = " select $arr_fields[1], $arr_fields[2], $arr_fields[3], $arr_fields[7], $arr_fields[8], UPDATE_USER, UPDATE_DATE from $table where trim($arr_fields[0])='".$$arr_fields[0]."' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		$$arr_fields[2] = $data[$arr_fields[2]];
		$$arr_fields[3] = $data[$arr_fields[3]];
		$$arr_fields[7] = $data[$arr_fields[7]];
		$$arr_fields[8] = $data[$arr_fields[8]];
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
		$$arr_fields[3] = 1;
		$$arr_fields[7] = "";
		$$arr_fields[8] = "";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>