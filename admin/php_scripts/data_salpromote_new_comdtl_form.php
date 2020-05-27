<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);		

	function list_per_level_salpromote ($name, $val) {
		global $db_list, $DPISDB, $TR_PER_TYPE, $RPT_N, $LEVEL_TITLE;
		$cmd = "	select LEVEL_NO FROM PER_LEVEL
						where PER_TYPE = $TR_PER_TYPE AND LEVEL_ACTIVE = 1  
						order by PER_TYPE, LEVEL_SEQ_NO ";
		$db_list->send_cmd($cmd);
		//$db_list->show_error();
		if ($RPT_N) 
			echo "<select name=\"$name\" class=\"selectbox\" >
				<option value=''>== ".$LEVEL_TITLE." ==</option>";
		else
			echo "<select name=\"$name\" class=\"selectbox\" onchange=\"document.all.PROCESS_IFRAME_1.src = 'find_promote_c_comdtl_layer.html?LEVEL_NO=' + this.value\">
				<option value=''>== ".$LEVEL_TITLE." ==</option>";
		while ($data_list = $db_list->get_array()) {
			//$data_list = array_change_key_case($data_list, CASE_LOWER);
			$tmp_dat = trim($data_list[LEVEL_NO]);
			$qm_arr[$tmp_dat] = $tmp_dat;
			$sel = (($tmp_dat) == trim($val))? "selected" : "";
			echo "<option value='$tmp_dat' $sel>". level_no_format($tmp_dat) ."</option>";
		}
		echo "</select>";
		return $val;
	}	

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if(!$TR_GENDER) 		$TR_GENDER = 1;
	if(!$TR_PER_TYPE) 		$TR_PER_TYPE = 1;	
	
	// ค้นหาลำดับของ PER_COMDTL เพื่อแสดงในกรณีเพิ่มรายละเอียด
	if($_GET[CMD_SEQ])		$CMD_SEQ = $_GET[CMD_SEQ];		//ดู หรือแก้ไข
	if ( trim($COM_ID) && trim(!$CMD_SEQ) ) {
		$cmd = " select max(CMD_SEQ) as max_id from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$CMD_SEQ_LAST = trim($data[max_id]);		// $CMD_SEQ ตัวล่าสุด
		//echo $cmd.">>>>>>>".$CMD_SEQ_LAST;
		$CMD_SEQ = $CMD_SEQ_LAST+1;				//เพิ่มใหม่//(!$VIEW_PER && !$UPD_PER)
	}
	
	if($command=="ADD" || $command=="UPDATE"){
		$CMD_DATE =  save_date($CMD_DATE);
		
		$POS_ID = $POEM_ID = $POEMS_ID = "";
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
		$CMD_SPSALARY = str_replace(",", "", $CMD_SPSALARY) + 0;
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
		$cmd = "select COM_ID from PER_COMDTL where COM_ID = $COM_ID and CMD_SEQ = $CMD_SEQ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$repeat_COM_ID = $data[COM_ID];
		if(!$repeat_COM_ID){
			$cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, EN_CODE, CMD_DATE, CMD_POSITION, 
									CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_OLD_SALARY,  
									POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, 
									PL_CODE_ASSIGN, PN_CODE_ASSIGN, EP_CODE_ASSIGN, 
									CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM,   
									PER_CARDNO, UPDATE_USER, UPDATE_DATE, PL_NAME_WORK, ORG_NAME_WORK ,CMD_POS_NO_NAME, CMD_POS_NO)
							 values ($COM_ID, $CMD_SEQ, $PER_ID, $EN_CODE, '$CMD_DATE', '$CMD_POSITION', 
									'$CMD_LEVEL', '$CMD_ORG1', '$CMD_ORG2', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', $CMD_OLD_SALARY, 
									$POS_ID, $POEM_ID, $POEMS_ID, $LEVEL_NO, $CMD_SALARY, $CMD_SPSALARY, 
									$PL_CODE_ASSIGN, $PN_CODE_ASSIGN, $EP_CODE_ASSIGN, 
									'$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', 0, 
									$PER_CARDNO, $SESS_USERID, '$UPDATE_DATE', $PL_NAME_WORK, $ORG_NAME_WORK , '$CMD_POS_NO_NAME', '$CMD_POS_NO') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		}
		$UPD_PER=1;
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
								LEVEL_NO = $LEVEL_NO, 								
								CMD_SALARY = $CMD_SALARY, 
								CMD_SPSALARY = $CMD_SPSALARY, 
								PL_CODE_ASSIGN = '', 
								PN_CODE_ASSIGN = '', 
								CMD_NOTE1 = '$CMD_NOTE1', 
								CMD_NOTE2 = '$CMD_NOTE2', 
								MOV_CODE = '$MOV_CODE', 
								CMD_SAL_CONFIRM = 0, 
								PER_CARDNO = $PER_CARDNO, 
								PL_NAME_WORK = $PL_NAME_WORK, 
								ORG_NAME_WORK = $ORG_NAME_WORK, 
								UPDATE_USER = $SESS_USERID, 
								UPDATE_DATE = '$UPDATE_DATE',
								CMD_POS_NO_NAME='$CMD_POS_NO_NAME',
								CMD_POS_NO='$CMD_POS_NO'
						 where COM_ID=$COM_ID and CMD_SEQ=$CMD_SEQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

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

	//---กรณีเพิ่มคนใหม่ ให้มันสร้าง option ให้กับ select ของอัตราเงินเดือน เพื่อสามารถเลือกได้
	if(trim($LEVEL_NO) && (!$RPT_N || $PER_TYPE==2)) {
		$selectfield="";
		if($PER_TYPE==2){
			$selectfield="LAYERE_SALARY";
			$cmd = " select $selectfield FROM PER_LAYEREMP_NEW where (PG_CODE='$PG_CODE' AND LAYERE_ACTIVE=1) order by $selectfield ";
		}else if($PER_TYPE==3){
			$selectfield="LAYER_SALARY";
			$cmd = " select $selectfield FROM PER_LAYER_NEW where (LEVEL_NO like '$LEVEL_NO%' AND LAYER_ACTIVE=1) order by $selectfield ";
		}
		$count_tmp = $db_dpis1->send_cmd($cmd);
		//$db_dpis1->show_error();
		//echo $cmd;
		while ( $data_dpis1 = $db_dpis1->get_array() ) {
			$j++;
			$salary_text = number_format($data_dpis1[$selectfield], 2, '.', ',');
			$list_layer_temp.= "<option value='$data_dpis1[$selectfield]'>$salary_text</option>";
		}		// end while
	}			// end if 
	//-----------------------------------------------------------------------------

	// ค้นหาเพื่อแสดงผลข้อมูล	
	if( ($UPD_PER || $VIEW_PER) && trim($COM_ID) && trim($CMD_SEQ) ){
		$cmd = "	select	PER_ID, EN_CODE, CMD_DATE, CMD_POSITION, CMD_LEVEL, 
									CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_OLD_SALARY, 
									POS_ID, POEM_ID, LEVEL_NO, CMD_SALARY, PL_CODE_ASSIGN, PN_CODE_ASSIGN, 
									CMD_NOTE1, CMD_NOTE2, MOV_CODE, PL_NAME_WORK, ORG_NAME_WORK , 
									CMD_POS_NO_NAME, CMD_POS_NO, UPDATE_USER, UPDATE_DATE,CMD_SPSALARY
						from		PER_COMDTL
						where	COM_ID=$COM_ID and CMD_SEQ=$CMD_SEQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_ID = trim($data[PER_ID]);
		$EN_CODE = trim($data[EN_CODE]);
		$CMD_SPSALARY=trim($data[CMD_SPSALARY]);/*เดิมไม่มี เพิ่มเติมใหม่*/
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
		$CMD_ORG1 = trim($data[CMD_ORG1]); 
		$CMD_ORG2 = trim($data[CMD_ORG2]); 
		$CMD_ORG3 = trim($data[CMD_ORG3]); 
		$CMD_ORG4 = trim($data[CMD_ORG4]); 
		$CMD_ORG5 = trim($data[CMD_ORG5]); 
		$CMD_OLD_SALARY = number_format(trim($data[CMD_OLD_SALARY]), 2, '.', ','); 
		$CMD_SALARY = trim($data[CMD_SALARY]); 
		$PL_NAME_WORK = trim($data[PL_NAME_WORK]); 
		$ORG_NAME_WORK = trim($data[ORG_NAME_WORK]); 
		$LEVEL_NO = trim($data[LEVEL_NO]); 
		$CMD_LEVEL = trim($data[CMD_LEVEL]); 
		$CMD_NOTE1 = trim($data[CMD_NOTE1]); 
		$CMD_NOTE2 = trim($data[CMD_NOTE2]); 
		
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER_DTL = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE_DTL = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$CMD_LEVEL' ";
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$CMD_LEVEL_NAME = trim($data_dpis1[LEVEL_NAME]);

		$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";		
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$LEVEL_NAME = trim($data_dpis1[LEVEL_NAME]);
		
		$cmd = " select EN_NAME from PER_EDUCNAME where trim(EN_CODE)='$EN_CODE' ";		
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$EN_NAME = trim($data_dpis1[EN_NAME]);

		$cmd = " select PER_CARDNO, b.PN_NAME, PER_NAME, PER_SURNAME, PER_BIRTHDATE, PER_TYPE 
					  from 	PER_PERSONAL a, PER_PRENAME b 
					  where trim(PER_ID)=$PER_ID and a.PN_CODE=b.PN_CODE ";	
		$db_dpis1->send_cmd($cmd);
		$data_dpis2 = $db_dpis1->get_array();
		$PER_CARDNO = trim($data_dpis2[PER_CARDNO]);	
		$PER_NAME = trim($data_dpis2[PN_NAME]) . trim($data_dpis2[PER_NAME]) ." ". trim($data_dpis2[PER_SURNAME]);
		$PER_BIRTHDATE = show_date_format($data_dpis2[PER_BIRTHDATE], 1);
		$PER_TYPE = trim($data_dpis2[PER_TYPE]);

	//  ===== ถ้าเป็นข้าราชการ SELECT ข้อมูลตำแหน่งจาก table PER_LINE =====  TR_PER_TYPE=1
		if ($PER_TYPE == 1) {
			$POS_ID = trim($data[POS_ID]);
			$PL_PN_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]); 		
			
			$cmd = " select PL_CODE, PL_NAME from PER_LINE where trim(PL_CODE) = '$PL_PN_CODE_ASSIGN' ";			
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$PL_PN_NAME_ASSIGN = trim($data1[PL_NAME]);
			$POS_POEM_NO = $POS_POEM_NAME = "";
			if($POS_ID){
				$cmd = " 	select 	POS_NO, a.PL_CODE, PL_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, DEPARTMENT_ID 
								from 		PER_POSITION a, PER_LINE b 
								where 	POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE";
				$db_dpis1->send_cmd($cmd);	
				$data1 = $db_dpis1->get_array();
				$POS_POEM_NO = trim($data1[POS_NO]);
				$POS_POEM_NAME = trim($data1[PL_NAME]);
			}
		}

		//  ===== ถ้าเป็นลูกจ้างประจำ SELECT ข้อมูลตำแหน่งจาก table PER_POS_NAME =====  TR_PER_TYPE=2
		if ($PER_TYPE == 2) {
			$POEM_ID = trim($data[POEM_ID]); 
			$PL_PN_CODE_ASSIGN = trim($data[PN_CODE_ASSIGN]); 			
				
			$cmd = " select PN_CODE, PN_NAME from PER_POS_NAME where trim(PN_CODE) = '$PL_PN_CODE_ASSIGN' ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$PL_PN_NAME_ASSIGN = trim($data_dpis1[PN_NAME]);
			if($POEM_ID && $POEM_ID != "NULL"){
				$cmd = " 	select 	POEM_NO, a.PN_CODE, PN_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, DEPARTMENT_ID,b.PG_CODE 
								from 		PER_POS_EMP a, PER_POS_NAME b
								where 	POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE ";	
				$db_dpis1->send_cmd($cmd);			
				$data1 = $db_dpis1->get_array();						
				$POS_POEM_NO = trim($data1[POEM_NO]);
				$POS_POEM_NAME = trim($data1[PN_NAME]);
				$PG_CODE = trim($data1[PG_CODE]);
			}
		}
		
		//  ===== ถ้าเป็นพนักงานราชการ SELECT ข้อมูลตำแหน่งจาก table PER_EMPSER_POS_NAME =====  TR_PER_TYPE=3
		if ($PER_TYPE == 3) {
			$POEMS_ID = $POS_POEM_ID = trim($data[POEMS_ID]); 
			$PL_PN_CODE_ASSIGN = trim($data[EP_CODE_ASSIGN]); 
		
			$cmd = " select EP_CODE, EP_NAME from PER_EMPSER_POS_NAME 
					where trim(EP_CODE) IN ('$PL_PN_CODE', '$PL_PN_CODE_ASSIGN')";
			$db_dpis1->send_cmd($cmd);
			while ( $data_dpis1 = $db_dpis1->get_array() ) {
				$temp_id = trim($data_dpis1[EP_CODE]);
				$PL_PN_NAME = ($temp_id == $PL_PN_CODE)?  trim($data_dpis1[EP_NAME]) : $PL_PN_NAME;
				$PL_PN_NAME_ASSIGN = ($temp_id == $PL_PN_CODE_ASSIGN)?  trim($data_dpis1[EP_NAME]) : $PL_PN_NAME_ASSIGN;
			}	
			
			$cmd = " 	select 	POEMS_NO, a.EP_CODE, EP_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, DEPARTMENT_ID 
					from 	PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
					where 	POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE ";	
			$db_dpis1->send_cmd($cmd);			
			$data1 = $db_dpis1->get_array();						
			$POS_POEM_NO = trim($data1[POEMS_NO]);
			$POS_POEM_NAME = trim($data1[EP_NAME]);
		}	
		
		// แสดง listbox เงินเดือนตามระดับ
		//__echo "<br>[::] ".trim($LEVEL_NO)." && ".$RPT_N." || ".$PER_TYPE;
		if((!$RPT_N || $PER_TYPE==2)) {
			$selectfield="";
			if($PER_TYPE==2){
				$selectfield="LAYERE_SALARY";
				$cmd = " select $selectfield FROM PER_LAYEREMP_NEW where (PG_CODE='$PG_CODE' AND LAYERE_ACTIVE=1) order by $selectfield ";
			}else if($PER_TYPE==3){
				$selectfield="LAYER_SALARY";
				$cmd = " select $selectfield FROM PER_LAYER_NEW where (LEVEL_NO like '$LEVEL_NO%' AND LAYER_ACTIVE=1) order by $selectfield ";
			}
			$count_tmp = $db_dpis1->send_cmd($cmd);
			//___echo "$cmd<br>";
			while ( $data_dpis1 = $db_dpis1->get_array() ) {
				$j++;
				$salary_text = number_format($data_dpis1[$selectfield], 2, '.', ',');
				$sel = ($CMD_SALARY == $data_dpis1[$selectfield])? "selected" : "";
				$list_layer_temp.= "<option value='$data_dpis1[$selectfield]' $sel>$salary_text</option>";
			}		// end while
		}			// end if 	
		//##########################
		//แสดงทั้งหมด เพิ่ม ดู แก้ไข 
		//##########################
		$CMD_DATE = show_date_format($data[CMD_DATE], 1);
		$MOV_CODE = trim($data[MOV_CODE]); 
		
		$DEPARTMENT_ID = trim($data1[DEPARTMENT_ID]);
		$DTLFORM_ORG_ID = trim($data1[ORG_ID]);		
		$DTLFORM_ORG_ID_1 = trim($data1[ORG_ID_1]);
		$DTLFORM_ORG_ID_2 = trim($data1[ORG_ID_2]);
		$tmp_ORG_ID = "";
		if ($DEPARTMENT_ID)  		$tmp_ORG_ID[] =  $DEPARTMENT_ID;
		if ($DTLFORM_ORG_ID)  		$tmp_ORG_ID[] =  $DTLFORM_ORG_ID;
		if ($DTLFORM_ORG_ID_1)  		$tmp_ORG_ID[] =  $DTLFORM_ORG_ID_1;
	    if ($DTLFORM_ORG_ID_2)       $tmp_ORG_ID[] =  $DTLFORM_ORG_ID_2; 
		$POS_POEM_ORG1 = $POS_POEM_ORG2 = $POS_POEM_ORG3 = $POS_POEM_ORG4 = $POS_POEM_ORG5 = "";
		$search_org_id = implode(',',$tmp_ORG_ID);	
		if($search_org_id){ 
			$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID IN ($search_org_id) ";
			$db_dpis->send_cmd($cmd);
			while ( $data1 = $db_dpis->get_array() ) {
				$POS_POEM_ORG2 = ($DEPARTMENT_ID == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$POS_POEM_ORG2";
				$POS_POEM_ORG3 = ($DTLFORM_ORG_ID == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$POS_POEM_ORG3";
				$POS_POEM_ORG4 = ($DTLFORM_ORG_ID_1 == trim($data1[ORG_ID]))? 	trim($data1[ORG_NAME]) : "$POS_POEM_ORG4";
				$POS_POEM_ORG5 = ($DTLFORM_ORG_ID_2 == trim($data1[ORG_ID]))? 	trim($data1[ORG_NAME]) : "$POS_POEM_ORG5";
			}	// while		
		}		
		$cmd = " select MOV_NAME from PER_MOVMENT where trim(MOV_CODE)='$MOV_CODE' ";		
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$MOV_NAME = trim($data_dpis1[MOV_NAME]);
		$MINISTRY_ID = $POS_POEM_ORG1 = "";
		if($DEPARTMENT_ID){
			$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			$data1 = $db_dpis->get_array();
			$MINISTRY_ID = $data1[ORG_ID_REF];
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			$data1 = $db_dpis->get_array();
			$POS_POEM_ORG1 = $data1[ORG_NAME];
		}
	} else { // not ($UPD_PER || $VIEW_PER) && trim($COM_ID) && trim($CMD_SEQ)
		$CMD_POSITION = "";
		$CMD_PM_NAME = "";
		$CMD_POSPOEM_NO_NAME = ""; 
		$CMD_POSPOEM_NO = ""; 
		$CMD_ORG1 = ""; 
		$CMD_ORG2 = ""; 
		$CMD_ORG3 = ""; 
		$CMD_ORG4 = ""; 
		$CMD_ORG5 = ""; 
		$CMD_OLD_SALARY = ""; 
		$CMD_SALARY = ""; 
		$CMD_NOTE1 = ""; 
		$CMD_NOTE2 = ""; 
		$PL_NAME_WORK = ""; 
		$ORG_NAME_WORK = ""; 

		$CMD_LEVEL = ""; 
		$CMD_LEVEL_NAME = "";

		$LEVEL_NO = ""; 
		$LEVEL_NAME = "";

		$CMD_DATE = "";
		
		$PER_ID = "";
		$PER_CARDNO = "";	
		$PER_NAME = "";
		$PER_BIRTHDATE = "";
		
//		$PER_TYPE = "";
			
		$EN_CODE = "";
		$EN_NAME = "";

		$MOV_CODE = ""; 
		$MOV_NAME = "";		

		$PL_PN_CODE_ASSIGN = ""; 		
		$PL_PN_NAME_ASSIGN = "";
			
		$POS_ID = "";
		$POS_POEM_NO = "";
		$POS_POEM_NAME = "";

		$POEM_ID = ""; 
		$PG_CODE = "";
		$PL_PN_NAME = "";
		
		$POS_POEM_ORG1 = $POS_POEM_ORG2 = $POS_POEM_ORG3 = $POS_POEM_ORG4 = $POS_POEM_ORG5 = "";
		unset($SHOW_UPDATE_USER_DTL);
		unset($SHOW_UPDATE_DATE_DTL);
	} 	// 	if($COM_ID)
/*	
	// แสดงรายละเอียดบัญชี
	if (trim($COM_ID)) {
			$cmd = "	select	COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
											a.COM_TYPE, COM_CONFIRM, COM_STATUS, b.COM_DESC, a.DEPARTMENT_ID , a.ORG_ID
								from	PER_COMMAND a, PER_COMTYPE b
								where	COM_ID=$COM_ID  and a.COM_TYPE=b.COM_TYPE	";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			
			$COM_NO = trim($data[COM_NO]);
			$COM_NAME = trim($data[COM_NAME]);
			$COM_DATE = show_date_format($data[COM_DATE], 1);

			$COM_NOTE = trim($data[COM_NOTE]);
			$COM_PER_TYPE = trim($data[COM_PER_TYPE]);
			$COM_CONFIRM = trim($data[COM_CONFIRM]);
			$COM_STATUS = trim($data[COM_STATUS]);
			
			$COM_TYPE = trim($data[COM_TYPE]);
			$COM_TYPE_NAME = trim($data[COM_DESC]);

			$ORG_ID_DTL = $data[ORG_ID];
			$DEPARTMENT_ID = $data[DEPARTMENT_ID];
			
			if($DEPARTMENT_ID){	//กรณีที่เพิ่มของ กทม แบบไม่มี DEPARTMENT_ID ก็จะเอาค่า MINISTRY_NAME จาก session load_per_control
				$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$DEPARTMENT_NAME = $data[ORG_NAME];
				$MINISTRY_ID = $data[ORG_ID_REF];
		
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$MINISTRY_NAME = $data[ORG_NAME];
			}
			
			if($ORG_ID_DTL){
				$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_DTL ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$ORG_NAME_DTL = $data[ORG_NAME];
			}
	}   */
?>