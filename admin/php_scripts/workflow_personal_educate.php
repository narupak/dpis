<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	//die($command."<<<");
//	echo "command=$command, VIEW=$VIEW, UPD=$UPD, PAGE ADD=".$PAGE_AUTH["add"].", PAGE EDIT=".$PAGE_AUTH["edit"].", PAGE DEL=".$PAGE_AUTH["del"].", PAGE_AUTHadd=$PAGE_AUTHadd, PAGE_AUTHedit=$PAGE_AUTHedit<br>";
	if(trim($PAGE_AUTHadd)){ $PAGE_AUTH["add"]=$PAGE_AUTHadd; }
	if(trim($PAGE_AUTHedit)){	 $PAGE_AUTH["edit"]=$PAGE_AUTHedit; }
//	echo $PAGE_AUTH["add"]."*".$PAGE_AUTH["edit"]."-->".$ATTACH_FILE."==".$SESS_USERGROUP_LEVEL."-->".$TMP_EDU_WF_STATUS."<br>";	
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
		
		if (!$EDU_ID) {
			$cmd = "	select  EDU_ID  from  PER_WORKFLOW_EDUCATE
							where  PER_ID=$PER_ID and EDU_WF_STATUS!='04'
							order by EDU_ID DESC ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$EDU_ID = $data[EDU_ID];
			if ($EDU_ID) {
				$VIEW = 1;
			}
		}
	} // end if

	$EDU_FUND = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($EDU_FUND)));
	if (!$EDU_GRADE) $EDU_GRADE = "NULL";
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
	
	if($command=="REORDER"){
		foreach($ARR_EDU_ORDER as $EDU_ID => $EDU_SEQ){
			$cmd = " update PER_WORKFLOW_EDUCATE set EDU_SEQ='$EDU_SEQ' where EDU_ID=$EDU_ID ";
			$db_dpis->send_cmd($cmd);
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > จัดลำดับประวัติการศึกษา [$PER_ID : $PER_NAME]");
	} // end if

	$ARR_EDU_SEQ = (array) null;
	$ARR_EDU_TYPE = (array) null;
	$edu_change_idx = (array) null;

	if($command=="ADD" && $PER_ID){
		$cmd = " select max(EDU_ID) as max_id from PER_WORKFLOW_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$EDU_ID = $data[max_id] + 1;

		// อ่านข้อมูลใน PER_WORKFLOW_EDUCATE เพื่อเตรียม EDU_TYPE เพื่อค้นหา SEQ ที่มี ซ้ำ เพื่อจะตัดออก
		$cmd = " select EDU_SEQ, EDU_TYPE 
						from PER_WORKFLOW_EDUCATE 
					  where PER_ID=$PER_ID order by EDU_SEQ ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
//			$data = array_change_key_case($data, CASE_LOWER);
			 if ($data[EDU_SEQ] != $EDU_SEQ) {
//				echo "data eduseq=".$data[EDU_SEQ]." != $EDU_SEQ ".$data[EDU_TYPE]."<br>";
				$ARR_EDU_SEQ[] = trim($data[EDU_SEQ]);
				$ARR_EDU_TYPE[] = trim($data[EDU_TYPE]);
			}
		}
		//echo "edu seq list[".implode(",",$ARR_EDU_SEQ)."],type list[".implode(",",$ARR_EDU_TYPE)."]<br>";
		
		if (trim($EDU_TYPE))		$EDU_TYPE_TXT = "||";

		// ตรวจสอบ ในทุก EDU_SEQ ว่า SEQ ไหนมีตัวซ้ำที่ต้องการเปลี่ยน กะจะตัดออก
		for ($i=0; $i<count($EDU_TYPE); $i++) {
			$edu_change_idx[$EDU_TYPE[$i]] = -1; 
			if ($EDU_TYPE[$i] == 1 || $EDU_TYPE[$i] == 2 || $EDU_TYPE[$i] == 4) { // 1.วุฒิที่ใช้บรรจุ  2.วุฒิในตำแหน่งปัจจุบัน  4.วุฒิสูงสุด
				for($edu_i=0; $edu_i < count($ARR_EDU_TYPE); $edu_i++) {
					if (strpos($ARR_EDU_TYPE[$edu_i], $EDU_TYPE[$i]) !== false) {
						$edu_change_idx[$EDU_TYPE[$i]] = $edu_i; // เก็บ index ของ seq ที่ต้องการตัดออกไว้
					}
				} // end loop for $edu_i
			} else {
				$edu_change_idx[$EDU_TYPE[$i]] = 0;
			}
			$EDU_TYPE_TXT.= "$EDU_TYPE[$i]||"; // จัดรูปแบบ EDU_TYPE_TEXT เพื่อการบันทึกใหม่
		} // end loop for $i
		//echo "EDU_TYPE_TXT=$EDU_TYPE_TXT<br>";
		$ST_CODE = (trim($ST_CODE))? "'" . $ST_CODE . "'"  : "NULL";
		$CT_CODE = (trim($CT_CODE))? "'" . $CT_CODE . "'"  : "NULL";
		$EL_CODE = (trim($EL_CODE))? "'" . $EL_CODE . "'"  : "NULL";
		$EN_CODE = (trim($EN_CODE))? "'" . $EN_CODE . "'"  : "NULL";
		$EM_CODE = (trim($EM_CODE))? "'" . $EM_CODE . "'"  : "NULL";
		$INS_CODE = (trim($INS_CODE))? "'" . $INS_CODE . "'"  : "NULL";	
		
		// เอา edu_type 1 2 4  ที่มีซ้ำใน record อื่น ออก...
		for ($i=0; $i<count($EDU_TYPE); $i++) {
			$out_edu_type = false;
			if ($EDU_TYPE[$i] == 1 || $EDU_TYPE[$i] == 2 || $EDU_TYPE[$i] == 4) { // 1.วุฒิที่ใช้บรรจุ  2.วุฒิในตำแหน่งปัจจุบัน  4.วุฒิสูงสุด
				if ($edu_change_idx[$EDU_TYPE[$i]] > -1) { // กรณีที่ มี edu_type วุฒิที่ใช้บรรจุ ใน seq อื่น
					$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]] = str_replace("".$EDU_TYPE[$i]."||","",$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]]); // เอา edu_type=1 2 4 ออกจาก edu_type ที่ edu_seq=$edu_change_idx
					$out_edu_type = true;
				}
			}
			if ($out_edu_type) {
//				echo "($EDU_TYPE[$i]) af upd-".$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]]."<br>";
				$cmd = " update PER_WORKFLOW_EDUCATE
																   set EDU_TYPE='".$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]]."',
																	 	 UPDATE_USER=$SESS_USERID, 
																		 UPDATE_DATE='$UPDATE_DATE'
								where PER_ID=$PER_ID and EDU_SEQ='".$ARR_EDU_SEQ[$edu_change_idx[$EDU_TYPE[$i]]]."'
					 		";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		} // end for loop $i

		$EDU_ENDDATE =  save_date($EDU_ENDDATE);
		$EDU_BOOK_DATE =  save_date($EDU_BOOK_DATE);

		$cmd2="select EDU_SEQ from PER_WORKFLOW_EDUCATE where PER_ID=$PER_ID and EDU_SEQ='$EDU_SEQ' ";
		$count=$db_dpis->send_cmd($cmd2);
//		echo "cmd2=$cmd2(count=$count)<br>";
//		$db_dpis->show_error(); echo "<hr><br>";
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจากคุณระบุลำดับ ซ้ำ !!!");
				-->   </script>	<? }  
		else {
			$cmd = " insert into PER_WORKFLOW_EDUCATE	(EDU_ID, PER_ID, EDU_SEQ, EDU_STARTYEAR, EDU_ENDYEAR, ST_CODE, CT_CODE, 
					EDU_FUND, EN_CODE, EM_CODE, INS_CODE, EDU_TYPE, PER_CARDNO, UPDATE_USER, UPDATE_DATE,
					EL_CODE, EDU_ENDDATE, EDU_GRADE, EDU_HONOR, EDU_BOOK_NO, EDU_BOOK_DATE, EDU_REMARK, EDU_INSTITUTE, EDU_WF_STATUS)
					values ($EDU_ID, $PER_ID, '$EDU_SEQ', '$EDU_STARTYEAR', '$EDU_ENDYEAR', $ST_CODE, $CT_CODE, 
					'$EDU_FUND', $EN_CODE, $EM_CODE, $INS_CODE, '$EDU_TYPE_TXT', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE',
					$EL_CODE, '$EDU_ENDDATE', $EDU_GRADE, '$EDU_HONOR', '$EDU_BOOK_NO', '$EDU_BOOK_DATE', '$EDU_REMARK', '$EDU_INSTITUTE', '01') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "insert cmd=$cmd<br>";
                        //die('');
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการศึกษา [$PER_ID : $PER_NAME : $EN_CODE]");
		} // end if
	} // end if เช็คข้อมูลซ้ำ
	
	if($command=="UPDATE" && $PER_ID && $EDU_ID){
		// อ่านข้อมูลใน PER_WORKFLOW_EDUCATE เพื่อเตรียม EDU_TYPE เพื่อค้นหา SEQ ที่มี ซ้ำ เพื่อจะตัดออก
		$cmd = " select EDU_SEQ, EDU_TYPE 
						from PER_WORKFLOW_EDUCATE 
					  where PER_ID=$PER_ID order by EDU_SEQ ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
//			$data = array_change_key_case($data, CASE_LOWER);
			 if ($data[EDU_SEQ] != $EDU_SEQ) {
//				echo "data eduseq=".$data[EDU_SEQ]." != $EDU_SEQ ".$data[EDU_TYPE]."<br>";
				$ARR_EDU_SEQ[] = trim($data[EDU_SEQ]);
				$ARR_EDU_TYPE[] = trim($data[EDU_TYPE]);
			}
		}
		
		if (trim($EDU_TYPE))		$EDU_TYPE_TXT = "||";
		
		// ตรวจสอบ ในทุก EDU_SEQ ว่า SEQ ไหนมีตัวซ้ำที่ต้องการเปลี่ยน กะจะตัดออก
		for ($i=0; $i<count($EDU_TYPE); $i++) {
			$edu_change_idx[$EDU_TYPE[$i]] = -1; 
			if ($EDU_TYPE[$i] == 1 || $EDU_TYPE[$i] == 2 || $EDU_TYPE[$i] == 4) { // 1.วุฒิที่ใช้บรรจุ  2.วุฒิในตำแหน่งปัจจุบัน  4.วุฒิสูงสุด
				for($edu_i=0; $edu_i < count($ARR_EDU_TYPE); $edu_i++) {
					if (strpos($ARR_EDU_TYPE[$edu_i], $EDU_TYPE[$i]) !== false) {
						$edu_change_idx[$EDU_TYPE[$i]] = $edu_i;
//						break;
					}
				} // end loop for $edu_i
			} else {
				$edu_change_idx[$EDU_TYPE[$i]] = 0;
			}
			$EDU_TYPE_TXT.= "$EDU_TYPE[$i]||";
		} // end loop for $i
//		echo "idx=$edu_change_idx<br>";
		$ST_CODE = (trim($ST_CODE))? "'" . $ST_CODE . "'"  : "NULL";
		$CT_CODE = (trim($CT_CODE))? "'" . $CT_CODE . "'"  : "NULL";
		$EL_CODE = (trim($EL_CODE))? "'" . $EL_CODE . "'"  : "NULL";
		$EN_CODE = (trim($EN_CODE))? "'" . $EN_CODE . "'"  : "NULL";
		$EM_CODE = (trim($EM_CODE))? "'" . $EM_CODE . "'"  : "NULL";
		$INS_CODE = (trim($INS_CODE))? "'" . $INS_CODE . "'"  : "NULL";		

		// เอา edu_type 1 2 4  ที่มีซ้ำใน record อื่น ออก...
		for ($i=0; $i<count($EDU_TYPE); $i++) {
			$out_edu_type = false;
			if ($EDU_TYPE[$i] == 1 || $EDU_TYPE[$i] == 2 || $EDU_TYPE[$i] == 4) { // 1.วุฒิที่ใช้บรรจุ  2.วุฒิในตำแหน่งปัจจุบัน  4.วุฒิสูงสุด
				if ($edu_change_idx[$EDU_TYPE[$i]] > -1) { // กรณีที่ มี edu_type วุฒิที่ใช้บรรจุ ใน seq อื่น
					$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]] = str_replace("".$EDU_TYPE[$i]."||","",$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]]); // เอา edu_type=1 2 4 ออกจาก edu_type ที่ edu_seq=$edu_change_idx
					$out_edu_type = true;
				}
			}
			if ($out_edu_type) {
//				echo "($EDU_TYPE[$i]) af upd-".$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]]."<br>";
				$cmd = " update PER_WORKFLOW_EDUCATE
																   set EDU_TYPE='".$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]]."',
																		 UPDATE_USER=$SESS_USERID, 
																		 UPDATE_DATE='$UPDATE_DATE'
								where PER_ID=$PER_ID and EDU_SEQ='".$ARR_EDU_SEQ[$edu_change_idx[$EDU_TYPE[$i]]]."' ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		} // end for loop $i		

		$EDU_ENDDATE =  save_date($EDU_ENDDATE);
		$EDU_BOOK_DATE =  save_date($EDU_BOOK_DATE);

		$cmd2="select   EDU_SEQ from PER_WORKFLOW_EDUCATE where PER_ID=$PER_ID and EDU_SEQ='$EDU_SEQ' and EDU_ID<>$EDU_ID";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr><br>";
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถแก้ไขข้อมูลได้ เนื่องจากคุณระบุลำดับ ซ้ำ !!!");
				-->   </script>	<? }  
		else {	
			$cmd = " update PER_WORKFLOW_EDUCATE set
							EDU_SEQ='$EDU_SEQ', 
							EDU_STARTYEAR='$EDU_STARTYEAR', 
							EDU_ENDYEAR='$EDU_ENDYEAR', 
							ST_CODE=$ST_CODE, 
							CT_CODE=$CT_CODE,
							EDU_FUND='$EDU_FUND', 
							EN_CODE=$EN_CODE, 
							EM_CODE=$EM_CODE, 
							INS_CODE=$INS_CODE, 
							EDU_TYPE='$EDU_TYPE_TXT',
							PER_CARDNO='$PER_CARDNO', 
							EL_CODE=$EL_CODE, 
							EDU_ENDDATE='$EDU_ENDDATE',
							EDU_GRADE=$EDU_GRADE, 
							EDU_HONOR='$EDU_HONOR', 
							EDU_BOOK_NO='$EDU_BOOK_NO', 
							EDU_BOOK_DATE='$EDU_BOOK_DATE', 
							EDU_REMARK='$EDU_REMARK', 
							EDU_INSTITUTE='$EDU_INSTITUTE', 
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'
						where EDU_ID=$EDU_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการศึกษา [$PER_ID : $PER_NAME : $EN_CODE]");
		} // end if
	} // end if เช็คข้อมูลซ้ำ
	
	if($command=="DELETE" && $PER_ID && $EDU_ID){
		$cmd = " select EN_CODE from PER_WORKFLOW_EDUCATE where EDU_ID=$EDU_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EN_CODE = $data[EN_CODE];
		
		$cmd = " delete from PER_WORKFLOW_EDUCATE where EDU_ID=$EDU_ID ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติการศึกษา [$PER_ID : $PER_NAME : $EN_CODE]");
	} // end if

//  UPDATE EDU_WF_STATUS
	$updtype=explode("-",$command);
	$F_REFRESH = "";
        //echo $command."<br>";
	if($updtype[0]=="UPD" && $PER_ID && $EDU_ID){
		$EDU_WF_STATUS = $updtype[1];
		$cmd2="select * from PER_WORKFLOW_EDUCATE where PER_ID=$PER_ID and EDU_ID=$EDU_ID";
		$count=$db_dpis->send_cmd($cmd2);
//		$db_dpis->show_error(); echo "<hr><br>";
		if($count) { 
			$cmd = " 	update PER_WORKFLOW_EDUCATE set
								EDU_WF_STATUS='$updtype[1]', 
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE'
							where PER_ID=$PER_ID and EDU_ID=$EDU_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$F_REFRESH = "1";
		
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการศึกษา [$PER_ID : $PER_NAME : $EN_CODE]");
			
			if ($EDU_WF_STATUS=="04") {
				$command ="COMMAND"; 	// กรณี 04 อนุมัติ - ถ่ายโอนข้อมูลเข้าแฟ้มจริง หลังจาก update status ที่ แฟ้ม temp
															// โดยแก้ $command ให้ค่า = "COMMAND" เพื่อจะได้เข้า loop ใน if ต่อจากนี้
			}
		} // end if
	} // end if  UPDATE EDU_WF_STATUS
	
	if($command=="COMMAND" && $PER_ID){
		$cmd = " select max(EDU_ID) as max_id from PER_EDUCATE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$EDU_ID = $data[max_id] + 1;
		
		// อ่านข้อมูลใน PER_EDUCATE เพื่อเตรียม EDU_TYPE เพื่อค้นหา SEQ ที่มี ซ้ำ เพื่อจะตัดออก
		$cmd = " select EDU_SEQ, EDU_TYPE 
						from PER_EDUCATE 
					  where PER_ID=$PER_ID order by EDU_SEQ ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while ($data = $db_dpis->get_array()) {
//			$data = array_change_key_case($data, CASE_LOWER);
			 if ($data[EDU_SEQ] != $EDU_SEQ) {
//				echo "data eduseq=".$data[EDU_SEQ]." != $EDU_SEQ ".$data[EDU_TYPE]."<br>";
				$ARR_EDU_SEQ[] = trim($data[EDU_SEQ]);
				$ARR_EDU_TYPE[] = trim($data[EDU_TYPE]);
			}
		}

		if (trim($EDU_TYPE))		$EDU_TYPE_TXT = "||";
		
		// ตรวจสอบ ในทุก EDU_SEQ ว่า SEQ ไหนมีตัวซ้ำที่ต้องการเปลี่ยน กะจะตัดออก
		for ($i=0; $i<count($EDU_TYPE); $i++) {
			$edu_change_idx[$EDU_TYPE[$i]] = -1; 
			if ($EDU_TYPE[$i] == 1 || $EDU_TYPE[$i] == 2 || $EDU_TYPE[$i] == 4) { // 1.วุฒิที่ใช้บรรจุ  2.วุฒิในตำแหน่งปัจจุบัน  4.วุฒิสูงสุด
				for($edu_i=0; $edu_i < count($ARR_EDU_TYPE); $edu_i++) {
					if (strpos($ARR_EDU_TYPE[$edu_i], $EDU_TYPE[$i]) !== false) {
						$edu_change_idx[$EDU_TYPE[$i]] = $edu_i;
//						break;
					}
				} // end loop for $edu_i
			} else {
				$edu_change_idx[$EDU_TYPE[$i]] = 0;
			}
			$EDU_TYPE_TXT.= "$EDU_TYPE[$i]||";
		} // end loop for $i
		$ST_CODE = (trim($ST_CODE))? "'" . $ST_CODE . "'"  : "NULL";
		$CT_CODE = (trim($CT_CODE))? "'" . $CT_CODE . "'"  : "NULL";
		$EL_CODE = (trim($EL_CODE))? "'" . $EL_CODE . "'"  : "NULL";
		$EN_CODE = (trim($EN_CODE))? "'" . $EN_CODE . "'"  : "NULL";
		$EM_CODE = (trim($EM_CODE))? "'" . $EM_CODE . "'"  : "NULL";
		$INS_CODE = (trim($INS_CODE))? "'" . $INS_CODE . "'"  : "NULL";	
		
		// เอา edu_type 1 2 4  ที่มีซ้ำใน record อื่น ออก...
		for ($i=0; $i<count($EDU_TYPE); $i++) {
			$out_edu_type = false;
			if ($EDU_TYPE[$i] == 1 || $EDU_TYPE[$i] == 2 || $EDU_TYPE[$i] == 4) { // 1.วุฒิที่ใช้บรรจุ  2.วุฒิในตำแหน่งปัจจุบัน  4.วุฒิสูงสุด
				if ($edu_change_idx[$EDU_TYPE[$i]] > -1) { // กรณีที่ มี edu_type วุฒิที่ใช้บรรจุ ใน seq อื่น
					$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]] = str_replace("".$EDU_TYPE[$i]."||","",$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]]); // เอา edu_type=1 2 4 ออกจาก edu_type ที่ edu_seq=$edu_change_idx
					$out_edu_type = true;
				}
			}
			if ($out_edu_type) {
//				echo "($EDU_TYPE[$i]) af upd-".$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]]."<br>";
				$cmd = " update PER_EDUCATE
										   set EDU_TYPE='".$ARR_EDU_TYPE[$edu_change_idx[$EDU_TYPE[$i]]]."',
												 UPDATE_USER=$SESS_USERID, 
												 UPDATE_DATE='$UPDATE_DATE'
								where PER_ID=$PER_ID and EDU_SEQ='".$ARR_EDU_SEQ[$edu_change_idx[$EDU_TYPE[$i]]]."'
					 		";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}
		} // end for loop $i

		$EDU_ENDDATE =  save_date($EDU_ENDDATE);
		$EDU_BOOK_DATE =  save_date($EDU_BOOK_DATE);

		$cmd = " select max(EDU_SEQ) as max_id from PER_EDUCATE where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$EDU_SEQ = $data[max_id] + 1;
                 /*เดิม*/               
		/*$cmd = " insert into PER_EDUCATE	(EDU_ID, PER_ID, EDU_SEQ, EDU_STARTYEAR, EDU_ENDYEAR, ST_CODE, CT_CODE, 
					EDU_FUND, EN_CODE, EM_CODE, INS_CODE, EDU_TYPE, PER_CARDNO, UPDATE_USER, UPDATE_DATE,
					EL_CODE, EDU_ENDDATE, EDU_GRADE, EDU_HONOR, EDU_BOOK_NO, EDU_BOOK_DATE, EDU_REMARK, EDU_INSTITUTE)
					values ($EDU_ID, $PER_ID, '$EDU_SEQ', '$EDU_STARTYEAR', '$EDU_ENDYEAR', $ST_CODE, $CT_CODE, 
					'$EDU_FUND', $EN_CODE, $EM_CODE, $INS_CODE, '$EDU_TYPE_TXT', '$PER_CARDNO', $UPDATE_USER, '$UPDATE_DATE',
					$EL_CODE, '$EDU_ENDDATE', $EDU_GRADE, '$EDU_HONOR', '$EDU_BOOK_NO', '$EDU_BOOK_DATE', '$EDU_REMARK', '$EDU_INSTITUTE') ";*/
                
		/*Release 5.1.0.7 Begin*/
                $cmd = " insert into PER_EDUCATE (EDU_ID, PER_ID, EDU_SEQ, EDU_STARTYEAR, EDU_ENDYEAR, ST_CODE, CT_CODE, 
					EDU_FUND, EN_CODE, EM_CODE, INS_CODE, EDU_TYPE, PER_CARDNO, UPDATE_USER, UPDATE_DATE,
					EL_CODE, EDU_ENDDATE, EDU_GRADE, EDU_HONOR, EDU_BOOK_NO, EDU_BOOK_DATE, EDU_REMARK, EDU_INSTITUTE)
                        values ($EDU_ID, $PER_ID, '$EDU_SEQ', '$EDU_STARTYEAR', '$EDU_ENDYEAR', $ST_CODE, $CT_CODE, 
					'$EDU_FUND', $EN_CODE, $EM_CODE, $INS_CODE, '$EDU_TYPE_TXT', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE',
					$EL_CODE, '$EDU_ENDDATE', $EDU_GRADE, '$EDU_HONOR', '$EDU_BOOK_NO', '$EDU_BOOK_DATE', '$EDU_REMARK', '$EDU_INSTITUTE') ";
                /*Release 5.1.0.7 end*/
                //echo $cmd."<br><br>";
               // die('');
                $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

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
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/41';
				$MOVERENAME = "PER_ATTACHMENT41";
			}else{
				$MOVESECOND_SUBDIR = $MOVEFIRST_SUBDIR.'/'.$MOVE_CATEGORY;
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/'.$EDU_ID;
				$MOVERENAME = $MOVE_CATEGORY.$EDU_ID;
			}
//			echo "$MOVEFINAL_PATH with $MOVERENAME";
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
			//___echo "TEST : [ $EDU_ID ] <$FILE_PATH ~~ $MOVE_PATH> $FILE_PATH => $MOVEFINAL_PATH<br>$cmdup<br>";
			//###################################

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการศึกษา [$PER_ID : $PER_NAME : $EN_CODE]");
	} // end if

	if(($UPD && $PER_ID && $EDU_ID) || ($VIEW && $PER_ID && $EDU_ID)){
		$cmd = "	select	EDU_SEQ, EDU_STARTYEAR, EDU_ENDYEAR, EDU_FUND, EDU_TYPE, ST_CODE, CT_CODE, 
											EN_CODE, EM_CODE, INS_CODE, UPDATE_USER, UPDATE_DATE, EL_CODE, EDU_ENDDATE, 
											EDU_GRADE, EDU_HONOR, EDU_BOOK_NO, EDU_BOOK_DATE, EDU_REMARK, EDU_INSTITUTE, EDU_WF_STATUS
							from		PER_WORKFLOW_EDUCATE
							where	EDU_ID=$EDU_ID and EDU_WF_STATUS!='04' ";	
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EDU_SEQ = $data[EDU_SEQ];
		$EDU_STARTYEAR = $data[EDU_STARTYEAR];
		$EDU_ENDYEAR = $data[EDU_ENDYEAR];
		$EDU_FUND = $data[EDU_FUND];
		$EDU_GRADE = $data[EDU_GRADE];
		$EDU_HONOR = trim($data[EDU_HONOR]);
		$EDU_BOOK_NO = trim($data[EDU_BOOK_NO]);
		$EDU_REMARK = trim($data[EDU_REMARK]);
		$EDU_INSTITUTE = trim($data[EDU_INSTITUTE]);
		$EDU_WF_STATUS = trim($data[EDU_WF_STATUS]);
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		$EDU_ENDDATE = show_date_format($data[EDU_ENDDATE], 1);
		$EDU_BOOK_DATE = show_date_format($data[EDU_BOOK_DATE], 1);

		$temp_edutype = $data[EDU_TYPE];
		$EDU_TYPE_tmp = explode("||", $temp_edutype);
		for ($i=1; $i<count($EDU_TYPE_tmp)-1; $i++) {
			if ($EDU_TYPE_tmp[$i] == 1)		$EDU_TYPE_tmp1 = 1 ;
			elseif ($EDU_TYPE_tmp[$i] == 2)		$EDU_TYPE_tmp2 = 2 ;
			elseif ($EDU_TYPE_tmp[$i] == 3)		$EDU_TYPE_tmp3 = 3 ;
			elseif ($EDU_TYPE_tmp[$i] == 4)		$EDU_TYPE_tmp4 = 4 ;
			elseif ($EDU_TYPE_tmp[$i] == 5)		$EDU_TYPE_tmp5 = 5 ;
		}

		$ST_NAME = $CT_NAME = $EL_NAME = $EN_NAME = $EM_NAME = $INS_NAME = $INS_COUNTRY = "";
		$ST_CODE = $data[ST_CODE];
		if($ST_CODE){
			$cmd = " select ST_NAME from PER_SCHOLARTYPE where ST_CODE='$ST_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ST_NAME = $data2[ST_NAME];
		} // end if

		$CT_CODE = $data[CT_CODE];
		if($CT_CODE){
			$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CT_NAME = $data2[CT_NAME];
		} // end if

		$EL_CODE = $data[EL_CODE];
		if($EL_CODE){
			$cmd = " select EL_NAME from PER_EDUCLEVEL where EL_CODE='$EL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$EL_NAME = $data2[EL_NAME];
		} // end if

		$EN_CODE = $data[EN_CODE];
		if($EN_CODE){
			$cmd = " select EN_NAME from PER_EDUCNAME where EN_CODE='$EN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$EN_NAME = $data2[EN_NAME];
		} // end if

		$EM_CODE = $data[EM_CODE];
		if($EM_CODE){
			$cmd = " select EM_NAME from PER_EDUCMAJOR where EM_CODE='$EM_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$EM_NAME = $data2[EM_NAME];
		} // end if

		$INS_CODE = $data[INS_CODE];
		if($INS_CODE){
			if($DPISDB=="odbc"){
				$cmd = " select 	INS.INS_NAME, CT.CT_NAME
								 from 		PER_INSTITUTE as INS
												left join PER_COUNTRY as CT on (INS.CT_CODE=CT.CT_CODE)
								 where 	INS.INS_CODE='$INS_CODE' ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select 	PER_INSTITUTE.INS_NAME, PER_COUNTRY.CT_NAME
								 from 		PER_INSTITUTE, PER_COUNTRY
								 where 	PER_INSTITUTE.CT_CODE=PER_COUNTRY.CT_CODE(+) and PER_INSTITUTE.INS_CODE='$INS_CODE' ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select 	INS.INS_NAME, CT.CT_NAME
								 from 		PER_INSTITUTE as INS
												left join PER_COUNTRY as CT on (INS.CT_CODE=CT.CT_CODE)
								 where 	INS.INS_CODE='$INS_CODE' ";
			} // end if
			
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$INS_NAME = $data2[INS_NAME];
			$INS_COUNTRY = $data2[CT_NAME];
		} // end if
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($EDU_ID);
		unset($EDU_SEQ);
		unset($EDU_STARTYEAR);
		unset($EDU_ENDYEAR);
		unset($EDU_FUND);
		unset($EDU_TYPE);
		unset($EDU_TYPE_LIST);
		unset($ST_CODE);
		unset($ST_NAME);
		unset($CT_CODE);
		unset($CT_NAME);
		unset($EN_CODE);
		unset($EN_NAME);
		unset($EM_CODE);
		unset($EM_NAME);
		unset($INS_CODE);
		unset($INS_NAME);
		$INS_COUNTRY = "ไทย";
		unset($EL_CODE);
		unset($EL_NAME);
		unset($EDU_ENDDATE);
		unset($EDU_GRADE);
		unset($EDU_HONOR);
		unset($EDU_BOOK_NO);
		unset($EDU_BOOK_DATE);
		unset($EDU_REMARK);
		unset($EDU_INSTITUTE);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>