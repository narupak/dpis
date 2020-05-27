<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");			

        $db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        $db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        $db_dpis4 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
	


	switch($CTRL_TYPE){ 
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
		case 5 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;

			break;
		case 6 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;

			break;
	} // end switch case
	
	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

	//	กำหนดค่า default timezone		//phpinfo();
	function set_default_timezone($timezone){
		if (version_compare(phpversion(), '5', '>=')){
			if(function_exists('date_default_timezone_set')) { 
				$result_set = date_default_timezone_set($timezone); 	// PHP  >= 5.1.0
				//echo date_default_timezone_get();	// PHP  >= 5.1.0
			} 
		}else{		// < version 5
			$result_set = ini_set('date.timezone',$timezone);
		}
	return $result_set;
	}
        /* function ลบ โฟรเดอร์ และ ลบรายการภายในโฟรเดอร์นั้นๆ ทั้งหมด */        
	function rrmdir($dir) {
            if (is_dir($dir)) {
              $objects = scandir($dir);
              foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                  if (filetype($dir."/".$object) == "dir") 
                     rrmdir($dir."/".$object); 
                  else unlink   ($dir."/".$object);
                }
              }
              reset($objects);
              rmdir($dir);
              return TRUE;
            }
        }
       /* end function */
	
	set_default_timezone('Asia/Bangkok');	// ทำเวลาให้เป็นเวลาโซนที่กำหนด
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$CREATE_DATE = date("Y-m-d H:i:s");
	$TODATE = date("Y-m-d");
	$temp_date = explode("-", $TODATE);
	$temp_todate = ($temp_date[0]) ."-". $temp_date[1] ."-". $temp_date[2];
	/*if(!$search_abs_startdate) {
		$search_abs_startdate = "01/". $temp_date[1] ."/". ($temp_date[0] + 543);
	}
	
	if(!$search_abs_enddate) {		// จากเดือนปัจจุบันไปอีก 60 วัน
		if($temp_date[1]<11){
			$search_abs_endmonth = ($temp_date[1] + 2);
			if($search_abs_endmonth<10) $search_abs_endmonth = "0".($search_abs_endmonth);
			$search_abs_endyear = $temp_date[0];
		}else{	// เดือน 11 กับ 12 ต้องไปเริ่มปีหน้า
			$search_abs_endmonth = "0".($temp_date[1] - 10);
			$search_abs_endyear = ($temp_date[0] + 1);
		}
		$max_date = get_max_date($search_abs_endmonth, $search_abs_endyear);
		//$search_abs_enddate = $max_date."/". $search_abs_endmonth ."/". ($search_abs_endyear + 543);
                $search_abs_enddate = $temp_date[2]."/". $temp_date[1] ."/". ($temp_date[0] + 543);
	}*/
	
	
	
	
	
//	echo "-> $result_set  / $CREATE_DATE <br>";

/*--------------กรณี Login เข้ามาเป็น User ธรรมดา--------------------------------------------------------*/

if($SESS_PER_ID && ($command=="" || $command=="CANCEL" ||$command=="SEARCH" ) ){ 	// ผู้ล็อกอินเข้ามา
		$PER_ID = $SESS_PER_ID;

		if($DPISDB=="oci8"){
			$cmd = "	select 		a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, 
												b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
												a.PER_CARDNO,  c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
												c.PL_CODE, d.PN_CODE, e.EP_CODE
								  from 		PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e
								  where 	a.PN_CODE=b.PN_CODE and a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+)  
								  				and a.PER_ID=$PER_ID ";

		} // end if	
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
//		$db_dpis->show_error();

		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] . " " . $data[PER_SURNAME];				
		$PER_TYPE = $data[PER_TYPE];
		$PER_ID = $data[PER_ID];
		$PER_CARDNO = $data[PER_CARDNO];
		
		if($PER_TYPE==1) $ORG_ID = $data[ORG_ID];
		elseif($PER_TYPE==2) $ORG_ID = $data[EMP_ORG_ID];
		elseif ($PER_TYPE == 3) $ORG_ID = $data[EMPS_ORG_ID];
		

		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME = $data2[ORG_NAME];
		
		$command="";
	} // end if


	if($PER_ID){
		
		if($ORG_ID){
			$cmd = " select ORG_ID_REF from PER_ORG where OL_CODE='03' and ORG_ID=$ORG_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$DEPARTMENT_ID = $data[ORG_ID_REF];
		}
		
		if($DEPARTMENT_ID){
			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where OL_CODE='02' and ORG_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$DEPARTMENT_NAME = $data[ORG_NAME];
			$MINISTRY_ID = $data[ORG_ID_REF];
		}

		if($MINISTRY_ID){
			$cmd = " select ORG_NAME from PER_ORG where OL_CODE='01' and ORG_ID=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$MINISTRY_NAME = $data[ORG_NAME];
		}
} // end if

/*--------------------------------------------------------------------------------------------------------------*/
	
	$alert_confirm_absent = "";

        
        /*----------------------------------------------------------Begin-----------------------------------------------------------*/
		$cmdChk ="  SELECT COUNT(COLUMN_NAME) AS CNT
		FROM USER_TAB_COLS
		WHERE TABLE_NAME = 'TA_REQUESTTIME'
		AND UPPER(COLUMN_NAME) IN('POS_STATUS','POS_APPROVE') ";
		$db_dpis->send_cmd($cmdChk);
		$dataChk = $db_dpis->get_array();
		if($dataChk[CNT]=="0"){
			$POS_STATUS = '0';
			$POS_APPROVE_Save = "";	
			$show_approve = 0;
		}else{
			$show_approve = 1;
			if(empty($POS_STATUS)){$POS_STATUS='0';}
			if(!empty($POS_APPROVE0)){
					$POS_APPROVE_Save="'".htmlspecialchars($POS_APPROVE0) ."'"; 
			}else{
					$POS_APPROVE_Save="NULL";
			}
		}

        if($command == "ADD"){
			$val_PER_TYPE = $PER_TYPE;
			$val_PER_ID = $PER_ID;
			$val_PER_CARDNO = "'".$PER_CARDNO."'";
			
			if(empty($forgate1)){
				//if($forgate2=="1"){$val_REQUEST_TYPE="'1'";}
				//if($forgate3=="1"){$val_REQUEST_TYPE="'2'";}
				$val_REQUEST_TYPE="'1'";
			}else{
				/*if($forgate1=="1" && $forgate3=="1"){
					$val_REQUEST_TYPE="'3'";
				}else{
					$val_REQUEST_TYPE="'1'";
				}*/
				
				$val_REQUEST_TYPE="'1'";
				
				
			}
			
			$val_SUBMITTED_DATE ="'".save_date($HIDSUBMITTED_DATE_SAVE)."'";
			$val_REQUEST_DATE ="'".save_date($REQUEST_DATE)."'";
			$START_DATE_Param = save_date($REQUEST_DATE);
			
			$START_DATE="";
			if($forgate1=="1"){
				$val_START_FLAG = "'1'";
				$val_START_TIME = "'".$END_HH_1.$END_II_1."'";
				$START_DATE = $END_HH_1.$END_II_1;
				$START_TIME_Param =$END_HH_1.":".$END_II_1.":00";
			}else{
				$val_START_FLAG = "'0'";
				$val_START_TIME = "NULL";
			}
			$END_DATE="";
			if($forgate2=="1"){
				$val_END_FLAG = "'1'";
				$val_END_TIME = "'".$END_HH_2.$END_II_2."'";
				$END_DATE = $END_HH_2.$END_II_2;
				$END_TIME_Param =$END_HH_2.":".$END_II_2.":00";
			}else{
				$val_END_FLAG = "'0'";
				$val_END_TIME = "NULL";
			}
			$val_MEETING_FLAG = "'2'";
			if($MEETING_FLAG=="1"){$val_MEETING_FLAG = "'1'";}
			$val_SCAN_FLAG = "'2'";
			if($SCAN_FLAG=="1"){$val_SCAN_FLAG = "'1'";}
			$val_OTH_FLAG = "'2'";
			if($OTH_FLAG=="1"){$val_OTH_FLAG = "'1'";}
			
			$val_REQ_SPEC = "'2'";
			if($REQ_SPEC == "1"){$val_REQ_SPEC="'1'";}

			$val_REQ_SPEC_NOTE=null;
			if(!empty($REQ_SPEC_NOTE) && $REQ_SPEC=="1"){
				$val_REQ_SPEC_NOTE=trim($REQ_SPEC_NOTE);
			}
			
			$val_OTH_NOTE = "NULL";
			if(!empty($OTH_NOTE)){
				$val_OTH_NOTE="'".trim($OTH_NOTE)."'";
			}
			
			/*if(!empty($REQUEST_NOTE)){
				$val_REQUEST_NOTE="'".trim($REQUEST_NOTE)."'";
			}*/
			
			/*if($forgate3=="1"){
				$val_REQ_FLAG = "'1'";
				$val_REQ_TIME = "'".$END_HH_3.$END_II_3."'";
				$END_DATE = $END_HH_3.$END_II_3;
				$END_TIME_Param =$END_HH_3.":".$END_II_3.":00";
			}else{
				$val_REQ_FLAG = "'0'";
				$val_REQ_TIME = "NULL";
			}*/
			
			$val_REQ_FLAG = "'0'";
			
			// ใช้ 1=ลาชั่วโมง, 2=ไม่ได้ลาชั่วโมง
			// ใช้ 1=ลาชั่วโมง, 2=ไม่ได้ลาชั่วโมง
			$val_REQUEST_NOTE="'2'";
			if($REQUEST_NOTE=="1"){$val_REQUEST_NOTE = "'1'";}
			
			$val_REQ_TIME="'2'";
			if($REQ_TIME=="1"){$val_REQ_TIME = "'1'";}
			
			
			if($PER_AUDIT_FLAG==0 && $SESS_USERGROUP!=1 && $NAME_GROUP_HRD!='HRD' ){
				//if($CHK_REQ_STATUS=="1"){
					$val_REQ_STATUS = "1";
				//}else{
					//$val_REQ_STATUS = "0";
				//}
				
			}else{
				$val_REQ_STATUS = "1";
			}
			
			$val_CREATE_USER = $SESS_USERID;
			$val_CREATE_DATE = "'".$CREATE_DATE."'";
			$val_UPDATE_USER = $SESS_USERID;
			$val_UPDATE_DATE = "'".$UPDATE_DATE."'";
			
			if(!empty($REVIEW1_PER_NAME)){
				if(!empty($ABS_REVIEW1_FLAG)){
					$val_ALLOW_FLAG = $ABS_REVIEW1_FLAG;
					$val_ALLOW_DATE = "'".$CREATE_DATE."'";
				}else{
					$val_ALLOW_FLAG = 0;
					$val_ALLOW_DATE = "NULL";
				}
				$val_ALLOW_USER = $REVIEW1_PER_ID;
				
			}else{
				$val_ALLOW_FLAG = "0";
				$val_ALLOW_USER = "NULL";
				$val_ALLOW_DATE  = "NULL";
			}
			$val_ALLOW_NOTE = "NULL";
			if(!empty($ALLOW_NOTE)){
				$val_ALLOW_NOTE = "'".trim($ALLOW_NOTE)."'";
			}
			if(!empty($APPROVE_PER_NAME)){
				if(!empty($ABS_APPROVE_FLAG)){
					$val_APPROVE_FLAG = $ABS_APPROVE_FLAG;
					$val_APPROVE_DATE = "'".$CREATE_DATE."'";
				}else{
					$val_APPROVE_FLAG = 0;
					$val_APPROVE_DATE = "NULL";
				}
				
				$val_APPROVE_USER =$APPROVE_PER_ID;
				
			}else{
				$val_APPROVE_FLAG= "0";
				$val_APPROVE_USER= "NULL";
				$val_APPROVE_DATE  = "NULL";
			}
			$val_APPROVE_NOTE="NULL";
			if(!empty($APPROVE_NOTE)){
				$val_APPROVE_NOTE= "'".trim($APPROVE_NOTE)."'";
			}
			
			$Save_WC_CODE=$WC_CODE;

			$cmdChk6 ="select count(*) as CNT from SYSTEM_CONFIG WHERE UPPER(CONFIG_NAME)= 'IS_OPEN_TIMEATT_ES'";
            $db_dpis->send_cmd($cmdChk6);
			$dataChk6 = $db_dpis->get_array();
			
			if($dataChk6[CNT]<=0){	
					$Save_WC_CODE="02";
			}else{
				if($IS_OPEN_TIMEATT_ES!="OPEN"){
					$Save_WC_CODE="02";
				}
			}
			

			$Save_NEXTDAY_EXIT=$NEXTDAY_EXIT;
			if(empty($Save_NEXTDAY_EXIT)){
				$Save_NEXTDAY_EXIT="0";
			}
							
			
			$Y=substr($REQUEST_DATE,6,4);
			$M=substr($REQUEST_DATE,3,4)+0;
			$cmd2 = " select PER_CARDNO,ORG_ID from PER_PERSONAL where PER_ID=$val_PER_ID"; 
			$db_dpis2->send_cmd($cmd2);
			$data2 = $db_dpis2->get_array();
			$DEPARTMENT_ID_ASS = $data2[ORG_ID]; 
                        $PER_CARDNO_save = trim($data2[PER_CARDNO]);
			
			$cmd1 = " select CLOSE_YEAR from PER_WORK_TIME_CONTROL where CLOSE_YEAR=$Y AND CLOSE_MONTH=$M AND CLOSE_DATE IS NOT NULL AND DEPARTMENT_ID=".$DEPARTMENT_ID_ASS;
			$count_duplicate1 = $db_dpis->send_cmd($cmd1);
			if($count_duplicate1 <= 0){
				
					$cmd = " select REQUEST_DATE from TA_REQUESTTIME where PER_ID=$val_PER_ID AND REQUEST_DATE=". $val_REQUEST_DATE ." ";
					$count_duplicate = $db_dpis->send_cmd($cmd);
					if($count_duplicate <= 0){
						$cmd ="INSERT INTO TA_REQUESTTIME (REC_ID,PER_TYPE,PER_ID,PER_CARDNO,REQUEST_TYPE,SUBMITTED_DATE,REQUEST_DATE,START_FLAG,
											START_TIME,END_FLAG,END_TIME,MEETING_FLAG,SCAN_FLAG,OTH_FLAG,OTH_NOTE, REQUEST_NOTE,
											REQ_FLAG,REQ_TIME,REQ_STATUS,CREATE_USER,CREATE_DATE,UPDATE_USER,UPDATE_DATE,ALLOW_FLAG,
											ALLOW_USER,ALLOW_DATE,ALLOW_NOTE,APPROVE_FLAG,APPROVE_USER,APPROVE_DATE,APPROVE_NOTE,
											WC_CODE,NEXTDAY_EXIT,POS_STATUS,POS_APPROVE, REQ_SPEC, REQ_SPEC_NOTE)
							VALUES(TA_REQUESTTIME_SEQ.NEXTVAL,$val_PER_TYPE,$val_PER_ID,$val_PER_CARDNO,$val_REQUEST_TYPE,$val_SUBMITTED_DATE,$val_REQUEST_DATE,$val_START_FLAG,
									$val_START_TIME,$val_END_FLAG,$val_END_TIME,$val_MEETING_FLAG,$val_SCAN_FLAG,$val_OTH_FLAG,$val_OTH_NOTE,$val_REQUEST_NOTE,
									$val_REQ_FLAG,$val_REQ_TIME,$val_REQ_STATUS,$val_CREATE_USER,$val_CREATE_DATE,$val_UPDATE_USER,$val_UPDATE_DATE,$val_ALLOW_FLAG,
									$val_ALLOW_USER,$val_ALLOW_DATE,$val_ALLOW_NOTE,$val_APPROVE_FLAG,$val_APPROVE_USER,$val_APPROVE_DATE,$val_APPROVE_NOTE,
									'$Save_WC_CODE',$Save_NEXTDAY_EXIT, $POS_STATUS, $POS_APPROVE_Save,$val_REQ_SPEC, '$val_REQ_SPEC_NOTE' )";
						$db_dpis->send_cmd($cmd);
						//echo "<pre>".$cmd;
						$cmd = " SELECT TA_REQUESTTIME_SEQ.currval AS CURID FROM dual ";
						$db_dpis1->send_cmd($cmd);
						$data1 = $db_dpis1->get_array();
						$val_REC_ID = $data1[CURID];
						
						insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 >$MENU_TITLE_LV2 > บันทึกคำร้องไม่ได้ลงเวลา [".$val_REC_ID." : ".$val_PER_TYPE." : ".$val_PER_CARDNO." : ".$val_REQUEST_TYPE."]");
						
                                                //========================================== การอัพโหลดไฟล์ ================================================
                                                    function calsizebyte ($size) {
                                                        if ($size < 1024) {
                                                                $issize = $size."b";
                                                        } else {
                                                                $issize = sprintf ("%0.0f",$size / 1024)."KB";
                                                                if ($issize >= 1024) $issize = sprintf ("%0.02f",$issize / 1024)."MB";
                                                        }
                                                        return $issize;
                                                    }
                                                    if($db_type=="oci8"){
                                                        $db_obj = $db_dpis3;
                                                    }else{
                                                        $db_obj = $db;
                                                    }
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
                                                    
                                                    $PATH_MAIN = "../attachments";
                                                    $CATEGORY = "TA_REQUESTTIME";  //โฟรเดอร์ชื่องาน
                                                    $LAST_SUBDIR = $val_REC_ID; //โฟรเดอร์สุดท้าย เป็น ไอดีของรายการนั้นๆ

                                                    //ตัวแปลสำหรับเก็บค่าลง data base
                                                    $description = "";
                                                    $size = calsizebyte($filename_size);
                                                    //ชื่อที่เก็บลงใน database
                                                    $file_namedb = $PER_CARDNO_save."_".$CATEGORY.$LAST_SUBDIR;
                                                    //ตรวจสอบโฟลเดอร์
                                                    $FIRST_SUBDIR = $PATH_MAIN.'/'.$PER_CARDNO_save;
                                                    $SECOND_SUBDIR = $FIRST_SUBDIR.'/'.$CATEGORY;
                                                    $FINAL_PATH = $SECOND_SUBDIR.'/'.$LAST_SUBDIR;

                                                    //echo "file_namedb=$file_namedb , FIRST_SUBDIR=$FIRST_SUBDIR , SECOND_SUBDIR=$SECOND_SUBDIR , FINAL_PATH=$FINAL_PATH command=$command<br>";

                                                    //echo "filename_name :: $filename_name<br>";
                                                    if ($filename_name != "") {
                                                        $pos = strrpos($filename_name, ".");	
                                                        $file_name = substr($filename_name, 0, ($pos - strlen($filename_name)));
                                                        $file_ext = substr($filename_name, ($pos+1));
                                                        $filename_encode = $file_namedb."_". $SESS_USERID ."_". date("Ymd") ."_". md5($file_name) .".". $file_ext;

                                                        //สร้างโฟลเดอร์ใหม่ ถ้ายังไม่มีอยู่
                                                        if (!is_dir($FINAL_PATH)) {
                                                                $msgresult1 = $msgresult2 = $msgresult3 = "";
                                                                $mode = 0755;
                                                                //โฟล์เดอร์แรก
                                                                if (!is_dir($FIRST_SUBDIR)) {
                                                                        $result1= mkdir($FIRST_SUBDIR,$mode);
                                                                }
                                                                //โฟล์เดอร์ที่สอง
                                                                if (!is_dir($SECOND_SUBDIR)) {
                                                                        if($result1 || is_dir($FIRST_SUBDIR)){
                                                                                $result2 = mkdir($SECOND_SUBDIR,$mode);
                                                                        }else{
                                                                                $msgresult1 = " <br><span style='color:#FF0000'>สร้างโฟลเดอร์ $FIRST_SUBDIR ไม่ได้ !!!</span><br>";
                                                                        }
                                                                }					
                                                                //โฟล์เดอร์สุดท้าย
                                                                if($result2 || is_dir($SECOND_SUBDIR)){
                                                                        $result3= mkdir($FINAL_PATH,$mode);
                                                                }else{
                                                                        $msgresult1="";
                                                                        $msgresult2 = "<span style='color:#FF0000'>สร้างโฟลเดอร์ $SECOND_SUBDIR ไม่ได้ !!!</span><br>";
                                                                }
                                                                //umask(0);		echo umask();  //test
                                                                if(!$result3){
                                                                        $msgresult2="";
                                                                        $msgresult3 = "<span style='color:#FF0000'>สร้างโฟลเดอร์ $FINAL_PATH ไม่ได้ !!!</span><br>";
                                                                }
                                                        }
                                                        if($msgresult3){	echo $msgresult3;	}

                                                        //เมื่อสร้างโฟลเดอร์ได้แล้ว ก็เอาไฟล์นำเข้าไป
                                                        if (is_dir($FINAL_PATH)){
                                                            if(move_uploaded_file($filename,"$FINAL_PATH/$filename_encode") ){
                                                                //เก็บข้อมูลลง database
                                                                if($db_type=="oci8") {	
                                                                        //ดึง editor_attachment ID ล่าสุดมา
                                                                        $cmd = " select ID from EDITOR_ATTACHMENT order by ID DESC"; //  $cmd = " select max(ID) as max_id from EDITOR_ATTACHMENT";
                                                                        $db_dpis4->send_cmd($cmd);
                                                                        //$db_dpis4->show_error();
                                                                        $data = $db_dpis4->get_array();
                                                                        $EA_ID = $data[ID]+1;						
                                                                        $cmd = 	" 	insert into EDITOR_ATTACHMENT
                                                                                                        (ID,REAL_FILENAME, SHOW_FILENAME, FILE_TYPE, DESCRIPTION, FILE_SIZE, USER_ID, USER_GROUP_ID, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
                                                                                                        values 
                                                                                                        ($EA_ID,'$filename_encode', '$filename_name', '$filename_type', '$description', '$size', $SESS_USERID, $SESS_USERGROUP, $update_date, $update_by, $update_date, $update_by) ";
                                                                }else{	
                                                                        $cmd = 	" 	insert into editor_attachment 
                                                                                                        (real_filename, show_filename, file_type, description, size, user_id, user_group_id, create_date, update_date, update_by)
                                                                                                        values 
                                                                                                        ('$filename_encode', '$filename_name', '$filename_type', '$description', '$size', $SESS_USERID, $SESS_USERGROUP, $update_date, $update_date, $update_by) ";
                                                                }
                                                                $db_obj->send_cmd($cmd);
                                                                //echo "นำเข้าไฟล์ $filename_name [$description] แล้ว<br>";
                                                                insert_log("UPLOAD $file_namedb FILE $filename_name [$description]");
                                                            } // if success upload file
                                                        }
                                                    }

                                                //======================================== End การอัพโหลดไฟล์ ==============================================
                                                
                                                
                                                
						// ยังไม่ต้อง ไปบันทึกที่ PER_WORK_TIME ให้ไปบันทึกตอนประมวณผลทีเดียว echo "<br><br>".$cmd;
						echo "<script>window.location='../admin/es_t0206_inout_list.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2;</script>";
						/*?MENU_ID_LV0=41&MENU_ID_LV1=510&MENU_ID_LV2=435*/
					   /*echo "<script>window.location='../admin/es_t0206_inout_list.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2&UPD=1&REC_ID=$val_REC_ID';</script>";*/
					}else{
						$data = $db_dpis->get_array();			
						$err_text = "วันที่ขออนุญาตซ้ำ [".show_date_format($data[REQUEST_DATE], $DATE_DISPLAY)."]";
					} // endif
			}else{
				
						$err_text_dup = "<br>มีการยืนยัน/ปิดรอบข้อมูลการลงเวลาของเดือนนี้ ไปแล้ว<br>หากต้องการทำรายการ กรุณาติดต่อ กจ. หรือผู้ดูแลข้อมูลประจำหน่วยงาน";
		    } // endif
			$command="";
        }
		
		 if($command == "UPDATE"){
            
            $val_PER_TYPE = $PER_TYPE;
            $val_PER_ID = $PER_ID;
            $val_PER_CARDNO = "'".$PER_CARDNO."'";
            
			if(empty($forgate1)){
				//if($forgate2=="1"){$val_REQUEST_TYPE="'1'";}
				//if($forgate3=="1"){$val_REQUEST_TYPE="'2'";}
				$val_REQUEST_TYPE="'1'";
			}else{
				/*if($forgate1=="1" && $forgate3=="1"){
					$val_REQUEST_TYPE="'3'";
				}else{
					$val_REQUEST_TYPE="'1'";
				}*/
				
				$val_REQUEST_TYPE="'1'";
				
			}
            $val_SUBMITTED_DATE ="'".save_date($HIDSUBMITTED_DATE_SAVE)."'";
            $val_REQUEST_DATE ="'".save_date($REQUEST_DATE)."'";
            $START_DATE_Param = save_date($REQUEST_DATE);
            
            $START_DATE="";
            if($forgate1=="1"){
                $val_START_FLAG = "'1'";
                $val_START_TIME = "'".$END_HH_1.$END_II_1."'";
                $START_DATE = $END_HH_1.$END_II_1;
                $START_TIME_Param =$END_HH_1.":".$END_II_1.":00";
            }else{
                $val_START_FLAG = "'0'";
                $val_START_TIME = "NULL";
            }
			$END_DATE="";
            if($forgate2=="1"){
                $val_END_FLAG = "'1'";
                $val_END_TIME = "'".$END_HH_2.$END_II_2."'";
                $END_DATE = $END_HH_2.$END_II_2;
                $END_TIME_Param =$END_HH_2.":".$END_II_2.":00";
            }else{
                $val_END_FLAG = "'0'";
                $val_END_TIME = "NULL";
			}
            $val_MEETING_FLAG = "'2'";
            if($MEETING_FLAG=="1"){$val_MEETING_FLAG = "'1'";}
            $val_SCAN_FLAG = "'2'";
            if($SCAN_FLAG=="1"){$val_SCAN_FLAG = "'1'";}
            $val_OTH_FLAG = "'2'";
            if($OTH_FLAG=="1"){$val_OTH_FLAG = "'1'";}
			
			$val_REQ_SPEC = "'2'";
			if($REQ_SPEC == "1"){$val_REQ_SPEC="'1'";}

			$val_REQ_SPEC_NOTE=null;
			if(!empty($REQ_SPEC_NOTE)&& $REQ_SPEC=="1"){
				$val_REQ_SPEC_NOTE=trim($REQ_SPEC_NOTE);
			}
            $val_OTH_NOTE = "NULL";
            if(!empty($OTH_NOTE)){
                $val_OTH_NOTE="'".trim($OTH_NOTE)."'";
            }
           /* if(!empty($REQUEST_NOTE)){
                $val_REQUEST_NOTE="'".trim($REQUEST_NOTE)."'";
            }*/
            
           /* if($forgate3=="1"){
                $val_REQ_FLAG = "'1'";
                $val_REQ_TIME = "'".$END_HH_3.$END_II_3."'";
                $END_DATE = $END_HH_3.$END_II_3;
                $END_TIME_Param =$END_HH_3.":".$END_II_3.":00";
            }else{
                $val_REQ_FLAG = "'0'";
                $val_REQ_TIME = "NULL";
            }*/
			$val_REQ_FLAG = "'0'";
			
			// ใช้ 1=ลาชั่วโมง, 2=ไม่ได้ลาชั่วโมง
			$val_REQUEST_NOTE="'2'";
			if($REQUEST_NOTE=="1"){$val_REQUEST_NOTE = "'1'";}
			
			$val_REQ_TIME="'2'";
			if($REQ_TIME=="1"){$val_REQ_TIME = "'1'";}
			
			
			
			
			if($PER_AUDIT_FLAG==0 && $SESS_USERGROUP!=1 && $NAME_GROUP_HRD!='HRD'  ){
				if($ODL_CHK_REQ_STATUS=="1"){
					$val_REQ_STATUS = "1";
				}else{
					//if($CHK_REQ_STATUS=="1"){
					$val_REQ_STATUS = "1";
					//}else{
						//$val_REQ_STATUS = "0";
					//}
				}
				
			}else{
				$val_REQ_STATUS = "1";
			}
            
            $val_CREATE_USER = $SESS_USERID;
            $val_CREATE_DATE = "'".$CREATE_DATE."'";
            $val_UPDATE_USER = $SESS_USERID;
            $val_UPDATE_DATE = "'".$UPDATE_DATE."'";
            
            if(!empty($REVIEW1_PER_NAME)){
				if(!empty($ABS_REVIEW1_FLAG)){
					$val_ALLOW_FLAG = $ABS_REVIEW1_FLAG;
					$val_ALLOW_DATE = "'".$CREATE_DATE."'";
				}else{
					$val_ALLOW_FLAG = 0;
					$val_ALLOW_DATE = "NULL";
				}
                $val_ALLOW_USER = $REVIEW1_PER_ID;
                
            }else{
                $val_ALLOW_FLAG = "0";
                $val_ALLOW_USER = "NULL";
                $val_ALLOW_DATE  = "NULL";
            }
            $val_ALLOW_NOTE = "NULL";
            if(!empty($ALLOW_NOTE)){
                $val_ALLOW_NOTE = "'".trim($ALLOW_NOTE)."'";
            }
            if(!empty($APPROVE_PER_NAME)){
				if(!empty($ABS_APPROVE_FLAG)){
					$val_APPROVE_FLAG = $ABS_APPROVE_FLAG;
					$val_APPROVE_DATE = "'".$CREATE_DATE."'";
				}else{
					$val_APPROVE_FLAG = 0;
					$val_APPROVE_DATE = "NULL";
				}
                
                $val_APPROVE_USER =$APPROVE_PER_ID;
                
            }else{
                $val_APPROVE_FLAG= "0";
                $val_APPROVE_USER= "NULL";
                $val_APPROVE_DATE  = "NULL";
            }
            $val_APPROVE_NOTE="NULL";
            if(!empty($APPROVE_NOTE)){
                $val_APPROVE_NOTE= "'".trim($APPROVE_NOTE)."'";
            }
			
			$Save_WC_CODE=$WC_CODE;

			$cmdChk6 ="select count(*) as CNT from SYSTEM_CONFIG WHERE UPPER(CONFIG_NAME)= 'IS_OPEN_TIMEATT_ES'";
            $db_dpis->send_cmd($cmdChk6);
			$dataChk6 = $db_dpis->get_array();
			
			if($dataChk6[CNT]<=0){	
					$Save_WC_CODE="02";
			}else{
				if($IS_OPEN_TIMEATT_ES!="OPEN"){
					$Save_WC_CODE="02";
				}
			}
			
			$Save_NEXTDAY_EXIT=$NEXTDAY_EXIT;
			if(empty($Save_NEXTDAY_EXIT)){
				$Save_NEXTDAY_EXIT="0";
			}
			
			$cmd = " select REQUEST_DATE from TA_REQUESTTIME where PER_ID=$val_PER_ID AND REQUEST_DATE=". $val_REQUEST_DATE ." AND REC_ID !=$REC_ID  ";
			$count_duplicate = $db_dpis->send_cmd($cmd);
			if($count_duplicate <= 0){
				
				$ALLOW_DATE_save = "";
				$ALLOW_FLAG_save = "";
				$ALLOW_USER_save = "";
				$ALLOW_NOTE_save = "";
				if($ODL_ABS_REVIEW1_FLAG==0 || $SESS_USERGROUP==1){
					$ALLOW_FLAG_save = "ALLOW_FLAG=$val_ALLOW_FLAG,";
					$ALLOW_USER_save = "ALLOW_USER=$val_ALLOW_USER,";
					$ALLOW_DATE_save = "ALLOW_DATE=$val_ALLOW_DATE,";
					$ALLOW_NOTE_save = "ALLOW_NOTE=$val_ALLOW_NOTE,";
					
				}
				
				$APPROVE_DATE_save = "";
				$APPROVE_FLAG_save = "";
				$APPROVE_USER_save = "";
				$APPROVE_NOTE_save = "";
				if($ODL_ABS_APPROVE_FLAG==0 || $SESS_USERGROUP==1){
					$APPROVE_FLAG_save = "APPROVE_FLAG=$val_APPROVE_FLAG,";
					$APPROVE_USER_save = "APPROVE_USER=$val_APPROVE_USER,";
					$APPROVE_DATE_save = "APPROVE_DATE=$val_APPROVE_DATE,";
					$APPROVE_NOTE_save = "APPROVE_NOTE=$val_APPROVE_NOTE,";
				}
				
				if($PER_ID!=$SESS_PER_ID &&  ($SESS_USERGROUP!=1) && ($NAME_GROUP_HRD!='HRD') && (empty($PER_AUDIT_FLAG))){
					
					if($SESS_PER_ID==$REVIEW1_PER_ID){
						$cmd ="UPDATE TA_REQUESTTIME  SET 
							".$ALLOW_FLAG_save.$ALLOW_USER_save.$ALLOW_DATE_save.$ALLOW_NOTE_save."
							UPDATE_USER=$val_UPDATE_USER,
							UPDATE_DATE=$val_UPDATE_DATE,
							POS_STATUS =$POS_STATUS ,
							POS_APPROVE =$POS_APPROVE_Save
							WHERE REC_ID=$REC_ID";
						$db_dpis->send_cmd($cmd);
						
					}else if($SESS_PER_ID==$APPROVE_PER_ID){
						$cmd ="UPDATE TA_REQUESTTIME  SET 
							".$APPROVE_FLAG_save.$APPROVE_USER_save.$APPROVE_DATE_save.$APPROVE_NOTE_save."
							UPDATE_USER=$val_UPDATE_USER,
							UPDATE_DATE=$val_UPDATE_DATE,
							POS_STATUS =$POS_STATUS ,
							POS_APPROVE =$POS_APPROVE_Save
							WHERE REC_ID=$REC_ID";
						$db_dpis->send_cmd($cmd);
					}
					
				}else if($PER_ID==$SESS_PER_ID &&  ($SESS_USERGROUP!=1) && ($NAME_GROUP_HRD!='HRD') && (empty($PER_AUDIT_FLAG))){
					if($APPROVE_FLAG==1 || $ALLOW_FLAG==1){
						$cmd ="UPDATE TA_REQUESTTIME  SET 
							".$ALLOW_USER_save."
							".$APPROVE_USER_save."
							UPDATE_USER=$val_UPDATE_USER,
							UPDATE_DATE=$val_UPDATE_DATE,
							POS_STATUS =$POS_STATUS ,
							POS_APPROVE =$POS_APPROVE_Save
							WHERE REC_ID=$REC_ID";
						$db_dpis->send_cmd($cmd);
					}else{
						$cmd ="UPDATE TA_REQUESTTIME  SET 
							PER_TYPE=$val_PER_TYPE,PER_ID=$val_PER_ID,
							PER_CARDNO=$val_PER_CARDNO,REQUEST_TYPE=$val_REQUEST_TYPE,
							SUBMITTED_DATE=$val_SUBMITTED_DATE,REQUEST_DATE=$val_REQUEST_DATE,
							START_FLAG=$val_START_FLAG,
							START_TIME=$val_START_TIME,END_FLAG=$val_END_FLAG,
							END_TIME=$val_END_TIME,MEETING_FLAG=$val_MEETING_FLAG,
							SCAN_FLAG=$val_SCAN_FLAG,OTH_FLAG=$val_OTH_FLAG,
							OTH_NOTE=$val_OTH_NOTE,REQUEST_NOTE=$val_REQUEST_NOTE,
							REQ_FLAG=$val_REQ_FLAG,REQ_TIME=$val_REQ_TIME,
							REQ_SPEC = $val_REQ_SPEC,REQ_SPEC_NOTE =  '$val_REQ_SPEC_NOTE',
							REQ_STATUS=$val_REQ_STATUS,UPDATE_USER=$val_UPDATE_USER,
							POS_STATUS =$POS_STATUS ,POS_APPROVE =$POS_APPROVE_Save ,
							UPDATE_DATE=$val_UPDATE_DATE,
							".$ALLOW_FLAG_save.$ALLOW_USER_save.$ALLOW_DATE_save.$ALLOW_NOTE_save."
							".$APPROVE_FLAG_save.$APPROVE_USER_save.$APPROVE_DATE_save.$APPROVE_NOTE_save."
							WC_CODE='$Save_WC_CODE',
							NEXTDAY_EXIT='$Save_NEXTDAY_EXIT'
							WHERE REC_ID=$REC_ID";
						$db_dpis->send_cmd($cmd);
						
					}
					//echo "<pre>".$cmd;
					
					
				}else{
					$cmd ="UPDATE TA_REQUESTTIME  SET 
							PER_TYPE=$val_PER_TYPE,PER_ID=$val_PER_ID,
							PER_CARDNO=$val_PER_CARDNO,REQUEST_TYPE=$val_REQUEST_TYPE,
							SUBMITTED_DATE=$val_SUBMITTED_DATE,REQUEST_DATE=$val_REQUEST_DATE,
							START_FLAG=$val_START_FLAG,
							START_TIME=$val_START_TIME,END_FLAG=$val_END_FLAG,
							END_TIME=$val_END_TIME,MEETING_FLAG=$val_MEETING_FLAG,
							SCAN_FLAG=$val_SCAN_FLAG,OTH_FLAG=$val_OTH_FLAG,
							OTH_NOTE=$val_OTH_NOTE,REQUEST_NOTE=$val_REQUEST_NOTE,
							REQ_FLAG=$val_REQ_FLAG,REQ_TIME=$val_REQ_TIME,
							REQ_SPEC = $val_REQ_SPEC,REQ_SPEC_NOTE =  '$val_REQ_SPEC_NOTE',
							REQ_STATUS=$val_REQ_STATUS,UPDATE_USER=$val_UPDATE_USER,
							POS_STATUS =$POS_STATUS ,POS_APPROVE =$POS_APPROVE_Save ,
							UPDATE_DATE=$val_UPDATE_DATE,
							".$ALLOW_FLAG_save.$ALLOW_USER_save.$ALLOW_DATE_save.$ALLOW_NOTE_save."
							".$APPROVE_FLAG_save.$APPROVE_USER_save.$APPROVE_DATE_save.$APPROVE_NOTE_save."
							WC_CODE='$Save_WC_CODE',
							NEXTDAY_EXIT='$Save_NEXTDAY_EXIT'
							WHERE REC_ID=$REC_ID";
					 $db_dpis->send_cmd($cmd);
					
					
				}
				
				//echo "<pre>".$cmd;
				
				
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 >$MENU_TITLE_LV2 > แก้ไขคำร้องไม่ได้ลงเวลา [".$REC_ID." : ".$val_PER_TYPE." : ".$val_PER_CARDNO." : ".$val_REQUEST_TYPE."]");
				
				// ยังไม่ต้อง ไปบันทึกที่ PER_WORK_TIME ให้ไปบันทึกตอนประมวณผลทีเดียว echo "<br><br>".$cmd;
				
				
				/*?MENU_ID_LV0=41&MENU_ID_LV1=510&MENU_ID_LV2=435*/
			   /*echo "<script>window.location='../admin/es_t0206_inout_list.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2&UPD=1&REC_ID=$REC_ID';</script>";*/
			}else{
				$data = $db_dpis->get_array();			
				$err_text = "วันที่ขออนุญาตซ้ำ [".show_date_format($data[REQUEST_DATE], $DATE_DISPLAY)."]";
			} // endif
				$command ="";
			//--------------------------------- หาข้อมูลคน Login---------------------------------------------------------------------
				$PER_ID = $SESS_PER_ID;
				if($DPISDB=="oci8"){
					$cmd = "	select 		a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, 
														b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
														a.PER_CARDNO,  c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
														c.PL_CODE, d.PN_CODE, e.EP_CODE
										from 		PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e
										where 	a.PN_CODE=b.PN_CODE and a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+)  
														and a.PER_ID=$PER_ID ";

				} // end if	
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
			//		$db_dpis->show_error();

				$PER_NAME = $data[PN_NAME] . $data[PER_NAME] . " " . $data[PER_SURNAME];				
				$PER_TYPE = $data[PER_TYPE];
				$PER_ID = $data[PER_ID];
				$PER_CARDNO = $data[PER_CARDNO];
				
				if($PER_TYPE==1) $ORG_ID = $data[ORG_ID];
				elseif($PER_TYPE==2) $ORG_ID = $data[EMP_ORG_ID];
				elseif ($PER_TYPE == 3) $ORG_ID = $data[EMPS_ORG_ID];
				

				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$ORG_NAME = $data2[ORG_NAME];
        }
        
        /*----------------------------------------------------------End-----------------------------------------------------------*/
	
if($UPD){

	$cmd = "	  select 	PER_TYPE,PER_ID,PER_CARDNO,REQUEST_TYPE,SUBMITTED_DATE,REQUEST_DATE,START_FLAG,
                                START_TIME,END_FLAG,END_TIME,MEETING_FLAG,SCAN_FLAG,OTH_FLAG,OTH_NOTE,REQUEST_NOTE,
                                REQ_FLAG,REQ_TIME,REQ_STATUS,UPDATE_USER,UPDATE_DATE,ALLOW_FLAG,
                                ALLOW_USER,ALLOW_DATE,ALLOW_NOTE,APPROVE_FLAG,APPROVE_USER,APPROVE_DATE,APPROVE_NOTE,
								WC_CODE,NEXTDAY_EXIT, POS_STATUS, POS_APPROVE,REQ_SPEC,
								REQ_SPEC_NOTE
					  from 		TA_REQUESTTIME
					  where 		REC_ID=$REC_ID ";
//echo "<pre>".$cmd;
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$PER_ID = $data[PER_ID];
	$POS_STATUS = $data[POS_STATUS];
	$POS_APPROVE = $data[POS_APPROVE];
	$cmd ="select g.PN_SHORTNAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW
				from PER_PERSONAL a 
				left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE) 
				where PER_ID = $PER_ID ";
	$db->send_cmd($cmd);
	$data2 = $db->get_array();
	$PER_NAME =  trim($data2[FULLNAME_SHOW]);
	
	$PER_CARDNO = $data[PER_CARDNO];
	$PER_TYPE = $data[PER_TYPE];
	$WC_CODE = $data[WC_CODE];

	if($SESS_USERGROUP!=1){
	$cmd ="select WC_NAME
				from PER_WORK_CYCLE
				where WC_CODE = '$WC_CODE' ";
	$db->send_cmd($cmd);
	$data2 = $db->get_array();
	$WC_NAME =  trim($data2[WC_NAME]);
	}
	$NEXTDAY_EXIT = $data[NEXTDAY_EXIT];
	
	$P_EXTRATIME_SHOW="";
	if($NEXTDAY_EXIT==1){
		$P_EXTRATIME_SHOW="ของวันถัดไป";
	}
	
	if($DPISDB=="oci8"){
			$cmd = "	select 		a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, 
												b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
												a.PER_CARDNO,  c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
												c.PL_CODE, d.PN_CODE, e.EP_CODE
								  from 		PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e
								  where 	a.PN_CODE=b.PN_CODE and a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+)  
								  				and a.PER_ID=$PER_ID ";

		} // end if	
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();
//		$db_dpis->show_error();

		
		if($PER_TYPE==1) $ORG_ID = $data1[ORG_ID];
		elseif($PER_TYPE==2) $ORG_ID = $data1[EMP_ORG_ID];
		elseif ($PER_TYPE == 3) $ORG_ID = $data1[EMPS_ORG_ID];
		

		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ORG_NAME = $data2[ORG_NAME];
	} // end if

	if($PER_ID){
		
		if($ORG_ID){
			$cmd = " select ORG_ID_REF from PER_ORG where OL_CODE='03' and ORG_ID=$ORG_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DEPARTMENT_ID = $data2[ORG_ID_REF];
		}
		
		if($DEPARTMENT_ID){
			$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where OL_CODE='02' and ORG_ID=$DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$DEPARTMENT_NAME = $data2[ORG_NAME];
			$MINISTRY_ID = $data2[ORG_ID_REF];
		}

		if($MINISTRY_ID){
			$cmd = " select ORG_NAME from PER_ORG where OL_CODE='01' and ORG_ID=$MINISTRY_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$MINISTRY_NAME = $data2[ORG_NAME];
		}
	
	$SUBMITTED_DATE_SAVE = show_date_format($data[SUBMITTED_DATE], 1);
	$HIDSUBMITTED_DATE_SAVE = show_date_format($data[SUBMITTED_DATE], 1);
	$REQUEST_DATE = show_date_format($data[REQUEST_DATE], 1);
	$forgate1 = $data[START_FLAG];
	$END_HH_1 = substr($data[START_TIME],0,2);
	$END_II_1 = substr($data[START_TIME],2,2);
	$forgate2 = $data[END_FLAG];
	$END_HH_2 = substr($data[END_TIME],0,2);
	$END_II_2 = substr($data[END_TIME],2,2);
	$MEETING_FLAG = $data[MEETING_FLAG];
	$SCAN_FLAG = $data[SCAN_FLAG];
	$OTH_FLAG = $data[OTH_FLAG];
	$OTH_NOTE = $data[OTH_NOTE];
	//$forgate3 = $data[REQ_FLAG];
	//$END_HH_3 = substr($data[REQ_TIME],0,2);
	//$END_II_3 = substr($data[REQ_TIME],2,2);
	$REQUEST_NOTE = $data[REQUEST_NOTE];
	$REQ_TIME = $data[REQ_TIME];
	$REQ_SPEC = $data[REQ_SPEC];
	$REQ_SPEC_NOTE = $data[REQ_SPEC_NOTE];
	$REQ_STATUS = $data[REQ_STATUS];
	$ODL_CHK_REQ_STATUS = $data[REQ_STATUS];
	 $HIDAPPROVE_FLAG = $data[APPROVE_FLAG];
	 $HIDALLOW_FLAG = $data[ALLOW_FLAG];
         
    $APPROVE_FLAG = $data[APPROVE_FLAG];
	$ALLOW_FLAG = $data[ALLOW_FLAG];

	
	if($data[REQUEST_TYPE] =="1"){
		$hid_filenameExport ="in";
	}else{
		$hid_filenameExport ="out";
	}
	
	
	$REVIEW1_PER_ID = $data[ALLOW_USER];
	$OLD_REVIEW1_PER_ID = $data[ALLOW_USER];
	$cmd ="select g.PN_SHORTNAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW
				from PER_PERSONAL a 
				left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE) 
				where PER_ID = $REVIEW1_PER_ID ";
	$db->send_cmd($cmd);
	$data2 = $db->get_array();
	$REVIEW1_PER_NAME = $data2[FULLNAME_SHOW];
	$ABS_REVIEW1_FLAG = $data[ALLOW_FLAG];
	$ODL_ABS_REVIEW1_FLAG = $data[ALLOW_FLAG];
	$ALLOW_DATE = show_date_format($data[ALLOW_DATE], 1);
	$ALLOW_NOTE = $data[ALLOW_NOTE];
	
	$APPROVE_PER_ID = $data[APPROVE_USER];
	$OLD_APPROVE_PER_ID = $data[APPROVE_USER];
	$cmd ="select g.PN_SHORTNAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW
				from PER_PERSONAL a 
				left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE) 
				where PER_ID = $APPROVE_PER_ID ";
	$db->send_cmd($cmd);
	$data2 = $db->get_array();
	$APPROVE_PER_NAME = $data2[FULLNAME_SHOW];
	$ABS_APPROVE_FLAG = $data[APPROVE_FLAG];
	$ODL_ABS_APPROVE_FLAG = $data[APPROVE_FLAG];
	$APPROVE_DATE = show_date_format($data[APPROVE_DATE], 1);
	$APPROVE_NOTE = $data[APPROVE_NOTE];
	
	
	
	
	
	
	$UPDATE_USER = $data[UPDATE_USER];
	$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
	$db->send_cmd($cmd);
	$data2 = $db->get_array();
	$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
	//$SHOW_UPDATE_DATE = $data[UPDATE_DATE];
	$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

} // end if


if ($command == "SETFLAG_ALLOW") {
		
		if($list_allow_all=="1"){ /* บันทึกทั้งหมด*/
				$search_condition ="";
				if(!$PER_TYPE){ 	$PER_TYPE=1;  }
				$search_abs_startdate = trim($search_abs_startdate);
				$search_abs_enddate = trim($search_abs_enddate);	
				
				if ($SESS_USERGROUP !=1 && $NAME_GROUP_HRD!='HRD'){
					 if ( ($SESS_USERGROUP !=1 && $NAME_GROUP_HRD!='HRD') && $PER_AUDIT_FLAG==1 ){
						 $search_condition .= " AND ( A.PER_ID = $SESS_PER_ID or  A.ALLOW_USER = $SESS_PER_ID or A.APPROVE_USER = $SESS_PER_ID or A.CREATE_USER=$SESS_USERID)";
					 }else{
							if ($SESS_PER_ID ){
								if($search_onlyme_flag==1){
									$search_condition .= " AND (A.PER_ID = $SESS_PER_ID)";
								}else{
									$search_condition .= " AND (A.PER_ID = $SESS_PER_ID or  A.ALLOW_USER = $SESS_PER_ID or A.APPROVE_USER = $SESS_PER_ID)";
								}
							}
					 
					 }
							
				
				}
				
			
				if($SESS_USERGROUP==1 || $NAME_GROUP_HRD=='HRD'){	
					if($search_org_id){
						if($select_org_structure==0)		$search_condition .= " AND (D.ORG_ID=$search_org_id or E.ORG_ID=$search_org_id or F.ORG_ID=$search_org_id or G.ORG_ID=$search_org_id)";
						if($select_org_structure==1)		$search_condition .= " AND (B.ORG_ID=$search_org_id)";			
					}elseif($search_department_id){
						$search_condition .= " AND (B.DEPARTMENT_ID = $search_department_id)";
					}elseif($search_ministry_id){
						$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$search_ministry_id ";
						$db_dpis->send_cmd($cmd);
						while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
						$search_condition .= " AND (B.DEPARTMENT_ID in (". implode(",", $arr_org) ."))";
					} // end if
				
				}else if($PER_AUDIT_FLAG==1){
					
					if($search_org_id){
						$search_condition .= " AND (B.ORG_ID=$search_org_id)";	
					}
				}	
			
			
			
				
			
				if ($search_per_type !=0){
					$search_condition .= " AND (A.PER_TYPE = $search_per_type)";
				}
				
				if ($search_abs_startdate && $search_abs_enddate) {
					$temp_date = explode("/", $search_abs_startdate);
					$temp_start = ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0];
					$temp_date = explode("/", $search_abs_enddate);
					$temp_end = ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0];		
					$search_condition .= " AND (A.REQUEST_DATE >= '$temp_start' and A.REQUEST_DATE <= '$temp_end')";
				}else if ($search_abs_startdate && !$search_abs_enddate) {
					$temp_date = explode("/", $search_abs_startdate);
					$temp_start = ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0];	
					$search_condition .= " AND (A.REQUEST_DATE = '$temp_start' )";
				 }else if (!$search_abs_startdate && $search_abs_enddate) {
					$temp_date = explode("/", $search_abs_enddate);
					$temp_end = ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0];	
					$search_condition .= " AND (A.REQUEST_DATE = '$temp_end' )";
				}
			
				
				if($search_abs_approve==0){
					$search_condition .= " AND (A.APPROVE_FLAG = 0 or A.APPROVE_FLAG is null)";
				} else if($search_abs_approve !=0 && $search_abs_approve !=4){
					$search_condition .= " AND (A.APPROVE_FLAG =$search_abs_approve)";
				}
				
				
				if($search_Request_Type !=0){
					if($search_Request_Type ==1){
							$search_condition .= " AND (A.MEETING_FLAG =1)";
					}else if($search_Request_Type ==2){
							$search_condition .= " AND (A.SCAN_FLAG =1)";
					}else if($search_Request_Type ==3){
							$search_condition .= " AND (A.OTH_FLAG =1)";
					}else if($search_Request_Type ==4){
							$search_condition .= " AND (A.REQUEST_NOTE =1)";
					}else if($search_Request_Type ==5){
							$search_condition .= " AND (A.REQ_TIME =1)";
					}
				}
				
			
				
				if(trim($search_per_name)) $search_condition .= " AND (B.PER_NAME like '$search_per_name%')";
				if(trim($search_per_surname)) $search_condition .= " AND (B.PER_SURNAME like '$search_per_surname%')";
				
				$db_insert = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
				$cmd = " SELECT A.REC_ID,A.PER_ID,A.REQUEST_DATE
                                FROM  TA_REQUESTTIME A,PER_PERSONAL B,  PER_POSITION D, 
                                        PER_POS_EMP E, PER_POS_EMPSER F, PER_POS_TEMP G 
                                WHERE A.PER_ID=B.PER_ID (+) 
                                        AND B.POS_ID=D.POS_ID(+) 
                                        AND B.POEM_ID=E.POEM_ID(+) 
                                        AND B.POEMS_ID=F.POEMS_ID(+) 
                                        AND B.POT_ID=G.POT_ID(+)
                                        $search_condition ";
										
					$count_page_data = $db_dpis->send_cmd($cmd);
					if($count_page_data){
						while($data = $db_dpis->get_array()){
							$cmd = " update TA_REQUESTTIME set ALLOW_FLAG = 1,
										ALLOW_DATE = '$UPDATE_DATE',
										UPDATE_USER=$SESS_USERID,UPDATE_DATE = '$UPDATE_DATE'
										where ALLOW_FLAG = 0 AND REC_ID=".$data[REC_ID];
							$db_insert->send_cmd($cmd);
							
						insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 >$MENU_TITLE_LV2 > บันทึกข้อมูลคำร้องไม่ได้ลงเวลา (รับรอง) [".$data[REC_ID]." : ".$data[PER_ID]." : ".$data[REQUEST_DATE]."]");
							
						}
				 	}
				
			
		}else{  /*กรณีที่เลือกบางรายการ*/

			if(count($list_allow_id)>0){
				for($i=0;$i<=count($list_allow_id);$i++){
					if(!empty($list_allow_id[$i])){
						$val =  explode("_",$list_allow_id[$i]);
						$cmd = " update TA_REQUESTTIME set ALLOW_FLAG = 1,
										ALLOW_DATE = '$UPDATE_DATE',
										UPDATE_USER=$SESS_USERID,UPDATE_DATE = '$UPDATE_DATE'
										where REC_ID=".$val[0];
						$db_dpis->send_cmd($cmd);
						insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 >$MENU_TITLE_LV2 > บันทึกข้อมูลคำร้องไม่ได้ลงเวลา (รับรอง) [".$val[0]." : ".$val[1]." : ".$val[2]."]");
					}
					
				}
			}
		
		}
		
		$search_condition ="";
		$command = "SEARCH";	
		
		/*คืนค่าให้เห็นของคนอื่นที่อยู่ในสังกัด*/
		
		
		/*-------------------------------------------------*/
		

	}
	
	
	if ($command == "SETFLAG_APPROVE") {
		if($list_approve_all=="1"){ /* บันทึกทั้งหมด*/
				$search_condition = "";
				if(!$PER_TYPE){ 	$PER_TYPE=1;  }
				$search_abs_startdate = trim($search_abs_startdate);
				$search_abs_enddate = trim($search_abs_enddate);	
				
				if ($SESS_USERGROUP !=1 && $NAME_GROUP_HRD!='HRD'){
					 if ( ($SESS_USERGROUP !=1 && $NAME_GROUP_HRD!='HRD') && $PER_AUDIT_FLAG==1 ){
						
						
						 $search_condition .= " AND (A.PER_ID = $SESS_PER_ID or  A.ALLOW_USER = $SESS_PER_ID or A.APPROVE_USER = $SESS_PER_ID or A.CREATE_USER=$SESS_USERID )";
					 }else{
							if ($SESS_PER_ID ){
								if($search_onlyme_flag==1){
									$search_condition .= " AND (A.PER_ID = $SESS_PER_ID)";
								}else{
									$search_condition .= " AND (A.PER_ID = $SESS_PER_ID or  A.ALLOW_USER = $SESS_PER_ID or A.APPROVE_USER = $SESS_PER_ID)";
								}
							}
					 
					 }
							
				
				}
				
			
				if($SESS_USERGROUP==1 || $NAME_GROUP_HRD=='HRD'){	
					if($search_org_id){
						if($select_org_structure==0)		$search_condition .= " AND (D.ORG_ID=$search_org_id or E.ORG_ID=$search_org_id or F.ORG_ID=$search_org_id or G.ORG_ID=$search_org_id)";
						if($select_org_structure==1)		$search_condition .= " AND (B.ORG_ID=$search_org_id)";			
					}elseif($search_department_id){
						$search_condition .= " AND (B.DEPARTMENT_ID = $search_department_id)";
					}elseif($search_ministry_id){
						$cmd = " select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$search_ministry_id ";
						$db_dpis->send_cmd($cmd);
						while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
						$search_condition .= " AND (B.DEPARTMENT_ID in (". implode(",", $arr_org) ."))";
					} // end if
				
				}else if($PER_AUDIT_FLAG==1){
					
					if($search_org_id){
						$search_condition .= " AND (B.ORG_ID=$search_org_id)";	
					}
				}	
			
			
			
				
			
				if ($search_per_type !=0){
					$search_condition .= " AND (A.PER_TYPE = $search_per_type)";
				}
				
				if ($search_abs_startdate && $search_abs_enddate) {
					$temp_date = explode("/", $search_abs_startdate);
					$temp_start = ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0];
					$temp_date = explode("/", $search_abs_enddate);
					$temp_end = ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0];		
					$search_condition .= " AND (A.REQUEST_DATE >= '$temp_start' and A.REQUEST_DATE <= '$temp_end')";
				}else if ($search_abs_startdate && !$search_abs_enddate) {
					$temp_date = explode("/", $search_abs_startdate);
					$temp_start = ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0];	
					$search_condition .= " AND (A.REQUEST_DATE = '$temp_start' )";
				 }else if (!$search_abs_startdate && $search_abs_enddate) {
					$temp_date = explode("/", $search_abs_enddate);
					$temp_end = ($temp_date[2] - 543) ."-". $temp_date[1] ."-". $temp_date[0];	
					$search_condition .= " AND (A.REQUEST_DATE = '$temp_end' )";
				}
			
				
				if($search_abs_approve==0){
					$search_condition .= " AND (A.APPROVE_FLAG = 0 or A.APPROVE_FLAG is null)";
				} else if($search_abs_approve !=0 && $search_abs_approve !=4){
					$search_condition .= " AND (A.APPROVE_FLAG =$search_abs_approve)";
				}
				
				
				if($search_Request_Type !=0){
					if($search_Request_Type ==1){
							$search_condition .= " AND (A.MEETING_FLAG =1)";
					}else if($search_Request_Type ==2){
							$search_condition .= " AND (A.SCAN_FLAG =1)";
					}else if($search_Request_Type ==3){
							$search_condition .= " AND (A.OTH_FLAG =1)";
					}else if($search_Request_Type ==4){
							$search_condition .= " AND (A.REQUEST_NOTE =1)";
					}else if($search_Request_Type ==5){
							$search_condition .= " AND (A.REQ_TIME =1)";
					}
				}
				
			
				
				if(trim($search_per_name)) $search_condition .= " AND (B.PER_NAME like '$search_per_name%')";
				if(trim($search_per_surname)) $search_condition .= " AND (B.PER_SURNAME like '$search_per_surname%')";

				
				
				$db_insert = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
				$cmd = " SELECT A.REC_ID,A.PER_ID,A.REQUEST_DATE
                                FROM  TA_REQUESTTIME A,PER_PERSONAL B,  PER_POSITION D, 
                                        PER_POS_EMP E, PER_POS_EMPSER F, PER_POS_TEMP G 
                                WHERE A.PER_ID=B.PER_ID (+) 
                                        AND B.POS_ID=D.POS_ID(+) 
                                        AND B.POEM_ID=E.POEM_ID(+) 
                                        AND B.POEMS_ID=F.POEMS_ID(+) 
                                        AND B.POT_ID=G.POT_ID(+)
                                        $search_condition ";
					$count_page_data = $db_dpis->send_cmd($cmd);
					if($count_page_data){
						while($data = $db_dpis->get_array()){
							$cmd = " update TA_REQUESTTIME set APPROVE_FLAG = 1,
										APPROVE_DATE = '$UPDATE_DATE',
										UPDATE_USER=$SESS_USERID,UPDATE_DATE = '$UPDATE_DATE'
										where APPROVE_FLAG = 0 AND REC_ID=".$data[REC_ID];
							$db_insert->send_cmd($cmd);
							
							insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 >$MENU_TITLE_LV2 > บันทึกข้อมูลคำร้องไม่ได้ลงเวลา (อนุมัติ) [".$data[REC_ID]." : ".$data[PER_ID]." : ".$data[REQUEST_DATE]."]");
							
						}
				 	}
		
			
		}else{  /*กรณีที่เลือกบางรายการ*/
		
			if(count($list_approve_id)>0){
				for($i=0;$i<=count($list_approve_id);$i++){
					if(!empty($list_approve_id[$i])){
						$val =  explode("_",$list_approve_id[$i]);
						$cmd = " update TA_REQUESTTIME set APPROVE_FLAG = 1,
										APPROVE_DATE = '$UPDATE_DATE',
										UPDATE_USER=$SESS_USERID,UPDATE_DATE = '$UPDATE_DATE'
										where REC_ID=".$val[0];
						$db_dpis->send_cmd($cmd);
						insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 >$MENU_TITLE_LV2 > บันทึกข้อมูลคำร้องไม่ได้ลงเวลา (อนุมัติ) [".$val[0]." : ".$val[1]." : ".$val[2]."]");
					}
					
				}
			}
		
		}
		
		$search_condition ="";
		$command = "SEARCH";		

	}
	
	if($command=="DELETE"){
                
                $cmd = " select PER_CARDNO  from TA_REQUESTTIME	WHERE  REC_ID=$REC_ID ";
                $db_dpis->send_cmd($cmd);
                $data = $db_dpis->get_array();
                //$data = array_change_key_case($data, CASE_LOWER);
                $del_PER_CARDNO = trim($data[PER_CARDNO]);
                //=================================== ลบไฟล์แนบ ===========================================
                        $PATH_MAIN = "../attachments";
                        $PART_FILE = $PATH_MAIN."/".$del_PER_CARDNO."/TA_REQUESTTIME/";
                        $LAST_DIR = $REC_ID;
                        if (!file_exists($PART_FILE)){ mkdir($PART_FILE);}
                        if (!file_exists($PART_FILE.$LAST_DIR)){ mkdir($PART_FILE.$LAST_DIR);}
                        //**scan file
                        $all_files = scandir($PART_FILE.$LAST_DIR);
                        $FILE_ID_CHK = "";
                        //echo count($all_files).">2";
                        if(count($all_files)>2){
                            for ($i=0;$i<count($all_files);$i++){
                                if($i>1){
                                        $cmd = "select 	ID
                                                        from 	EDITOR_ATTACHMENT
                                                        where REAL_FILENAME = '".$all_files[$i]."'";
                                        $db_dpis2->send_cmd($cmd);
                                        $data = $db_dpis2->get_array();
                                        //$data = array_change_key_case($data, CASE_LOWER);
                                        $file_id = $data[ID];
                                    $FILE_ID_CHK .= $file_id.",";
                                }    
                            }
                        }    
//                        var_dump($all_files);
//                        echo "<br>";
//                        var_dump($FILE_ID_CHK);
                        //ลบไฟล์รายการนี้ในโฟรเดอร์ทั้งหมด
                        $CHK_REDIR = rrmdir($PART_FILE.$LAST_DIR);
//                        if($CHK_REDIR)echo "DEL SUCCESS";
//                        else echo "DEL NOT SUCCESS";
                           
                        if(trim($FILE_ID_CHK)){
				$FILE_ID_CHK = substr($FILE_ID_CHK,0,-1);
				$cmd_dell = "delete from EDITOR_ATTACHMENT where ID in ($FILE_ID_CHK)";
				$db_dpis->send_cmd($cmd_dell);
                                //insert_log("DELETE $file_namedb FILE");
			}
                        
                //=================================== ลบไฟล์แนบ ===========================================    
            
		$cmd = " DELETE FROM  TA_REQUESTTIME	WHERE  REC_ID=$REC_ID ";
                $db_dpis->send_cmd($cmd);
                insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ลบข้อมูลคำร้องไม่ได้ลงเวลา [$REC_ID : $Hid_del_per_id : $Hid_del_req_date]");
			
			$command = "SEARCH";			
		
	}

?>