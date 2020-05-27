<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

	//ดึงข้อมูลบุคคลมาแสดง
	if($PER_ID && $command != "CANCEL"){
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
		$PER_CARDNO = (trim($data[PER_CARDNO]))?trim($data[PER_CARDNO]): "NULL";		
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);

		if (!$ADR_ID) {
			$cmd = "	select  ADR_ID  from  PER_WORKFLOW_ADDRESS
							where  PER_ID=$PER_ID and ADR_WF_STATUS!='04'
							order by ADR_ID DESC ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$ADR_ID = $data[ADR_ID];
			if ($ADR_ID) {
				$VIEW = 1;
			}
		}	
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($db_type=="mysql") {
		$update_date = "NOW()";
		$update_by = "'$SESS_USERNAME'";
	} elseif($db_type=="mssql") {
		$update_date = "GETDATE()";
		$update_by = $SESS_USERID;
	} elseif($db_type=="oci8" || $db_type=="odbc") {
		$update_date = date("Y-m-d H:i:s");
		$update_date = "'$update_date'";
		$update_by = $SESS_USERID;
	}
	
	if($command=="ADD" && $PER_ID){
		$cmd = " select max(ADR_ID) as max_id from PER_WORKFLOW_ADDRESS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$ADR_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_WORKFLOW_ADDRESS (ADR_ID, PER_ID, ADR_TYPE, ADR_NO, ADR_ROAD, ADR_SOI, ADR_MOO, ADR_VILLAGE, 
							ADR_BUILDING, ADR_DISTRICT, AP_CODE, PV_CODE, ADR_HOME_TEL, ADR_OFFICE_TEL, ADR_FAX, ADR_MOBILE,
							ADR_EMAIL, ADR_POSTCODE, ADR_REMARK, UPDATE_USER, UPDATE_DATE, ADR_WF_STATUS)
						values ($ADR_ID, $PER_ID, $ADR_TYPE, '$ADR_NO', '$ADR_ROAD', '$ADR_SOI', '$ADR_MOO', '$ADR_VILLAGE', 
							'$ADR_BUILDING', '$ADR_DISTRICT', '$AP_CODE', '$PV_CODE1', '$TEL_HOME', '$TEL_OFFICE', '$TEL_FAX', '$TEL_MOBILE', 
							'$EMAIL', '$ADR_ZIPCODE', '$ADR_REMARK', $SESS_USERID, '$UPDATE_DATE', '01') ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "$cmd<br>";

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติที่อยู่ [$PER_ID : $PER_NAME : $ADR_TYPE]");
	} // end if

	if($command=="UPDATE" && $PER_ID && $ADR_ID){
		$cmd = " update PER_WORKFLOW_ADDRESS set
						ADR_TYPE=$ADR_TYPE ,
						ADR_NO='$ADR_NO' ,
						ADR_ROAD='$ADR_ROAD' ,
						ADR_SOI ='$ADR_SOI' ,
						ADR_MOO='$ADR_MOO' ,
						ADR_VILLAGE='$ADR_VILLAGE' ,
						ADR_BUILDING='$ADR_BUILDING' ,
						ADR_DISTRICT='$ADR_DISTRICT' ,
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
	
	if($command=="DELETE" && $PER_ID && $ADR_ID){
		$cmd = " select  ADR_TYPE from PER_WORKFLOW_ADDRESS where ADR_ID=$ADR_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$ADR_TYPE = $data[ADR_TYPE];
		
		$cmd = " delete from PER_WORKFLOW_ADDRESS where ADR_ID=$ADR_ID ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติที่อยู่ [$PER_ID : $PER_NAME : $ADR_TYPE]");
	} // end if

//  UPDATE ADR_WF_STATUS
	$updtype=explode("-",$command);
	$F_REFRESH = "";
	if($updtype[0]=="UPD" && $PER_ID && $ADR_ID){
		$ADR_WF_STATUS = $updtype[1];
		$cmd2="select * from PER_WORKFLOW_ADDRESS where PER_ID=$PER_ID and ADR_ID=$ADR_ID";
		$count=$db_dpis->send_cmd($cmd2);
//		$db_dpis->show_error(); echo "<hr><br>";
		if($count) { 
			$cmd = " 	update PER_WORKFLOW_ADDRESS set
								ADR_WF_STATUS='$updtype[1]', 
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE'
							where PER_ID=$PER_ID and ADR_ID=$ADR_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$F_REFRESH = "1";
		
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการศึกษา [$PER_ID : $PER_NAME : $EN_CODE]");
			
			if ($ADR_WF_STATUS=="04") {
				$command ="COMMAND"; 	// กรณี 04 อนุมัติ - ถ่ายโอนข้อมูลเข้าแฟ้มจริง หลังจาก update status ที่ แฟ้ม temp
															// โดยแก้ $command ให้ค่า = "COMMAND" เพื่อจะได้เข้า loop ใน if ต่อจากนี้
			}
		} // end if
	} // end if  UPDATE ADR_WF_STATUS
	
	if($command=="COMMAND" && $PER_ID){
		$cmd = " select max(ADR_ID) as max_id from PER_ADDRESS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$ADR_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_ADDRESS (ADR_ID, PER_ID, ADR_TYPE, ADR_NO, ADR_ROAD, ADR_SOI, ADR_MOO, ADR_VILLAGE, 
						ADR_BUILDING, ADR_DISTRICT, AP_CODE, PV_CODE, ADR_HOME_TEL, ADR_OFFICE_TEL, ADR_FAX, ADR_MOBILE,
						ADR_EMAIL, ADR_POSTCODE, ADR_REMARK, UPDATE_USER, UPDATE_DATE)
						values ($ADR_ID, $PER_ID, $ADR_TYPE, '$ADR_NO', '$ADR_ROAD', '$ADR_SOI', '$ADR_MOO', '$ADR_VILLAGE', 
						'$ADR_BUILDING', '$ADR_DISTRICT', '$AP_CODE', '$PV_CODE1', '$TEL_HOME', '$TEL_OFFICE', '$TEL_FAX', '$TEL_MOBILE', 
						'$EMAIL', '$ADR_ZIPCODE', '$ADR_REMARK', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "$cmd<br>";

			//################################################
			//---เมื่ออนุมัติแล้ว ทำการย้ายโฟล์เดอร์ทั้งหมดไปเก็บไว้อีกโฟลเดอร์นึง
			//if($ATTACH_FILE==1){
			$PATH_MAIN = "../attachments";		$mode = 0755;		$MOVERENAME = "";
			$MOVE_CATEGORY = str_replace("_WORKFLOW","",$CATEGORY);	// ตัดคำ _WORKFLOW หาชื่อ category ที่จะย้ายไป
			$FILE_PATH = $PATH_MAIN."/".$PER_CARDNO."/".$CATEGORY."/".$LAST_SUBDIR;		//ชื่อโฟล์เดอร์ของ workflow ที่มีไฟล์เก็บอยู่
			//$FILE_PATH_HOST = "$PATH_MAIN\$PER_CARDNO\$CATEGORY\$LAST_SUBDIR";		//ชื่อโฟล์เดอร์ของ workflow ที่มีไฟล์เก็บอยู่
			
			//ตรวจสอบโฟลเดอร์	(ชื่อโฟลเดอร์ที่จะย้ายไฟล์ไปเก็บไว้)
			$MOVEFIRST_SUBDIR = $PATH_MAIN.'/'.$PER_CARDNO;
			if($ATTACH_FILE==1){
				$MOVESECOND_SUBDIR = $MOVEFIRST_SUBDIR.'/PER_ATTACHMENT';
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/70';
				$MOVERENAME = "PER_ATTACHMENT70";
			}else{
				$MOVESECOND_SUBDIR = $MOVEFIRST_SUBDIR.'/'.$MOVE_CATEGORY;
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/'.$ADR_ID;
				$MOVERENAME = $MOVE_CATEGORY.$ADR_ID;
			}
			echo "$MOVEFINAL_PATH with $MOVERENAME";
				//==================================================
				//---หาว่ามีชื่อโฟลเดอร์นี้อยู่แล้วหรือไม่ ?
				//---สร้างโฟลเดอร์ใหม่ ถ้ายังไม่มีอยู่
				if (!is_dir($MOVEFINAL_PATH)) {		//---1 : ไม่มีโฟลเดอร์นี้อยู่ สร้างขึ้นมาใหม่
					$msgresult1 = $msgresult2 = $msgresult3 = "";

					//โฟล์เดอร์แรก
					if (!is_dir($MOVEFIRST_SUBDIR)) {
						$result1= mkdir($MOVEFIRST_SUBDIR,$mode);
					}
					//โฟล์เดอร์ที่สอง
					if (!is_dir($MOVESECOND_SUBDIR)) {
						if($result1 || is_dir($MOVEFIRST_SUBDIR)){
							$result2 = mkdir($MOVESECOND_SUBDIR,$mode);
						}else{
							$msgresult1 = " <br><span style='color:#FF0000'>สร้างโฟลเดอร์ $MOVEFIRST_SUBDIR ไม่ได้ !!!</span><br>";
						}
					}					
					//โฟล์เดอร์สุดท้าย
					if($result2 || is_dir($MOVESECOND_SUBDIR)){
						$result3= mkdir($MOVEFINAL_PATH,$mode);
					}else{
						$msgresult1="";
						$msgresult2 = "<span style='color:#FF0000'>สร้างโฟลเดอร์ $MOVESECOND_SUBDIR ไม่ได้ !!!</span><br>";
					}
					//umask(0);		echo umask();  //test
					if(!$result3){
						$msgresult2="";
						$msgresult3 = "<span style='color:#FF0000'>สร้างโฟลเดอร์ $MOVEFINAL_PATH ไม่ได้ !!!</span><br>";
					}
				if($msgresult3){	echo $msgresult3;	}
				if($result3 || is_dir($MOVEFINAL_PATH)){		//--->สร้างโฟลเดอร์ได้แล้ว ก็นำไฟล์ย้ายมาเก็บ//rename
					//---วน loop อ่านไฟล์ทั้งหมดที่เก็บไว้ เพื่อย้ายไปโฟล์เดอร์ใหม่
					if (is_dir($FILE_PATH)) {
						if ($dh = opendir($FILE_PATH)) {
							while (($file = readdir($dh)) !== false) {
							if ($file != "." && $file != "..") {
								$movefile = str_replace($CATEGORY.$LAST_SUBDIR,$MOVERENAME,$file);	//ตัดคำทิ้ง ให้ชื่อไฟล์ใหม่
								if(rename("$FILE_PATH/$file","$MOVEFINAL_PATH/$movefile")){
									//echo "สร้าง/ย้ายสำเร็จ<br>";		unlink("$FINAL_PATH/$file");		//---เมื่อย้ายไปเก็บไว้แล้ว ก็ลบไฟล์นั้นทิ้ง
									//--- อัพเดทชื่อใหม่ใน db เพื่อให้มาเชื่อมโยงแสดงรายการได้
									$cmdup = " update 	editor_attachment set real_filename = '$movefile',update_date=$update_date,update_by=$update_by where real_filename='$file' ";
									$db->send_cmd($cmdup);
								}
							} // end if
							} // while loop 
							closedir($dh);
						} // end if
						//__rmdir($FINAL_PATH);	//ลบโฟล์เดอร์นั้นทิ้ง
					} // end if is_dir
				} // end if result
			}else{				//---2 : โฟล์เดอร์นี้ถูกสร้างขึ้นแล้ว ย้ายไฟล์ไปเก็บไว้ได้เลย
					//---วน loop อ่านไฟล์ทั้งหมดที่เก็บไว้ เพื่อย้ายไปโฟล์เดอร์ใหม่
					if (is_dir($FILE_PATH)) {
						if ($dh = opendir($FILE_PATH)) {
							while (($file = readdir($dh)) !== false) {
							if ($file != "." && $file != "..") {
								$movefile = str_replace($CATEGORY.$LAST_SUBDIR,$MOVERENAME,$file);	//ตัดคำทิ้ง ให้ชื่อไฟล์ใหม่
								if(rename("$FILE_PATH/$file","$MOVEFINAL_PATH/$movefile")){
									//echo "ย้ายสำเร็จ<br>";		unlink("$FINAL_PATH/$file");		//---เมื่อย้ายไปเก็บไว้แล้ว ก็ลบไฟล์นั้นทิ้ง
									//--- อัพเดทชื่อใหม่ใน db เพื่อให้มาเชื่อมโยงแสดงรายการได้
									$cmdup = " update 	editor_attachment set real_filename = '$movefile',update_date=$update_date,update_by=$update_by where real_filename='$file' ";
									$db->send_cmd($cmdup);
								}
							} // end if
							} // while loop 
							closedir($dh);
						} // end if
						//__rmdir($FINAL_PATH);	//ลบโฟล์เดอร์นั้นทิ้ง
					} // end if is_dir
			}
			//___echo "TEST : [ $ADR_ID ] <$FILE_PATH ~~ $MOVE_PATH> $FILE_PATH => $MOVEFINAL_PATH<br>$cmdup<br>";
			//###################################

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติที่อยู่ [$PER_ID : $PER_NAME : $ADR_TYPE]");
	} // end if เช็คข้อมูลซ้ำ

	//เซตค่าให้ input
	if(($UPD && $PER_ID && $ADR_ID) || ($VIEW && $PER_ID && $ADR_ID)){
		$cmd = " select		ADR_ID, PER_ID, ADR_TYPE, ADR_NO, ADR_ROAD, ADR_SOI , ADR_MOO, ADR_VILLAGE, ADR_BUILDING, ADR_DISTRICT,
											AP_CODE, PV_CODE, ADR_HOME_TEL, ADR_OFFICE_TEL, ADR_FAX, ADR_MOBILE, ADR_EMAIL, ADR_POSTCODE,
											ADR_REMARK, UPDATE_USER, UPDATE_DATE, ADR_WF_STATUS
							from		PER_WORKFLOW_ADDRESS
							where	ADR_ID=$ADR_ID and ADR_WF_STATUS!='04' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();

		$ADR_ID=$data[ADR_ID]; 
		$ADR_TYPE_tmp=$data[ADR_TYPE];
		if ($ADR_TYPE_tmp == 1)		$ADR_TYPE_tmp1 = 1 ;
		elseif ($ADR_TYPE_tmp == 2)		$ADR_TYPE_tmp2 = 2 ;
		elseif ($ADR_TYPE_tmp == 3)		$ADR_TYPE_tmp3 = 3 ;
		elseif ($ADR_TYPE_tmp == 4)		$ADR_TYPE_tmp4 = 4 ;	
		
		$ADR_NO=$data[ADR_NO];
		$ADR_ROAD=$data[ADR_ROAD]; 
		$ADR_SOI=$data[ADR_SOI]; 
		$ADR_MOO=$data[ADR_MOO]; 
		$ADR_VILLAGE=$data[ADR_VILLAGE];
		$ADR_BUILDING=$data[ADR_BUILDING];
		$ADR_DISTRICT=$data[ADR_DISTRICT]; 
		$ADR_WF_STATUS=$data[ADR_WF_STATUS]; 
		
		$AP_CODE=$data[AP_CODE]; 
		$PV_CODE1=$data[PV_CODE];
		$AP_NAME = $PV_NAME1 = "";
		if($AP_CODE){
			$cmd = " select  AP_NAME from PER_AMPHUR  where AP_CODE='$AP_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$AP_NAME = $data2[AP_NAME];
		} // end if

		if($PV_CODE1){
			$cmd = " select  PV_NAME from PER_PROVINCE  where PV_CODE='$PV_CODE1' ";
			$db_dpis2->send_cmd($cmd);
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
		unset($AP_CODE);
		unset($PV_CODE1); 
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