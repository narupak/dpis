<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");	

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);		

	if($_GET[CMD_SEQ])		$CMD_SEQ = $_GET[CMD_SEQ];		//ดู หรือแก้ไข
//	echo "CMD_SEQ=$CMD_SEQ, VIEW_PER=$VIEW_PER, UPD_PER=$UPD_PER<br>";
	// ค้นหาลำดับของ PER_COMDTL เพื่อแสดงในกรณีเพิ่มรายละเอียด
	if ( trim($COM_ID) && trim(!$CMD_SEQ) ) {
		$cmd = " select max(CMD_SEQ) as max_id from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$CMD_SEQ_LAST = trim($data[max_id]);	// $CMD_SEQ ตัวล่าสุด
		$CMD_SEQ = $CMD_SEQ_LAST + 1;
	}	
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	if(!$TR_GENDER) 		$TR_GENDER = 1;
	if(!$TR_PER_TYPE) 	$TR_PER_TYPE = 1;	
	if (!$PL_NAME_WORK) $PL_NAME_WORK = "มอบหมายงาน";

	if($command=="ADD" || $command=="UPDATE"){
		$CMD_DATE = save_date($CMD_DATE); 
		$CMD_DATE2 = save_date($CMD_DATE2); 
		
		$POS_ID = $POEM_ID = $POEMS_ID = $POT_ID = "";
		$PL_CODE = $PL_CODE_ASSIGN = $PN_CODE = $PN_CODE_ASSIGN = $EP_CODE = $EP_CODE_ASSIGN = "";
		if ($PER_TYPE == 1) {
			$PL_CODE = trim($PL_PN_CODE);
			$PL_CODE_ASSIGN = trim($PL_PN_CODE_ASSIGN);
			$POS_ID = trim($POS_POEM_ID);
		} elseif ($PER_TYPE == 2) {
			$PN_CODE = trim($PL_PN_CODE);
			$PN_CODE_ASSIGN = trim($PL_PN_CODE_ASSIGN);
			$POEM_ID = trim($POS_POEM_ID);
		} elseif ($PER_TYPE == 3) {
			$EP_CODE = trim($PL_PN_CODE);
			$EP_CODE_ASSIGN = trim($PL_PN_CODE_ASSIGN);
			$POEMS_ID = trim($POS_POEM_ID);			
		}	elseif ($PER_TYPE == 4) {
			$TP_CODE = trim($PL_PN_CODE);
			$TP_CODE_ASSIGN = trim($PL_PN_CODE_ASSIGN);
			$POT_ID = trim($POS_POEM_ID);			
		}	// end if	
		
		$CMD_POS_NO_NAME = trim($CMD_POSPOEM_NO_NAME);
		$CMD_POS_NO = trim($CMD_POSPOEM_NO);
		$CMD_OLD_SALARY = str_replace(",", "", $CMD_OLD_SALARY) + 0;
		$CMD_SALARY = str_replace(",", "", $CMD_SALARY) + 0;	
		$CMD_NOTE1 = str_replace("'", "&rsquo;", $CMD_NOTE1);
		$CMD_NOTE2 = str_replace("'", "&rsquo;", $CMD_NOTE2);
		
		$EN_CODE = trim($EN_CODE)? "'".$EN_CODE."'" : "NULL";
		$LEVEL_NO = trim($LEVEL_NO)? "'".$LEVEL_NO."'" : "NULL";
		$PER_CARDNO = trim($PER_CARDNO)? "'".$PER_CARDNO."'" : "NULL";
		$POS_ID = trim($POS_ID)? $POS_ID : "NULL";
		$POEM_ID = trim($POEM_ID)? $POEM_ID : "NULL";
		$POEMS_ID = trim($POEMS_ID)? $POEMS_ID : "NULL";		
		$PL_CODE = trim($PL_CODE)? "'".$PL_CODE."'" : "NULL";
		$PN_CODE = trim($PN_CODE)? "'".$PN_CODE."'" : "NULL";
		$EP_CODE = trim($EP_CODE)? "'".$EP_CODE."'" : "NULL";		
		$PL_CODE_ASSIGN = trim($PL_CODE_ASSIGN)? "'".$PL_CODE_ASSIGN."'" : "NULL";
		$PN_CODE_ASSIGN = trim($PN_CODE_ASSIGN)? "'".$PN_CODE_ASSIGN."'" : "NULL";	
		$EP_CODE_ASSIGN = trim($EP_CODE_ASSIGN)? "'".$EP_CODE_ASSIGN."'" : "NULL";		
		$PL_NAME_WORK = trim($PL_NAME_WORK)? "'".$PL_NAME_WORK."'" : "NULL";		
		$ORG_NAME_WORK = trim($ORG_NAME_WORK)? "'".$ORG_NAME_WORK."'" : "NULL";		
		if ($CMD_PM_NAME) $CMD_POSITION = "$CMD_POSITION\|$CMD_PM_NAME";
	} // end if
	
	if($command=="ADD"){
            $VAL_ORG_ASS3 = 'NULL';
            $VAL_ORG_ASS4 = 'NULL';
            $VAL_ORG_ASS5 = 'NULL';
            $VAL_ORG_ASS6 = 'NULL';
            $VAL_ORG_ASS7 = 'NULL';
            $VAL_ORG_ASS8 = 'NULL';
            if(!empty($CMD_ORG_ASS3)){
                $VAL_ORG_ASS3 = "'".$ORG_ID_ASS3."|".trim($CMD_ORG_ASS3)."'";
            }
            if(!empty($CMD_ORG_ASS4)){
                $VAL_ORG_ASS4 = "'".$ORG_ID_ASS4."|".trim($CMD_ORG_ASS4)."'";
            }
            if(!empty($CMD_ORG_ASS5)){
                $VAL_ORG_ASS5 = "'".$ORG_ID_ASS5."|".trim($CMD_ORG_ASS5)."'";
            }
            if(!empty($CMD_ORG_ASS6)){
                $VAL_ORG_ASS6 = "'".$ORG_ID_ASS6."|".trim($CMD_ORG_ASS6)."'";
            }
            if(!empty($CMD_ORG_ASS7)){
                $VAL_ORG_ASS7 = "'".$ORG_ID_ASS7."|".trim($CMD_ORG_ASS7)."'";
            }
            if(!empty($CMD_ORG_ASS8)){
                $VAL_ORG_ASS8 = "'".$ORG_ID_ASS8."|".trim($CMD_ORG_ASS8)."'";
            }
            $cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, EN_CODE, CMD_DATE, CMD_POSITION, 
                        CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_ORG6, CMD_ORG7, 
                        CMD_ORG8, CMD_OLD_SALARY, PL_CODE, PN_CODE, EP_CODE, CMD_AC_NO, CMD_ACCOUNT, 
                        POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, PL_CODE_ASSIGN, 
                        PN_CODE_ASSIGN, EP_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_DATE2, CMD_SAL_CONFIRM,   
                        PER_CARDNO, UPDATE_USER, UPDATE_DATE, PL_NAME_WORK, ORG_NAME_WORK, CMD_LEVEL_POS, CMD_POS_NO_NAME, CMD_POS_NO,
                        CMD_ORG_ASS3,CMD_ORG_ASS4,CMD_ORG_ASS5,CMD_ORG_ASS6,CMD_ORG_ASS7,CMD_ORG_ASS8)
                    values ($COM_ID, $CMD_SEQ, $PER_ID, $EN_CODE, '$CMD_DATE', '$CMD_POSITION', 
                        '$CMD_LEVEL', '$MINISTRY_NAME', '$DEPARTMENT_NAME', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', 
                        '$CMD_ORG6', '$CMD_ORG7', '$CMD_ORG8', $CMD_OLD_SALARY, $PL_CODE, $PN_CODE, $EP_CODE, 
                        '$CMD_AC_NO', '$CMD_ACCOUNT', $POS_ID, $POEM_ID, $POEMS_ID, $LEVEL_NO, $CMD_SALARY, 0, 
                        $PL_CODE_ASSIGN, $PN_CODE_ASSIGN, $EP_CODE_ASSIGN, '$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', 
                        '$CMD_DATE2',  0, $PER_CARDNO, $SESS_USERID, '$UPDATE_DATE', $PL_NAME_WORK, 
                        $ORG_NAME_WORK, '$CMD_LEVEL1', '$CMD_POS_NO_NAME', '$CMD_POS_NO',
                        $VAL_ORG_ASS3,$VAL_ORG_ASS4,$VAL_ORG_ASS5,$VAL_ORG_ASS6,$VAL_ORG_ASS7,$VAL_ORG_ASS8) ";
            /*เดิม*/
		/*$cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, EN_CODE, CMD_DATE, CMD_POSITION, 
						CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_ORG6, CMD_ORG7, 
						CMD_ORG8, CMD_OLD_SALARY, PL_CODE, PN_CODE, EP_CODE, CMD_AC_NO, CMD_ACCOUNT, 
						POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, PL_CODE_ASSIGN, 
						PN_CODE_ASSIGN, EP_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_DATE2, CMD_SAL_CONFIRM,   
						PER_CARDNO, UPDATE_USER, UPDATE_DATE, PL_NAME_WORK, ORG_NAME_WORK, CMD_LEVEL_POS, CMD_POS_NO_NAME, CMD_POS_NO)
						values ($COM_ID, $CMD_SEQ, $PER_ID, $EN_CODE, '$CMD_DATE', '$CMD_POSITION', 
						'$CMD_LEVEL', '$MINISTRY_NAME', '$DEPARTMENT_NAME', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', 
						'$CMD_ORG6', '$CMD_ORG7', '$CMD_ORG8', $CMD_OLD_SALARY, $PL_CODE, $PN_CODE, $EP_CODE, 
						'$CMD_AC_NO', '$CMD_ACCOUNT', $POS_ID, $POEM_ID, $POEMS_ID, $LEVEL_NO, $CMD_SALARY, 0, 
						$PL_CODE_ASSIGN, $PN_CODE_ASSIGN, $EP_CODE_ASSIGN, '$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', 
						'$CMD_DATE2',  0, $PER_CARDNO, $SESS_USERID, '$UPDATE_DATE', $PL_NAME_WORK, 
						$ORG_NAME_WORK, '$CMD_LEVEL1', '$CMD_POS_NO_NAME', '$CMD_POS_NO') ";*/
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "insert-cmd=$cmd<br>";
		
		//---กำหนดให้ยังคงอยู่หน้าเดิมและเพิ่ม ลำดับถัดไปเพื่อเพิ่มคนถัดไป / เคลียร์ค่าให้เป็นว่างเพื่อเลือกเพิ่มคนใหม่
		$CH_ADD="";	//เคลียร์ค่า เพื่อให้แสดงปุ่มถูกต้อง
		$UPD_PER="";		$VIEW_PER="";
		$CMD_SEQ = $CMD_SEQ+1;
		
		$PER_ID="";			
		$PER_NAME = "";
		$PER_BIRTHDATE = "";
		$EN_CODE="";
		$EN_NAME="";
		$CMD_POSPOEM_NO_NAME="";
		$CMD_POSPOEM_NO="";
		$CMD_POSITION="";
		$CMD_PM_CODE="";
		$CMD_PM_NAME="";
		$CMD_LEVEL2="";
		$CMD_LEVEL="";
		$CMD_LEVEL3="";
		$CMD_LEVEL1="";
		$LAYER_SALARY_MIN="";
		$LAYER_SALARY_MAX="";
		$CMD_ORG1="";
		$CMD_ORG2="";
		$CMD_ORG3="";
		$CMD_ORG4="";
		$CMD_ORG5="";
		$CMD_ORG6="";
		$CMD_ORG7="";
		$CMD_ORG8="";
		$CMD_OLD_SALARY="";
		$PL_CODE="";
		$PN_CODE="";
		$EP_CODE="";
		$CMD_AC_NO="";
		$CMD_ACCOUNT="";
		$POS_ID="";
		$POEM_ID="";
		$POEMS_ID="";
		$LEVEL_NO="";
		$LEVEL_NAME="";
		$CMD_SALARY="";
		$PL_CODE_ASSIGN="";
		$PN_CODE_ASSIGN="";
		$EP_CODE_ASSIGN="";
		$CMD_NOTE1="";
		$CMD_NOTE2="";
		$MOV_CODE="";			
		$MOV_NAME = "";
		$CMD_DATE2="";
		$PER_CARDNO="";
		
		$POS_POEM_NO_NAME = "";
		$POS_POEM_NO = "";
		$POS_POEM_NAME = "";
		$POS_POEM_ID = "";
		$POS_POEM_ORG1 = "";
		$POS_POEM_ORG2 = "";
		$POS_POEM_ORG3 = "";
		$POS_POEM_ORG4 = "";
		$POS_POEM_ORG5 = "";
		$POS_POEM_ORG6 = "";
		$POS_POEM_ORG7 = "";
		$POS_POEM_ORG8 = "";
		$POS_PM_CODE = "";
		$POS_PM_NAME = "";
		$PL_PN_NAME_ASSIGN = "";
		$PL_NAME_WORK = $MOV_NAME;
		$ORG_NAME_WORK = "";
		$CMD_ES_CODE = "";
		$CMD_ES_NAME = "";
		$ES_CODE = "";
		$ES_NAME = "";
		$CMD_DATE = show_date_format($CMD_DATE, 1);
		$CMD_DATE2 = show_date_format(trim($CMD_DATE2), 1);
		unset($SHOW_UPDATE_USER_DTL);
		unset($SHOW_UPDATE_DATE_DTL);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $ADD_PERSON_TITLE$COM_TYPE_NM [ $COM_ID : $PER_ID ]");
	} // end if

	if($command=="UPDATE" && $COM_ID){
            $VAL_ORG_ASS3 = 'NULL';
            $VAL_ORG_ASS4 = 'NULL';
            $VAL_ORG_ASS5 = 'NULL';
            $VAL_ORG_ASS6 = 'NULL';
            $VAL_ORG_ASS7 = 'NULL';
            $VAL_ORG_ASS8 = 'NULL';
            if(!empty($CMD_ORG_ASS3)){
                $VAL_ORG_ASS3 = "'".$ORG_ID_ASS3."|".trim($CMD_ORG_ASS3)."'";
            }
            if(!empty($CMD_ORG_ASS4)){
                $VAL_ORG_ASS4 = "'".$ORG_ID_ASS4."|".trim($CMD_ORG_ASS4)."'";
            }
            if(!empty($CMD_ORG_ASS5)){
                $VAL_ORG_ASS5 = "'".$ORG_ID_ASS5."|".trim($CMD_ORG_ASS5)."'";
            }
            if(!empty($CMD_ORG_ASS6)){
                $VAL_ORG_ASS6 = "'".$ORG_ID_ASS6."|".trim($CMD_ORG_ASS6)."'";
            }
            if(!empty($CMD_ORG_ASS7)){
                $VAL_ORG_ASS7 = "'".$ORG_ID_ASS7."|".trim($CMD_ORG_ASS7)."'";
            }
            if(!empty($CMD_ORG_ASS8)){
                $VAL_ORG_ASS8 = "'".$ORG_ID_ASS8."|".trim($CMD_ORG_ASS8)."'";
            }
            $cmd = " update PER_COMDTL set
                            PER_ID = $PER_ID, 
                            EN_CODE = $EN_CODE, 
                            CMD_DATE = '$CMD_DATE', 
                            CMD_POSITION = '$CMD_POSITION', 
                            CMD_LEVEL = '$CMD_LEVEL', 
                            CMD_ORG1 = '$MINISTRY_NAME', 
                            CMD_ORG2 = '$DEPARTMENT_NAME', 
                            CMD_ORG3 = '$CMD_ORG3', 
                            CMD_ORG4 = '$CMD_ORG4', 
                            CMD_ORG5 = '$CMD_ORG5', 
                            CMD_ORG6 = '$CMD_ORG6', 
                            CMD_ORG7 = '$CMD_ORG7', 
                            CMD_ORG8 = '$CMD_ORG8', 
                            CMD_OLD_SALARY = $CMD_OLD_SALARY, 
                            PL_CODE = $PL_CODE, 
                            PN_CODE = $PN_CODE, 
                            EP_CODE = $EP_CODE, 
                            CMD_AC_NO = '$CMD_AC_NO', 
                            CMD_ACCOUNT = '$CMD_ACCOUNT', 
                            POS_ID = $POS_ID, 
                            POEM_ID = $POEM_ID, 
                            POEMS_ID = $POEMS_ID, 
                            LEVEL_NO = $LEVEL_NO, 
                            CMD_SALARY = $CMD_SALARY, 
                            CMD_SPSALARY = 0, 
                            PL_CODE_ASSIGN = $PL_CODE_ASSIGN, 
                            PN_CODE_ASSIGN = $PN_CODE_ASSIGN, 
                            EP_CODE_ASSIGN = $EP_CODE_ASSIGN, 
                            CMD_NOTE1 = '$CMD_NOTE1', 
                            CMD_NOTE2 = '$CMD_NOTE2', 
                            MOV_CODE = '$MOV_CODE', 
                            CMD_DATE2 = '$CMD_DATE2', 
                            CMD_SAL_CONFIRM = 0, 
                            PER_CARDNO = $PER_CARDNO, 
                            UPDATE_USER = $SESS_USERID, 
                            UPDATE_DATE = '$UPDATE_DATE',
                            PL_NAME_WORK = $PL_NAME_WORK, 
                            ORG_NAME_WORK = $ORG_NAME_WORK, 
                            CMD_LEVEL_POS='$CMD_LEVEL1',
                            CMD_POS_NO_NAME='$CMD_POS_NO_NAME',
                            CMD_POS_NO='$CMD_POS_NO',
                            CMD_ORG_ASS3=$VAL_ORG_ASS3,
                            CMD_ORG_ASS4=$VAL_ORG_ASS4,
                            CMD_ORG_ASS5=$VAL_ORG_ASS5,
                            CMD_ORG_ASS6=$VAL_ORG_ASS6,
                            CMD_ORG_ASS7=$VAL_ORG_ASS7,
                            CMD_ORG_ASS8=$VAL_ORG_ASS8
             where COM_ID=$COM_ID and CMD_SEQ=$CMD_SEQ  ";
            /*เดิม*/
		/*$cmd = " update PER_COMDTL set
								PER_ID = $PER_ID, 
								EN_CODE = $EN_CODE, 
								CMD_DATE = '$CMD_DATE', 
								CMD_POSITION = '$CMD_POSITION', 
								CMD_LEVEL = '$CMD_LEVEL', 
								CMD_ORG1 = '$MINISTRY_NAME', 
								CMD_ORG2 = '$DEPARTMENT_NAME', 
								CMD_ORG3 = '$CMD_ORG3', 
								CMD_ORG4 = '$CMD_ORG4', 
								CMD_ORG5 = '$CMD_ORG5', 
								CMD_ORG6 = '$CMD_ORG6', 
								CMD_ORG7 = '$CMD_ORG7', 
								CMD_ORG8 = '$CMD_ORG8', 
								CMD_OLD_SALARY = $CMD_OLD_SALARY, 
								PL_CODE = $PL_CODE, 
								PN_CODE = $PN_CODE, 
								EP_CODE = $EP_CODE, 
								CMD_AC_NO = '$CMD_AC_NO', 
								CMD_ACCOUNT = '$CMD_ACCOUNT', 
								POS_ID = $POS_ID, 
								POEM_ID = $POEM_ID, 
								POEMS_ID = $POEMS_ID, 
								LEVEL_NO = $LEVEL_NO, 
								CMD_SALARY = $CMD_SALARY, 
								CMD_SPSALARY = 0, 
								PL_CODE_ASSIGN = $PL_CODE_ASSIGN, 
								PN_CODE_ASSIGN = $PN_CODE_ASSIGN, 
								EP_CODE_ASSIGN = $EP_CODE_ASSIGN, 
								CMD_NOTE1 = '$CMD_NOTE1', 
								CMD_NOTE2 = '$CMD_NOTE2', 
								MOV_CODE = '$MOV_CODE', 
								CMD_DATE2 = '$CMD_DATE2', 
								CMD_SAL_CONFIRM = 0, 
								PER_CARDNO = $PER_CARDNO, 
								UPDATE_USER = $SESS_USERID, 
								UPDATE_DATE = '$UPDATE_DATE',
								PL_NAME_WORK = $PL_NAME_WORK, 
								ORG_NAME_WORK = $ORG_NAME_WORK, 
								CMD_LEVEL_POS='$CMD_LEVEL1',
								CMD_POS_NO_NAME='$CMD_POS_NO_NAME',
								CMD_POS_NO='$CMD_POS_NO'
						 where COM_ID=$COM_ID and CMD_SEQ=$CMD_SEQ  ";*/
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $EDIT_PERSON_TITLE$COM_TYPE_NM [ $COM_ID : $PER_ID ]");
		
		$UPD_PER="";		$VIEW_PER="";
		// ให้ย้อนกลับไป หน้าที่ 2 
		$show_topic = 2;	
	} // end if
	
	if($command=="DELETE" && $COM_ID){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID and PER_ID=$PER_ID ";	
		$db_dpis->send_cmd($cmd);
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $DEL_PERSON_TITLE$COM_TYPE_NM [ $COM_ID : $PER_ID ]");		
	} // end if

	// ค้นหาเพื่อแสดงผลข้อมูล	
	// ********** dum แก้ไข
	if((($PAGE_AUTH["add"]=="Y" && trim($CMD_SEQ_LAST)>0) || $UPD_PER || $VIEW_PER) && trim($COM_ID) && trim($CMD_SEQ)){
		$CH_ADD="";	//เคลียร์ค่า เพื่อให้แสดงปุ่มถูกต้อง 
		if($PAGE_AUTH["add"]=="Y" && trim($CMD_SEQ_LAST)){	//กรณีเพิ่มใหม่เอาข้อมูลก่อนหน้ามาแสดง เฉพาะวันที่แต่งตั้ง และประเภทความเคลื่อนไหว ($CMD_DATE/$MOV_CODE/$MOV_NAME)
			$CMD_SEQ_DTL = $CMD_SEQ_LAST;
		}else{
			$CMD_SEQ_DTL = $CMD_SEQ;
		}
		$cmd = "	select	CMD_SEQ, PER_ID, EN_CODE, CMD_DATE, CMD_POSITION, CMD_LEVEL, CMD_ORG1, 
							CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_ORG6, CMD_ORG7, CMD_ORG8, 
							CMD_OLD_SALARY, PL_CODE, PN_CODE, EP_CODE, CMD_AC_NO, CMD_ACCOUNT, POS_ID, 
							POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, PL_CODE_ASSIGN, PN_CODE_ASSIGN, EP_CODE_ASSIGN, 
							CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_DATE2, PL_NAME_WORK, ORG_NAME_WORK, 
							CMD_LEVEL_POS, CMD_POS_NO_NAME, CMD_POS_NO, UPDATE_USER, UPDATE_DATE,
                                                        CMD_ORG_ASS3,CMD_ORG_ASS4,CMD_ORG_ASS5,CMD_ORG_ASS6,CMD_ORG_ASS7,CMD_ORG_ASS8
					from	PER_COMDTL
					where	COM_ID=$COM_ID and CMD_SEQ=$CMD_SEQ_DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		if($UPD_PER || $VIEW_PER){		//เฉพาะ เพิ่ม ดู และแก้ไข
			if($DPISDB=="mysql")	{
				$tmp_data = explode("|", trim($data[CMD_POSITION]));
			}else{
				$tmp_data = explode("\|", trim($data[CMD_POSITION]));
			}
			//ในกรณีที่มี CMD_PM_NAME
			if(is_array($tmp_data)){
				$CMD_POSITION = $tmp_data[0];
				$CMD_PM_NAME = $tmp_data[1];
			}else{
				$CMD_POSITION = $data[CMD_POSITION];
			}
			$CMD_POSPOEM_NO_NAME = trim($data[CMD_POS_NO_NAME]); 
			$CMD_POSPOEM_NO = trim($data[CMD_POS_NO]); 
			$CMD_ORG1 = trim($data[CMD_ORG1]); 
			$CMD_ORG2 = trim($data[CMD_ORG2]); 
			$CMD_ORG3 = trim($data[CMD_ORG3]); 
			$CMD_ORG4 = trim($data[CMD_ORG4]); 
			$CMD_ORG5 = trim($data[CMD_ORG5]); 
			$CMD_ORG6 = trim($data[CMD_ORG6]); 
			$CMD_ORG7 = trim($data[CMD_ORG7]); 
			$CMD_ORG8 = trim($data[CMD_ORG8]); 
			$CMD_OLD_SALARY = number_format(trim($data[CMD_OLD_SALARY]), 2, '.', ','); 
			$CMD_SALARY = number_format(trim($data[CMD_SALARY]), 2, '.', ','); 
			$CMD_NOTE1 = trim($data[CMD_NOTE1]); 
			$CMD_NOTE2 = trim($data[CMD_NOTE2]); 
			$PL_NAME_WORK = trim($data[PL_NAME_WORK]); 
			$ORG_NAME_WORK = trim($data[ORG_NAME_WORK]); 
			
			$CMD_LEVEL = $data[CMD_LEVEL];
			$CMD_LEVEL1 = $data[CMD_LEVEL_POS];
                        
                        /*Release 5.1.0.9 Begin*/
                        $CMD_ORG_ASS3='';$ORG_ID_ASS3='';
                        $CMD_ORG_ASS4='';$ORG_ID_ASS4='';
                        $CMD_ORG_ASS5='';$ORG_ID_ASS5='';
                        $CMD_ORG_ASS6='';$ORG_ID_ASS6='';
                        $CMD_ORG_ASS7='';$ORG_ID_ASS7='';
                        $CMD_ORG_ASS8='';$ORG_ID_ASS8='';
                        if(!empty($data[CMD_ORG_ASS3])){$ARR_ORG_ASS3=explode("|",$data[CMD_ORG_ASS3]);$CMD_ORG_ASS3=$ARR_ORG_ASS3[1];$ORG_ID_ASS3=$ARR_ORG_ASS3[0];}
                        if(!empty($data[CMD_ORG_ASS4])){$ARR_ORG_ASS4=explode("|",$data[CMD_ORG_ASS4]);$CMD_ORG_ASS4=$ARR_ORG_ASS4[1];$ORG_ID_ASS4=$ARR_ORG_ASS4[0];}
                        if(!empty($data[CMD_ORG_ASS5])){$ARR_ORG_ASS5=explode("|",$data[CMD_ORG_ASS5]);$CMD_ORG_ASS5=$ARR_ORG_ASS5[1];$ORG_ID_ASS5=$ARR_ORG_ASS5[0];}
                        if(!empty($data[CMD_ORG_ASS6])){$ARR_ORG_ASS6=explode("|",$data[CMD_ORG_ASS6]);$CMD_ORG_ASS6=$ARR_ORG_ASS6[1];$ORG_ID_ASS6=$ARR_ORG_ASS6[0];}
                        if(!empty($data[CMD_ORG_ASS7])){$ARR_ORG_ASS7=explode("|",$data[CMD_ORG_ASS7]);$CMD_ORG_ASS7=$ARR_ORG_ASS7[1];$ORG_ID_ASS7=$ARR_ORG_ASS7[0];}
                        if(!empty($data[CMD_ORG_ASS8])){$ARR_ORG_ASS8=explode("|",$data[CMD_ORG_ASS8]);$CMD_ORG_ASS8=$ARR_ORG_ASS8[1];$ORG_ID_ASS8=$ARR_ORG_ASS8[0];}
                        /*Release 5.1.0.9 End*/
			
			$UPDATE_USER = $data[UPDATE_USER];
			$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
			$db->send_cmd($cmd);
			$data2 = $db->get_array();
			$SHOW_UPDATE_USER_DTL = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
			$SHOW_UPDATE_DATE_DTL = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

			if(trim($CMD_LEVEL)){
				$cmd="select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$CMD_LEVEL'";
				$db_dpis->send_cmd($cmd);
				$Level = $db_dpis->get_array();
				$CMD_LEVEL_NAME=$Level[LEVEL_NAME];
			}
			if(trim($CMD_LEVEL1)){
				$cmd="select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$CMD_LEVEL1'";
				$db_dpis->send_cmd($cmd);
				$Level = $db_dpis->get_array();
				$CMD_LEVEL_NAME_POS=$Level[LEVEL_NAME];
			}

			$CMD_LEVEL2 = $CMD_LEVEL_NAME; 
			$CMD_LEVEL3 = $CMD_LEVEL_NAME_POS; 
			$LEVEL_NO = trim($data[LEVEL_NO]); 
			$LEVEL_NO_show = level_no_format($LEVEL_NO);	

			$CMD_DATE = show_date_format(trim($data[CMD_DATE]), 1);
			$CMD_DATE2 = show_date_format(trim($data[CMD_DATE2]), 1);
			
			$PER_ID = trim($data[PER_ID]);
			$EN_CODE = trim($data[EN_CODE]);
			$MOV_CODE = trim($data[MOV_CODE]); 
			
			$cmd = " select 	PER_CARDNO, b.PN_NAME, PER_NAME, PER_SURNAME, PER_BIRTHDATE, PER_TYPE 
						  from 	PER_PERSONAL a, PER_PRENAME b 
						  where trim(PER_ID)=$PER_ID and a.PN_CODE=b.PN_CODE ";	
			$db_dpis1->send_cmd($cmd);
			$data_dpis2 = $db_dpis1->get_array();
			$PER_CARDNO = trim($data_dpis2[PER_CARDNO]);	
			$PER_NAME = trim($data_dpis2[PN_NAME]) . trim($data_dpis2[PER_NAME]) ." ". trim($data_dpis2[PER_SURNAME]);
			$PER_BIRTHDATE = show_date_format($data_dpis2[PER_BIRTHDATE], 1);
			$PER_TYPE = trim($data_dpis2[PER_TYPE]);

			$cmd = " select EN_NAME from PER_EDUCNAME where trim(EN_CODE)='$EN_CODE' ";		
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$EN_NAME = trim($data_dpis1[EN_NAME]);

			$cmd = " select MOV_NAME from PER_MOVMENT where trim(MOV_CODE)='$MOV_CODE' ";		
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$MOV_NAME = trim($data_dpis1[MOV_NAME]);		
		}
	} 	// 	if($COM_ID){	
?>