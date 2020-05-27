<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");
	include("php_scripts/load_per_control.php");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	if (!$POH_LAST_POSITION) $POH_LAST_POSITION = "N";
	if (!$POH_ISREAL) $POH_ISREAL = "Y";
	 /*
                ATTACH_FILE=1 : แสดงทั้งหมด
                ATTACH_FILE=2 : แสดงแบบรายการ
                */
        if(empty($hiddenATTACH_FILE)){
            if($ATTACH_FILE==1){
                $btnwidth=250;
                $Bntlbl='แสดงไฟล์ตามค่า Config แบบทั้งหมด';
                $ATTACH_FILE=1;
                $key =2;
            }else if($ATTACH_FILE==2){
                $btnwidth=350;
                $Bntlbl='แสดงไฟล์ตามค่า Config แบบรายข้อมูล (Transaction)';
                $ATTACH_FILE=2;
                $key=1;
            }
        }else{
            if($hiddenATTACH_FILE==1){
                $btnwidth=250;
                $Bntlbl='แสดงไฟล์ตามค่า Config แบบทั้งหมด';$ATTACH_FILE=1; $key = 2;
            }else if($hiddenATTACH_FILE==2){
                $btnwidth=350;
                $Bntlbl='แสดงไฟล์ตามค่า Config แบบรายข้อมูล (Transaction)';$ATTACH_FILE=2; $key = 1;
            }
        }
        //echo $key;

	
        /*Release 5.2.1.7 เดิม*/
	//if (!$POH_ASS_ORG) $POH_ASS_ORG = $ORG_ID3; /*เดิม*/
	//if (!$POH_ASS_ORG_NAME) $POH_ASS_ORG_NAME = $ORG_NAME_3; /*เดิม*/
       
	//if (!$POH_ASS_ORG1) $POH_ASS_ORG1 = $POH_UNDER_ORG1; /*เดิม*/
	//if (!$POH_ASS_ORG1_NAME) $POH_ASS_ORG1_NAME = $POH_UNDER_ORG1_NAME; /*เดิม*/
	
	//if (!$POH_ASS_ORG2) $POH_ASS_ORG2 = $POH_UNDER_ORG2; /*เดิม*/
	//if (!$POH_ASS_ORG2_NAME) $POH_ASS_ORG2_NAME = $POH_UNDER_ORG2_NAME; /*เดิม*/
	
	
	// ค่าจากการเลือกที่ form 
	if($_POST[POH_POS_NO])						$POH_POS_NO = trim($_POST[POH_POS_NO]);
	if($_POST[POH_POS_NO_NAME])		$POH_POS_NO_NAME = trim($_POST[POH_POS_NO_NAME]);
	
	$set_style_poh_docdate_edit_layer="display:none; visibility:hidden;";

	if ($command=="ADD" || $command=="UPDATE") {
		$POH_EFFECTIVEDATE = save_date($POH_EFFECTIVEDATE); 
		if($POH_ENDDATE) $POH_ENDDATE = save_date($POH_ENDDATE); 
		if($POH_DOCDATE) $POH_DOCDATE = save_date($POH_DOCDATE); 
		if($POH_DOCDATE_EDIT) $POH_DOCDATE_EDIT = save_date($POH_DOCDATE_EDIT); 
		$POH_ORGMGT = ($POH_ORGMGT == 1)?   $POH_ORGMGT : 2 ;
		$POH_SALARY = (str_replace(",", "", $POH_SALARY)) + 0;
		$POH_SALARY_POS = (str_replace(",", "", $POH_SALARY_POS)) + 0;
		$POH_REMARK = str_replace("'", "&rsquo;", $POH_REMARK);	
		$POH_REMARK1 = str_replace("'", "&rsquo;", $POH_REMARK1);	
		$POH_REMARK2 = str_replace("'", "&rsquo;", $POH_REMARK2);	
		
		$PM_CODE = (trim($PM_CODE))? "'$PM_CODE'" : "NULL";
		$POH_LEVEL_NO = (trim($POH_LEVEL_NO))? "'$POH_LEVEL_NO'" : "NULL";
		$LEVEL_NO = (trim($LEVEL_NO))? "'$LEVEL_NO'" : "NULL";
		$PT_CODE = (trim($PT_CODE))? "'$PT_CODE'" : "NULL";
		$CT_CODE_tmp = (trim($CT_CODE))? "'$CT_CODE'" : "NULL";
		$POH_PV_CODE = (trim($POH_PV_CODE))? "'$POH_PV_CODE'" : "NULL";
		$AP_CODE = (trim($AP_CODE))? "'$AP_CODE'" : "NULL";
		$PER_CARDNO = (trim($PER_CARDNO))? "'$PER_CARDNO'" : "NULL";
		$ORG_ID1 = (trim($ORG_ID1))? $ORG_ID1 : 1;
		$ORG_ID2 = (trim($ORG_ID2))? $ORG_ID2 : 1;
		$ORG_ID3 = (trim($ORG_ID3))? $ORG_ID3 : 1;		
		$POH_SEQ_NO = (trim($POH_SEQ_NO))? $POH_SEQ_NO : 1;		
		if (!$POH_CMD_SEQ) $POH_CMD_SEQ = "NULL";
		if (!$POH_ACTH_SEQ) $POH_ACTH_SEQ = "NULL";
		$ES_CODE = (trim($ES_CODE))? "'$ES_CODE'" : "NULL";
		
		$PN_CODE = $EP_CODE = $TP_CODE = "NULL";
		if ($PER_TYPE==1 || $PER_TYPE==5) {	
			$PL_CODE = (trim($PL_CODE))? "'$PL_CODE'" : "NULL";	
			$PN_CODE = $EP_CODE = $TP_CODE = "NULL";
		} elseif ($PER_TYPE==2) {
			$PN_CODE = (trim($PL_CODE))? "'$PL_CODE'" : "NULL";	
			$PL_CODE = $EP_CODE = $TP_CODE = "NULL";
		} elseif ($PER_TYPE==3) {
			$EP_CODE = (trim($PL_CODE))? "'$PL_CODE'" : "NULL";	
			$PL_CODE = $PN_CODE = $TP_CODE = "NULL";
		} elseif ($PER_TYPE==4) {
			$TP_CODE = (trim($PL_CODE))? "'$PL_CODE'" : "NULL";	
			$PL_CODE = $PN_CODE = $EP_CODE = "NULL";
		}
	}

	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_POSITIONHIS set AUDIT_FLAG = 'N' where POH_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_POSITIONHIS set AUDIT_FLAG = 'Y' where POH_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}

	if($command=="ADD" && $PER_ID){
		if ($POH_LAST_POSITION=="Y") {
			$cmd = " UPDATE PER_POSITIONHIS SET POH_LAST_POSITION='N' WHERE PER_ID=$PER_ID ";
			$db_dpis->send_cmd($cmd);

			if ($DEPARTMENT_NAME=="กรมการปกครอง" || $MFA_FLAG==1) {
				if ($POH_POS_NO) {
					$cmd = " select POS_ID from PER_POSITION where POS_NO_NAME IS NULL and POS_NO='$POH_POS_NO' ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					$POS_ID = $data2[POS_ID];

					$cmd = " UPDATE PER_PERSONAL SET 
									PER_DOCNO='$POH_DOCNO', 
									PER_DOCDATE='$POH_DOCDATE', 
									MOV_CODE='$MOV_CODE', 
									ES_CODE='$ES_CODE', 
									LEVEL_NO='$POH_LEVEL_NO', 
									POS_ID=$POS_ID 
									WHERE PER_ID=$PER_ID ";									
					$db_dpis->send_cmd($cmd);
				} else {
					$cmd = " UPDATE PER_PERSONAL SET 
									PER_DOCNO='$POH_DOCNO', 
									PER_DOCDATE='$POH_DOCDATE', 
									MOV_CODE='$MOV_CODE', 
									ES_CODE='$ES_CODE', 
									LEVEL_NO='$POH_LEVEL_NO' 
									WHERE PER_ID=$PER_ID ";
					$db_dpis->send_cmd($cmd);
				}
			}
		}

		$cmd = " select max(POH_ID) as max_id from PER_POSITIONHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$POH_ID = $data[max_id] + 1;	
		
		$cmd = " insert into PER_POSITIONHIS (POH_ID, PER_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_ENDDATE, 
						POH_DOCNO, POH_DOCDATE, POH_POS_NO, POH_ORGMGT, ORG_ID_1, ORG_ID_2, ORG_ID_3, 
						POH_UNDER_ORG1, POH_UNDER_ORG2, POH_UNDER_ORG3, POH_UNDER_ORG4, POH_UNDER_ORG5, 
						POH_ASS_ORG, POH_ASS_ORG1, POH_ASS_ORG2, POH_SALARY, POH_SALARY_POS, POH_REMARK, 
						POH_ORG1, POH_ORG2, POH_ORG3, PL_CODE, PN_CODE, EP_CODE, TP_CODE, PER_CARDNO, 
						PM_CODE, LEVEL_NO, PT_CODE, CT_CODE, PV_CODE, AP_CODE, POH_ORG_TRANSFER, POH_ORG, 
						POH_PM_NAME, POH_PL_NAME, POH_SEQ_NO, POH_LAST_POSITION, POH_CMD_SEQ, POH_ISREAL, 
						ES_CODE, POH_LEVEL_NO, POH_REMARK1, POH_REMARK2, POH_POS_NO_NAME, POH_SPECIALIST, 
						UPDATE_USER, UPDATE_DATE, POH_DOCNO_EDIT, POH_DOCDATE_EDIT, POH_REF_DOC, POH_ACTH_SEQ, POH_ASS_DEPARTMENT,POH_ASS_MINISTRY)
						values ($POH_ID, $PER_ID, '$POH_EFFECTIVEDATE', '$MOV_CODE', '$POH_ENDDATE', '$POH_DOCNO', 
						'$POH_DOCDATE', '$POH_POS_NO', $POH_ORGMGT, $ORG_ID1, $ORG_ID2, $ORG_ID3,
						'$POH_UNDER_ORG1_NAME', '$POH_UNDER_ORG2_NAME', '$POH_UNDER_ORG3_NAME', 
						'$POH_UNDER_ORG4_NAME', '$POH_UNDER_ORG5_NAME', '$POH_ASS_ORG_NAME', 
						'$POH_ASS_ORG1_NAME', '$POH_ASS_ORG2_NAME', '$POH_SALARY', '$POH_SALARY_POS', 
						'$POH_REMARK', '$ORG_NAME_1', '$ORG_NAME_2', '$ORG_NAME_3', $PL_CODE, $PN_CODE, 
						$EP_CODE, $TP_CODE, $PER_CARDNO, $PM_CODE, $LEVEL_NO, $PT_CODE, $CT_CODE_tmp, $POH_PV_CODE, 
						$AP_CODE, '$POH_ORG_TRANSFER', '$POH_ORG', '$PM_NAME', '$POH_PL_NAME', $POH_SEQ_NO,  
						'$POH_LAST_POSITION', $POH_CMD_SEQ, '$POH_ISREAL', $ES_CODE, $POH_LEVEL_NO, 
						'$POH_REMARK1', '$POH_REMARK2', '$POH_POS_NO_NAME', '$POH_SPECIALIST', $SESS_USERID, '$UPDATE_DATE', 
						'$POH_DOCNO_EDIT', '$POH_DOCDATE_EDIT', '$POH_REF_DOC', $POH_ACTH_SEQ, '$POH_ASS_DEPARTMENT_NAME', '$POH_ASS_MINISTRY_NAME')  ";
		$db_dpis->send_cmd($cmd);
		//echo "<pre> $cmd<br>";
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการดำรงตำแหน่ง [$PER_ID : $POH_ID : $PL_CODE]");
	} // end if
	

	if($command=="UPDATE" && $PER_ID && $POH_ID){		
		if ($POH_LAST_POSITION=="Y") {
			$cmd = " UPDATE PER_POSITIONHIS SET POH_LAST_POSITION='N' WHERE PER_ID=$PER_ID ";
			$db_dpis->send_cmd($cmd);

			if ($DEPARTMENT_NAME=="กรมการปกครอง" || $MFA_FLAG==1) {
				if ($POH_POS_NO) {
					$cmd = " select POS_ID from PER_POSITION where POS_NO_NAME IS NULL and POS_NO='$POH_POS_NO' ";
					$db_dpis2->send_cmd($cmd);
					//$db_dpis2->show_error();
					$data2 = $db_dpis2->get_array();
					$POS_ID = $data2[POS_ID];

					$cmd = " UPDATE PER_PERSONAL SET 
									PER_DOCNO='$POH_DOCNO', 
									PER_DOCDATE='$POH_DOCDATE', 
									MOV_CODE='$MOV_CODE', 
									ES_CODE=$ES_CODE, 
									LEVEL_NO=$POH_LEVEL_NO, 
									POS_ID=$POS_ID 
									WHERE PER_ID=$PER_ID ";
					$db_dpis->send_cmd($cmd);
				} else {
					$cmd = " UPDATE PER_PERSONAL SET 
									PER_DOCNO='$POH_DOCNO', 
									PER_DOCDATE='$POH_DOCDATE', 
									MOV_CODE='$MOV_CODE', 
									ES_CODE=$ES_CODE, 
									LEVEL_NO=$POH_LEVEL_NO 
									WHERE PER_ID=$PER_ID ";
					$db_dpis->send_cmd($cmd);
					
				}
			}
		}

		$cmd = " UPDATE PER_POSITIONHIS SET
						POH_EFFECTIVEDATE='$POH_EFFECTIVEDATE', 
						MOV_CODE='$MOV_CODE', 
						POH_ENDDATE='$POH_ENDDATE', 
						POH_DOCNO='$POH_DOCNO', 
						POH_DOCDATE='$POH_DOCDATE', 
						POH_DOCNO_EDIT='$POH_DOCNO_EDIT', 
						POH_DOCDATE_EDIT='$POH_DOCDATE_EDIT', 
						POH_POS_NO='$POH_POS_NO', 
						PM_CODE=$PM_CODE, 
						LEVEL_NO=$LEVEL_NO, 
						PL_CODE=$PL_CODE, 
						PN_CODE=$PN_CODE, 
						EP_CODE=$EP_CODE, 
						TP_CODE=$TP_CODE, 
						PT_CODE=$PT_CODE, 
						CT_CODE=$CT_CODE_tmp, 
						PV_CODE=$POH_PV_CODE, 
						AP_CODE=$AP_CODE, 
						POH_ORGMGT=$POH_ORGMGT, 
						ORG_ID_1=$ORG_ID1, 
						ORG_ID_2=$ORG_ID2, 
						ORG_ID_3=$ORG_ID3, 
						POH_UNDER_ORG1='$POH_UNDER_ORG1_NAME', 
						POH_UNDER_ORG2='$POH_UNDER_ORG2_NAME', 
						POH_UNDER_ORG3='$POH_UNDER_ORG3_NAME', 
						POH_UNDER_ORG4='$POH_UNDER_ORG4_NAME', 
						POH_UNDER_ORG5='$POH_UNDER_ORG5_NAME', 
						POH_ASS_ORG='$POH_ASS_ORG_NAME', 
						POH_ASS_ORG1='$POH_ASS_ORG1_NAME', 
						POH_ASS_ORG2='$POH_ASS_ORG2_NAME',
						POH_ASS_ORG3='$POH_ASS_ORG3_NAME',
						POH_ASS_ORG4='$POH_ASS_ORG4_NAME',
						POH_ASS_ORG5='$POH_ASS_ORG5_NAME',						
						POH_SALARY=$POH_SALARY, 
						POH_SALARY_POS=$POH_SALARY_POS, 
						POH_REMARK='$POH_REMARK', 
						PER_CARDNO=$PER_CARDNO, 
						POH_ORG1='$ORG_NAME_1', 
						POH_ORG2='$ORG_NAME_2', 
						POH_ORG3='$ORG_NAME_3', 
						POH_ORG_TRANSFER='$POH_ORG_TRANSFER', 
						POH_ORG='$POH_ORG', 
						POH_PM_NAME='$PM_NAME', 
						POH_PL_NAME='$POH_PL_NAME', 
						POH_SEQ_NO=$POH_SEQ_NO, 
						POH_LAST_POSITION='$POH_LAST_POSITION', 
						POH_CMD_SEQ=$POH_CMD_SEQ, 
						POH_ISREAL='$POH_ISREAL', 
						ES_CODE=$ES_CODE, 
						POH_LEVEL_NO=$POH_LEVEL_NO, 
						POH_REMARK1='$POH_REMARK1', 
						POH_REMARK2='$POH_REMARK2', 
						POH_POS_NO_NAME='$POH_POS_NO_NAME',
						POH_SPECIALIST='$POH_SPECIALIST',
						POH_REF_DOC='$POH_REF_DOC',
						POH_ACTH_SEQ=$POH_ACTH_SEQ, 
						POH_ASS_DEPARTMENT='$POH_ASS_DEPARTMENT_NAME', 
						POH_ASS_MINISTRY='$POH_ASS_MINISTRY_NAME',
						UPDATE_USER=$SESS_USERID, 
						UPDATE_DATE='$UPDATE_DATE'
				WHERE POH_ID=$POH_ID ";
		$db_dpis->send_cmd($cmd);
		//echo "$PER_TYPE // $cmd<br>";		
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการดำรงตำแหน่ง [$PER_ID : $POH_ID : $PL_CODE]");
	} // end if
	
	if($command=="DELETE" && $PER_ID && ($POH_ID || $POH_ID=='0')){
		$cmd = " select PL_CODE from PER_POSITIONHIS where POH_ID=$POH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_CODE = $data[PL_CODE];
		
		$cmd = " delete from PER_POSITIONHIS where POH_ID=$POH_ID ";
                $db_dpis->send_cmd($cmd);
                
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติการดำรงตำแหน่ง [$PER_ID : $POH_ID : $PL_CODE]");
	} // end if

	// ??????????????????เรคคอร์ดปัจจุบัน
	if($PER_ID){		// ถ้าเอาไว้บรรทัดบนก่อนการ ADD / UPDATE มันจะดึงค่าตรงนี้ที่หา record ปัจจุบันมาไปอัพเดทให้กับตัวที่เลือกมาเพิ่ม / แก้ไข ******14/09/2012
		$ORG_ID1 = (trim($ORG_ID1))? $ORG_ID1 : $MINISTRY_ID;
		$ORG_NAME_1 = (trim($ORG_NAME_1))? $ORG_NAME_1 : $MINISTRY_NAME;
		$ORG_ID2 = (trim($ORG_ID2))? $ORG_ID2 : $DEPARTMENT_ID;
		$ORG_NAME_2 = (trim($ORG_NAME_2))? $ORG_NAME_2 :  $DEPARTMENT_NAME;
		$CT_CODE = (trim($CT_CODE))? $CT_CODE : "140";
		$CT_NAME = (trim($CT_NAME))? $CT_NAME : "ไทย";		

		if($DPISDB=="odbc"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO,PER.DEPARTMENT_ID, 
									POS_ID, POEM_ID, POEMS_ID, POT_ID, LEVEL_NO
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
							PER_PERSONAL.PER_TYPE, PER_PERSONAL.PER_CARDNO,PER_PERSONAL.DEPARTMENT_ID, POS_ID, POEM_ID, POEMS_ID, POT_ID, LEVEL_NO
 					from		PER_PERSONAL, PER_PRENAME
					where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and 
							PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO,PER.DEPARTMENT_ID, 
									POS_ID, POEM_ID, POEMS_ID, POT_ID, LEVEL_NO
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		} // end if
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		//echo " ~~ $cmd ";
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_TYPE = trim($data[PER_TYPE]);
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
		$POS_ID = trim($data[POS_ID]);
		$POEM_ID = trim($data[POEM_ID]);
		$POEMS_ID = trim($data[POEMS_ID]);
		$POT_ID = trim($data[POT_ID]);

		$POH_LEVEL_NO = trim($data[LEVEL_NO]);		
	  	$cmd = " select POSITION_LEVEL, LEVEL_NAME from PER_LEVEL where LEVEL_NO = '$POH_LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();					
		$LEVEL_NAME = trim($data[LEVEL_NAME]);		
		$POSITION_LEVEL = $data[POSITION_LEVEL];

		if ($PER_TYPE == 1) {
			$cmd = "	select	POS_NO_NAME, POS_NO, a.PL_CODE, PL_NAME, a.PM_CODE, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, 
							ORG_ID_4, ORG_ID_5, LEVEL_NO 
							from		PER_POSITION a, PER_LINE b 
							where	POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE ";
							
		} elseif ($PER_TYPE == 2) {
			$cmd = "	select	POEM_NO_NAME, POEM_NO, a.PN_CODE, PN_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, 
							ORG_ID_4, ORG_ID_5, LEVEL_NO
							from		PER_POS_EMP a, PER_POS_NAME b 
							where	POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE";
		} elseif ($PER_TYPE == 3) {
			$cmd = "	select	POEMS_NO_NAME, POEMS_NO, a.EP_CODE, EP_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, 
							ORG_ID_4, ORG_ID_5, LEVEL_NO
							from		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b 
							where	POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE";
		}
		elseif ($PER_TYPE == 4) {
			$cmd = "	select	POT_NO_NAME, POT_NO, a.TP_CODE, TP_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, 
							ORG_ID_4, ORG_ID_5, LEVEL_NO
							from		PER_POS_TEMP a, PER_TEMP_POS_NAME b 
							where	POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE";
		}
		$db_dpis->send_cmd($cmd);		
		$data = $db_dpis->get_array();
                
		if($PER_TYPE == 1) { 
			$PL_CODE = trim($data[PL_CODE]); 
			$PL_NAME = trim($data[PL_NAME]); 
			$POH_POS_NO = trim($data[POS_NO]);
			$POH_POS_NO_NAME = trim($data[POS_NO_NAME]);
			$SAH_PAY_NO = trim($data[POS_NO]);
		} else if($PER_TYPE == 2) { 
			$PL_CODE = trim($data[PN_CODE]); 
			$PL_NAME = trim($data[PN_NAME]); 
			$POH_POS_NO = trim($data[POEM_NO]);
			$POH_POS_NO_NAME = trim($data[POEM_NO_NAME]);
			$SAH_PAY_NO = trim($data[POEM_NO]);
		} else if($PER_TYPE == 3) { 
			$PL_CODE = trim($data[EP_CODE]); 
			$PL_NAME = trim($data[EP_NAME]); 
			$POH_POS_NO = trim($data[POEMS_NO]);
			$POH_POS_NO_NAME = trim($data[POEMS_NO_NAME]);
			$SAH_PAY_NO = trim($data[POEMS_NO]);
		} else if($PER_TYPE == 4) { 
			$PL_CODE = trim($data[TP_CODE]); 
			$PL_NAME = trim($data[TP_NAME]); 
			$POH_POS_NO = trim($data[POT_NO]);
			$POH_POS_NO_NAME = trim($data[POT_NO_NAME]);
			$SAH_PAY_NO = trim($data[POT_NO]);
		} 
                
                //echo "1: PL_NAME:".$PL_NAME."<br>PL_CODE:".$PL_CODE;
                
		$POS_ORG_ID = $data[ORG_ID];
		$POS_ORG_ID_1 = $data[ORG_ID_1];
		$POS_ORG_ID_2 = $data[ORG_ID_2];
		$POS_ORG_ID_3 = $data[ORG_ID_3];
		$POS_ORG_ID_4 = $data[ORG_ID_4];
		$POS_ORG_ID_5 = $data[ORG_ID_5];	
		$PM_CODE = trim($data[PM_CODE]);
		$LEVEL_NO = trim($data[LEVEL_NO]);		
		
		if ($PER_ID_DEPARTMENT_ID) $tmp_ORG_ID[] =  $PER_ID_DEPARTMENT_ID;
		if ($POS_ORG_ID)			$tmp_ORG_ID[] =  $POS_ORG_ID;
		if ($POS_ORG_ID_1)	$tmp_ORG_ID[] =  $POS_ORG_ID_1;
		if ($POS_ORG_ID_2)	$tmp_ORG_ID[] =  $POS_ORG_ID_2;
		if ($POS_ORG_ID_3)	$tmp_ORG_ID[] =  $POS_ORG_ID_3;
		if ($POS_ORG_ID_4)	$tmp_ORG_ID[] =  $POS_ORG_ID_4;
		if ($POS_ORG_ID_5)	$tmp_ORG_ID[] =  $POS_ORG_ID_5;
		$search_org_id = implode(", ", $tmp_ORG_ID);
		
		$cmd = " select ORG_ID, ORG_NAME from PER_ORG 	where ORG_ID in ($search_org_id) ";
		$db_dpis->send_cmd($cmd);		
		while ( $data = $db_dpis->get_array() ) {
			$DEPARTMENT_NAME = ($PER_ID_DEPARTMENT_ID == trim($data[ORG_ID]))? 		trim($data[ORG_NAME]) : "$DEPARTMENT_NAME";
			$POS_ORG_NAME = ($POS_ORG_ID == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $POS_ORG_NAME ;
			$POS_ORG_NAME_1 = ($POS_ORG_ID_1 == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $POS_ORG_NAME_1 ;
			$POS_ORG_NAME_2 = ($POS_ORG_ID_2 == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $POS_ORG_NAME_2 ;
			$POS_ORG_NAME_3 = ($POS_ORG_ID_3 == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $POS_ORG_NAME_3 ;
			$POS_ORG_NAME_4 = ($POS_ORG_ID_4 == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $POS_ORG_NAME_4 ;
			$POS_ORG_NAME_5 = ($POS_ORG_ID_5 == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $POS_ORG_NAME_5 ;	
		}
		$ORG_ID3 = $POS_ORG_ID;
		$ORG_NAME_3 = $POS_ORG_NAME;
		$POH_UNDER_ORG1 = $POS_ORG_ID_1;
		$POH_UNDER_ORG1_NAME = $POS_ORG_NAME_1;
		$POH_UNDER_ORG2 = $POS_ORG_ID_2;
		$POH_UNDER_ORG2_NAME = $POS_ORG_NAME_2;
		$POH_UNDER_ORG3 = $POS_ORG_ID_3;
		$POH_UNDER_ORG3_NAME = $POS_ORG_NAME_3;
		$POH_UNDER_ORG4 = $POS_ORG_ID_4;
		$POH_UNDER_ORG4_NAME = $POS_ORG_NAME_4;
		$POH_UNDER_ORG5 = $POS_ORG_ID_5;
		$POH_UNDER_ORG5_NAME = $POS_ORG_NAME_5;
	
		$cmd = " select PM_NAME from PER_MGT where PM_CODE='$PM_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PM_NAME = $data[PM_NAME];
		
		$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$PER_ID_DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_ID = $data[ORG_ID_REF];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];

		if ($PM_NAME) $POH_PL_NAME = $PM_NAME." (".$PL_NAME.$POSITION_LEVEL.")";
		else $POH_PL_NAME = $PL_NAME.$POSITION_LEVEL;
		
		if ($POS_ORG_NAME=="-") $POS_ORG_NAME = "";		
		if ($POS_ORG_NAME_1=="-") $POS_ORG_NAME_1 = "";		
		if ($POS_ORG_NAME_2=="-") $POS_ORG_NAME_2 = "";		
		if ($OT_CODE == "03") 
			if (!$POS_ORG_NAME_2 && !$POS_ORG_NAME_1 && $DEPARTMENT_NAME=="กรมการปกครอง") 
				$POH_ORG = "ที่ทำการปกครอง".$POS_ORG_NAME." ".$POS_ORG_NAME;
			else 
				$POH_ORG = trim($POS_ORG_NAME_2." ".$POS_ORG_NAME_1." ".$POS_ORG_NAME);
		elseif ($OT_CODE == "01") $POH_ORG = trim($POS_ORG_NAME_2." ".$POS_ORG_NAME_1." ".$POS_ORG_NAME." ".$DEPARTMENT_NAME);
		else $POH_ORG = trim($POS_ORG_NAME_2." ".$POS_ORG_NAME_1." ".$POS_ORG_NAME);
		//$POH_ASS_DEPARTMENT_NAME = $DEPARTMENT_NAME;
		//$POH_ASS_DEPARTMENT = $PER_ID_DEPARTMENT_ID;
	} // end if PER_ID

	//echo "<br>===> ($UPD && $PER_ID && $POH_ID) || ($VIEW && $PER_ID && $POH_ID)";
	if(($UPD && $PER_ID && $POH_ID) || ($VIEW && $PER_ID && $POH_ID)){
            
		$cmd = " SELECT POH_ID, POH_EFFECTIVEDATE, MOV_CODE, POH_ENDDATE, POH_DOCNO, POH_DOCDATE, POH_POS_NO, 
                            POH_POS_NO_NAME, PM_CODE, LEVEL_NO, PL_CODE, PN_CODE, EP_CODE, TP_CODE, PT_CODE, CT_CODE, 
                            PV_CODE, AP_CODE, POH_ORGMGT, ORG_ID_1, ORG_ID_2, ORG_ID_3, POH_UNDER_ORG1, POH_UNDER_ORG2, 
                            POH_UNDER_ORG3, POH_UNDER_ORG4, POH_UNDER_ORG5, POH_ASS_ORG, POH_ASS_ORG1, 
                            POH_ASS_ORG2, POH_SALARY, POH_SALARY_POS, POH_REMARK, POH_ORG1, POH_ORG2, POH_ORG3, 
                            POH_ORG_TRANSFER, POH_ORG, POH_PM_NAME, POH_PL_NAME, POH_SEQ_NO, POH_LAST_POSITION, 
                            POH_CMD_SEQ, POH_ISREAL, ES_CODE, POH_LEVEL_NO, POH_REMARK1, POH_REMARK2, POH_SPECIALIST, 
                            UPDATE_USER, UPDATE_DATE, POH_DOCNO_EDIT, POH_DOCDATE_EDIT, POH_REF_DOC, POH_ACTH_SEQ, POH_ASS_DEPARTMENT,POH_ASS_MINISTRY
                        FROM PER_POSITIONHIS 
                        WHERE POH_ID=$POH_ID ";
		$db_dpis->send_cmd($cmd);
                //echo "<pre>".$cmd;
		$data = $db_dpis->get_array();
		//$db_dpis->show_error();
	
		$POH_EFFECTIVEDATE = show_date_format(trim($data[POH_EFFECTIVEDATE]), 1);
		$POH_ENDDATE = show_date_format(trim($data[POH_ENDDATE]), 1);
		$POH_DOCDATE = show_date_format(trim($data[POH_DOCDATE]), 1);
		$POH_DOCNO = $data[POH_DOCNO];

		$cmd ="select COM_ID from PER_COMMAND where COM_NO = '$POH_DOCNO' ";
		$db->send_cmd($cmd);
//		echo "cmd=$cmd<br>";
		$data2 = $db->get_array();
		$COM_ID = trim($data2[COM_ID]);

/*******************************************************************************/
		$PATH_MAIN = "../attachments";	

		$file_namedb = "PER_COMMAND_".$COM_ID;
		$FIRST_SUBDIR = $PATH_MAIN;
		$SECOND_SUBDIR = $FIRST_SUBDIR.'/'."PER_COMMAND";
		$FINAL_PATH = $SECOND_SUBDIR.'/'.$COM_ID;

		$FILE_PATH = $FINAL_PATH;
		$real_filename = "";
		$cmd ="select * from EDITOR_ATTACHMENT where REAL_FILENAME like '$file_namedb%' ";
		$count_atta = $db->send_cmd($cmd);
		if ($count_atta) {
			$data2 = $db->get_array();
			$ATTAFILE_ID = trim($data2[ID]);
			$ATTAFILE_REAL_NAME = trim($data2[REAL_FILENAME]);
			$real_filename = $ATTAFILE_REAL_NAME;
			$ATTAFILE_SHOW_NAME = trim($data2[SHOW_FILENAME]);
			$ATTAFILE_DESCRIPTION = trim($data2[DESCRIPTION]);
			$ATTAFILE_FILE_SIZE = trim($data2[FILE_SIZE]);
			$ATTAFILE_USER_ID = trim($data2[USER_ID]);
		}
//		echo "FILE_PATH=$FILE_PATH , real_filename=$real_filename<br>";
/*******************************************************************************/
		$doc_numfiles=0;
		if ($dh = opendir($FILE_PATH)) {
			while (($file = readdir($dh)) !== false) {	//---อ่านไฟล์ทั้งหมดมาจาก folder ($FILE_PATH) นั้น
				if ($file != "." && $file != "..") {
					$doc_numfiles++;
				} // end if
	   		} // while loop readdir
			closedir($dh);
		} // end if
//		echo "tmpnumfiles=$tmpnumfiles<br>";

		$POH_DOCDATE_EDIT = show_date_format(trim($data[POH_DOCDATE_EDIT]), 1);
		$POH_DOCNO_EDIT = $data[POH_DOCNO_EDIT];
		$POH_POS_NO_NAME = $data[POH_POS_NO_NAME];
		$POH_POS_NO = $data[POH_POS_NO];
		$LEVEL_NO = $data[LEVEL_NO];
		$POH_LEVEL_NO = $data[POH_LEVEL_NO];
		$POH_SALARY = number_format($data[POH_SALARY], 2, '.', ',');
		$POH_SALARY_POS = number_format($data[POH_SALARY_POS], 2, '.', ',');
		$POH_REMARK = trim($data[POH_REMARK]);
		$CHK_POH_ORGMGT = ($data[POH_ORGMGT] == 1)?  "checked" : "" ;
		$POH_UNDER_ORG1_NAME = $data[POH_UNDER_ORG1];
		$POH_UNDER_ORG2_NAME = $data[POH_UNDER_ORG2];
		$POH_UNDER_ORG3_NAME = $data[POH_UNDER_ORG3];
		$POH_UNDER_ORG4_NAME = $data[POH_UNDER_ORG4];
		$POH_UNDER_ORG5_NAME = $data[POH_UNDER_ORG5];
		
		$POH_ASS_ORG_NAME = $data[POH_ASS_ORG];
		$cmd = " select ORG_ID from PER_ORG_ASS where ORG_NAME='". trim($data[POH_ASS_ORG]) ."' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POH_ASS_ORG = $data2[ORG_ID];	
			
		$POH_ASS_ORG1_NAME = $data[POH_ASS_ORG1];
		$cmd = " select ORG_ID from PER_ORG_ASS where ORG_NAME='". trim($data[POH_ASS_ORG1]) ."' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POH_ASS_ORG1 = $data2[ORG_ID];
		
		$POH_ASS_ORG2_NAME = $data[POH_ASS_ORG2];
		$cmd = " select ORG_ID from PER_ORG_ASS where ORG_NAME='". trim($data[POH_ASS_ORG2]) ."' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POH_ASS_ORG2 = $data2[ORG_ID];
			
		$POH_ORG_TRANSFER = trim($data[POH_ORG_TRANSFER]);
		$POH_ORG = trim($data[POH_ORG]);
		$POH_PM_NAME = trim($data[POH_PM_NAME]);
		$POH_PL_NAME = trim($data[POH_PL_NAME]);
		$POH_SEQ_NO = $data[POH_SEQ_NO];
		$POH_LAST_POSITION = trim($data[POH_LAST_POSITION]);
		$POH_CMD_SEQ = $data[POH_CMD_SEQ];
		$POH_ISREAL = trim($data[POH_ISREAL]);
		$POH_REMARK1 = trim($data[POH_REMARK1]);
		$POH_REMARK2 = trim($data[POH_REMARK2]);
		$POH_SPECIALIST = trim($data[POH_SPECIALIST]);
		$POH_REF_DOC = trim($data[POH_REF_DOC]);
		$POH_ACTH_SEQ = $data[POH_ACTH_SEQ];
		
		$POH_ASS_MINISTRY_NAME = $data[POH_ASS_MINISTRY];	
		$cmd = " select ORG_ID from PER_ORG_ASS where ORG_NAME='". trim($data[POH_ASS_MINISTRY]) ."' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POH_ASS_MINISTRY = $data2[ORG_ID];
		
		$POH_ASS_DEPARTMENT_NAME = $data[POH_ASS_DEPARTMENT];
		$cmd = " select ORG_ID from PER_ORG_ASS where ORG_NAME='". trim($data[POH_ASS_DEPARTMENT]) ."' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POH_ASS_DEPARTMENT = $data2[ORG_ID];
		if (!$POH_ASS_DEPARTMENT_NAME) $POH_ASS_DEPARTMENT_NAME = $SESS_DEPARTMENT_NAME;
		if (!$POH_ASS_MINISTRY_NAME) $POH_ASS_MINISTRY_NAME = $SESS_MINISTRY_NAME;
		
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		$PL_NAME = "";
                $PL_CODE = "";
		// === ชื่อตำแหน่ง
		if (trim($data[PL_CODE])) {
			$cmd = " select PL_NAME from PER_LINE where PL_CODE='". trim($data[PL_CODE]) ."' ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PL_NAME];
			$PL_CODE = $data[PL_CODE];
                       //echo "<br>(1)<br>";
		}		
		if (trim($data[PN_CODE])) {
			$cmd = " select PN_NAME from PER_POS_NAME where PN_CODE='". trim($data[PN_CODE]) ."' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[PN_NAME];
			$PL_CODE = $data[PN_CODE];
                        //echo "<br>(2)<br>";
		}		
		if (trim($data[EP_CODE])) {
			$cmd = " select EP_NAME from PER_EMPSER_POS_NAME where EP_CODE='". trim($data[EP_CODE]) ."' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[EP_NAME];
			$PL_CODE = $data[EP_CODE];
                        //echo "<br>(3)<br>";
		}		
		if (trim($data[TP_CODE])) {
			$cmd = " select TP_NAME from PER_TEMP_POS_NAME where TP_CODE='". trim($data[TP_CODE]) ."' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PL_NAME = $data2[TP_NAME];
			$PL_CODE = $data[TP_CODE];
                        //echo "<br>(4)<br>";
		}		
		if ($POH_PL_NAME && !$PL_NAME) $PL_NAME = $POH_PL_NAME;		//กำหนดให้ PL_NAME = POH_PL_NAME

                //echo "<br>2: PL_NAME:".$PL_NAME."<br>PL_CODE:".$PL_CODE;
		$ORG_ID3 = $data[ORG_ID_3];
		if ($ORG_ID3) {
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID3' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_3 = $data2[ORG_NAME];
		}
		$ORG_NAME_3 = trim($data[POH_ORG3]);

		$ORG_ID2 = $data[ORG_ID_2];
		if ($ORG_ID2) {
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID2' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_2 = $data2[ORG_NAME];
		}
		$ORG_NAME_2 = trim($data[POH_ORG2]);

		$ORG_ID1 = $data[ORG_ID_1];
		if ($ORG_ID1) {
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID='$ORG_ID1' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_1 = $data2[ORG_NAME];
		}
		$ORG_NAME_1 = trim($data[POH_ORG1]);

		$CT_CODE = $data[CT_CODE];
		$cmd = " select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$CT_NAME = $data2[CT_NAME];
		
		$POH_PV_CODE = trim($data[PV_CODE]);
		$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$POH_PV_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$POH_PV_NAME = $data2[PV_NAME];
		
		$AP_CODE = $data[AP_CODE];
		$cmd = " select AP_NAME from PER_AMPHUR where AP_CODE='$AP_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$AP_NAME = $data2[AP_NAME];
        
		$PT_CODE = $data[PT_CODE];
		$cmd = " select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PT_NAME= $data2[PT_NAME];
        
        /*http://dpis.ocsc.go.th/Service/node/2275  Release 5.2.1.26 
         ตำแหน่งปี 2535 จะเเสดง ว ช่วงระดับ6-9 เท่านั้น */
         
        $UPD_POSITION_LEVEL = level_no_format($POH_LEVEL_NO, 2);
        if ($PT_NAME && $PT_NAME!="ทั่วไป" && $UPD_POSITION_LEVEL >= "6" && $UPD_POSITION_LEVEL <="9"){
            $PT_NAME = $PT_NAME;
        } else{
            $PT_NAME="";
        }

		$PM_CODE = $data[PM_CODE];
		$cmd = " select PM_NAME from PER_MGT where PM_CODE='$PM_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PM_NAME = $data2[PM_NAME];
		if ($POH_PM_NAME) $PM_NAME = $POH_PM_NAME;
		
		$MOV_CODE = $data[MOV_CODE];
		$cmd = " select MOV_NAME from PER_MOVMENT pcd where MOV_CODE='$MOV_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$MOV_NAME = $data2[MOV_NAME];

		$POH_DOCEDIT_TITLE = "";
		$RESULT_FIND_POH_DOCEDIT= strpos($MOV_NAME,"แก้ไข"); 	 // MOV NAME แก้ไขคำสั่ง 
		if ($RESULT_FIND_POH_DOCEDIT === FALSE){
			$RESULT_FIND_POH_DOCEDIT=0;
		}else{
			$RESULT_FIND_POH_DOCEDIT=1;
			$POH_DOCEDIT_TITLE = "แก้ไข";
		}
		
		$RESULT_FIND_POH_DOCCC = strpos($MOV_NAME,"ยกเลิกคำสั่ง"); 	 // MOV NAME ยกเลิกคำสั่ง 
		if($RESULT_FIND_POH_DOCCC === FALSE){
			$RESULT_FIND_POH_DOCCC=0;	
		}else{
			$RESULT_FIND_POH_DOCCC=1;	
			$POH_DOCEDIT_TITLE = "ยกเลิกคำสั่ง";
		}
		
		if($RESULT_FIND_POH_DOCEDIT==1 || $RESULT_FIND_POH_DOCCC==1){
			$set_style_poh_docdate_edit_layer="display:table-row; visibility:visible;";	//พบ
		}else{
			$set_style_poh_docdate_edit_layer="display:none; visibility:hidden;";	//ไม่พบ		
		}

		$ES_CODE = $data[ES_CODE];
		$cmd = " select ES_NAME from PER_EMP_STATUS where ES_CODE='$ES_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$ES_NAME = $data2[ES_NAME];
	} // end if
	
	if (!$PER_ID) {
		unset($ORG_ID1);
		unset($ORG_NAME_1);
		unset($ORG_ID2);
		unset($ORG_NAME_2);
		unset($CT_CODE);
		unset($CT_NAME);	
	
		if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){ 
			$MINISTRY_ID = "";
			$MINISTRY_NAME = "";
		} // end if
		if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){ 
			$DEPARTMENT_ID = "";
			$DEPARTMENT_NAME = "";
		} // end if			
	}
	
	if( !$UPD && !$VIEW ){
		unset($POH_ID);
		unset($POH_DOCNO);
		unset($POH_DOCDATE);
		unset($POH_DOCNO_EDIT);
		unset($POH_DOCDATE_EDIT);
		unset($POH_TYPE);
		unset($POH_EFFECTIVEDATE);
		unset($POH_ENDDATE);
		unset($POH_ORGMGT);
		unset($POH_REMARK);
		unset($POH_REMARK1);
		unset($POH_REMARK2);
		unset($POH_SPECIALIST);
		unset($POH_REF_DOC);
//		unset($LEVEL_NO);
//		unset($POH_LEVEL_NO);
		unset($POH_SALARY);
		$POH_SALARY_POS = 0;
//		unset($POH_POS_NO);
//		unset($POH_POS_NO_NAME);
		
		unset($MOV_CODE);
		unset($MOV_NAME);
//		unset($PL_CODE);
//		unset($PL_NAME);
		unset($POH_PV_CODE);
		unset($POH_PV_NAME);
		unset($AP_CODE);
		unset($AP_NAME);
		unset($PT_CODE);		
		unset($PT_NAME);
//		unset($PM_CODE);
//		unset($PM_NAME);
		$ES_CODE = "02";
		$ES_NAME = "ตรงตามตำแหน่ง";
		
		$ORG_ID1 = $MINISTRY_ID;
		$ORG_NAME_1 = $MINISTRY_NAME;
		if($DEPARTMENT_ID){	$ORG_ID2 = $DEPARTMENT_ID;	}
		$ORG_NAME_2 = $DEPARTMENT_NAME;		
//		unset($ORG_ID3);
//		unset($ORG_NAME_3);
		
//		unset($POH_UNDER_ORG1);
//		unset($POH_UNDER_ORG1_NAME);
//		unset($POH_UNDER_ORG2);
//		unset($POH_UNDER_ORG2_NAME);
//		unset($POH_UNDER_ORG3);
//		unset($POH_UNDER_ORG3_NAME);
//		unset($POH_UNDER_ORG4);
//		unset($POH_UNDER_ORG4_NAME);
//		unset($POH_UNDER_ORG5);
//		unset($POH_UNDER_ORG5_NAME);
		unset($POH_ASS_MINISTRY);
		unset($POH_ASS_MINISTRY_NAME);
		unset($POH_ASS_DEPARTMENT);
		unset($POH_ASS_DEPARTMENT_NAME);
		
		unset($POH_ASS_ORG);
		unset($POH_ASS_ORG_NAME);
		unset($POH_ASS_ORG1);
		unset($POH_ASS_ORG1_NAME);
		unset($POH_ASS_ORG2);
		unset($POH_ASS_ORG2_NAME);			
		unset($POH_ORG_TRANSFER);
//		unset($POH_ORG);
//		unset($POH_PM_NAME);
//		unset($POH_PL_NAME);
		unset($POH_SEQ_NO);
		$POH_LAST_POSITION = "N";
		unset($POH_CMD_SEQ);
		unset($POH_ACTH_SEQ);
//		unset($POH_ASS_DEPARTMENT);
//		unset($POH_ASS_DEPARTMENT_NAME);
//		if($DEPARTMENT_ID){	$POH_ASS_DEPARTMENT = $DEPARTMENT_ID;	}
//		$POH_ASS_DEPARTMENT_NAME = $DEPARTMENT_NAME;		
		$POH_ISREAL = "Y";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>