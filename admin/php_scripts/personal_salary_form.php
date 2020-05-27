<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$set_style_sah_docdate_edit_layer="display:none; visibility:hidden;";
	
	if($PER_ID){
		if($DPISDB=="odbc"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO,PER.DEPARTMENT_ID, 
									POS_ID, POEM_ID, POEMS_ID, POT_ID, LEVEL_NO
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
							PER_PERSONAL.PER_TYPE, PER_PERSONAL.PER_CARDNO ,PER_PERSONAL.DEPARTMENT_ID, POS_ID, POEM_ID, POEMS_ID, POT_ID, LEVEL_NO
					from		PER_PERSONAL, PER_PRENAME
					where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO,PER.DEPARTMENT_ID, 
									POS_ID, POEM_ID, POEMS_ID, POT_ID, LEVEL_NO
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		} // end if
		
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_TYPE = trim($data[PER_TYPE]);
		$PER_CARDNO = (trim($data[PER_CARDNO]))? "'".trim($data[PER_CARDNO])."'" : "NULL";
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
		$POS_ID = trim($data[POS_ID]);
		$POEM_ID = trim($data[POEM_ID]);
		$POEMS_ID = trim($data[POEMS_ID]);
		$POT_ID = trim($data[POT_ID]);

/***
		$LEVEL_NO = trim($data[LEVEL_NO]);		
	  	$cmd = " select POSITION_LEVEL, LEVEL_NAME from PER_LEVEL where LEVEL_NO = '$LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();					
		$LEVEL_NAME = trim($data[LEVEL_NAME]);		
***/		
		$POSITION_LEVEL = $data[POSITION_LEVEL];

		if ($PER_TYPE == 1) {
			$cmd = "	select	POS_NO_NAME, POS_NO, a.PL_CODE, PL_NAME, a.PM_CODE, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, 
							ORG_ID_4, ORG_ID_5 
							from		PER_POSITION a, PER_LINE b 
							where	POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE ";
							
		} elseif ($PER_TYPE == 2) {
			$cmd = "	select	POEM_NO_NAME, POEM_NO, a.PN_CODE, PN_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, 
							ORG_ID_4, ORG_ID_5
							from		PER_POS_EMP a, PER_POS_NAME b 
							where	POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE";
		} elseif ($PER_TYPE == 3) {
			$cmd = "	select	POEMS_NO_NAME, POEMS_NO, a.EP_CODE, EP_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, 
							ORG_ID_4, ORG_ID_5
							from		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b 
							where	POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE";
		}
		elseif ($PER_TYPE == 4) {
			$cmd = "	select	POT_NO_NAME, POT_NO, a.TP_CODE, TP_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, 
							ORG_ID_4, ORG_ID_5
							from		PER_POS_TEMP a, PER_TEMP_POS_NAME b 
							where	POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE";
		}
		$db_dpis->send_cmd($cmd);		
		$data = $db_dpis->get_array();
		if($PER_TYPE == 1) { 
			$PL_NAME = trim($data[PL_NAME]); 
			/*$SAH_POS_NO = trim($data[POS_NO]);
			$SAH_POS_NO_NAME = trim($data[POS_NO_NAME]);
			$SAH_PAY_NO = trim($data[POS_NO]);*/
		} else if($PER_TYPE == 2) { 
			$PL_NAME = trim($data[PN_NAME]); 
			/*$SAH_POS_NO = trim($data[POEM_NO]);
			$SAH_POS_NO_NAME = trim($data[POEM_NO_NAME]);
			$SAH_PAY_NO = trim($data[POEM_NO]);*/
		} else if($PER_TYPE == 3) { 
			$PL_NAME = trim($data[EP_NAME]); 
			/*$SAH_POS_NO = trim($data[POEMS_NO]);
			$SAH_POS_NO_NAME = trim($data[POEMS_NO_NAME]);
			$SAH_PAY_NO = trim($data[POEMS_NO]);*/
		} else if($PER_TYPE == 4) { 
			$PL_NAME = trim($data[TP_NAME]); 
			/*$SAH_POS_NO = trim($data[POT_NO]);
			$SAH_POS_NO_NAME = trim($data[POT_NO_NAME]);
			$SAH_PAY_NO = trim($data[POT_NO]);*/
		} 
		$POS_ORG_ID = $data[ORG_ID];
		$POS_ORG_ID_1 = $data[ORG_ID_1];
		$POS_ORG_ID_2 = $data[ORG_ID_2];
		$POS_ORG_ID_3 = $data[ORG_ID_3];
		$POS_ORG_ID_4 = $data[ORG_ID_4];
		$POS_ORG_ID_5 = $data[ORG_ID_5];	
		$PM_CODE = trim($data[PM_CODE]);
		
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
			$ORG_NAME = ($POS_ORG_ID == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $ORG_NAME ;
			$ORG_NAME_1 = ($POS_ORG_ID_1 == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $ORG_NAME_1 ;
			$ORG_NAME_2 = ($POS_ORG_ID_2 == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $ORG_NAME_2 ;
			$ORG_NAME_3 = ($POS_ORG_ID_3 == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $ORG_NAME_3 ;
			$ORG_NAME_4 = ($POS_ORG_ID_4 == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $ORG_NAME_4 ;
			$ORG_NAME_5 = ($POS_ORG_ID_5 == trim($data[ORG_ID]))?  trim($data[ORG_NAME]) : $ORG_NAME_5 ;	
		}
	
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

/****
		if ($PM_NAME) $SAH_POSITION = $PM_NAME." (".$PL_NAME.$POSITION_LEVEL.")";
		else $SAH_POSITION = $PL_NAME.$POSITION_LEVEL;
		
		if ($ORG_NAME=="-") $ORG_NAME = "";		
		if ($ORG_NAME_1=="-") $ORG_NAME_1 = "";		
		if ($ORG_NAME_2=="-") $ORG_NAME_2 = "";		
		if ($OT_CODE == "03") 
			if (!$ORG_NAME_2 && !$ORG_NAME_1 && $DEPARTMENT_NAME=="กรมการปกครอง") 
				$SAH_ORG = "ที่ทำการปกครอง".$ORG_NAME." ".$ORG_NAME;
			else 
				$SAH_ORG = trim($ORG_NAME_2." ".$ORG_NAME_1." ".$ORG_NAME);
		elseif ($OT_CODE == "01") $SAH_ORG = trim($ORG_NAME_2." ".$ORG_NAME_1." ".$ORG_NAME." ".$DEPARTMENT_NAME);
		else $SAH_ORG = trim($ORG_NAME_2." ".$ORG_NAME_1." ".$ORG_NAME);
	****/
	
		// หลังเลือก วันที่มีผล หาข้อมูลมาเติมก่อน ******
//		echo "command=$command, PER_ID=$PER_ID, break_action=$break_action, UPD=$UPD, VIEW=$VIEW<br>";
                //echo ">>>".$break_action;
		if($break_action==1){
			$SAH_EFFECTIVEDATE = save_date($SAH_EFFECTIVEDATE); 
			$cmd = "	SELECT SAH_PAY_NO , SAH_POS_NO_NAME , SAH_POS_NO , SAH_OLD_SALARY , SAH_POSITION , SAH_ORG , LEVEL_NO
							FROM		PER_SALARYHIS
							WHERE	PER_ID = $PER_ID and SAH_EFFECTIVEDATE  <= '$SAH_EFFECTIVEDATE' 
							order by SAH_EFFECTIVEDATE desc ";
			//echo "<pre>".$cmd;
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$SAH_EFFECTIVEDATE = show_date_format(trim($SAH_EFFECTIVEDATE), 1);
			$SAH_OLD_SALARY = $data[SAH_OLD_SALARY];
			$SAH_POSITION = $data[SAH_POSITION];
			$SAH_ORG = $data[SAH_ORG];
			$LEVEL_NO = $data[LEVEL_NO];
			if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง") { 
				$SAH_PAY_NO = $data[SAH_PAY_NO];
			}else{ 
				if ($POSITION_NO_CHAR=="Y") { 
					$SAH_POS_NO_NAME = $data[SAH_POS_NO_NAME];
				} 
				$SAH_POS_NO = $data[SAH_POS_NO];
				$SAH_PAY_NO = $SAH_POS_NO;
			} 
		} // end break_action
	} // end if PER_ID

	$UPDATE_DATE = date("Y-m-d H:i:s");
	if (!$SAH_PERCENT_UP) $SAH_PERCENT_UP = "NULL";
	if (!$SAH_SALARY_UP) $SAH_SALARY_UP = "NULL";
	if (!$SAH_SALARY_EXTRA) $SAH_SALARY_EXTRA = "NULL";
	if (!$SAH_SALARY_MIDPOINT) $SAH_SALARY_MIDPOINT = "NULL";
	if (!$SAH_OLD_SALARY) $SAH_OLD_SALARY = "NULL";
	$SAH_SEQ_NO = (trim($SAH_SEQ_NO))? $SAH_SEQ_NO : 1;		
	if (!$SAH_KF_CYCLE) $SAH_KF_CYCLE = "NULL";
	if (!$SAH_TOTAL_SCORE) $SAH_TOTAL_SCORE = "NULL";
	if (!$SAH_LAST_SALARY) $SAH_LAST_SALARY = "N";
	if (!$SAH_CMD_SEQ) $SAH_CMD_SEQ = "NULL";
	if($_POST[SAH_EFFECTIVEDATE])	$SAH_EFFECTIVEDATE = $_POST[SAH_EFFECTIVEDATE];
	if ($SESS_DEPARTMENT_NAME!="กรมการปกครอง" && $SAH_POS_NO) $SAH_PAY_NO = $SAH_POS_NO;

	if($command=="ADD" && $PER_ID){
		if($break_action!=1){	
			$SAH_EFFECTIVEDATE = save_date($SAH_EFFECTIVEDATE); 
			if($SAH_DOCDATE) $SAH_DOCDATE = save_date($SAH_DOCDATE); 
			if($SAH_ENDDATE) $SAH_ENDDATE = save_date($SAH_ENDDATE); 
			if($SAH_DOCDATE_EDIT) $SAH_DOCDATE_EDIT = save_date($SAH_DOCDATE_EDIT); 

			if ($SAH_LAST_SALARY=="Y") {
				$cmd = " UPDATE PER_SALARYHIS SET SAH_LAST_SALARY='N' WHERE PER_ID=$PER_ID ";
				$db_dpis->send_cmd($cmd);

				$cmd = " select POS_ID from PER_POSITION where POS_NO='$SAH_PAY_NO' ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PAY_ID = $data2[POS_ID];
				if (!$PAY_ID) $PAY_ID = "NULL";
				
				$cmd = " UPDATE PER_PERSONAL SET 
								PER_DOCNO='$SAH_DOCNO', 
								PER_DOCDATE='$SAH_DOCDATE', 
								MOV_CODE='$MOV_CODE', 
								PER_SALARY=$SAH_SALARY, 
								PAY_ID=$PAY_ID 
								WHERE PER_ID=$PER_ID ";
				$db_dpis->send_cmd($cmd);
				
				$cmd = "select POS_ID from per_personal where PER_ID=$PER_ID and PER_STATUS = 1 and PER_TYPE=1";
				$db_dpis3->send_cmd($cmd);
				$data2 = $db_dpis3->get_array();
				if($data2[POS_ID]){
					$cmd = " UPDATE PER_POSITION SET POS_SALARY=$SAH_SALARY WHERE POS_ID=$data2[POS_ID] ";
					$db_dpis3->send_cmd($cmd);
				}
			}
		}	//end if($break_action!=1)
		
		$cmd = " select max(SAH_ID) as max_id from PER_SALARYHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$SAH_ID = $data[max_id] + 1;
		$cmd = " insert into PER_SALARYHIS (SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, 
						SAH_DOCNO, SAH_DOCDATE, SAH_ENDDATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE, 
						SAH_PERCENT_UP, SAH_SALARY_UP, SAH_SALARY_EXTRA, SAH_SEQ_NO, SAH_REMARK, 
						LEVEL_NO, SAH_POS_NO, SAH_POSITION, SAH_ORG, EX_CODE, SAH_PAY_NO, SAH_SALARY_MIDPOINT, 
						SAH_KF_YEAR, SAH_KF_CYCLE, SAH_TOTAL_SCORE, SAH_LAST_SALARY, SM_CODE, SAH_CMD_SEQ, 
						SAH_OLD_SALARY, SAH_POS_NO_NAME, SAH_SPECIALIST, SAH_DOCNO_EDIT, SAH_DOCDATE_EDIT, 
						SAH_REF_DOC, SAH_REMARK1, SAH_REMARK2)
						values ($SAH_ID, $PER_ID, '$SAH_EFFECTIVEDATE', '$MOV_CODE', $SAH_SALARY, '$SAH_DOCNO', 
						'$SAH_DOCDATE', '$SAH_ENDDATE', $PER_CARDNO, $SESS_USERID, '$UPDATE_DATE', 
						$SAH_PERCENT_UP, $SAH_SALARY_UP, $SAH_SALARY_EXTRA, $SAH_SEQ_NO, '$SAH_REMARK', 
						'$LEVEL_NO', '$SAH_POS_NO', '$SAH_POSITION', '$SAH_ORG', '$EX_CODE', '$SAH_PAY_NO', 
						$SAH_SALARY_MIDPOINT, '$SAH_KF_YEAR', $SAH_KF_CYCLE, $SAH_TOTAL_SCORE, '$SAH_LAST_SALARY', 
						'$SM_CODE', $SAH_CMD_SEQ, $SAH_OLD_SALARY, '$SAH_POS_NO_NAME', '$SAH_SPECIALIST', '$SAH_DOCNO_EDIT', 
						'$SAH_DOCDATE_EDIT', '$SAH_REF_DOC', '$SAH_REMARK1', '$SAH_REMARK2') ";
		$db_dpis->send_cmd($cmd);
		
		//$db_dpis->show_error();
		//die;
		// เคลียร์ค่าเพื่อเพิ่มตัวถัดไป
		if($break_action!=1){	
			$SAH_ID="";
			$SAH_EFFECTIVEDATE="";
			$MOV_CODE="";	$MOV_NAME="";
			$SAH_SALARY="";
			$SAH_DOCNO=""; 
			$SAH_DOCDATE="";
			$SAH_ENDDATE="";
			$SAH_PERCENT_UP="";
			$SAH_SALARY_UP="";
			$SAH_SALARY_EXTRA="";
			$SAH_REMARK=""; 
			$SAH_REMARK1=""; 
			$SAH_REMARK2=""; 
			$LEVEL_NO=0;
			$SAH_POS_NO="";
			$SAH_POSITION="";
			$SAH_ORG="";
			$SAH_PAY_NO=""; 
			$SAH_SALARY_MIDPOINT="";
			$SAH_KF_YEAR="";
			$SAH_KF_CYCLE="";
			$SAH_TOTAL_SCORE="";
			$SAH_LAST_SALARY="";
			$SM_CODE="";		$SM_NAME="";
			$SAH_CMD_SEQ="";
			$SAH_OLD_SALARY="";
			$SAH_POS_NO_NAME="";
			$SAH_SPECIALIST=""; 
			$SAH_DOCNO_EDIT="";
			$SAH_DOCDATE_EDIT="";
			$SAH_REF_DOC=""; 

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติการรับเงินเดือน [$PER_ID : $SAH_ID : $MOV_CODE]");
			echo "<script language=\"JavaScript\">parent.refresh_opener('A<::>0')</script>";		//ให้ refresh หน้า แล้ว self close
			
		} // end if($break_action!=1)
	} // end if
	

	if($command=="UPDATE" && $PER_ID && $SAH_ID){
		if($break_action!=1){	
			$SAH_EFFECTIVEDATE = save_date($SAH_EFFECTIVEDATE); 
			if($SAH_DOCDATE) $SAH_DOCDATE = save_date($SAH_DOCDATE); 
			if($SAH_ENDDATE) $SAH_ENDDATE = save_date($SAH_ENDDATE); 
			if($SAH_DOCDATE_EDIT) $SAH_DOCDATE_EDIT = save_date($SAH_DOCDATE_EDIT); 
			
			if ($SAH_LAST_SALARY=="Y") {
				$cmd = " UPDATE PER_SALARYHIS SET SAH_LAST_SALARY='N' WHERE PER_ID=$PER_ID ";
				$db_dpis->send_cmd($cmd);

				$cmd = " select POS_ID from PER_POSITION where POS_NO='$SAH_PAY_NO' ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				$PAY_ID = $data2[POS_ID];
				if (!$PAY_ID) $PAY_ID = "NULL";

				$cmd = " UPDATE PER_PERSONAL SET 
								PER_DOCNO='$SAH_DOCNO', 
								PER_DOCDATE='$SAH_DOCDATE', 
								MOV_CODE='$MOV_CODE', 
								PER_SALARY=$SAH_SALARY, 
								PAY_ID=$PAY_ID 
								WHERE PER_ID=$PER_ID ";
				$db_dpis->send_cmd($cmd);
				
				$cmd = "select POS_ID from per_personal where PER_ID=$PER_ID and PER_STATUS = 1 and PER_TYPE=1";
				$db_dpis3->send_cmd($cmd);
				$data2 = $db_dpis3->get_array();
				if($data2[POS_ID]){
					$cmd = " UPDATE PER_POSITION SET POS_SALARY=$SAH_SALARY WHERE POS_ID=$data2[POS_ID] ";
					$db_dpis3->send_cmd($cmd);
				}
		
			}

			$cmd = " UPDATE PER_SALARYHIS SET SAH_EFFECTIVEDATE='$SAH_EFFECTIVEDATE', 
						MOV_CODE='$MOV_CODE', 
						SAH_SALARY=$SAH_SALARY, 
						SAH_DOCNO='$SAH_DOCNO', 
						SAH_DOCDATE='$SAH_DOCDATE', 
						SAH_ENDDATE='$SAH_ENDDATE',   
						PER_CARDNO=$PER_CARDNO, 
						SAH_PERCENT_UP=$SAH_PERCENT_UP, 
						SAH_SALARY_UP=$SAH_SALARY_UP, 
						SAH_SALARY_EXTRA=$SAH_SALARY_EXTRA, 
						SAH_SEQ_NO=$SAH_SEQ_NO, 
						SAH_REMARK='$SAH_REMARK',   
						LEVEL_NO='$LEVEL_NO',   
						SAH_POS_NO='$SAH_POS_NO',   
						SAH_POSITION='$SAH_POSITION',   
						SAH_ORG='$SAH_ORG',   
						EX_CODE='$EX_CODE',   
						SAH_PAY_NO='$SAH_PAY_NO',   
						SAH_SALARY_MIDPOINT=$SAH_SALARY_MIDPOINT, 
						SAH_KF_YEAR='$SAH_KF_YEAR',   
						SAH_KF_CYCLE=$SAH_KF_CYCLE, 
						SAH_TOTAL_SCORE=$SAH_TOTAL_SCORE, 
						SAH_LAST_SALARY='$SAH_LAST_SALARY',   
						SM_CODE='$SM_CODE',   
						SAH_CMD_SEQ=$SAH_CMD_SEQ, 
						SAH_OLD_SALARY=$SAH_OLD_SALARY, 
						SAH_POS_NO_NAME = '$SAH_POS_NO_NAME', 
						SAH_SPECIALIST='$SAH_SPECIALIST',   
						SAH_DOCNO_EDIT='$SAH_DOCNO_EDIT',   
						SAH_DOCDATE_EDIT='$SAH_DOCDATE_EDIT',   
						SAH_REF_DOC='$SAH_REF_DOC',   
						SAH_REMARK1='$SAH_REMARK1',   
						SAH_REMARK2='$SAH_REMARK2',   
						UPDATE_USER=$SESS_USERID, 
						UPDATE_DATE='$UPDATE_DATE'
						WHERE SAH_ID=$SAH_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error(); die;
			// echo "-> $cmd";
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติการรับเงินเดือน [$PER_ID : $SAH_ID : $MOV_CODE]");
			echo "<script language=\"JavaScript\">parent.refresh_opener('U<::>0')</script>";

		} // end if($break_action!=1)
	} // end if
	
	if($command=="DELETE" && $PER_ID && $SAH_ID){
		$cmd = " select MOV_CODE from PER_SALARYHIS where SAH_ID=$SAH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MOV_CODE = $data[MOV_CODE];

		$cmd = " delete from PER_SALARYHIS where SAH_ID=$SAH_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติการรับเงินเดือน [$PER_ID : $SAH_ID : $MOV_CODE]");
	} // end if

	if(($UPD && $PER_ID && $SAH_ID) || ($VIEW && $PER_ID && $SAH_ID)){
		$cmd = "	SELECT SAH_ID, SAH_EFFECTIVEDATE, psh.MOV_CODE, pm.MOV_NAME, SAH_SALARY, SAH_DOCNO, 
							SAH_DOCDATE, 	SAH_ENDDATE, SAH_PERCENT_UP, SAH_SALARY_UP, SAH_SALARY_EXTRA, 
							SAH_SEQ_NO, SAH_REMARK, LEVEL_NO, SAH_POS_NO, SAH_POS_NO_NAME, SAH_POSITION, SAH_ORG, EX_CODE, 
							SAH_PAY_NO, SAH_SALARY_MIDPOINT, SAH_KF_YEAR, SAH_KF_CYCLE, SAH_TOTAL_SCORE, 
							SAH_LAST_SALARY, SM_CODE, SAH_CMD_SEQ, SAH_OLD_SALARY, SAH_SPECIALIST, SAH_DOCNO_EDIT, 
							SAH_DOCDATE_EDIT, SAH_REF_DOC, SAH_REMARK1, SAH_REMARK2, psh.UPDATE_USER, psh.UPDATE_DATE
				FROM		PER_SALARYHIS psh, PER_MOVMENT pm 
				WHERE		psh.SAH_ID=$SAH_ID and psh.MOV_CODE=pm.MOV_CODE ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		//echo $cmd;
		$data = $db_dpis->get_array();
		$SAH_ID = $data[SAH_ID];
		$SAH_EFFECTIVEDATE = show_date_format(trim($data[SAH_EFFECTIVEDATE]), 1);
		$SAH_DOCDATE = show_date_format(trim($data[SAH_DOCDATE]), 1);
		$SAH_ENDDATE = show_date_format(trim($data[SAH_ENDDATE]), 1);
		$SAH_DOCDATE_EDIT = show_date_format(trim($data[SAH_DOCDATE_EDIT]), 1);
		$MOV_CODE = trim($data[MOV_CODE]);
		$MOV_NAME = trim($data[MOV_NAME]);		
		
		$SAH_DOCEDIT_TITLE = "";
		$RESULT_FIND_SAH_DOCEDIT= strpos($MOV_NAME,"แก้ไข"); 	 // MOV NAME แก้ไขคำสั่ง 
		if ($RESULT_FIND_SAH_DOCEDIT === FALSE){
			$RESULT_FIND_SAH_DOCEDIT=0;
		}else{
			$RESULT_FIND_SAH_DOCEDIT=1;
			$SAH_DOCEDIT_TITLE = "แก้ไข";
		}
		
		$RESULT_FIND_SAH_DOCCC= strpos($MOV_NAME,"ยกเลิกคำสั่ง"); 	 // MOV NAME ยกเลิกคำสั่ง 
		if ($RESULT_FIND_SAH_DOCCC === FALSE){
			$RESULT_FIND_SAH_DOCCC=0;
		}else{
			$RESULT_FIND_SAH_DOCCC=1;
			$SAH_DOCEDIT_TITLE = "ยกเลิกคำสั่ง";
		}
		
		if($RESULT_FIND_SAH_DOCEDIT==1 || $RESULT_FIND_SAH_DOCCC==1){
//			$set_style_sah_docdate_edit_layer="display:block; visibility:visible;";	//พบ
			$set_style_sah_docdate_edit_layer="display:table-row; visibility:visible;";	//พบ
		}else{
			$set_style_sah_docdate_edit_layer="display:none; visibility:hidden;";	//ไม่พบ		
		}
		
		$SAH_SALARY = $data[SAH_SALARY];
		$SAH_DOCNO = trim($data[SAH_DOCNO]);

		$cmd ="select COM_ID from PER_COMMAND where COM_NO = '$SAH_DOCNO' ";
		$db_dpis2->send_cmd($cmd);
//		echo "cmd=$cmd<br>";
		$data2 = $db_dpis2->get_array();
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
                //echo $cmd;
		$count_atta = $db_dpis->send_cmd($cmd);
		if ($count_atta) {
			$data2 = $db_dpis->get_array();
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

		$SAH_PERCENT_UP = number_format($data[SAH_PERCENT_UP], 4, '.', ',');
		$SAH_SALARY_UP = $data[SAH_SALARY_UP];
		$SAH_SALARY_EXTRA = $data[SAH_SALARY_EXTRA];
		$SAH_SEQ_NO = $data[SAH_SEQ_NO];
		$SAH_REMARK = trim($data[SAH_REMARK]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$SAH_POS_NO_NAME = trim($data[SAH_POS_NO_NAME]);  
		$SAH_POS_NO = trim($data[SAH_POS_NO]);
		$SAH_POSITION = trim($data[SAH_POSITION]);
		$SAH_ORG = trim($data[SAH_ORG]);
		$EX_CODE = trim($data[EX_CODE]);
		$SAH_PAY_NO = trim($data[SAH_PAY_NO]);
		$SAH_SALARY_MIDPOINT = $data[SAH_SALARY_MIDPOINT];
		$SAH_KF_YEAR = trim($data[SAH_KF_YEAR]);
		$SAH_KF_CYCLE = $data[SAH_KF_CYCLE];
		$SAH_TOTAL_SCORE = $data[SAH_TOTAL_SCORE];
		$SAH_LAST_SALARY = trim($data[SAH_LAST_SALARY]);
		$SM_CODE = trim($data[SM_CODE]);
		$SAH_CMD_SEQ = $data[SAH_CMD_SEQ];
		$SAH_OLD_SALARY = $data[SAH_OLD_SALARY];
		$SAH_SPECIALIST = trim($data[SAH_SPECIALIST]);
		$SAH_DOCNO_EDIT = $data[SAH_DOCNO_EDIT];
		$SAH_REF_DOC = $data[SAH_REF_DOC];
		$SAH_REMARK1 = trim($data[SAH_REMARK1]);
		$SAH_REMARK2 = trim($data[SAH_REMARK2]);
		$UPDATE_USER = $data[UPDATE_USER];

		$cmd ="select EX_NAME from PER_EXTRATYPE where EX_CODE = '$EX_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data2 = $db_dpis->get_array();
		$EX_NAME = trim($data2[EX_NAME]);

		$cmd ="select SM_NAME from PER_SALARY_MOVMENT where SM_CODE = '$SM_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data2 = $db_dpis->get_array();
		$SM_NAME = trim($data2[SM_NAME]);

		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		$data2 = $db_dpis->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	}

	if( !$UPD && !$VIEW ){
		if ($SAH_OLD_SALARY=="NULL") $SAH_OLD_SALARY = "";		
		if	($SAH_CMD_SEQ=="NULL") $SAH_CMD_SEQ = "";
		if ($SAH_PERCENT_UP=="NULL") $SAH_PERCENT_UP="";
		if ($SAH_SALARY_EXTRA=="NULL") $SAH_SALARY_EXTRA="";
		if ($SAH_TOTAL_SCORE=="NULL") $SAH_TOTAL_SCORE="";
		if ($SAH_SALARY_UP=="NULL") $SAH_SALARY_UP="";
		if ($SAH_SALARY_MIDPOINT=="NULL") $SAH_SALARY_MIDPOINT="";
		
		unset($SAH_ID);
/*
//		unset($SAH_EFFECTIVEDATE);
		unset($SAH_ENDDATE);
		unset($SAH_SALARY);
		unset($SAH_DOCNO);
		unset($SAH_DOCDATE);
		unset($SAH_PERCENT_UP);
		unset($SAH_SALARY_UP);
		unset($SAH_SALARY_EXTRA);
		unset($SAH_SEQ_NO);

		unset($SAH_REMARK);
		unset($SAH_SPECIALIST);
//		unset($LEVEL_NO);
//		unset($SAH_POS_NO_NAME);
//		unset($SAH_POS_NO);
//		unset($SAH_POSITION);
//		unset($SAH_ORG);
//		unset($SAH_PAY_NO);
		unset($SAH_SALARY_MIDPOINT);
		unset($SAH_KF_YEAR);
		unset($SAH_KF_CYCLE);
		unset($SAH_TOTAL_SCORE);
		$SAH_LAST_SALARY = "N";
		unset($SAH_CMD_SEQ);
//		unset($SAH_OLD_SALARY);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	
		unset($MOV_CODE);
		unset($MOV_NAME);
*/		
		$EX_CODE = "024";
		$EX_NAME = "เงินเดือน";
/*
		unset($SM_CODE);
		unset($SM_NAME);
*/
	} // end if
?>