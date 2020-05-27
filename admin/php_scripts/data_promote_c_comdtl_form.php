<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);		
	
	
	function list_per_level_promote_c ($name, $val) {
		global $db_list, $DPISDB, $TR_PER_TYPE, $RPT_N, $LEVEL_TITLE;
		$cmd = "	select LEVEL_NO, LEVEL_NAME FROM PER_LEVEL
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
			$tmp_name = trim($data_list[LEVEL_NAME]);
			$qm_arr[$tmp_dat] = $tmp_dat;
			$sel = (($tmp_dat) == trim($val))? "selected" : "";
			echo "<option value='$tmp_dat' $sel>". $tmp_name ."</option>";
		}
		echo "</select>";
		return $val;
		//echo "<pre>";		
		//print_r($data_list);
		//echo "</pre>";	
	}	
	
	// ค้นหาลำดับของ PER_COMDTL เพื่อแสดงในกรณีเพิ่มรายละเอียด
	if ( trim($COM_ID) && trim(!$PER_ID) ) {
		$cmd = " select max(CMD_SEQ) as max_id from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$CMD_SEQ = trim($data[max_id]) + 1;	
	}	
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	if(!$TR_GENDER) 		$TR_GENDER = 1;
	if(!$TR_PER_TYPE) 	$TR_PER_TYPE = 1;	
	
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
		}	// end if		
		
		$CMD_OLD_SALARY = str_replace(",", "", $CMD_OLD_SALARY) + 0;
		if($RPT_N && ($PER_TYPE==1 || $PER_TYPE==3 || $PER_TYPE==4)){
			$CMD_SALARY = $CMD_SALARY_KEYIN;
		}else{
			$CMD_SALARY = $CMD_SALARY_SELECT;
		} // end if
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
	} // end if
	
	if($command=="ADD"){
		$cmd = " insert into PER_COMDTL
							(	COM_ID, CMD_SEQ, PER_ID, CMD_EDUCATE, CMD_DATE, CMD_POSITION, CMD_LEVEL, 
								CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_OLD_SALARY,  
								POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, 
								PL_CODE_ASSIGN, PN_CODE_ASSIGN, EP_CODE_ASSIGN, 
								CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM,   
								PER_CARDNO, UPDATE_USER, UPDATE_DATE )
						 values
						 	(	$COM_ID, $CMD_SEQ, $PER_ID, $EN_CODE, '$CMD_DATE', '$CMD_POSPOEM_NO\|$CMD_POSITION', '$CMD_LEVEL', 
								'$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', $CMD_OLD_SALARY, 
								$POS_ID, $POEM_ID, $POEMS_ID, $LEVEL_NO, $CMD_SALARY, 0, 
								$PL_CODE_ASSIGN, $PN_CODE_ASSIGN, $EP_CODE_ASSIGN, 
								'$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', 0, 
								$PER_CARDNO, $SESS_USERID, '$UPDATE_DATE' )
						  ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูลข้าราชการ/ลูกจ้างแนบท้ายบัญชีคำสั่งย้าย [ $COM_ID : $PER_ID ]");
	} // end if

	if($command=="UPDATE" && $COM_ID){
		$cmd = " update PER_COMDTL set
								CMD_SEQ = $CMD_SEQ, 
								CMD_EDUCATE = $EN_CODE, 
								CMD_DATE = '$CMD_DATE', 
								CMD_POSITION = '$CMD_POSPOEM_NO\|$CMD_POSITION', 
								CMD_LEVEL = '$CMD_LEVEL', 
								CMD_ORG3 = '$CMD_ORG3', 
								CMD_ORG4 = '$CMD_ORG4', 
								CMD_ORG5 = '$CMD_ORG5', 
								CMD_OLD_SALARY = $CMD_OLD_SALARY, 
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
								CMD_SAL_CONFIRM = 0, 
								PER_CARDNO = $PER_CARDNO, 
								UPDATE_USER = $SESS_USERID, UPDATE_DATE = '$UPDATE_DATE'
						 where COM_ID=$COM_ID and PER_ID=$PER_ID	  ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูลข้าราชการ/ลูกจ้างแนบท้ายบัญชีคำสั่งย้าย [ $COM_ID : $PER_ID ]");
	} // end if
	
	if($command=="DELETE" && $COM_ID){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID and PER_ID=$PER_ID ";	
		$db_dpis->send_cmd($cmd);
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ลบข้อมูลข้าราชการ/ลูกจ้างแนบท้ายบัญชีคำสั่งย้าย [ $COM_ID : $PER_ID ]");		
	} // end if


	// ค้นหาเพื่อแสดงผลข้อมูล	
	if(($UPD || $VIEW) && trim($COM_ID) && trim($PER_ID)){
		$cmd = "	select	CMD_SEQ, PER_ID, CMD_EDUCATE, CMD_DATE, CMD_POSITION, CMD_LEVEL, 
							CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_OLD_SALARY, POS_ID, POEM_ID, POEMS_ID, 
							POS_ID, POEM_ID, LEVEL_NO, CMD_SALARY, PL_CODE_ASSIGN, PN_CODE_ASSIGN, 
							CMD_NOTE1, CMD_NOTE2, MOV_CODE 
				from		PER_COMDTL
				where	COM_ID=$COM_ID and PER_ID=$PER_ID
					   ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_ID = trim($data[PER_ID]);
		$CMD_SEQ = trim($data[CMD_SEQ]); 
		$EN_CODE = trim($data[CMD_EDUCATE]);

		if($DPISDB=="mysql"){
			$temp = explode("|", trim($data[CMD_POSITION]));
		}else{
			$temp = explode("\|", trim($data[CMD_POSITION]));
		}
		$CMD_POSPOEM_NO = $temp[0];
		$CMD_POSITION = $temp[1]; 
		$CMD_LEVEL = level_no_format($data[CMD_LEVEL]); 
		
		$cmd = "select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$CMD_LEVEL'";
		$db_dpis->send_cmd($cmd);
		$Level = $db_dpis->get_array();
		$CMD_LEVEL2=$Level[LEVEL_NAME];
		
		$CMD_ORG3 = trim($data[CMD_ORG3]); 
		$CMD_ORG4 = trim($data[CMD_ORG4]); 
		$CMD_ORG5 = trim($data[CMD_ORG5]); 
		$CMD_OLD_SALARY = number_format(trim($data[CMD_OLD_SALARY]), 2, '.', ','); 
		$LEVEL_NO = trim($data[LEVEL_NO]); 
		if ($RPT_N) 
			$CMD_SALARY = trim($data[CMD_SALARY]); 
		else
			$CMD_SALARY_value = trim($data[CMD_SALARY]); 
			$CMD_NOTE1 = trim($data[CMD_NOTE1]); 
			$CMD_NOTE2 = trim($data[CMD_NOTE2]); 
		
			$CMD_DATE = show_date_format($data[CMD_DATE], 1);
		
		$cmd = " select 	PER_CARDNO, b.PN_NAME, PER_NAME, PER_SURNAME, PER_BIRTHDATE, PER_TYPE 
					  	 from 		PER_PERSONAL a, PER_PRENAME b 
					  	 where 	PER_ID=$PER_ID and a.PN_CODE=b.PN_CODE ";	
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$PER_CARDNO = trim($data_dpis1[PER_CARDNO]);	
		$PER_NAME = trim($data_dpis1[PN_NAME]) . trim($data_dpis1[PER_NAME]) ." ". trim($data_dpis1[PER_SURNAME]);
		$PER_BIRTHDATE = show_date_format($data_dpis1[PER_BIRTHDATE], 1);
		$PER_TYPE = trim($data_dpis1[PER_TYPE]);

		// แสดง listbox เงินเดือนตามระดับ
		if( trim($LEVEL_NO) && !$RPT_N ) {
			$cmd = " select LAYER_SALARY FROM PER_LAYER where LEVEL_NO like '$LEVEL_NO%' order by LAYER_SALARY ";
			$count_tmp = $db_dpis1->send_cmd($cmd);
			while ( $data_dpis1 = $db_dpis1->get_array() ) {
				$j++;
				$salary_text = number_format($data_dpis1[LAYER_SALARY], 2, '.', ',');
				$sel = ($CMD_SALARY_value == $data_dpis1[LAYER_SALARY])? "selected" : "";
				$list_layer_temp.= "<option value='$data_dpis1[LAYER_SALARY]' $sel>$salary_text</option>";
			}		// end while
		}			// end if 
		
		//  ===== ถ้าเป็นข้าราชการ SELECT ข้อมูลตำแหน่งจาก table PER_LINE =====  TR_PER_TYPE=1
		if ($PER_TYPE == 1) {			
			$POS_ID = $POS_POEM_ID = trim($data[POS_ID]);
			$PL_PN_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]); 
			
			$cmd = " select PL_CODE, PL_NAME from PER_LINE where trim(PL_CODE) = '$PL_PN_CODE_ASSIGN' ";			
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$PL_PN_NAME_ASSIGN = trim($data1[PL_NAME]);
			
			$cmd = " 	select 	POS_NO, a.PL_CODE, PL_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, DEPARTMENT_ID 
							from 		PER_POSITION a, PER_LINE b 
							where 	POS_ID=$POS_POEM_ID and a.PL_CODE=b.PL_CODE";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$POS_POEM_NO = trim($data1[POS_NO]);
			$POS_POEM_NAME = trim($data1[PL_NAME]);
		}

		//  ===== ถ้าเป็นลูกจ้างประจำ SELECT ข้อมูลตำแหน่งจาก table PER_POS_NAME =====  TR_PER_TYPE=2
		if ($PER_TYPE == 2) {
			$POEM_ID = $POS_POEM_ID = trim($data[POEM_ID]); 
			$PL_PN_CODE_ASSIGN = trim($data[PN_CODE_ASSIGN]); 				
			
			$cmd = " select PN_CODE, PN_NAME from PER_POS_NAME where trim(PN_CODE) = '$PL_PN_CODE_ASSIGN' ";
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$PL_PN_NAME_ASSIGN = trim($data_dpis1[PN_NAME]);
			
			$cmd = " 	select 	POEM_NO, a.PN_CODE, PN_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, DEPARTMENT_ID 
							from 		PER_POS_EMP a, PER_POS_NAME b
							where 	POEM_ID=$POS_POEM_ID and a.PN_CODE=b.PN_CODE ";	
			$db_dpis1->send_cmd($cmd);			
			$data1 = $db_dpis1->get_array();						
			$POS_POEM_NO = trim($data1[POEM_NO]);
			$POS_POEM_NAME = trim($data1[PN_NAME]);
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
		//##########################
		//แสดงทั้งหมด เพิ่ม ดู แก้ไข 
		//##########################
		$MOV_CODE = trim($data[MOV_CODE]); 
		
		$DEPARTMENT_ID = trim($data1[DEPARTMENT_ID]);
		$DTLFORM_ORG_ID = trim($data1[ORG_ID]);		
		$DTLFORM_ORG_ID_1 = trim($data1[ORG_ID_1]);
		$DTLFORM_ORG_ID_2 = trim($data1[ORG_ID_2]);
		
		if ($DEPARTMENT_ID) $tmp_ORG_ID[] =  $DEPARTMENT_ID;
		if ($DTLFORM_ORG_ID)			$tmp_ORG_ID[] =  $DTLFORM_ORG_ID;
		if ($DTLFORM_ORG_ID_1)		$tmp_ORG_ID[] =  $DTLFORM_ORG_ID_1;
		if ($DTLFORM_ORG_ID_2)		$tmp_ORG_ID[] =  $DTLFORM_ORG_ID_2;
		$search_org_id = implode(", ", $tmp_ORG_ID);
		
		$POS_POEM_ORG1 = $POS_POEM_ORG2 = $POS_POEM_ORG3 = $POS_POEM_ORG4 = $POS_POEM_ORG5 = "";
		$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID IN ($search_org_id) ";
		$db_dpis->send_cmd($cmd);
		while ( $data1 = $db_dpis->get_array() ) {
			$POS_POEM_ORG2 = ($DEPARTMENT_ID == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$POS_POEM_ORG2";
			$POS_POEM_ORG3 = ($DTLFORM_ORG_ID == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$POS_POEM_ORG3";
			$POS_POEM_ORG4 = ($DTLFORM_ORG_ID_1 == trim($data1[ORG_ID]))? 	trim($data1[ORG_NAME]) : "$POS_POEM_ORG4";
			$POS_POEM_ORG5 = ($DTLFORM_ORG_ID_2 == trim($data1[ORG_ID]))? 	trim($data1[ORG_NAME]) : "$POS_POEM_ORG5";
		}	// while				
		
		$cmd = " select MOV_NAME from PER_MOVMENT where trim(MOV_CODE)='$MOV_CODE' ";		
		$db_dpis->send_cmd($cmd);
		$data1 = $db_dpis->get_array();
		$MOV_NAME = trim($data1[MOV_NAME]);		
		
		$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data1 = $db_dpis->get_array();
		$MINISTRY_ID = $data1[ORG_ID_REF];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data1 = $db_dpis->get_array();
		$POS_POEM_ORG1 = $data1[ORG_NAME];
	} 	// 	if($COM_ID)
?>