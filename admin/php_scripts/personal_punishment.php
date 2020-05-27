<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$cmdChkModify ="SELECT COUNT(COLUMN_NAME) AS CNT FROM USER_TAB_COLS WHERE TABLE_NAME = 'PER_PUNISHMENT' AND UPPER(COLUMN_NAME) IN('PUN_VIOLATEDATE_END')";
                $db_dpis3->send_cmd($cmdChkModify);
                $dataModify = $db_dpis3->get_array();
				
        /*Release 5.2.1.2 Begin*/
        $cmdModify = "SELECT COLUMN_NAME, DATA_TYPE ,DATA_LENGTH
                     FROM USER_TAB_COLUMNS
                     WHERE TABLE_NAME = 'PER_PUNISHMENT'
                     AND UPPER(COLUMN_NAME) IN('PUN_RECEIVE_NO','PUN_SEND_NO','INV_NO','PUN_NO') ";
         $cnt = $db_dpis->send_cmd($cmdModify);
         if($cnt){
            while ($data_fld = $db_dpis->get_array_array()) {
                $column_name = strtoupper($data_fld[0]);
                $data_length = $data_fld[2];
                if($column_name=='INV_NO' && $data_length==20){
                    $cmdModify = "ALTER TABLE PER_PUNISHMENT MODIFY INV_NO VARCHAR2(100)";
                    $db_dpis2->send_cmd($cmdModify);
                }
                if($column_name=='PUN_NO' && $data_length==20){
                    $cmdModify = "ALTER TABLE PER_PUNISHMENT MODIFY PUN_NO VARCHAR2(100)";
                    $db_dpis2->send_cmd($cmdModify);
                }
                if($column_name=='PUN_RECEIVE_NO' && $data_length==20){
                    $cmdModify = "ALTER TABLE PER_PUNISHMENT MODIFY PUN_RECEIVE_NO VARCHAR2(100)";
                    $db_dpis2->send_cmd($cmdModify);
                }
                if($column_name=='PUN_SEND_NO' && $data_length==20){
                    $cmdModify = "ALTER TABLE PER_PUNISHMENT MODIFY PUN_SEND_NO VARCHAR2(100)";
                    $db_dpis2->send_cmd($cmdModify);
                }
                $db_dpis2->send_cmd("COMMIT");
            }
         }
         /*Release 5.2.1.2 End*/
        
	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	if($PER_ID){
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
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_PUNISHMENT set AUDIT_FLAG = 'N' where PUN_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_PUNISHMENT set AUDIT_FLAG = 'Y' where PUN_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		$PUN_STARTDATE = save_date($PUN_STARTDATE); 
		$PUN_ENDDATE = save_date($PUN_ENDDATE); 
		$PUN_REPORTDATE = save_date($PUN_REPORTDATE); 
		$PUN_VIOLATEDATE = save_date($PUN_VIOLATEDATE); 
		$PUN_DOCDATE = save_date($PUN_DOCDATE);
		$PUN_VIOLATEDATE_END = save_date($PUN_VIOLATEDATE_END);
		
		$cmd2 = " select  CRD_CODE, PUN_TYPE, PEN_CODE, PUN_STARTDATE, PUN_ENDDATE from PER_PUNISHMENT 
							where PER_ID=$PER_ID and CRD_CODE='$CRD_CODE' and PUN_TYPE=$PUN_TYPE and  
							PEN_CODE='$PEN_CODE' and PUN_STARTDATE='$PUN_STARTDATE' and PUN_ENDDATE='$PUN_ENDDATE' ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  
                    <script> 
                    <!--  
			alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจากคุณระบุกรณีความผิด, สั่งให้, วันที่รับโทษ และวันที่สิ้นสุดโทษ ซ้ำ !!!");
				-->   
                    </script>	<? }  
		else {	  	
			$cmd = " select max(PUN_ID) as max_id from PER_PUNISHMENT ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$PUN_ID = $data[max_id] + 1;
			
			$cmd = " insert into PER_PUNISHMENT (PUN_ID, PER_ID, INV_NO, PUN_NO, PUN_REF_NO, PUN_TYPE, 
							PUN_STARTDATE, PUN_ENDDATE, CRD_CODE, PEN_CODE, PUN_PAY, PUN_SALARY, 
							PUN_REMARK, PER_CARDNO, UPDATE_USER, UPDATE_DATE, PUN_RECEIVE_NO, 
							PUN_SEND_NO, PUN_NOTICE, PUN_REPORTDATE, PUN_VIOLATEDATE, PUN_DOCDATE, PUN_VIOLATEDATE_END)
							values ($PUN_ID, $PER_ID, '$INV_NO', '$PUN_NO', '$PUN_REF_NO', '$PUN_TYPE', 
							'$PUN_STARTDATE', '$PUN_ENDDATE', '$CRD_CODE', '$PEN_CODE', '0', '0', 
							'$PUN_REMARK', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE', '$PUN_RECEIVE_NO', 
							'$PUN_SEND_NO', '$PUN_NOTICE', '$PUN_REPORTDATE', '$PUN_VIOLATEDATE', '$PUN_DOCDATE', '$PUN_VIOLATEDATE_END')  ";
			//echo $cmd;
                        $db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติทางวินัย [$PER_ID : $PUN_ID : $CRD_CODE]");
			$ADD_NEXT = 1;
		} // end if
	}// end if เช็คข้อมูลซ้ำ

	if($command=="UPDATE" && $PER_ID && $PUN_ID){
		$PUN_STARTDATE = save_date($PUN_STARTDATE); 
		$PUN_ENDDATE = save_date($PUN_ENDDATE); 
		$PUN_REPORTDATE = save_date($PUN_REPORTDATE); 
		$PUN_VIOLATEDATE = save_date($PUN_VIOLATEDATE); 
		$PUN_DOCDATE = save_date($PUN_DOCDATE); 
		$PUN_VIOLATEDATE_END = save_date($PUN_VIOLATEDATE_END);
		/*
		$cmd2 = " select  CRD_CODE, PUN_TYPE, PEN_CODE, PUN_STARTDATE, PUN_ENDDATE from PER_PUNISHMENT 
							where PER_ID=$PER_ID and CRD_CODE='$CRD_CODE' and PUN_TYPE=$PUN_TYPE and  
							PEN_CODE='$PEN_CODE' and PUN_STARTDATE='$PUN_STARTDATE' and PUN_ENDDATE='$PUN_ENDDATE' ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
		alert("ไม่สามารถแก้ไขข้อมูลได้ เนื่องจากคุณระบุกรณีความผิด, สั่งให้, วันที่รับโทษ และวันที่สิ้นสุดโทษ ซ้ำ !!!");
				-->   </script>	<? }  
		else {	
	*/
		$cmd = " UPDATE PER_PUNISHMENT SET
							INV_NO='$INV_NO', 
							PUN_NO='$PUN_NO', 
							PUN_REF_NO='$PUN_REF_NO', 
							PUN_TYPE='$PUN_TYPE', 
							PUN_STARTDATE='$PUN_STARTDATE', 
							PUN_ENDDATE='$PUN_ENDDATE', 
							CRD_CODE='$CRD_CODE', 
							PEN_CODE='$PEN_CODE', 
							PUN_REMARK='$PUN_REMARK', 
							PER_CARDNO='$PER_CARDNO', 
							PUN_RECEIVE_NO='$PUN_RECEIVE_NO', 
							PUN_SEND_NO='$PUN_SEND_NO', 
							PUN_NOTICE='$PUN_NOTICE', 
							PUN_REPORTDATE='$PUN_REPORTDATE', 
							PUN_VIOLATEDATE='$PUN_VIOLATEDATE',
							PUN_VIOLATEDATE_END = '$PUN_VIOLATEDATE_END',
							PUN_DOCDATE='$PUN_DOCDATE', 
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'
							WHERE PUN_ID=$PUN_ID";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติทางวินัย [$PER_ID : $PUN_ID : $CRD_CODE]");
		//} // end if
	}// end if เช็คข้อมูลซ้ำ
	
	if($command=="DELETE" && $PER_ID && $PUN_ID){
		$cmd = " select CRD_CODE from PER_PUNISHMENT where PUN_ID=$PUN_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$CRD_CODE = $data[CRD_CODE];
		
		$cmd = " delete from PER_PUNISHMENT where PUN_ID=$PUN_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติทางวินัย [$PER_ID : $PUN_ID : $CRD_CODE]");
	} // end if

	if(($UPD && $PER_ID && $PUN_ID) || ($VIEW && $PER_ID && $PUN_ID)){
		$cmd = "	SELECT 		PUN_ID, INV_NO, PUN_NO, PUN_REF_NO, ppm.CRD_CODE, pcd.CRD_NAME, PUN_STARTDATE, PUN_ENDDATE,  
							ppm.PEN_CODE, pp.PEN_NAME, PUN_TYPE, PUN_REMARK, PUN_RECEIVE_NO, PUN_SEND_NO, PUN_NOTICE, 
							PUN_REPORTDATE, PUN_VIOLATEDATE, PUN_VIOLATEDATE_END, PUN_DOCDATE, ppm.UPDATE_USER, ppm.UPDATE_DATE    
				FROM		PER_PUNISHMENT ppm, PER_CRIME_DTL pcd, PER_PENALTY pp   
				WHERE		PUN_ID=$PUN_ID and ppm.CRD_CODE=pcd.CRD_CODE and ppm.PEN_CODE=pp.PEN_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		
		$INV_NO = $data[INV_NO];
		$PUN_NO = $data[PUN_NO];
		$PUN_REF_NO = $data[PUN_REF_NO];
		$PUN_TYPE = $data[PUN_TYPE];
		$PUN_REMARK = $data[PUN_REMARK];
		$PUN_RECEIVE_NO = $data[PUN_RECEIVE_NO];
		$PUN_SEND_NO = $data[PUN_SEND_NO];
		$PUN_NOTICE = $data[PUN_NOTICE];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		$PUN_STARTDATE = show_date_format(trim($data[PUN_STARTDATE]), 1);
		$PUN_ENDDATE = show_date_format(trim($data[PUN_ENDDATE]), 1);
		$PUN_REPORTDATE = show_date_format(trim($data[PUN_REPORTDATE]), 1);
		$PUN_VIOLATEDATE = show_date_format(trim($data[PUN_VIOLATEDATE]), 1);
		$PUN_VIOLATEDATE_END = show_date_format(trim($data[PUN_VIOLATEDATE_END]), 1);
		$PUN_DOCDATE = show_date_format(trim($data[PUN_DOCDATE]), 1);
		$CRD_CODE = $data[CRD_CODE];
		$CRD_NAME = $data[CRD_NAME];
		$PEN_CODE = $data[PEN_CODE];
		$PEN_NAME = $data[PEN_NAME];		

		if($CRD_CODE){
			$cmd = " select CR_NAME from PER_CRIME pc, PER_CRIME_DTL pcd where CRD_CODE='$CRD_CODE' and pc.CR_CODE=pcd.CR_CODE";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CR_NAME = $data2[CR_NAME];
		} // end if
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($PUN_ID);
		unset($INV_NO);
		unset($PUN_NO);
		unset($PUN_REF_NO);
		unset($PUN_TYPE);
		unset($PUN_REMARK);
		unset($PUN_STARTDATE);
		unset($PUN_ENDDATE);
		unset($PUN_RECEIVE_NO);
		unset($PUN_SEND_NO);
		unset($PUN_NOTICE);
		unset($PUN_REPORTDATE);
		unset($PUN_VIOLATEDATE);
		unset($PUN_VIOLATEDATE_END);
		unset($PUN_DOCDATE);

		unset($CRD_CODE);
		unset($CRD_NAME);
		unset($CR_NAME);
		unset($PEN_CODE);
		unset($PEN_NAME);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
		
		//$PUN_TYPE = 1;
	} // end if
?>