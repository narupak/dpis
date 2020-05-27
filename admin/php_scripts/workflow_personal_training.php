<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	$UPDATE_DATE = date("Y-m-d H:i:s");
	if (!$TRN_DAY) $TRN_DAY = "NULL";
		
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
	
	if($PER_ID && $command != "CANCEL"){
		if($DPISDB=="odbc"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
							PER_PERSONAL.PER_TYPE, PER_PERSONAL.PER_CARDNO,PER_PERSONAL.DEPARTMENT_ID
 					from		PER_PERSONAL, PER_PRENAME
					where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and 
							PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		} // end if
		
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_CARDNO = (trim($data[PER_CARDNO]))?trim($data[PER_CARDNO]): "NULL";		
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);

		if (!$TRN_ID) {
			$cmd = "	select  TRN_ID  from  PER_WORKFLOW_TRAINING
							where  PER_ID=$PER_ID and TRN_WF_STATUS!='04'
							order by TRN_ID DESC ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$TRN_ID = $data[TRN_ID];
			if ($TRN_ID) {
				$VIEW = 1;
			}
		}
	} // end if

	if($command=="ADD" && $PER_ID){
		$TRN_STARTDATE =  save_date($TRN_STARTDATE);
		$TRN_ENDDATE =  save_date($TRN_ENDDATE);
		$TRN_BOOK_DATE =  save_date($TRN_BOOK_DATE);
		$CT_CODE = (trim($CT_CODE))? "'" . trim($CT_CODE) . "'" : "NULL";
		$CT_CODE_FUND = (trim($CT_CODE_FUND))? "'" . trim($CT_CODE_FUND) . "'" : "NULL";
		
		if($DPISDB=="oci8")
			$cmd2 = " select TRN_STARTDATE,TRN_ENDDATE from PER_WORKFLOW_TRAINING  
								where PER_ID='$PER_ID' and  substr(TRN_STARTDATE,0,10)='$TRN_STARTDATE' and 
								substr(TRN_ENDDATE,0,10)='$TRN_ENDDATE'  and TR_CODE='$TR_CODE' "; 
		if($DPISDB=="odbc")
			$cmd2 = " select TRN_STARTDATE,TRN_ENDDATE from PER_WORKFLOW_TRAINING  
								where PER_ID=$PER_ID and  LEFT(TRN_STARTDATE,10)='$TRN_STARTDATE' and 
								LEFT(TRN_ENDDATE,10)='$TRN_ENDDATE'  and TR_CODE='$TR_CODE' "; 
		if($DPISDB=="mysql")
			$cmd2 = " select TRN_STARTDATE,TRN_ENDDATE from PER_WORKFLOW_TRAINING  
								where PER_ID='$PER_ID' and  substr(TRN_STARTDATE,0,10)='$TRN_STARTDATE' and 
								substr(TRN_ENDDATE,0,10)='$TRN_ENDDATE'  and TR_CODE='$TR_CODE' "; 
		$count=$db_dpis->send_cmd($cmd2);
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจาก วันที่เริ่ม -วันที่สิ้นสุด และหลักสูตร ซ้ำ !!!");
				-->   </script>	<? 
		} else {	
			$cmd = " select max(TRN_ID) as max_id from PER_WORKFLOW_TRAINING ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$TRN_ID = $data[max_id] + 1;
		
			$cmd = " insert into PER_WORKFLOW_TRAINING	(TRN_ID, PER_ID, TRN_TYPE, TR_CODE, TRN_NO, TRN_STARTDATE, TRN_ENDDATE, 
							TRN_ORG, TRN_PLACE, CT_CODE, TRN_FUND, CT_CODE_FUND, PER_CARDNO, UPDATE_USER, UPDATE_DATE, 
							TRN_DAY, TRN_REMARK, TRN_PASS, TRN_BOOK_NO, TRN_BOOK_DATE, 
							TRN_PROJECT_NAME, TRN_COURSE_NAME, TRN_DEGREE_RECEIVE, TRN_POINT, TRN_WF_STATUS)
							values ($TRN_ID, $PER_ID, '$TRN_TYPE', '$TR_CODE', '$TRN_NO', '$TRN_STARTDATE', '$TRN_ENDDATE', 
							'$TRN_ORG', '$TRN_PLACE', $CT_CODE, '$TRN_FUND', $CT_CODE_FUND, '$PER_CARDNO', $SESS_USERID, 
							'$UPDATE_DATE', $TRN_DAY, '$TRN_REMARK', $TRN_PASS, '$TRN_BOOK_NO', '$TRN_BOOK_DATE', 
							'$TRN_PROJECT_NAME', '$TRN_COURSE_NAME', '$TRN_DEGREE_RECEIVE', '$TRN_POINT', '01') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการอบรม/ดูงาน/สัมมนา [$PER_ID : $TRN_ID : $TR_CODE]");
		} // end if
	} // end if check ข้อมูลซ้ำ

	if($command=="UPDATE" && $PER_ID && $TRN_ID){
		$TRN_STARTDATE =  save_date($TRN_STARTDATE);
		$TRN_ENDDATE =  save_date($TRN_ENDDATE);
		$TRN_BOOK_DATE =  save_date($TRN_BOOK_DATE);
		$CT_CODE = (trim($CT_CODE))? "'" . trim($CT_CODE) . "'" : "NULL";
		$CT_CODE_FUND = (trim($CT_CODE_FUND))? "'" . trim($CT_CODE_FUND) . "'" : "NULL";	
		
		if($DPISDB=="oci8")    
			$cmd2 = " select TRN_STARTDATE,TRN_ENDDATE from PER_WORKFLOW_TRAINING  
								where PER_ID='$PER_ID' and  substr(TRN_STARTDATE,0,10)='$TRN_STARTDATE' and 
								substr(TRN_ENDDATE,0,10)='$TRN_ENDDATE'  and TR_CODE='$TR_CODE' and TRN_ID<>$TRN_ID "; 
		if($DPISDB=="odbc")
			$cmd2 = "select TRN_STARTDATE,TRN_ENDDATE from PER_WORKFLOW_TRAINING  
								where PER_ID=$PER_ID and  LEFT(TRN_STARTDATE,10)='$TRN_STARTDATE' and 
								LEFT(TRN_ENDDATE,10)='$TRN_ENDDATE'  and TR_CODE='$TR_CODE' and TRN_ID<>$TRN_ID "; 
		if($DPISDB=="mysql")
			$cmd2 = " select TRN_STARTDATE,TRN_ENDDATE from PER_WORKFLOW_TRAINING  
								where PER_ID='$PER_ID' and  substr(TRN_STARTDATE,0,10)='$TRN_STARTDATE' and 
								substr(TRN_ENDDATE,0,10)='$TRN_ENDDATE'  and TR_CODE='$TR_CODE' and TRN_ID<>$TRN_ID "; 
		$count=$db_dpis->send_cmd($cmd2);
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถแก้ไขข้อมูลได้ เนื่องจาก วันที่เริ่ม -วันที่สิ้นสุด และหลักสูตร ซ้ำ !!!");
				-->   </script>	<? 
		} else {	
			$cmd = " UPDATE PER_WORKFLOW_TRAINING SET
							TRN_TYPE='$TRN_TYPE', 
							TR_CODE='$TR_CODE', 
							TRN_NO='$TRN_NO', 
							TRN_STARTDATE='$TRN_STARTDATE', 
							TRN_ENDDATE='$TRN_ENDDATE', 
							TRN_ORG='$TRN_ORG', 
							TRN_PLACE='$TRN_PLACE', 
							CT_CODE=$CT_CODE, 
							TRN_FUND='$TRN_FUND', 
							CT_CODE_FUND=$CT_CODE_FUND, 
							PER_CARDNO='$PER_CARDNO', 
							TRN_DAY=$TRN_DAY, 
							TRN_REMARK='$TRN_REMARK', 
							TRN_PASS=$TRN_PASS, 
							TRN_BOOK_NO='$TRN_BOOK_NO', 
							TRN_BOOK_DATE='$TRN_BOOK_DATE', 
							TRN_PROJECT_NAME='$TRN_PROJECT_NAME', 
							TRN_COURSE_NAME='$TRN_COURSE_NAME', 
							TRN_DEGREE_RECEIVE='$TRN_DEGREE_RECEIVE', 
							TRN_POINT='$TRN_POINT', 
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'
				WHERE TRN_ID=$TRN_ID";
			$db_dpis->send_cmd($cmd);
		
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการอบรม/ดูงาน/สัมมนา [$PER_ID : $TRN_ID : $TR_CODE]");
		} // end if
	}// end if check ข้อมูลซ้ำ
	
	if($command=="DELETE" && $PER_ID && $TRN_ID){
		$cmd = " select TR_CODE from PER_WORKFLOW_TRAINING where TRN_ID=$TRN_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$TR_CODE = $data[TR_CODE];
		
		$cmd = " delete from PER_WORKFLOW_TRAINING where TRN_ID=$TRN_ID ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติการอบรม/ดูงาน/สัมมนา [$PER_ID : $TRN_ID : $TR_CODE]");
	} // end if

//  UPDATE TRN_WF_STATUS	//อนุมัติแล้ว
	$updtype=explode("-",$command);
	$F_REFRESH = "";
	if($updtype[0]=="UPD" && $PER_ID && $TRN_ID){
		$TRN_WF_STATUS = $updtype[1];
		
		$cmd2 = " select * from PER_WORKFLOW_TRAINING  
						 where PER_ID='$PER_ID' and  TRN_ID=$TRN_ID "; 
		$count=$db_dpis->send_cmd($cmd2);
//		$db_dpis->show_error(); echo "<hr><br>";
		if($count) { 
			$cmd = " 	update PER_WORKFLOW_TRAINING set
								TRN_WF_STATUS='$updtype[1]', 
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE'
							where PER_ID=$PER_ID and TRN_ID=$TRN_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$F_REFRESH = "1";

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติฝึกอบรม [$PER_ID : $PER_NAME : $EN_CODE]");
			
			if ($TRN_WF_STATUS=="04") {
				$command ="COMMAND"; 	// กรณี 04 อนุมัติ - ถ่ายโอนข้อมูลเข้าแฟ้มจริง หลังจาก update status ที่ แฟ้ม temp
															// โดยแก้ $command ให้ค่า = "COMMAND" เพื่อจะได้เข้า loop ใน if ต่อจากนี้
			}
		} // end if
	} // end if  UPDATE TRN_WF_STATUS
	
	if($command=="COMMAND" && $PER_ID){
		$TRN_STARTDATE =  save_date($TRN_STARTDATE);
		$TRN_ENDDATE =  save_date($TRN_ENDDATE);
		$TRN_BOOK_DATE =  save_date($TRN_BOOK_DATE);
		$CT_CODE = (trim($CT_CODE))? "'" . trim($CT_CODE) . "'" : "NULL";
		$CT_CODE_FUND = (trim($CT_CODE_FUND))? "'" . trim($CT_CODE_FUND) . "'" : "NULL";
		
		if($DPISDB=="oci8")
			$cmd2 = " select TRN_STARTDATE,TRN_ENDDATE from PER_TRAINING  
								where PER_ID='$PER_ID' and  substr(TRN_STARTDATE,0,10)='$TRN_STARTDATE' and 
								substr(TRN_ENDDATE,0,10)='$TRN_ENDDATE'  and TR_CODE='$TR_CODE' "; 
		if($DPISDB=="odbc")
			$cmd2 = " select TRN_STARTDATE,TRN_ENDDATE from PER_TRAINING  
								where PER_ID=$PER_ID and  LEFT(TRN_STARTDATE,10)='$TRN_STARTDATE' and 
								LEFT(TRN_ENDDATE,10)='$TRN_ENDDATE'  and TR_CODE='$TR_CODE' "; 
		if($DPISDB=="mysql")
			$cmd2 = " select TRN_STARTDATE,TRN_ENDDATE from PER_TRAINING  
								where PER_ID='$PER_ID' and  substr(TRN_STARTDATE,0,10)='$TRN_STARTDATE' and 
								substr(TRN_ENDDATE,0,10)='$TRN_ENDDATE'  and TR_CODE='$TR_CODE' "; 
		$count=$db_dpis->send_cmd($cmd2);
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจาก วันที่เริ่ม -วันที่สิ้นสุด และหลักสูตร ซ้ำ !!!");
				-->   </script>	<? 
		} else {	
			$cmd = " select max(TRN_ID) as max_id from PER_TRAINING ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$TRN_ID = $data[max_id] + 1;
		
			$cmd = " insert into PER_TRAINING	(TRN_ID, PER_ID, TRN_TYPE, TR_CODE, TRN_NO, TRN_STARTDATE, TRN_ENDDATE, 
							TRN_ORG, TRN_PLACE, CT_CODE, TRN_FUND, CT_CODE_FUND, PER_CARDNO, UPDATE_USER, UPDATE_DATE, 
							TRN_DAY, TRN_REMARK, TRN_PASS, TRN_BOOK_NO, TRN_BOOK_DATE, 
							TRN_PROJECT_NAME, TRN_COURSE_NAME, TRN_DEGREE_RECEIVE, TRN_POINT)
							values ($TRN_ID, $PER_ID, '$TRN_TYPE', '$TR_CODE', '$TRN_NO', '$TRN_STARTDATE', '$TRN_ENDDATE', 
							'$TRN_ORG', '$TRN_PLACE', $CT_CODE, '$TRN_FUND', $CT_CODE_FUND, $PER_CARDNO, $SESS_USERID, 
							'$UPDATE_DATE', $TRN_DAY, '$TRN_REMARK', $TRN_PASS, '$TRN_BOOK_NO', '$TRN_BOOK_DATE', 
							'$TRN_PROJECT_NAME', '$TRN_COURSE_NAME', '$TRN_DEGREE_RECEIVE', '$TRN_POINT') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
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
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/42';
				$MOVERENAME = "PER_ATTACHMENT42";
			}else{
				$MOVESECOND_SUBDIR = $MOVEFIRST_SUBDIR.'/'.$MOVE_CATEGORY;
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/'.$TRN_ID;
				$MOVERENAME = $MOVE_CATEGORY.$TRN_ID;
			}
			//echo "$ATTACH_FILE : $MOVEFINAL_PATH with $MOVERENAME";
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
			//___echo "TEST : [ $TRN_ID ] <$FILE_PATH ~~ $MOVE_PATH> $FILE_PATH => $MOVEFINAL_PATH<br>$cmdup<br>";
			//###################################

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการอบรม/ดูงาน/สัมมนา [$PER_ID : $TRN_ID : $TR_CODE]");
		} // end if
	} // end if check ข้อมูลซ้ำ

	if(($UPD && $PER_ID && $TRN_ID) || ($VIEW && $PER_ID && $TRN_ID)){
		$cmd = " select	TRN_TYPE, TR_CODE, TRN_NO, TRN_STARTDATE, TRN_ENDDATE, TRN_ORG, TRN_PLACE, 
										CT_CODE, TRN_FUND, CT_CODE_FUND, UPDATE_USER, UPDATE_DATE, TRN_DAY, TRN_REMARK, 
										TRN_PASS, TRN_BOOK_NO, TRN_BOOK_DATE, TRN_PROJECT_NAME, TRN_COURSE_NAME, 
										TRN_DEGREE_RECEIVE, TRN_POINT, TRN_WF_STATUS
						from		PER_WORKFLOW_TRAINING
						where	TRN_ID=$TRN_ID and TRN_WF_STATUS!='04' ";	
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$TRN_TYPE = $data[TRN_TYPE];
		$TRN_NO = $data[TRN_NO];
		$TRN_ORG = $data[TRN_ORG];
		$TRN_PLACE = $data[TRN_PLACE];
		$TRN_FUND = $data[TRN_FUND];
		$TRN_DAY = $data[TRN_DAY];
		$TRN_REMARK = $data[TRN_REMARK];
		$TRN_PASS = $data[TRN_PASS];
		$TRN_BOOK_NO = $data[TRN_BOOK_NO];
		$TRN_BOOK_DATE = $data[TRN_BOOK_DATE];
		$TRN_PROJECT_NAME = $data[TRN_PROJECT_NAME];
		$TRN_COURSE_NAME = $data[TRN_COURSE_NAME];
		$TRN_DEGREE_RECEIVE = $data[TRN_DEGREE_RECEIVE];
		$TRN_POINT = $data[TRN_POINT];
		$TRN_WF_STATUS = $data[TRN_WF_STATUS];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		
		$TRN_STARTDATE = show_date_format($data[TRN_STARTDATE], 1);
		$TRN_ENDDATE = show_date_format($data[TRN_ENDDATE], 1);
		$TRN_BOOK_DATE = show_date_format($data[TRN_BOOK_DATE], 1);

		$TR_CODE = $data[TR_CODE];
		if($TR_CODE){
			$cmd = " select TR_NAME from PER_TRAIN where TR_CODE='$TR_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$TR_NAME = $data2[TR_NAME];
		} // end if

		$CT_NAME = $CT_NAME_FUND = "";
		$CT_CODE = $data[CT_CODE];
		if($CT_CODE){
			$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CT_NAME = $data2[CT_NAME];
		} // end if

		$CT_CODE_FUND = $data[CT_CODE_FUND];
		if($CT_CODE_FUND){
			$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE_FUND' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CT_NAME_FUND = $data2[CT_NAME];
		} // end if
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($TRN_ID);
		unset($TRN_TYPE);
		unset($TRN_NO);		
		unset($TRN_STARTDATE);
		unset($TRN_ENDDATE);
		unset($TRN_ORG);
		unset($TRN_PLACE);
		unset($TRN_FUND);
		unset($TRN_DAY);
		unset($TRN_REMARK);
		unset($TRN_PASS);
		unset($TRN_BOOK_NO);
		unset($TRN_BOOK_DATE);
		unset($TRN_PROJECT_NAME);
		unset($TRN_COURSE_NAME);
		unset($TRN_DEGREE_RECEIVE);
		unset($TRN_POINT);

		$CT_CODE = "140";
		$CT_NAME = "ไทย";
		unset($CT_CODE_FUND);
		unset($CT_NAME_FUND);
		unset($TR_CODE);
		unset($TR_NAME);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>