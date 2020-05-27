<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$ARR_ADDRESS_TYPE = array(1 =>"ที่อยู่ปัจจุบัน",2=>"ที่อยู่ตามทะเบียนบ้าน",3=>"ที่อยู่ตามบัตรประชาชน",4=>"ที่อยู่ตามภูมิลำเนา");

	//ดึงข้อมูลบุคคลมาแสดง
	if($PER_ID){
		if($DPISDB=="odbc"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
							PER_PERSONAL.PER_CARDNO ,PER_PERSONAL.DEPARTMENT_ID
					from		PER_PERSONAL, PER_PRENAME
					where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		} // end if
		
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_ADDRESS set AUDIT_FLAG = 'N' where ADR_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_ADDRESS set AUDIT_FLAG = 'Y' where ADR_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		if ($DT_NAME) $ADR_DISTRICT = $DT_NAME;
		$cmd = " select max(ADR_ID) as max_id from PER_ADDRESS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$ADR_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_ADDRESS (ADR_ID, PER_ID, ADR_TYPE, ADR_NO, ADR_ROAD, ADR_SOI, ADR_MOO, ADR_VILLAGE, 
						ADR_BUILDING, ADR_DISTRICT, DT_CODE, AP_CODE, PV_CODE, ADR_HOME_TEL, ADR_OFFICE_TEL, ADR_FAX, ADR_MOBILE,
						ADR_EMAIL, ADR_POSTCODE, ADR_REMARK, UPDATE_USER, UPDATE_DATE)
						values ($ADR_ID, $PER_ID, $ADR_TYPE, '$ADR_NO', '$ADR_ROAD', '$ADR_SOI', '$ADR_MOO', '$ADR_VILLAGE', 
						'$ADR_BUILDING', '$ADR_DISTRICT', '$DT_CODE', '$AP_CODE', '$PV_CODE1', '$TEL_HOME', '$TEL_OFFICE', '$TEL_FAX', '$TEL_MOBILE', 
						'$EMAIL', '$ADR_ZIPCODE', '$ADR_REMARK', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
//		echo "$cmd<br>";
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติที่อยู่ [$PER_ID : $PER_NAME : $ADR_TYPE]");
		$ADD_NEXT = 1;
	} // end if

	if($command=="UPDATE" && $PER_ID && $ADR_ID){
		if ($DT_NAME) $ADR_DISTRICT = $DT_NAME;
		$cmd = " update PER_ADDRESS set
						ADR_TYPE=$ADR_TYPE ,
						ADR_NO='$ADR_NO' ,
						ADR_ROAD='$ADR_ROAD' ,
						ADR_SOI ='$ADR_SOI' ,
						ADR_MOO='$ADR_MOO' ,
						ADR_VILLAGE='$ADR_VILLAGE' ,
						ADR_BUILDING='$ADR_BUILDING' ,
						ADR_DISTRICT='$ADR_DISTRICT' ,
						DT_CODE='$DT_CODE' ,
						AP_CODE='$AP_CODE' ,
						PV_CODE='$PV_CODE1' ,
						ADR_HOME_TEL= '$TEL_HOME' ,
						ADR_OFFICE_TEL='$TEL_OFFICE' ,
						ADR_FAX='$TEL_FAX' ,
						ADR_MOBILE= '$TEL_MOBILE',
						ADR_EMAIL='$EMAIL' ,
						ADR_POSTCODE='$ADR_ZIPCODE' ,
						ADR_REMARK='$ADR_REMARK' ,
						UPDATE_USER=$SESS_USERID ,
						UPDATE_DATE= '$UPDATE_DATE'
						where ADR_ID=$ADR_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "$cmd<br>";
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติที่อยู่ [$PER_ID : $PER_NAME : $ADR_TYPE]");
	} // end if
	
	if($command == "COPY_ADDRESS" && (trim($ADR_TYPE) && trim($ADR_COPY_TYPE))){
		$cmd = " select max(ADR_ID) as max_id from PER_ADDRESS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$ADR_ID = $data[max_id] + 1;	

	// หาว่ามีที่อยู่ที่ COPY มา อยู่ใน DB แล้วหรือยัง ?
		$cmd = " select * from PER_ADDRESS where (PER_ID=$PER_ID and ADR_TYPE=$ADR_COPY_TYPE)  ";
		$count_address_data = $db_dpis->send_cmd($cmd);
		
		$cmd = "select COLUMN_NAME from USER_TAB_COLUMNS where TABLE_NAME='PER_ADDRESS'    ";		// ALL_TAB_COLUMNS ทั้งหมด
		$db_dpis->send_cmd($cmd);
		while($data = $db_dpis->get_array()){		//ดึงชื่อ column
			$column_name = $data[COLUMN_NAME];
			$column_insert_name .= ((trim($column_insert_name))?",":"") . $column_name;
		} // end while	
			
		$cmd = " select  $column_insert_name from PER_ADDRESS where (PER_ID=$PER_ID and ADR_TYPE=$ADR_TYPE) ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();		//ดึงค่า จากที่อยู่ที่เลือกมา			

		if($count_address_data){	// UPDATE ลงตัวเดิม
			$str_update = "";
			$arr_column_insert_name = explode(",",$column_insert_name);
			for($i=0;$i < count($arr_column_insert_name); $i++){
				$column_value = $arr_column_insert_name[$i];
				if($column_value!="ADR_ID"&&$column_value!="PER_ID"&&$column_value!="ADR_TYPE"){
					if($column_value=='UPDATE_USER')	$data2[UPDATE_USER]=$SESS_USERID;	
					if($column_value=='UPDATE_DATE')	$data2[UPDATE_DATE]=$UPDATE_DATE;	
					$str_update .= ((trim($str_update))?",":"") . $column_value." = '".$data2[$column_value]."'";
				}
			} //end for
			if($str_update!=""){
				$cmd = "update PER_ADDRESS set
							$str_update	
							where (PER_ID=$PER_ID and ADR_TYPE=$ADR_COPY_TYPE) ";
			}
		}else{ // INSERT ตัวใหม่
			$arr_column_insert_name = explode(",",$column_insert_name);
			for($i=0;$i < count($arr_column_insert_name); $i++){
				$column_value = $arr_column_insert_name[$i];
				if($column_value=='ADR_ID')	$data2[ADR_ID]=$ADR_ID;
				if($column_value=='ADR_TYPE')	$data2[ADR_TYPE]=$ADR_COPY_TYPE;
				if($column_value=='UPDATE_USER')	$data2[UPDATE_USER]=$SESS_USERID;	
				if($column_value=='UPDATE_DATE')	$data2[UPDATE_DATE]=$UPDATE_DATE;	
				if (!$data2[$column_value]){
					$data2[$column_value] = "NULL";
				}else{
					$data2[$column_value] = "'".$data2[$column_value]."'";
				}	
				$column_insert_value .= ((trim($column_insert_value))?",":"") . $data2[$column_value];
			} //end for
		
			if($column_insert_name&&$column_insert_value){
				$cmd = " insert into PER_ADDRESS ($column_insert_name)
				values ($column_insert_value) ";
			} // end if
		}
		$db_dpis->send_cmd($cmd);
		//echo "-> $cmd";
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > คัดลอกประวัติที่อยู่จาก $ARR_ADDRESS_TYPE[$ADR_TYPE] ไป $ARR_ADDRESS_TYPE[$ADR_COTY_TYPE] [$PER_ID : $PER_NAME]");
	}
	
	if($command=="DELETE" && $PER_ID && $ADR_ID){
		$cmd = " select  ADR_TYPE from PER_ADDRESS where ADR_ID=$ADR_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$ADR_TYPE = $data[ADR_TYPE];
		
		$cmd = " delete from PER_ADDRESS where ADR_ID=$ADR_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติที่อยู่ [$PER_ID : $PER_NAME : $ADR_TYPE]");
	} // end if

	//เซตค่าให้ input
	if(($UPD && $PER_ID && $ADR_ID) || ($VIEW && $PER_ID && $ADR_ID)){
		$cmd = " select		ADR_ID, PER_ID, ADR_TYPE, ADR_NO, ADR_ROAD, ADR_SOI , ADR_MOO, ADR_VILLAGE, ADR_BUILDING, ADR_DISTRICT,
											DT_CODE, AP_CODE, PV_CODE, ADR_HOME_TEL, ADR_OFFICE_TEL, ADR_FAX, ADR_MOBILE, ADR_EMAIL, ADR_POSTCODE,
											ADR_REMARK, UPDATE_USER, UPDATE_DATE
							from		PER_ADDRESS
							where	ADR_ID=$ADR_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();

		$ADR_ID=$data[ADR_ID]; 
		$ADR_TYPE=$data[ADR_TYPE];		//$ADR_TYPE_tmp
		/*if ($ADR_TYPE_tmp == 1)		$ADR_TYPE_tmp1 = 1 ;
		elseif ($ADR_TYPE_tmp == 2)		$ADR_TYPE_tmp2 = 2 ;
		elseif ($ADR_TYPE_tmp == 3)		$ADR_TYPE_tmp3 = 3 ;
		elseif ($ADR_TYPE_tmp == 4)		$ADR_TYPE_tmp4 = 4 ;*/
		
		$ADR_NO=$data[ADR_NO];
		$ADR_ROAD=$data[ADR_ROAD]; 
		$ADR_SOI=$data[ADR_SOI]; 
		$ADR_MOO=$data[ADR_MOO]; 
		$ADR_VILLAGE=$data[ADR_VILLAGE];
		$ADR_BUILDING=$data[ADR_BUILDING];
		$ADR_DISTRICT=$data[ADR_DISTRICT]; 
		
		$DT_CODE=$data[DT_CODE]; 
		$AP_CODE=$data[AP_CODE]; 
		$PV_CODE1=$data[PV_CODE];
		$DT_NAME = $AP_NAME = $PV_NAME1 = "";
		if($DT_CODE){
			$cmd = " select  DT_NAME from PER_DISTRICT  where DT_CODE='$DT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DT_NAME = $data2[DT_NAME];
		} // end if

		if($AP_CODE){
			$cmd = " select  AP_NAME from PER_AMPHUR  where AP_CODE='$AP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$AP_NAME = $data2[AP_NAME];
		} // end if

		if($PV_CODE1){
			$cmd = " select  PV_NAME from PER_PROVINCE  where PV_CODE='$PV_CODE1' ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			
			$data2 = $db_dpis2->get_array();
			$PV_NAME1 = $data2[PV_NAME];
			
		} // end if		
		
		 $TEL_HOME=$data[ADR_HOME_TEL];
		 $TEL_OFFICE=$data[ADR_OFFICE_TEL]; 
		 $TEL_FAX=$data[ADR_FAX]; 
		 $TEL_MOBILE=$data[ADR_MOBILE];
		 $EMAIL=$data[ADR_EMAIL];
		 $ADR_ZIPCODE=$data[ADR_POSTCODE];
		 $ADR_REMARK=$data[ADR_REMARK];
		$UPDATE_USER = $data[UPDATE_USER];
		$PV_NAME2 = "$PV_NAME1";
		
		
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($ADR_ID);
		unset($ADR_TYPE); 
		unset($ADR_NO); 
		unset($ADR_ROAD);
		unset($ADR_SOI); 
		unset($ADR_MOO);
		unset($ADR_VILLAGE); 
		unset($ADR_BUILDING); 
		unset($ADR_DISTRICT); 
		unset($DT_CODE);
		unset($AP_CODE);
		unset($PV_CODE1); 
		unset($DT_NAME);
		unset($AP_NAME);
		unset($PV_NAME1); 
		 unset($TEL_HOME);
		 unset($TEL_OFFICE); 
		 unset($TEL_FAX); 
		 unset($TEL_MOBILE); 
		 unset($EMAIL); 
		 unset($ADR_ZIPCODE); 
		 unset($ADR_REMARK);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>