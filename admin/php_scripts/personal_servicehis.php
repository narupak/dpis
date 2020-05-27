<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

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
	$ORG_ID = (trim($ORG_ID))? $ORG_ID : "NULL";

	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_SERVICEHIS set AUDIT_FLAG = 'N' where SRH_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_SERVICEHIS set AUDIT_FLAG = 'Y' where SRH_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		$SRH_STARTDATE = save_date($SRH_STARTDATE); 
		$SRH_ENDDATE = save_date($SRH_ENDDATE); 
		$PER_ID_ASSIGN = (trim($PER_ID_ASSIGN))? "'".trim($PER_ID_ASSIGN)."'" : "NULL";
		$ORG_ID_ASSIGN = (trim($ORG_ID_ASSIGN))? "'".trim($ORG_ID_ASSIGN)."'" : "NULL";		
	
		$cmd = " select max(SRH_ID) as max_id from PER_SERVICEHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$SRH_ID = $data[max_id] + 1;
		
                $SRH_DOCNO = str_replace("'","",$SRH_DOCNO);
                $SRH_DOCNO = str_replace(",","",$SRH_DOCNO);
                
                $SRH_SRT_NAME = str_replace("'","",$SRH_SRT_NAME);
                $SRH_SRT_NAME = str_replace(",","",$SRH_SRT_NAME);
                
                $SRH_ORG = str_replace("'","",$SRH_ORG);
                $SRH_ORG = str_replace(",","",$SRH_ORG);
                
                $SRH_NOTE = str_replace("'","",$SRH_NOTE);
                $SRH_NOTE = str_replace(",","",$SRH_NOTE);
                 
		$cmd = " insert into PER_SERVICEHIS (SRH_ID, PER_ID, SV_CODE, SRT_CODE, ORG_ID, SRH_STARTDATE, SRH_ENDDATE, SRH_NOTE, 
				SRH_DOCNO, PER_ID_ASSIGN, ORG_ID_ASSIGN, PER_CARDNO, UPDATE_USER, UPDATE_DATE, SRH_ORG, SRH_SRT_NAME)
				values ($SRH_ID, $PER_ID, '$SV_CODE', '$SRT_CODE', $ORG_ID, '$SRH_STARTDATE', '$SRH_ENDDATE', '$SRH_NOTE', 
				'$SRH_DOCNO', $PER_ID_ASSIGN, $ORG_ID_ASSIGN, '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE', '$SRH_ORG', '$SRH_SRT_NAME') ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติราชการพิเศษ [$PER_ID : $SRH_ID : $SV_CODE]");
		$ADD_NEXT = 1;
	} // end if

	if($command=="UPDATE" && $PER_ID && $SRH_ID){
		$SRH_STARTDATE = save_date($SRH_STARTDATE); 
		$SRH_ENDDATE = save_date($SRH_ENDDATE); 
		$PER_ID_ASSIGN = (trim($PER_ID_ASSIGN))? "'".trim($PER_ID_ASSIGN)."'" : "NULL";
		$ORG_ID_ASSIGN = (trim($ORG_ID_ASSIGN))? "'".trim($ORG_ID_ASSIGN)."'" : "NULL";		
                
                $SRH_DOCNO = str_replace("'","",$SRH_DOCNO);
                $SRH_DOCNO = str_replace(",","",$SRH_DOCNO);
                
                $SRH_SRT_NAME = str_replace("'","",$SRH_SRT_NAME);
                $SRH_SRT_NAME = str_replace(",","",$SRH_SRT_NAME);
                
                $SRH_ORG = str_replace("'","",$SRH_ORG);
                $SRH_ORG = str_replace(",","",$SRH_ORG);
                
                $SRH_NOTE = str_replace("'","",$SRH_NOTE);
                $SRH_NOTE = str_replace(",","",$SRH_NOTE);
                
                if($SRH_ORG){
                    $ORG_NAME=$SRH_ORG;
                }else{
                    $ORG_NAME=$ORG_NAME;
                }
                
		$cmd = " UPDATE PER_SERVICEHIS SET
					SV_CODE='$SV_CODE', 
					SRT_CODE='$SRT_CODE', 
					ORG_ID=$ORG_ID, 
					SRH_STARTDATE='$SRH_STARTDATE', 
					SRH_ENDDATE='$SRH_ENDDATE', 
					SRH_NOTE='$SRH_NOTE', 
					SRH_DOCNO='$SRH_DOCNO', 
					PER_ID_ASSIGN=$PER_ID_ASSIGN, 
					ORG_ID_ASSIGN=$ORG_ID_ASSIGN, 
					PER_CARDNO='$PER_CARDNO', 
					SRH_ORG='$ORG_NAME', 
					SRH_SRT_NAME='$SRH_SRT_NAME', 
					UPDATE_USER=$SESS_USERID, 
					UPDATE_DATE='$UPDATE_DATE'
				WHERE SRH_ID = $SRH_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติราชการพิเศษ [$PER_ID : $SRH_ID : $SV_CODE]");
	} // end if
	
	if($command=="DELETE" && $PER_ID && $SRH_ID){
		$cmd = " select SV_CODE from PER_SERVICEHIS where SRH_ID=$SRH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$SV_CODE = $data[SV_CODE];
		
		$cmd = " delete from PER_SERVICEHIS where SRH_ID=$SRH_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติราชการพิเศษ [$PER_ID : $SRH_ID : $SV_CODE]");
	} // end if

	if(($UPD && $PER_ID && $SRH_ID) || ($VIEW && $PER_ID && $SRH_ID)){
		$cmd = " SELECT 	SRH_ID, SRH_STARTDATE, SRH_ENDDATE, SRH_DOCNO, SRH_NOTE,  ORG_ID_ASSIGN, 
											PER_ID_ASSIGN, psh.SV_CODE, psh.SRT_CODE, psh.ORG_ID, ps.SV_NAME, pst.SRT_NAME, 
											psh.UPDATE_USER, psh.UPDATE_DATE, po.ORG_NAME, SRH_ORG, SRH_SRT_NAME  
						FROM		PER_SERVICEHIS psh, PER_SERVICE ps, PER_SERVICETITLE pst, PER_ORG po
						WHERE	SRH_ID=$SRH_ID and psh.SV_CODE=ps.SV_CODE and psh.SRT_CODE=pst.SRT_CODE(+) and psh.ORG_ID=po.ORG_ID(+) ";
							//echo $cmd;
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$SRH_STARTDATE = show_date_format(trim($data[SRH_STARTDATE]), 1);
		$SRH_ENDDATE = show_date_format(trim($data[SRH_ENDDATE]), 1);
		$SRH_NOTE = $data[SRH_NOTE];
		$SRH_DOCNO = $data[SRH_DOCNO];
		$SV_CODE = $data[SV_CODE];
		$SV_NAME = $data[SV_NAME];
		$ORG_ID = $data[ORG_ID];
		$ORG_NAME = $data[ORG_NAME];
		$SRH_ORG = $data[SRH_ORG];
		$SRH_SRT_NAME = $data[SRH_SRT_NAME];
		$SRT_CODE = $data[SRT_CODE];
		$SRT_NAME = $data[SRT_NAME];		
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
		unset($SRH_ORG);
		unset($SRH_SRT_NAME);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>