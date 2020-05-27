<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");	

	$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        $db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	if($PER_ID && $command != "CANCEL"){
		if($DPISDB=="odbc"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO, PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
							PER_PERSONAL.PER_TYPE, PER_PERSONAL.PER_CARDNO, PER_PERSONAL.DEPARTMENT_ID 
					from		PER_PERSONAL, PER_PRENAME
					where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO, PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		} // end if
		
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_CARDNO = (trim($data[PER_CARDNO]))?trim($data[PER_CARDNO]): "NULL";		
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);

		if (!$SRH_ID) {
			$cmd = "	select  SRH_ID  from  PER_WORKFLOW_SERVICEHIS
							where  PER_ID=$PER_ID and SRH_WF_STATUS!='04'
							order by SRH_ID DESC ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$SRH_ID = $data[SRH_ID];
			if ($SRH_ID) {
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
		$SRH_STARTDATE =  save_date($SRH_STARTDATE);
		$SRH_ENDDATE =  save_date($SRH_ENDDATE);
		$PER_ID_ASSIGN = (trim($PER_ID_ASSIGN))? "'".trim($PER_ID_ASSIGN)."'" : "NULL";
		$ORG_ID_ASSIGN = (trim($ORG_ID_ASSIGN))? "'".trim($ORG_ID_ASSIGN)."'" : "NULL";		
	
		$cmd = " select max(SRH_ID) as max_id from PER_WORKFLOW_SERVICEHIS ";
                //echo "1:".$cmd."<br><br>";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$SRH_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_WORKFLOW_SERVICEHIS
			 	(SRH_ID, PER_ID, SV_CODE, SRT_CODE, ORG_ID, SRH_STARTDATE, SRH_ENDDATE, SRH_NOTE, SRH_DOCNO, PER_ID_ASSIGN, ORG_ID_ASSIGN, PER_CARDNO, UPDATE_USER, UPDATE_DATE, SRH_WF_STATUS)
				values
			 	($SRH_ID, $PER_ID, '".trim($SV_CODE)."', '$SRT_CODE', '$ORG_ID', '$SRH_STARTDATE', '$SRH_ENDDATE', '$SRH_NOTE', '$SRH_DOCNO', $PER_ID_ASSIGN, $ORG_ID_ASSIGN, '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE', '01') ";
		//echo "2:".$cmd."<br><br>";
                $db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติราชการพิเศษ [$PER_ID : $SRH_ID : $SV_CODE]");
	} // end if

	if($command=="UPDATE" && $PER_ID && $SRH_ID){
		$SRH_STARTDATE =  save_date($SRH_STARTDATE);
		$SRH_ENDDATE =  save_date($SRH_ENDDATE);
		$PER_ID_ASSIGN = (trim($PER_ID_ASSIGN))? "'".trim($PER_ID_ASSIGN)."'" : "NULL";
		$ORG_ID_ASSIGN = (trim($ORG_ID_ASSIGN))? "'".trim($ORG_ID_ASSIGN)."'" : "NULL";		
	
		$cmd = " UPDATE PER_WORKFLOW_SERVICEHIS SET
					SV_CODE='".trim($SV_CODE)."', SRT_CODE='$SRT_CODE', ORG_ID='$ORG_ID', SRH_STARTDATE='$SRH_STARTDATE', 
					SRH_ENDDATE='$SRH_ENDDATE', SRH_NOTE='$SRH_NOTE', SRH_DOCNO='$SRH_DOCNO', PER_ID_ASSIGN=$PER_ID_ASSIGN, 
					ORG_ID_ASSIGN=$ORG_ID_ASSIGN, PER_CARDNO='$PER_CARDNO', 
					UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
				WHERE SRH_ID = $SRH_ID ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติราชการพิเศษ [$PER_ID : $SRH_ID : $SV_CODE]");
	} // end if
	
	if($command=="DELETE" && $PER_ID && $SRH_ID){
		$cmd = " select SV_CODE from PER_WORKFLOW_SERVICEHIS where SRH_ID=$SRH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$SV_CODE = trim($data[SV_CODE]);
		
		$cmd = " delete from PER_WORKFLOW_SERVICEHIS where SRH_ID=$SRH_ID ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติราชการพิเศษ [$PER_ID : $SRH_ID : $SV_CODE]");
	} // end if

//  UPDATE SRH_WF_STATUS
	$updtype=explode("-",$command);
	$F_REFRESH = "";
	if($updtype[0]=="UPD" && $PER_ID && $SRH_ID){
		$SRH_WF_STATUS = $updtype[1];
		$cmd2="select * from PER_WORKFLOW_SERVICEHIS where PER_ID=$PER_ID and SRH_ID=$SRH_ID";
		$count=$db_dpis->send_cmd($cmd2);
//		$db_dpis->show_error(); echo "<hr><br>";
		if($count) { 
			$cmd = " 	update PER_WORKFLOW_SERVICEHIS set
								SRH_WF_STATUS='$updtype[1]', 
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE'
							where PER_ID=$PER_ID and SRH_ID=$SRH_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$F_REFRESH = "1";
		
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการศึกษา [$PER_ID : $PER_NAME : $EN_CODE]");
			
			if ($SRH_WF_STATUS=="04") {
				$command ="COMMAND"; 	// กรณี 04 อนุมัติ - ถ่ายโอนข้อมูลเข้าแฟ้มจริง หลังจาก update status ที่ แฟ้ม temp
															// โดยแก้ $command ให้ค่า = "COMMAND" เพื่อจะได้เข้า loop ใน if ต่อจากนี้
			}
		} // end if
	} // end if  UPDATE SRH_WF_STATUS
	
	if($command=="COMMAND" && $PER_ID){
		$SRH_STARTDATE =  save_date($SRH_STARTDATE);
		$SRH_ENDDATE =  save_date($SRH_ENDDATE);
		$PER_ID_ASSIGN = (trim($PER_ID_ASSIGN))? "'".trim($PER_ID_ASSIGN)."'" : "NULL";
		$ORG_ID_ASSIGN = (trim($ORG_ID_ASSIGN))? "'".trim($ORG_ID_ASSIGN)."'" : "NULL";		
	
		$cmd = " select max(SRH_ID) as max_id from PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$SRH_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_SERVICEHIS
			 	(SRH_ID, PER_ID, SV_CODE, SRT_CODE, ORG_ID, SRH_STARTDATE, SRH_ENDDATE, SRH_NOTE, SRH_DOCNO, PER_ID_ASSIGN, ORG_ID_ASSIGN, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
				values
			 	($SRH_ID, $PER_ID, '".trim($SV_CODE)."', '$SRT_CODE', '$ORG_ID', '$SRH_STARTDATE', '$SRH_ENDDATE', '$SRH_NOTE', '$SRH_DOCNO', $PER_ID_ASSIGN, $ORG_ID_ASSIGN, '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);

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
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/'.$SRH_ID;
				$MOVERENAME = $MOVE_CATEGORY.$SRH_ID;
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
			//___echo "TEST : [ $SRH_ID ] <$FILE_PATH ~~ $MOVE_PATH> $FILE_PATH => $MOVEFINAL_PATH<br>$cmdup<br>";
			//###################################

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติราชการพิเศษ [$PER_ID : $SRH_ID : $SV_CODE]");
	} // end if เช็คข้อมูลซ้ำ

	if(($UPD && $PER_ID && $SRH_ID) || ($VIEW && $PER_ID && $SRH_ID)){
		$cmd = "SELECT 	SRH_ID, SRH_STARTDATE, SRH_ENDDATE, SRH_DOCNO, SRH_NOTE,  ORG_ID_ASSIGN, 
											PER_ID_ASSIGN, psh.SV_CODE, psh.SRT_CODE, psh.ORG_ID, ps.SV_NAME, pst.SRT_NAME, 
											po.ORG_NAME, psh.UPDATE_USER, psh.UPDATE_DATE, psh.SRH_WF_STATUS 
						FROM		PER_WORKFLOW_SERVICEHIS psh, PER_SERVICE ps, PER_SERVICETITLE pst, PER_ORG po  
						WHERE	SRH_ID=$SRH_ID and psh.SV_CODE=ps.SV_CODE and psh.SRT_CODE=pst.SRT_CODE and psh.ORG_ID=po.ORG_ID and SRH_WF_STATUS!='04' ";
							//echo $cmd;
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$SRH_STARTDATE = show_date_format($data[SRH_STARTDATE], 1);
		$SRH_ENDDATE = show_date_format($data[SRH_ENDDATE], 1);
	
		$SRH_NOTE = $data[SRH_NOTE];
		$SRH_DOCNO = $data[SRH_DOCNO];

		$SV_CODE = trim($data[SV_CODE]);
		$SV_NAME = $data[SV_NAME];
		$ORG_ID = $data[ORG_ID];
		$ORG_NAME = $data[ORG_NAME];
		$SRT_CODE = $data[SRT_CODE];
		$SRT_NAME = $data[SRT_NAME];		
		$SRH_WF_STATUS = $data[SRH_WF_STATUS];		
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		
		$PER_NAME_ASSIGN="";
		$PER_ID_ASSIGN = $data[PER_ID_ASSIGN];
		if($PER_ID_ASSIGN){
			$cmd = " select ppn.PN_NAME, PER_NAME, PER_SURNAME 
					from PER_PERSONAL pps, PER_PRENAME ppn 
					where PER_ID=$PER_ID_ASSIGN and pps.PN_CODE=ppn.PN_CODE";
					//echo $cmd;
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PER_NAME_ASSIGN = trim($data2[PN_NAME]) . trim($data2[PER_NAME]) . " " . trim($data2[PER_SURNAME]);
			//echo $cmd;
			//echo "<br>";
		} // end if		
		
		$ORG_NAME_ASSIGN="";
		$ORG_ID_ASSIGN = $data[ORG_ID_ASSIGN];
		if($ORG_ID_ASSIGN){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_ASSIGN ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_ASSIGN = $data2[ORG_NAME];
			//echo $cmd;
		} // end if
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($SRH_ID);
		unset($SRH_NOTE);
		unset($SRH_DOCNO);
		unset($SRH_STARTDATE);
		unset($SRH_ENDDATE);

		unset($SV_CODE);
		unset($SV_NAME);
		unset($SRT_CODE);
		unset($SRT_NAME);
		unset($PER_ID_ASSIGN);
		unset($PER_NAME_ASSIGN);
		unset($ORG_ID);
		unset($ORG_NAME);
		unset($ORG_ID_ASSIGN);
		unset($ORG_NAME_ASSIGN);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>