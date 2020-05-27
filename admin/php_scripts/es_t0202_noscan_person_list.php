<? 
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
        $db_dpis4 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        $db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        
	
        /*Release 5.2.1.7 Begin http://dpis.ocsc.go.th/Service/node/1375*/
        $cmdModify = "SELECT COLUMN_NAME, DATA_TYPE ,DATA_LENGTH
                    FROM USER_TAB_COLUMNS
                    WHERE TABLE_NAME = 'TA_SET_EXCEPTPER'
                    AND COLUMN_NAME = 'REMARK'";
        $db_dpis2->send_cmd($cmdModify);
        $dataModify = $db_dpis2->get_array_array();
        $data_length = $dataModify[2];
        if($data_length==100){
            $cmdModify = "ALTER TABLE TA_SET_EXCEPTPER MODIFY ( REMARK VARCHAR2(255) )";
            $db_dpis2->send_cmd($cmdModify);
            $db_dpis2->send_cmd("COMMIT");
        }
        /*Release 5.2.1.7 End*/
        
		$UPDATE_DATE = date("Y-m-d H:i:s");
		$UPDATE_DATE_FLAG = date("Y-m-d");

		if ($command == "SETFLAG") {
			if($HidedelID_F){
				
				$cmd = " select REC_ID,END_DATE from TA_SET_EXCEPTPER where CANCEL_FLAG = 1 AND REC_ID in (".stripslashes($HidedelID_F).") ";
				$db_dpis1->send_cmd($cmd);
				while($data = $db_dpis1->get_array()){
					$END_DATE = "";
					if(!$data[END_DATE]){
						$END_DATE =  ",END_DATE='$UPDATE_DATE_FLAG'";
					}
					
					$cmd = " update TA_SET_EXCEPTPER set CANCEL_FLAG = 0 ".$END_DATE." where CANCEL_FLAG = 1 AND REC_ID=".$data[REC_ID];
					$db_dpis->send_cmd($cmd);
					
				}
				
			}
			

			$cmd = " update TA_SET_EXCEPTPER set CANCEL_FLAG = 1 where REC_ID in (".stripslashes($HidedelID_T).") ";
			$db_dpis->send_cmd($cmd);
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ตั้งค่าการใช้งานข้อมูล");
			$command="";
		}
		
		$START_DATE_save = save_date($TIME_START); 
		if($TIME_END){
			$END_DATE_save = save_date($TIME_END);
			$END_DATE_chk = save_date($TIME_END);
		}else{
			$END_DATE_save ="";
			$END_DATE_chk = save_date($TIME_START);
		}
		
		$REMARK_save = trim($REMARK); 
		if($FLAG_PRINT==1){
			$FLAG_PRINT_save = 1;
		}else{
			$FLAG_PRINT_save = 0;
		}
		
  	if($command=="ADD"){
			
                $chkPerId = 0;
                $HideID_save= explode(",",$HideID);

                foreach ($HideID_save as $value) {
                        $cmd = " select  PER_ID from TA_SET_EXCEPTPER 
                                                WHERE END_DATE IS NOT NULL 
                                                AND CANCEL_FLAG=1
                                                AND PER_ID=$value  
                                                AND 	(   (START_DATE  BETWEEN '$START_DATE_save' and '$END_DATE_chk')
                    or  (END_DATE BETWEEN '$START_DATE_save' and '$END_DATE_chk') 
                                        or ( '$START_DATE_save'  BETWEEN START_DATE and END_DATE )
                                        or ( '$END_DATE_chk'  BETWEEN START_DATE and END_DATE ) 
                    )
                                                         ";
                        $count_duplicate = $db_dpis->send_cmd($cmd);
                        if($count_duplicate > 0){
                                $chkPerId++;
                        }
                }
				
		$OpenDialog=0; 
		if($chkPerId>0){
			$Dup_ID=$HideID;
			$Dup_TIME_START=$START_DATE_save;
			$Dup_TIME_END=$END_DATE_save;
			$Dup_FLAG_PRINT=$FLAG_PRINT_save;
			$Dup_REMARK=$REMARK_save;
			$OpenDialog=1;  
			
		}else{
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
                            
                        //======================================== End การอัพโหลดไฟล์ ==============================================	
			
                        foreach ($HideID_save as $value) {
				
				$cmd = " select PER_CARDNO,PER_TYPE  from PER_PERSONAL WHERE PER_ID=".$value;
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				$PER_TYPE_save = trim($data[per_type]);
				$PER_CARDNO_save = trim($data[per_cardno]);
				
				
				$cmd = " insert into TA_SET_EXCEPTPER (REC_ID, PER_ID,PER_TYPE, PER_CARDNO, START_DATE, END_DATE,FLAG_PRINT , REMARK,CREATE_USER,CREATE_DATE, UPDATE_USER, UPDATE_DATE)
								  values (TA_SET_EXCEPTPER_SEQ.NEXTVAL, $value,$PER_TYPE_save, '$PER_CARDNO_save', '$START_DATE_save', '$END_DATE_save',$FLAG_PRINT_save, '$REMARK_save', $SESS_USERID, SYSDATE, $SESS_USERID, SYSDATE)   ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
		
				$cmd = " SELECT TA_SET_EXCEPTPER_SEQ.currval AS CURID FROM dual ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$REC_ID = $data[CURID];
				
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูลบุคคลที่ไม่ต้องลงเวลา [$value : $REC_ID : $START_DATE_save]");
                                
                        //========================================== การอัพโหลดไฟล์ ================================================
                                $PATH_MAIN = "../attachments";
                                $CATEGORY = "TA_SET_EXCEPTPER";  //โฟรเดอร์ชื่องาน
                                $LAST_SUBDIR = $REC_ID; //โฟรเดอร์สุดท้าย เป็น ไอดีของรายการนั้นๆ
                            
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
                                                $COPPY_PATH = "$FINAL_PATH/$filename_encode";
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
                                            else{    
                                            //echo "<span style='color:#FF0000'>ไม่สามารถนำเข้าไฟล์ได้</span><br>";
                                                if($COPPY_PATH){
                                                    //coppy file
                                                    $TO_PATH = "$FINAL_PATH/$filename_encode";
                                                    copy($COPPY_PATH, $TO_PATH);
                                                    if(file_exists($TO_PATH)){
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
                                                    }    
                                                }
                                            }
                                    }
                                }
                            //======================================== End การอัพโหลดไฟล์ ==============================================
                                
			} // end for
		
			/*echo "<script>window.location='../admin/es_t0202_noscan_person_list.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2';</script>";*/
		} //if($chkPerId>0){

 	} // end if
	
	
	if($command=="UPDATE"){
					
					$cmd = " UPDATE TA_SET_EXCEPTPER SET	
								  START_DATE='$START_DATE_save',
								  END_DATE='$END_DATE_save',
								  FLAG_PRINT=$FLAG_PRINT_save,
								  REMARK='$REMARK_save',
								  UPDATE_USER=$SESS_USERID, 
								  UPDATE_DATE=SYSDATE
								  WHERE REC_ID=$HIDREC_ID ";
					$db_dpis->send_cmd($cmd);
					insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูลบุคคลที่ไม่ต้องลงเวลา [$HIDPER_ID : $HIDREC_ID : $START_DATE_save]");
					$command="";
	}
	
	
	if($UPD){
		$cmd = "	  select 		g.PN_SHORTNAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW,
										wch.START_DATE,wch.END_DATE,wch.UPDATE_USER,TO_CHAR(wch.UPDATE_DATE,'yyyy-mm-dd') AS UPDATE_DATE,
										a.PER_CARDNO,wch.PER_ID,wch.REMARK,a.PER_CARDNO,a.POS_ID,a.POEM_ID,a.POEMS_ID,a.POT_ID,f.POSITION_LEVEL,
										wch.FLAG_PRINT
						  from 		TA_SET_EXCEPTPER wch
						  left join PER_PERSONAL a on(a.PER_ID=wch.PER_ID)
						  left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE) 
						  left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
						  where 		wch.REC_ID='$HIDREC_ID'";

		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$FULLNAME_SHOW = $data[FULLNAME_SHOW];
		$PER_CARDNO = $data[PER_CARDNO];
		$TIME_START = show_date_format($data[START_DATE], 1);
		$TIME_END = show_date_format($data[END_DATE], 1);
		$HIDPER_ID = $data[PER_ID];
		$FLAG_PRINT = $data[FLAG_PRINT];
		$REMARK = $data[REMARK];
		
		
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		//$SHOW_UPDATE_DATE = $data[UPDATE_DATE];
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		
		$POS_ID = $data[POS_ID];
		$POEM_ID = $data[POEM_ID]; 
		$POEMS_ID =$data[POEMS_ID];
		$POT_ID = $data[POT_ID];
		$LEVEL_NAME = $data[POSITION_LEVEL];
		
		//  ===== ถ้าเป็นข้าราชการ SELECT ข้อมูลตำแหน่งจาก table PER_POSITION =====  PER_TYPE=1
		if ($POS_ID) {			
			$cmd = " select 	ORG_ID, PL_CODE
					from 	PER_POSITION where POS_ID=$POS_ID  ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$TMP_ORG_ID = trim($data_dpis2[ORG_ID]);
			
			$PL_CODE = trim($data_dpis2[PL_CODE]);
			$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$PL_NAME = trim($data_dpis1[PL_NAME]);
			
		}
		
		//  ===== ถ้าเป็นลูกจ้างประจำ SELECT ข้อมูลตำแหน่งจาก table PER_POS_EMP =====  PER_TYPE=2
		if ($POEM_ID) {
			$cmd = " select 	ORG_ID,  PN_CODE
					from 	PER_POS_EMP where POEM_ID=$POEM_ID ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$TMP_ORG_ID = trim($data_dpis2[ORG_ID]);
			
			$PER_POS_CODE = trim($data_dpis2[PN_CODE]);
			$cmd = " select PN_NAME, PG_CODE from PER_POS_NAME where trim(PN_CODE)='$PER_POS_CODE' ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$PL_NAME = trim($data_dpis1[PN_NAME]);
		}
		
		
		//  ===== ถ้าเป็นพนักงานราชการ SELECT ข้อมูลตำแหน่งจาก table PER_POS_EMPSER =====  PER_TYPE=3
		if ($POEMS_ID) {
			$cmd = " select 	ORG_ID, EP_CODE
					from 	PER_POS_EMPSER where POEMS_ID=$POEMS_ID ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$TMP_ORG_ID = trim($data_dpis2[ORG_ID]);
			
			//  table  PER_POS_EMP = ตำแหน่งพนักงานราชการ
			$PER_POS_CODE = trim($data_dpis2[EP_CODE]);
			$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where trim(EP_CODE)='$PER_POS_CODE' ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$PL_NAME = trim($data_dpis1[EP_NAME]);	
		}
		
		//  ===== ถ้าเป็นลูกจ้างชั่วคราว SELECT ข้อมูลตำแหน่งจาก table PER_POS_TEMP =====  PER_TYPE=4
		if ($POT_ID) {
			$cmd = " select 	ORG_ID, TP_CODE
					from 	PER_POS_TEMP where POT_ID=$POT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$TMP_ORG_ID = trim($data_dpis2[ORG_ID]);
			
			//  table  PER_POS_TEMP = ตำแหน่งลูกจ้างชั่วคราว
			$PER_POS_CODE = trim($data_dpis2[TP_CODE]);
			$cmd = " select TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE)='$PER_POS_CODE' ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$PL_NAME = trim($data_dpis1[TP_NAME]);
		}
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$TMP_ORG_ID ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$ORG_NAME = trim($data_dpis1[ORG_NAME]);
		
	} // end if
	
	
	
	if($command=="DELETE"){

				
				$cmd = " select START_DATE  from TA_SET_EXCEPTPER	 where REC_ID= $HIDREC_ID";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				$del_START_DATE = trim($data[START_DATE]);
				
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ลบข้อมูลบุคคลที่ไม่ต้องลงเวลา [$HIDPER_ID :$HIDREC_ID : $del_START_DATE ]");

				$cmd = " delete from TA_SET_EXCEPTPER	 where REC_ID=$HIDREC_ID ";
				$db_dpis->send_cmd($cmd);

				echo "<script>window.location='../admin/es_t0202_noscan_person_list.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2';</script>";

 	} // end if*/
	
	if( (!$UPD && !$DEL) ){
		$TIME_START = "";
		$TIME_END = "";
		$REMARK = "";
		$HIDREC_ID = "";	
		$HIDPER_ID = "";	
		$HideID = "";	
		$FLAG_PRINT=0;
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
	
  
?>