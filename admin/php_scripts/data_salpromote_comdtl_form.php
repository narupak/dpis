<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);		
        ini_set("max_execution_time", 0);
	// ค้นหาลำดับของ PER_COMDTL เพื่อแสดงในกรณีเพิ่มรายละเอียด
	if($_GET[CMD_SEQ])		$CMD_SEQ = $_GET[CMD_SEQ];		//ดู หรือแก้ไข
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
	if(!$TR_PER_TYPE) 		$TR_PER_TYPE = 1;	
	
	if($command=="ADD" || $command=="UPDATE"){
		$CMD_DATE =  save_date($CMD_DATE);
		
		$POS_ID = $POEM_ID = $POEMS_ID = "";
		$PL_CODE = $PL_CODE_ASSIGN = $PN_CODE = $PN_CODE_ASSIGN = $EP_CODE = $EP_CODE_ASSIGN = "";
		$cmd = " select	PAY_ID, POS_ID, POEM_ID, POEMS_ID, PAY_ID from PER_PERSONAL where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง") $POS_ID = trim($data[PAY_ID]); 
		else $POS_ID = trim($data[POS_ID]); 
		$POEM_ID = trim($data[POEM_ID]); 
		$POEMS_ID = trim($data[POEMS_ID]); 

		if ($PER_TYPE == 1) {
			$PL_CODE = trim($PL_PN_CODE);
			$PL_CODE_ASSIGN = trim($PL_PN_CODE_ASSIGN);
		} elseif ($PER_TYPE == 2) {
			$PN_CODE = trim($PL_PN_CODE);
			$PN_CODE_ASSIGN = trim($PL_PN_CODE_ASSIGN);
		} elseif ($PER_TYPE == 3) {
			$EP_CODE = trim($PL_PN_CODE);
			$EP_CODE_ASSIGN = trim($PL_PN_CODE_ASSIGN);
		}	elseif ($PER_TYPE == 4) {
			$TP_CODE = trim($PL_PN_CODE);
			$TP_CODE_ASSIGN = trim($PL_PN_CODE_ASSIGN);
		}	// end if	
		
		$CMD_POS_NO_NAME = trim($CMD_POSPOEM_NO_NAME);
		$CMD_POS_NO = trim($CMD_POSPOEM_NO);
		$CMD_OLD_SALARY = str_replace(",", "", $CMD_OLD_SALARY) + 0;
		$CMD_SALARY = str_replace(",", "", $CMD_SALARY) + 0;	
		$CMD_SPSALARY = str_replace(",", "", $CMD_SPSALARY) + 0;
		$CMD_PERCENT = str_replace(",", "", $CMD_PERCENT) + 0;
		$CMD_NOTE1 = str_replace("'", "&rsquo;", $CMD_NOTE1);
		$CMD_NOTE2 = str_replace("'", "&rsquo;", $CMD_NOTE2);

		$EN_CODE = trim($EN_CODE)? "'".$EN_CODE."'" : "NULL";
		if (!$LEVEL_NO) $LEVEL_NO = $CMD_LEVEL;
		$LEVEL_NO = trim($LEVEL_NO)? "'".$LEVEL_NO."'" : "NULL";
		$PER_CARDNO = trim($PER_CARDNO)? "'".$PER_CARDNO."'" : "NULL";
		$POS_ID = trim($POS_ID)? $POS_ID : "NULL";
		$POEM_ID = trim($POEM_ID)? $POEM_ID : "NULL";
		$POEMS_ID = trim($POEMS_ID)? $POEMS_ID : "NULL";		
		$PL_CODE = trim($PL_CODE)? "'".$PL_CODE."'" : "NULL";
		$PM_CODE = trim($PM_CODE)? "'".$PM_CODE."'" : "NULL";
		$PN_CODE = trim($PN_CODE)? "'".$PN_CODE."'" : "NULL";
		$EP_CODE = trim($EP_CODE)? "'".$EP_CODE."'" : "NULL";		
		$PL_CODE_ASSIGN = trim($PL_CODE_ASSIGN)? "'".$PL_CODE_ASSIGN."'" : "NULL";
		$PN_CODE_ASSIGN = trim($PN_CODE_ASSIGN)? "'".$PN_CODE_ASSIGN."'" : "NULL";	
		$EP_CODE_ASSIGN = trim($EP_CODE_ASSIGN)? "'".$EP_CODE_ASSIGN."'" : "NULL";							
		$PL_NAME_WORK = trim($PL_NAME_WORK)? "'".$PL_NAME_WORK."'" : "NULL";							
		$ORG_NAME_WORK = trim($ORG_NAME_WORK)? "'".$ORG_NAME_WORK."'" : "NULL";							
		if ($CMD_PM_NAME) $CMD_POSITION = "$CMD_POSITION\|$CMD_PM_NAME";
		if(!$CMD_SEQ_NO) 	$CMD_SEQ_NO = $CMD_SEQ;	
		
		if ($PER_TYPE == 1) {
			$cmd = " select LAYER_TYPE from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$LAYER_TYPE = $data2[LAYER_TYPE] + 0;

			$cmd = " select LAYER_SALARY_MAX, LAYER_SALARY_MIDPOINT, LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, 
				 LAYER_SALARY_FULL, LAYER_EXTRA_MIDPOINT,LAYER_EXTRA_MIDPOINT1,LAYER_EXTRA_MIDPOINT2
				 from PER_LAYER where LAYER_TYPE = 0 and LEVEL_NO = '$CMD_LEVEL' and LAYER_NO = 0 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
                        
                        /* Release 5.2.1.20 http://dpis.ocsc.go.th/Service/node/1918*/ 
                        // เดิม if ($LAYER_TYPE==1 && ($CMD_LEVEL == "O3" || $CMD_LEVEL == "K5") && $CMD_OLD_SALARY <= $data2[LAYER_SALARY_FULL]) {
			if ($LAYER_TYPE==1 && ($CMD_LEVEL == "O3" || $CMD_LEVEL == "K5") && $CMD_OLD_SALARY < $data2[LAYER_SALARY_FULL]) {
				$LAYER_SALARY_MAX = $data2[LAYER_SALARY_FULL];
				$SALARY_POINT_MID = $data2[LAYER_EXTRA_MIDPOINT];
				$SALARY_POINT_MID1 = $data2[LAYER_EXTRA_MIDPOINT1];
				$SALARY_POINT_MID2 = $data2[LAYER_EXTRA_MIDPOINT2];
			} else {
				$LAYER_SALARY_MAX = $data2[LAYER_SALARY_MAX];
				$SALARY_POINT_MID = $data2[LAYER_SALARY_MIDPOINT];
				$SALARY_POINT_MID1 = $data2[LAYER_SALARY_MIDPOINT1];
				$SALARY_POINT_MID2 = $data2[LAYER_SALARY_MIDPOINT2];
			}

			if($SALARY_POINT_MID > $CMD_OLD_SALARY) {
				$CMD_MIDPOINT = $SALARY_POINT_MID1;
			} else {
				$CMD_MIDPOINT = $SALARY_POINT_MID2;
			}
		}
		$CMD_MIDPOINT = trim($CMD_MIDPOINT)? $CMD_MIDPOINT : "NULL";
	} // end if
	
//print("<pre>");	print_r($_POST);	print("</pre>");	
	if($command=="ADD"){
		$cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, EN_CODE, CMD_DATE, CMD_POSITION, 
								CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_OLD_SALARY,  
								POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, 
								PL_CODE_ASSIGN, PN_CODE_ASSIGN, EP_CODE_ASSIGN, 
								CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM,   
								PER_CARDNO, CMD_PERCENT, UPDATE_USER, UPDATE_DATE,
								PL_NAME_WORK, ORG_NAME_WORK,CMD_LEVEL_POS, CMD_SEQ_NO, CMD_MIDPOINT,CMD_POS_NO_NAME, CMD_POS_NO)
						 values ($COM_ID, $CMD_SEQ, $PER_ID, $EN_CODE, '$CMD_DATE', '$CMD_POSITION', 
								'$CMD_LEVEL', '$CMD_ORG1', '$CMD_ORG2', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', $CMD_OLD_SALARY, 
								$POS_ID, $POEM_ID, $POEMS_ID, $LEVEL_NO, $CMD_SALARY, $CMD_SPSALARY, 
								$PL_CODE_ASSIGN, $PN_CODE_ASSIGN, $EP_CODE_ASSIGN, 
								'$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', 0, 
								$PER_CARDNO, $CMD_PERCENT, $SESS_USERID, '$UPDATE_DATE',
								$PL_NAME_WORK, $ORG_NAME_WORK,'$CMD_LEVEL1', $CMD_SEQ_NO, $CMD_MIDPOINT, '$CMD_POS_NO_NAME', '$CMD_POS_NO') ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error();		
//echo $cmd."<br>";	

		//---กำหนดให้ยังคงอยู่หน้าเดิมและเพิ่ม ลำดับถัดไปเพื่อเพิ่มคนถัดไป / เคลียร์ค่าให้เป็นว่างเพื่อเลือกเพิ่มคนใหม่
		$CH_ADD="";	//เคลียร์ค่า เพื่อให้แสดงปุ่มถูกต้อง
		$UPD_PER="";		$VIEW_PER="";
		$CMD_SEQ = $CMD_SEQ+1;
		
		$CMD_SEQ_NO=$CMD_SEQ;
		$PER_ID="";			
		$PER_NAME = "";
		$PER_BIRTHDATE = "";
		$EN_CODE="";
		$EN_NAME="";
		$CMD_PM_NAME="";
		$CMD_POSITION="";
		$CMD_PM_CODE="";
		$CMD_PM_NAME="";
		$CMD_POSPOEM_NO = "";
		$CMD_POSPOEM_NO_NAME = "";
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
		$CMD_OLD_SALARY="";
		$CMD_PERCENT="";
		$PL_CODE="";
		$PN_CODE="";
		$EP_CODE="";
		$CMD_AC_NO="";
		$CMD_ACCOUNT="";
		$POS_ID="";
		$POEM_ID="";
		$POEMS_ID="";
		$LEVEL_NO="";
		$CMD_SALARY="";
		$PL_CODE_ASSIGN="";
		$PN_CODE_ASSIGN="";
		$EP_CODE_ASSIGN="";
		$CMD_NOTE1="";
		$CMD_NOTE2="";
		$PER_CARDNO="";
		
		$CMD_PM_NAME = "";
		$PL_PN_NAME_ASSIGN = "";
//		$MOV_CODE = "";
//		$MOV_NAME = "";
		$PL_NAME_WORK = "";
		$ORG_NAME_WORK = "";
		$CMD_ES_CODE = "";
		$CMD_ES_NAME = "";
		$ES_CODE = "";
		$ES_NAME = "";
		$POS_PM_CODE = "";
		$POS_PM_NAME = "";
		$CMD_DATE = show_date_format($CMD_DATE, 1);
		$CMD_DATE2 = show_date_format($CMD_DATE2, 1);
		unset($SHOW_UPDATE_USER_DTL);
		unset($SHOW_UPDATE_DATE_DTL);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $ADD_PERSON_TITLE$COM_TYPE_NM [ $COM_ID : $PER_ID ]");
	} // end if

	if($command=="UPDATE" && $COM_ID){
		$cmd = " update PER_COMDTL set
								PER_ID = $PER_ID, 
								EN_CODE = $EN_CODE, 
								CMD_DATE = '$CMD_DATE', 
								CMD_POSITION = '$CMD_POSITION', 
								CMD_ORG1 = '$CMD_ORG1', 
								CMD_ORG2 = '$CMD_ORG2', 
								CMD_ORG3 = '$CMD_ORG3', 
								CMD_ORG4 = '$CMD_ORG4', 
								CMD_ORG5 = '$CMD_ORG5', 
								CMD_OLD_SALARY = $CMD_OLD_SALARY, 
								POS_ID = $POS_ID, 
								POEM_ID = $POEM_ID, 
								POEMS_ID = $POEMS_ID, 
								CMD_SALARY = $CMD_SALARY, 
								CMD_SPSALARY = $CMD_SPSALARY, 
								PL_CODE_ASSIGN = $PL_CODE_ASSIGN, 
								PN_CODE_ASSIGN = $PN_CODE_ASSIGN, 
								EP_CODE_ASSIGN = $EP_CODE_ASSIGN, 
								CMD_NOTE1 = '$CMD_NOTE1', 
								CMD_NOTE2 = '$CMD_NOTE2', 
								MOV_CODE = '$MOV_CODE', 
								CMD_SAL_CONFIRM = 0, 
								PER_CARDNO = $PER_CARDNO, 
								CMD_PERCENT = $CMD_PERCENT, 
								PL_NAME_WORK = $PL_NAME_WORK, 
								ORG_NAME_WORK = $ORG_NAME_WORK, 
								UPDATE_USER = $SESS_USERID, 
								UPDATE_DATE = '$UPDATE_DATE',
								CMD_LEVEL_POS='$CMD_LEVEL1',
								CMD_SEQ_NO = $CMD_SEQ_NO,
								CMD_POS_NO_NAME='$CMD_POS_NO_NAME',
								CMD_POS_NO='$CMD_POS_NO' 
						 where COM_ID=$COM_ID and CMD_SEQ=$CMD_SEQ  ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $EDIT_PERSON_TITLE$COM_TYPE_NM [ $COM_ID : $PER_ID ]");

		$VIEW_PER="";
	//	$UPD_PER="";		
		$CMD_DATE = show_date_format($CMD_DATE, 1);
		// ให้ย้อนกลับไป หน้าที่ 2 
		// $show_topic = 2;
	} // end if
	
	if($command=="DELETE" && $COM_ID){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID and PER_ID=$PER_ID ";	
		$db_dpis->send_cmd($cmd);
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $DEL_PERSON_TITLE$COM_TYPE_NM [ $COM_ID : $PER_ID ]");		
	} // end if

	// ค้นหาเพื่อแสดงผลข้อมูล
	/*if($_GET['UPD']) $UPD_PER = $_GET['UPD']; 
	else $UPD_PER=1; 
	*/
//.	if($add_only) $UPD_PER=$add_only;
 //  echo "phpinc [ $add_only ] : if(".$UPD_PER." || ".$VIEW_PER.") && ".trim($COM_ID)." && ".trim($PER_ID);
	if((($PAGE_AUTH["add"]=="Y"  && trim($CMD_SEQ_LAST)>0) || $UPD_PER || $VIEW_PER) && trim($COM_ID) && trim($CMD_SEQ)){	//\CMD_SEQ (from submit form)
		$CH_ADD="";	//เคลียร์ค่า เพื่อให้แสดงปุ่มถูกต้อง
		if($PAGE_AUTH["add"]=="Y" && trim($CMD_SEQ_LAST)){	//กรณีเพิ่มใหม่เอาข้อมูลก่อนหน้ามาแสดง เฉพาะวันที่แต่งตั้ง และประเภทความเคลื่อนไหว ($CMD_DATE/$MOV_CODE/$MOV_NAME)
			$CMD_SEQ_DTL = $CMD_SEQ_LAST;
		}else{
			$CMD_SEQ_DTL = $CMD_SEQ;
		}
		$cmd = "	select	CMD_SEQ, PER_ID, EN_CODE, CMD_DATE, CMD_POSITION, CMD_LEVEL, 
						CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_OLD_SALARY, 
						POS_ID, POEM_ID, LEVEL_NO, CMD_SALARY, PL_CODE_ASSIGN, PN_CODE_ASSIGN, 
						CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SPSALARY, CMD_PERCENT,
						PL_NAME_WORK, ORG_NAME_WORK,CMD_LEVEL_POS, CMD_SEQ_NO, PL_CODE, PM_CODE, 
						CMD_POS_NO_NAME, CMD_POS_NO, UPDATE_USER, UPDATE_DATE
						from		PER_COMDTL
						where	COM_ID=$COM_ID and CMD_SEQ=$CMD_SEQ_DTL ";
		$data_count = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		//echo $cmd;   
		///echo "<br>$data_count = $cmd<br>";
		$data = $db_dpis->get_array();
//		if($data_count > 0){		
			//**กรณีเพิ่มคนใหม่####
			//**จะยังไม่มีข้อมูลในตาราง PER_COMDTL (เช่น PER_ID และ อื่นๆ [PL_CODE_ASSIGN]/PN_CODE_ASSIGN/EP_CODE_ASSIGN])
			//**ไม่ให้ fill ค่าแทนค่าเซต page ที่ดึงมาจาก find_retire_comdtl_personal.html มันจะเป็นค่าว่าง เพราะมันไม่มีข้อมูลเหล่านี้ 
			//**่ถ้ามีข้อมูลคนนี้อยู่แล้วในตาราง PER_COMDTL เป็นคนที่ซ้ำกันให้แสดงข้อความบอกว่ามีคนนี้อยู่แล้ว (เพิ่มใหม่เท่านั้น)
			if($UPD_PER || $VIEW_PER){		//เฉพาะ ดู และแก้ไข
				//ค้นหาข้อมูลบุคคล
				$PER_ID = $data[PER_ID];
				$EN_CODE = trim($data[EN_CODE]);
				
				$cmd = " select 	PER_CARDNO, b.PN_NAME, PER_NAME, PER_SURNAME, PER_BIRTHDATE, PER_TYPE,
											PAY_ID, POS_ID, POEM_ID, POEMS_ID
								from 	PER_PERSONAL a, PER_PRENAME b 
								where trim(PER_ID)=$PER_ID and a.PN_CODE=b.PN_CODE ";	
				$db_dpis1->send_cmd($cmd);
				$data_dpis2 = $db_dpis1->get_array();
				$PER_CARDNO = trim($data_dpis2[PER_CARDNO]);	
				$PER_NAME = trim($data_dpis2[PN_NAME]) . trim($data_dpis2[PER_NAME]) ." ". trim($data_dpis2[PER_SURNAME]);
				$PER_BIRTHDATE = show_date_format($data_dpis2[PER_BIRTHDATE], 1);
				$PER_TYPE = trim($data_dpis2[PER_TYPE]);
				if($add_only){ $msg_dup="พบข้อมูล $PER_NAME<br>ในรายการเลื่อนขั้นเงินเดือนแล้ว !!!"; }
				//echo $RPT_N."=>".$PER_TYPE;
				if ($RPT_N && $PER_TYPE==1){
					$CMD_SALARY = trim($data[CMD_SALARY]); 
				}else{          ///แก้ส่วนเงินเดือนใหม่พนักงานราชการไม่แสดง จะแสดงเฉพาะข้าราชการ
					$CMD_SALARY = trim($data[CMD_SALARY]); 
				}
				
				if($DPISDB=="mysql"){
					$tmp_data = explode("|", trim($data[CMD_POSITION]));
				}else{
					$tmp_data = explode("\|", trim($data[CMD_POSITION]));
				}
				//ในกรณีที่มี CMD_PM_NAME
				if(is_array($tmp_data)){
					$CMD_POSITION = ($tmp_data[0])? "$tmp_data[0]" : "";
					$CMD_PM_NAME = $tmp_data[1];
				}else{
					$CMD_POSITION = $data[CMD_POSITION];
				}
				$CMD_POSPOEM_NO_NAME = trim($data[CMD_POS_NO_NAME]); 
				$CMD_POSPOEM_NO = trim($data[CMD_POS_NO]); 
				$CMD_LEVEL = $data[CMD_LEVEL]; 
				$CMD_LEVEL1 = $data[CMD_LEVEL_POS];
				$CMD_ORG1 = trim($data[CMD_ORG1]); 
				$CMD_ORG2 = trim($data[CMD_ORG2]); 
				$CMD_ORG3 = trim($data[CMD_ORG3]); 
				$CMD_ORG4 = trim($data[CMD_ORG4]); 
				$CMD_ORG5 = trim($data[CMD_ORG5]); 
				$CMD_OLD_SALARY = trim($data[CMD_OLD_SALARY]); 
				$LEVEL_NO = trim($data[LEVEL_NO]); 
				$CMD_SEQ_NO = trim($data[CMD_SEQ_NO]); 
				//ดึงข้อมูลจาก PER_COMDTL ในกรณีดู หรือแก้ไข
				$CMD_SALARY_value = trim($data[CMD_SALARY]); 
				$CMD_SPSALARY = trim($data[CMD_SPSALARY]); 
				$CMD_PERCENT = $data[CMD_PERCENT]; 
				$CMD_NOTE1 = trim($data[CMD_NOTE1]); 
				$CMD_NOTE2 = trim($data[CMD_NOTE2]); 
				$PL_NAME_WORK = trim($data[PL_NAME_WORK]); 
				$ORG_NAME_WORK = trim($data[ORG_NAME_WORK]); 
	
				$UPDATE_USER = $data[UPDATE_USER];
				$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
				$db->send_cmd($cmd);
				$data2 = $db->get_array();
				$SHOW_UPDATE_USER_DTL = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
				$SHOW_UPDATE_DATE_DTL = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

				$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$CMD_LEVEL' ";		
				$db_dpis1->send_cmd($cmd);
				$data_dpis1 = $db_dpis1->get_array();
				$CMD_LEVEL2 = trim($data_dpis1[LEVEL_NAME]);
				
				if(trim($CMD_LEVEL1)){	
					$cmd="select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$CMD_LEVEL1'";
					$db_dpis1->send_cmd($cmd);
					$data_dpis1 = $db_dpis1->get_array();
					$CMD_LEVEL3 = trim($data_dpis1[LEVEL_NAME]);
				}
				
				$cmd = " select EN_NAME from PER_EDUCNAME where trim(EN_CODE)='$EN_CODE' ";		
				$db_dpis1->send_cmd($cmd);
				$data_dpis1 = $db_dpis1->get_array();
				$EN_NAME = trim($data_dpis1[EN_NAME]);
			
				//  ===== ถ้าเป็นลูกจ้างประจำ SELECT ข้อมูลตำแหน่งจาก table PER_POS_NAME =====  TR_PER_TYPE=2
				if ($PER_TYPE == 2) {
					if(trim($data[POEM_ID])){	
						$POEM_ID = trim($data[POEM_ID]); 
					}else{ //ไม่มีจากตาราง PER_COMDTL เอาจาก PER_PERSONAL
						$POEM_ID = trim($data_dpis2[POEM_ID]); 
					}
					if(!trim($POEM_ID)){ $msg_error="ไม่สามารถดึงเงินเดือนที่เลื่อนได้ เพราะไม่มี POEM_ID !!!";	}
					
					$cmd = " 	select 	POEM_NO, a.PN_CODE, PN_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, DEPARTMENT_ID, b.PG_CODE 
									from 		PER_POS_EMP a, PER_POS_NAME b
									where 	POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE ";	
					$db_dpis1->send_cmd($cmd);			
					$data1 = $db_dpis1->get_array();						
					$PG_CODE = trim($data1[PG_CODE]);
				}
		
				if(!$RPT_N && ($PER_TYPE!=1 || $PER_TYPE!=3 || $PER_TYPE!=4)){	//พนักงานราชการ
					$cmd = "select LAYER_SALARY FROM PER_LAYER where LEVEL_NO like '$LEVEL_NO%' order by LAYER_SALARY ";			
					$count_tmp = $db_dpis1->send_cmd($cmd);
					//echo "1-> $cmd <br>";
					while ( $data_dpis1 = $db_dpis1->get_array() ) {
						$j++;
						$salary_text = number_format($data_dpis1[LAYER_SALARY], 2, '.', ',');
						$sel = ($CMD_SALARY_value == $data_dpis1[LAYER_SALARY])? "selected" : "";
						$list_layer_temp.= "<option value='$data_dpis1[LAYER_SALARY]' $sel>$salary_text</option>";
					}	// end while
				}	// end if 
				
				if(trim($PG_CODE)) {	//ลูกจ้างประจำ
					$cmd = " select LAYERE_SALARY FROM PER_LAYEREMP where PG_CODE = '$PG_CODE' order by LAYERE_SALARY ";			
					$count_tmp = $db_dpis1->send_cmd($cmd);
					//echo "2-> $cmd <br>";
					while ( $data_dpis1 = $db_dpis1->get_array() ) {
						$j++;
						$salary_text = number_format($data_dpis1[LAYERE_SALARY], 2, '.', ',');
						$sel = ($CMD_SALARY_value == $data_dpis1[LAYERE_SALARY])? "selected" : "";
						$list_layer_temp.= "<option value='$data_dpis1[LAYERE_SALARY]' $sel>$salary_text</option>";
					} // end while
				} // end if 
			} else {
				$CH_ADD="";	//เคลียร์ค่า เพื่อให้แสดงปุ่มถูกต้อง
				$UPD_PER="";		$VIEW_PER="";
		
				$CMD_SEQ_NO=$CMD_SEQ;
				$PER_ID="";			
				$PER_NAME = "";
				$PER_BIRTHDATE = "";
				$EN_CODE="";
				$EN_NAME="";
				$CMD_PM_NAME="";
				$CMD_POSITION="";
				$CMD_PM_CODE="";
				$CMD_PM_NAME="";
				$CMD_POSPOEM_NO = "";
				$CMD_POSPOEM_NO_NAME = "";
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
				$CMD_OLD_SALARY="";
				$CMD_PERCENT="";
				$PL_CODE="";
				$PN_CODE="";
				$EP_CODE="";
				$CMD_AC_NO="";
				$CMD_ACCOUNT="";
				$POS_ID="";
				$POEM_ID="";
				$POEMS_ID="";
				$LEVEL_NO="";
				$CMD_SALARY="";
				$PL_CODE_ASSIGN="";
				$PN_CODE_ASSIGN="";
				$EP_CODE_ASSIGN="";
				$CMD_NOTE1="";
				$CMD_NOTE2="";
				$PER_CARDNO="";
		
				$CMD_PM_NAME = "";
				$PL_PN_NAME_ASSIGN = "";
//				$MOV_CODE = "";
//				$MOV_NAME = "";
				$PL_NAME_WORK = "";
				$ORG_NAME_WORK = "";
				$CMD_ES_CODE = "";
				$CMD_ES_NAME = "";
				$ES_CODE = "";
				$ES_NAME = "";
				$POS_PM_CODE = "";
				$POS_PM_NAME = "";
//				$CMD_DATE2 = show_date_format($CMD_DATE2, 1);
				unset($SHOW_UPDATE_USER);
				unset($SHOW_UPDATE_DATE);
			}
			//##########################
			//แสดงทั้งหมด เพิ่ม ดู แก้ไข 
			//##########################
			$CMD_DATE = show_date_format($data[CMD_DATE], 1);
			$MOV_CODE = trim($data[MOV_CODE]); 

			$cmd = " select MOV_NAME from PER_MOVMENT where trim(MOV_CODE)='$MOV_CODE' ";		
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$MOV_NAME = trim($data_dpis1[MOV_NAME]);	
		} 	//end if(($UPD_PER || $VIEW_PER) && trim($COM_ID) && trim($PER_ID))
?>