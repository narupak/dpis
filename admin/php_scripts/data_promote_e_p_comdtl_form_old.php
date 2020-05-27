<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);		

	function list_per_level_promote_e_p ($name, $val, $PL_CODE) {
		global $db_list,$db_dpis, $DPISDB, $COM_PER_TYPE, $RPT_N, $LEVEL_TITLE;
		global $PAGE_AUTH,$UPD,$CH_ADD,$PER_ID, $POS_ID,$UPD,$SESS_DEPARTMENT_NAME,$levelname;
		if($PAGE_AUTH["add"]=="Y" && !trim($PER_ID)){	$CH_ADD=1;	}		//ไม่ให้เปลี่ยนเป็นปุ่ม 'แก้ไข' ยังไม่ได้เลือก'ระดับ'ที่แต่งตั้ง และไม่ submit จึงยังไม่มี PER_ID
		$where = "";
		if ($COM_PER_TYPE==1) {
//			$cmd = "	select LEVEL_NO_MIN, LEVEL_NO_MAX FROM PER_LINE	where PL_CODE = '$PL_CODE' ";
            $cmd = "	select LEVEL_NO_MIN, LEVEL_NO_MAX FROM PER_CO_LEVEL a, PER_POSITION b where a.CL_NAME = b.CL_NAME and b.POS_ID=$POS_ID ";
			$count_data = $db_list->send_cmd($cmd);
//			echo "cmd=$cmd ($count_data)<br>";
			if ($count_data) {
				$data = $db_list->get_array();
				$LEVEL_NO_MIN = trim($data[LEVEL_NO_MIN]); 
				$LEVEL_NO_MAX = trim($data[LEVEL_NO_MAX]); 
				$where = "";
				if ($LEVEL_NO_MIN)
					$where .= " and LEVEL_NO >= '$LEVEL_NO_MIN' ";
				if ($LEVEL_NO_MAX)
					$where .= " and LEVEL_NO <= '$LEVEL_NO_MAX' ";
			}
		}

		$cmd = "	select LEVEL_NO, LEVEL_NAME , POSITION_LEVEL FROM PER_LEVEL
							where PER_TYPE = $COM_PER_TYPE AND LEVEL_ACTIVE = 1 $where
							order by PER_TYPE, LEVEL_SEQ_NO ";
		$db_list->send_cmd($cmd);
		//$db_list->show_error();
		if ($RPT_N){
//			$db_dpis->send_cmd($cmd);
/****
			//เซตช่อง ตำแหน่ง (ก.พ.7) : ใหม่ ตามระดับตำแหน่งที่เลือกใหม่
			echo "<script language='JavaScript'>\n";
			echo "var lname = new Array();\n";
			while ($data_ln = $db_dpis->get_array()) {
				$levno=trim($data_ln[LEVEL_NO]);
				$arrLEVEL=explode(" ",$data_ln[LEVEL_NAME]);						//ตัดประเภทออก
				$levname=trim($arrLEVEL[1]);
				echo "lname['".$levno."']='".$levname."';";
			}
			echo "</script>\n";
****/
//			echo "<select name=\"$name\" class=\"selectbox\" onchange=\"document.all.PROCESS_IFRAME_1.src = 'find_promote_e_p_comdtl_position.html?LEVEL_NO=' + this.value\">
			if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง") { 
				echo "<select name=\"$name\" id=\"$name\"  class=\"selectbox\" onChange=\"javascript:set_PL_NAME_WORK(document.getElementById('lname'+this.value).value,this.id,'$SESS_DEPARTMENT_NAME');\">";		//form1.submit();	setTextbox(this.value,'$SESS_DEPARTMENT_NAME');
			}else{	 //end if
				echo "<select name=\"$name\" id=\"$name\"  class=\"selectbox\" onChange=\"javascript:set_PL_NAME_WORK(document.getElementById('lname'+this.value).value,this.id,'$SESS_DEPARTMENT_NAME');\">";
			}
			echo "<option value=''>== ".$LEVEL_TITLE." ==</option>";
		}else{
			echo "<select name=\"$name\" id=\"$name\" class=\"selectbox\" onchange=\"set_PL_NAME_WORK(document.getElementById('lname'+this.value).value,this.id,'$SESS_DEPARTMENT_NAME');document.all.PROCESS_IFRAME_1.src = 'find_promote_c_comdtl_layer.html?LEVEL_NO=' + this.value\">
				<option value=''>== ".$LEVEL_TITLE." ==</option>";
		}
		while ($data_list = $db_list->get_array()) {
			//$data_list = array_change_key_case($data_list, CASE_LOWER);
			$tmp_dat = trim($data_list[LEVEL_NO]);
			$tmp_name = trim($data_list[LEVEL_NAME]);
			$tmp_pos_level[$tmp_dat] = trim($data_list[POSITION_LEVEL]);
			$qm_arr[$tmp_dat] = $tmp_dat;
			$sel = (($tmp_dat) == trim($val))? "selected" : "";
			echo "<option value='".$tmp_dat."' $sel>". $tmp_name ."</option>";	//'".$tmp_dat."[::]".$tmp_name."'
		}
		echo "</select>";
		foreach($tmp_pos_level as $key=>$value){
			echo "<input type=\"hidden\" id=\"lname$key\"  value=\"$value\">";
		}
		return $val;
	}	

	// ค้นหาลำดับของ PER_COMDTL เพื่อแสดงในกรณีเพิ่มรายละเอียด
	if($_GET[CMD_SEQ])		$CMD_SEQ = $_GET[CMD_SEQ];		//ดู หรือแก้ไข
	if ( trim($COM_ID) && !$CMD_SEQ ) {
		$cmd = " select max(CMD_SEQ) as max_id from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$CMD_SEQ_LAST = trim($data[max_id]);	// $CMD_SEQ ตัวล่าสุด
		$CMD_SEQ = $CMD_SEQ_LAST + 1;			//เพิ่มใหม่//(!$VIEW_PER && !$UPD_PER)
	}	

	$UPDATE_DATE = date("Y-m-d H:i:s");
	if(!$TR_GENDER) 		$TR_GENDER = 1;
	if(!$TR_PER_TYPE) 		$TR_PER_TYPE = 1;	

	if($command=="ADD" || $command=="UPDATE"){
		$CMD_DATE =  save_date($CMD_DATE);
		
		$POS_ID = $POEM_ID = $POEMS_ID = "";
		$PL_CODE = $PL_CODE_ASSIGN = $PN_CODE = $PN_CODE_ASSIGN = $EP_CODE = $EP_CODE_ASSIGN = "";
		if ($COM_PER_TYPE == 1) {
			$PL_CODE = trim($PL_PN_CODE);
			$PL_CODE_ASSIGN = trim($PL_PN_CODE_ASSIGN);
			$POS_ID = trim($POS_POEM_ID);
		} elseif ($COM_PER_TYPE == 2) {
			$PN_CODE = trim($PL_PN_CODE);
			$PN_CODE_ASSIGN = trim($PL_PN_CODE_ASSIGN);
			$POEM_ID = trim($POS_POEM_ID);
		} elseif ($COM_PER_TYPE == 3) {
			$EP_CODE = trim($PL_PN_CODE);
			$EP_CODE_ASSIGN = trim($PL_PN_CODE_ASSIGN);
			$POEMS_ID = trim($POS_POEM_ID);			
		}	elseif ($COM_PER_TYPE == 4) {
			$TP_CODE = trim($PL_PN_CODE);
			$TP_CODE_ASSIGN = trim($PL_PN_CODE_ASSIGN);
			$POT_ID = trim($POS_POEM_ID);			
		}	// end if	
		
		$CMD_POS_NO_NAME = trim($CMD_POSPOEM_NO_NAME);
		$CMD_POS_NO = trim($CMD_POSPOEM_NO);
		$CMD_OLD_SALARY = str_replace(",", "", $CMD_OLD_SALARY) + 0;
		if($RPT_N && ($COM_PER_TYPE==1 || $COM_PER_TYPE==3 || $COM_PER_TYPE==4)){
			$CMD_SALARY = $CMD_SALARY_KEYIN;
		}else{	// COM_PER_TYPE=2
			$CMD_SALARY = $CMD_SALARY_SELECT;
		} // end if
		$CMD_SALARY = str_replace(",", "", $CMD_SALARY) + 0;
		//$COM_PG_CODE_SALARY = trim($COM_PG_CODE_SALARY)? "'".$COM_PG_CODE_SALARY."'" : "NULL";	//อาจจะต้องเพิ่มฟิล์ดใหม่
		//$PG_CODE_SALARY =  trim($PG_CODE_SALARY)? "'".$PG_CODE_SALARY."'" : "NULL";	//อาจจะต้องเพิ่มฟิล์ดใหม่ ใน  PER_COMDTL เพราะมันไปดึงมาจาก PER_POS_EMP / PER_POS_NAME มันก็จะเป็นกลุ่ม เดิม และไม่อัพเดทเป็นค่าใหม่ที่เขาเลือกมา
		$CMD_NOTE1 = str_replace("'", "&rsquo;", $CMD_NOTE1);
		$CMD_NOTE2 = str_replace("'", "&rsquo;", $CMD_NOTE2);	
		
		$EN_CODE = trim($EN_CODE)? "'".$EN_CODE."'" : "NULL";
		$LEVEL_NO_PER = trim($CMD_LEVEL)? "'".$CMD_LEVEL."'" : "NULL";			//ระดับบุคคล (เดิม) -> PER_PERSONAL
		$LEVEL_NO_POS = trim($CMD_LEVEL1)? "'".$CMD_LEVEL1."'" : "NULL";		//ระดับตำแหน่ง (เดิม) -> PER_POSITION
		$LEVEL_NO = trim($LEVEL_NO)? "'".$LEVEL_NO."'" : "NULL";							//ระดับตำแหน่ง (ที่แต่งตั้งใหม่) -> PER_POSITION
		$PER_CARDNO = trim($PER_CARDNO)? "'".$PER_CARDNO."'" : "NULL";
		$sendPOS =  trim($sendPOS)? $sendPOS : "";			//sendPOS = เลขที่ตำแหน่ง POS_ID ใหม่ที่เลือกจากปุ่ม		
		$POS_ID = trim($POS_ID)? $POS_ID : "NULL";
		$POEM_ID = trim($POEM_ID)? $POEM_ID : "NULL";
		$POEMS_ID = trim($POEMS_ID)? $POEMS_ID : "NULL";		
		$PL_CODE = trim($PL_CODE)? "'".$PL_CODE."'" : "NULL";
		$PN_CODE = trim($PN_CODE)? "'".$PN_CODE."'" : "NULL";
		$EP_CODE = trim($EP_CODE)? "'".$EP_CODE."'" : "NULL";		
		$PL_CODE_ASSIGN = trim($PL_CODE_ASSIGN)? "'".$PL_CODE_ASSIGN."'" : "NULL";
		$PN_CODE_ASSIGN = trim($PN_CODE_ASSIGN)? "'".$PN_CODE_ASSIGN."'" : "NULL";	
		$EP_CODE_ASSIGN = trim($EP_CODE_ASSIGN)? "'".$EP_CODE_ASSIGN."'" : "NULL";				
		$PL_NAME_WORK = trim($PL_NAME_WORK)? "".$PL_NAME_WORK."" : "NULL";
		$PL_NAME_WORK = str_replace("::"," ",$PL_NAME_WORK);
		$ORG_NAME_WORK = trim($ORG_NAME_WORK)? "'".$ORG_NAME_WORK."'" : "NULL";		
		$CMD_NOW = trim($CMD_NOW)? "'".$CMD_NOW."'" : "NULL";		
		if ($CMD_PM_NAME) $CMD_POSITION = "$CMD_POSITION\|$CMD_PM_NAME";
	} // end if
	
	if($command=="ADD"){
		$cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, CMD_EDUCATE, CMD_DATE, CMD_POSITION, 
							CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_ORG6, CMD_ORG7, 
							CMD_ORG8, CMD_OLD_SALARY, PL_CODE, PN_CODE, EP_CODE, CMD_AC_NO, CMD_ACCOUNT, 
							POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, PL_CODE_ASSIGN, 
							PN_CODE_ASSIGN, EP_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM, 
							PER_CARDNO, UPDATE_USER, UPDATE_DATE, PL_NAME_WORK, ORG_NAME_WORK, 
							CMD_LEVEL_POS, CMD_ES_CODE, ES_CODE, PM_CODE ,CMD_POS_NO_NAME, CMD_POS_NO, CMD_NOW)
							values ($COM_ID, $CMD_SEQ, $PER_ID, $EN_CODE, '$CMD_DATE', '$CMD_POSITION', 
							$LEVEL_NO_PER, '$CMD_ORG1', '$CMD_ORG2', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', 
							'$CMD_ORG6', '$CMD_ORG7', '$CMD_ORG8', $CMD_OLD_SALARY, $PL_CODE, $PN_CODE, 
							$EP_CODE, '$CMD_AC_NO', '$CMD_ACCOUNT', $POS_ID, $POEM_ID, $POEMS_ID, 
							$LEVEL_NO, $CMD_SALARY, 0, $PL_CODE_ASSIGN, $PN_CODE_ASSIGN, $EP_CODE_ASSIGN, 
							'$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', 0, $PER_CARDNO, $SESS_USERID, '$UPDATE_DATE', 
							'$PL_NAME_WORK', $ORG_NAME_WORK, $LEVEL_NO_POS, '$CMD_ES_CODE', '$ES_CODE', 
							'$POS_PM_CODE', '$CMD_POS_NO_NAME', '$CMD_POS_NO', $CMD_NOW) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		//---กำหนดให้ยังคงอยู่หน้าเดิมและเพิ่ม ลำดับถัดไปเพื่อเพิ่มคนถัดไป / เคลียร์ค่าให้เป็นว่างเพื่อเลือกเพิ่มคนใหม่
		$CH_ADD="";	//เคลียร์ค่า เพื่อให้แสดงปุ่มถูกต้อง
		$UPD_PER="";		$VIEW_PER="";
		$CMD_SEQ = $CMD_SEQ+1;
		
		$PER_ID="";			
		$PER_NAME = "";
		$PER_BIRTHDATE = "";
		$EN_CODE="";
		$EN_NAME="";
//		$CMD_DATE="";
		$CMD_POSPOEM_NO="";
		$CMD_POSPOEM_NO_NAME = "";
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
//		$MOV_CODE="";			
//		$MOV_NAME = "";
		$CMD_DATE2="";
		$PER_CARDNO="";
		$CMD_NOW="";
		
		$CMD_POSPOEM_NO = "";
		$CMD_POSPOEM_NO_NAME ="";
		$POS_POEM_NO = "";
		$POS_POEM_NO_NAME = "";
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
		$PL_NAME_WORK = "";
		$ORG_NAME_WORK = "";
		$CMD_ES_CODE = "";
		$CMD_ES_NAME = "";
//		$ES_CODE = "";
//		$ES_NAME = "";
		$CMD_DATE = show_date_format($CMD_DATE, 1);
		unset($SHOW_UPDATE_USER_DTL);
		unset($SHOW_UPDATE_DATE_DTL);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $ADD_PERSON_TITLE$COM_TYPE_NM [ $COM_ID : $PER_ID ]");
	} // end if

	if($command=="UPDATE" && $COM_ID){
		$cmd = " update PER_COMDTL set
								PER_ID = $PER_ID, 
								CMD_EDUCATE = $EN_CODE, 
								CMD_DATE = '$CMD_DATE', 
								CMD_POSITION = '$CMD_POSITION', 
								CMD_LEVEL = $LEVEL_NO_PER, 
								CMD_ORG1 = '$CMD_ORG1', 
								CMD_ORG2 = '$CMD_ORG2', 
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
								CMD_SAL_CONFIRM = 0, 
								PER_CARDNO = $PER_CARDNO, 
								PL_NAME_WORK = '$PL_NAME_WORK', 
								ORG_NAME_WORK = $ORG_NAME_WORK, 
								CMD_NOW = $CMD_NOW, 
								UPDATE_USER = $SESS_USERID, 
								UPDATE_DATE = '$UPDATE_DATE',
								CMD_LEVEL_POS=$LEVEL_NO_POS,
								CMD_ES_CODE='$CMD_ES_CODE',
								ES_CODE='$ES_CODE',
								PM_CODE='$POS_PM_CODE',
								CMD_POS_NO_NAME='$CMD_POS_NO_NAME',
								CMD_POS_NO='$CMD_POS_NO'
						 where COM_ID=$COM_ID and CMD_SEQ = $CMD_SEQ ";
		$db_dpis->send_cmd($cmd);
		//echo "$cmd<br>";
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $EDIT_PERSON_TITLE$COM_TYPE_NM [ $COM_ID : $PER_ID ]");
		
		$UPD_PER="";		$VIEW_PER="";
		// ให้ย้อนกลับไป หน้าที่ 2 
		$show_topic = 2;	
		
		$LEVEL_NO = "";
		$PL_PN_CODE_ASSIGN = "";
		$PL_PN_NAME_ASSIGN = "";
		$PL_NAME_WORK = "";		
	} // end if
	
	if($command=="DELETE" && $COM_ID){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID and PER_ID=$PER_ID ";	
		$db_dpis->send_cmd($cmd);
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $DEL_PERSON_TITLE$COM_TYPE_NM [ $COM_ID : $PER_ID ]");		
	} // end if

	//---กรณีเพิ่มคนใหม่ ให้มันสร้าง option ให้กับ select ของอัตราเงินเดือน เพื่อสามารถเลือกได้
 	//echo $LEVEL_NO."[ $PG_CODE ] --".$RPT_N."--".$COM_PER_TYPE."===".$LEVEL_NO."---".$PG_CODE_SALARY."+++".$PG_CODE_SALARY_POS;
	if(trim($LEVEL_NO) && (!$RPT_N || ($COM_PER_TYPE==2 && $PG_CODE_SALARY))) {
		$list_layer_temp="";	$selectfield="";
		if($COM_PER_TYPE==2){
			$selectfield="LAYERE_SALARY";
			$cmd = " select $selectfield FROM PER_LAYEREMP where (PG_CODE='$PG_CODE_SALARY' AND LAYERE_ACTIVE=1) order by $selectfield ";
		}else if($COM_PER_TYPE==3){
			$selectfield="LAYER_SALARY";
			$cmd = " select $selectfield FROM PER_LAYER where (PER_TYPE=3 AND LEVEL_NO like '$LEVEL_NO%' AND LAYER_ACTIVE=1) order by $selectfield ";
		}
		$count_tmp = $db_dpis1->send_cmd($cmd);
//	$db_dpis1->show_error();
//echo $cmd;
		while ( $data_dpis1 = $db_dpis1->get_array() ) {
			$j++;
			$salary_text = number_format($data_dpis1[$selectfield], 2, '.', ',');
			$list_layer_temp.= "<option value='$data_dpis1[$selectfield]'>$salary_text</option>";
		}		// end while
	}			// end if 
	//echo $cmd."<<<";
	//-----------------------------------------------------------------------------
	
	// ค้นหาเพื่อแสดงผลข้อมูล	
//	echo $PAGE_AUTH['add']." && ".trim($CMD_SEQ_LAST)." || ".$UPD_PER." || ".$VIEW_PER." && ".trim($COM_ID)." && ".trim($CMD_SEQ)."<=".$_GET[CMD_SEQ];
	if((($PAGE_AUTH["add"]=="Y" && trim($CMD_SEQ_LAST)>0) || $UPD_PER || $VIEW_PER) && trim($COM_ID) && trim($CMD_SEQ)){
		$CH_ADD="";	//เคลียร์ค่า เพื่อให้แสดงปุ่มถูกต้อง
		if($PAGE_AUTH["add"]=="Y" && trim($CMD_SEQ_LAST)){	//กรณีเพิ่มใหม่เอาข้อมูลก่อนหน้ามาแสดง เฉพาะวันที่แต่งตั้ง และประเภทความเคลื่อนไหว ($CMD_DATE/$MOV_CODE/$MOV_NAME)
			$CMD_SEQ_DTL = $CMD_SEQ_LAST;
		}else{
			$CMD_SEQ_DTL = $CMD_SEQ;
		}
		$cmd = "	select	CMD_SEQ, PER_ID, CMD_EDUCATE, CMD_DATE, CMD_POSITION, CMD_LEVEL, 
							CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_ORG6, CMD_ORG7, 
							CMD_ORG8, CMD_OLD_SALARY, PL_CODE, PN_CODE, EP_CODE, CMD_AC_NO, CMD_ACCOUNT, 
							POS_ID, POEM_ID, LEVEL_NO, CMD_SALARY, PL_CODE_ASSIGN, PN_CODE_ASSIGN, 
							CMD_NOTE1, CMD_NOTE2, MOV_CODE, PL_NAME_WORK, ORG_NAME_WORK, 
							CMD_LEVEL_POS, CMD_ES_CODE, ES_CODE, PM_CODE, CMD_POS_NO_NAME, 
							CMD_POS_NO, CMD_NOW, UPDATE_USER, UPDATE_DATE
				from		PER_COMDTL
				where	COM_ID=$COM_ID and CMD_SEQ=$CMD_SEQ_DTL ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		if($UPD_PER || $VIEW_PER){		//เฉพาะ ดู และแก้ไข
			$PER_ID = trim($data[PER_ID]);
			$POS_PM_CODE = trim($data[PM_CODE]); 
			$EN_CODE = trim($data[CMD_EDUCATE]);
			$ES_CODE = trim($data[ES_CODE]); 

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
			$CMD_ORG6 = trim($data[CMD_ORG6]); 
			$CMD_ORG7 = trim($data[CMD_ORG7]); 
			$CMD_ORG8 = trim($data[CMD_ORG8]); 
			$CMD_OLD_SALARY = number_format($data[CMD_OLD_SALARY]); 
			$CMD_SALARY = number_format($data[CMD_SALARY]); 
			$LEVEL_NO = trim($data[LEVEL_NO]); 
			$CMD_LEVEL = trim($data[CMD_LEVEL]); 
			$CMD_LEVEL1 = trim($data[CMD_LEVEL_POS]);
			if($RPT_N && ($PER_TYPE==1 || $PER_TYPE==3 || $PER_TYPE==4)){
				$CMD_SALARY_KEYIN = $CMD_SALARY;
			}else{	// COM_PER_TYPE=2
				$CMD_SALARY_SELECT = $CMD_SALARY;
			} // end if
			$CMD_NOTE1 = trim($data[CMD_NOTE1]); 
			$CMD_NOTE2 = trim($data[CMD_NOTE2]); 
			$PL_NAME_WORK = trim($data[PL_NAME_WORK]); 
			if($_POST[PL_NAME_WORK])		$PL_NAME_WORK = trim($_POST[PL_NAME_WORK]);
			$PL_NAME_WORK = stripslashes($PL_NAME_WORK);
			$ORG_NAME_WORK = trim($data[ORG_NAME_WORK]); 
			$CMD_NOW = trim($data[CMD_NOW]); 
			$CMD_ES_CODE = trim($data[CMD_ES_CODE]); 

			$UPDATE_USER = $data[UPDATE_USER];
			$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
			$db->send_cmd($cmd);
			$data2 = $db->get_array();
			$SHOW_UPDATE_USER_DTL = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
			$SHOW_UPDATE_DATE_DTL = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
			
			$cmd = " select ES_NAME from PER_EMP_STATUS where ES_CODE='$CMD_ES_CODE' ";	
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$CMD_ES_NAME = trim($data_dpis1[ES_NAME]);		

			$cmd = " select ES_NAME from PER_EMP_STATUS where ES_CODE='$ES_CODE' ";		
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$ES_NAME = trim($data_dpis1[ES_NAME]);		

			$cmd = " select PM_NAME from PER_MGT where PM_CODE='$POS_PM_CODE' ";		
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$POS_PM_NAME = trim($data_dpis1[PM_NAME]);		

			$cmd = " select 	PER_CARDNO, b.PN_NAME, PER_NAME, PER_SURNAME, PER_BIRTHDATE, PER_TYPE 
						  from 	PER_PERSONAL a, PER_PRENAME b 
						  where trim(PER_ID)=$PER_ID and a.PN_CODE=b.PN_CODE ";	
			$db_dpis1->send_cmd($cmd);
			$data_dpis2 = $db_dpis1->get_array();
			$PER_CARDNO = trim($data_dpis2[PER_CARDNO]);	
			$PER_NAME = trim($data_dpis2[PN_NAME]) . trim($data_dpis2[PER_NAME]) ." ". trim($data_dpis2[PER_SURNAME]);
			$PER_BIRTHDATE = show_date_format($data_dpis2[PER_BIRTHDATE], 1);
			$COM_PER_TYPE = trim($data_dpis2[PER_TYPE]);

			if(trim($CMD_LEVEL)){
				$cmd=" select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$CMD_LEVEL' ";
				$db_dpis1->send_cmd($cmd);
				$data_dpis2 = $db_dpis1->get_array();
				$CMD_LEVEL2 = trim($data_dpis2[LEVEL_NAME]); 
			}
			
			if(trim($CMD_LEVEL1)){	
				$cmd=" select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$CMD_LEVEL1' ";
				$db_dpis1->send_cmd($cmd);
				$data_dpis2 = $db_dpis1->get_array();
				$CMD_LEVEL3 = trim($data_dpis2[LEVEL_NAME]); 
			}
			
			if(trim($LEVEL_NO)){	
				$cmd=" select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
				$db_dpis1->send_cmd($cmd);
				$data_dpis2 = $db_dpis1->get_array();
				$LEVEL_NAME = trim($data_dpis2[LEVEL_NAME]); 
			}

			//  ===== ถ้าเป็นข้าราชการ SELECT ข้อมูลตำแหน่งจาก table PER_LINE =====  TR_PER_TYPE=1
			if ($COM_PER_TYPE == 1) {			
				//$POS_POEM_ID = trim($data[POS_ID]);
				if($sendPOS)	 $POS_ID_SETNEW = trim($sendPOS);
				else $POS_ID_SETNEW = trim($data[POS_ID]);
				$POS_ID = $POS_POEM_ID = $POS_ID_SETNEW;
				$PL_PN_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]); 	
					
				$cmd = " select PL_CODE, PL_NAME from PER_LINE where trim(PL_CODE) = '$PL_PN_CODE_ASSIGN' ";			
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$PL_PN_NAME_ASSIGN = trim($data1[PL_NAME]);
				
				$cmd = " 	select 	POS_NO, POS_NO_NAME, a.PL_CODE, PL_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, DEPARTMENT_ID 
								from 		PER_POSITION a, PER_LINE b 
								where 	POS_ID=$POS_POEM_ID and a.PL_CODE=b.PL_CODE";
				$db_dpis1->send_cmd($cmd);	
				$data1 = $db_dpis1->get_array();
				$POS_POEM_NO = trim($data1[POS_NO]);
				$POS_POEM_NO_NAME = trim($data1[POS_NO_NAME]);
				$POS_POEM_NAME = trim($data1[PL_NAME]);
				$PL_CODE = trim($data1[PL_CODE]);
			}

			//  ===== ถ้าเป็นลูกจ้างประจำ SELECT ข้อมูลตำแหน่งจาก table PER_POS_NAME =====  TR_PER_TYPE=2
			if ($COM_PER_TYPE == 2) {
				//$POS_POEM_ID = trim($data[POEM_ID]); 
				if($sendPOS)	 $POS_ID_SETNEW = trim($sendPOS);
				else $POS_ID_SETNEW = trim($data[POEM_ID]);
				$POEM_ID = $POS_POEM_ID = $POS_ID_SETNEW;				
				$PL_PN_CODE_ASSIGN = trim($data[PN_CODE_ASSIGN]); 				
				
				$cmd = " select PN_CODE, PN_NAME from PER_POS_NAME where trim(PN_CODE) = '$PL_PN_CODE_ASSIGN' ";
				$db_dpis1->send_cmd($cmd);
				$data_dpis1 = $db_dpis1->get_array();
				$PL_PN_NAME_ASSIGN = trim($data_dpis1[PN_NAME]);

				$cmd = " 	select 	POEM_NO, POEM_NO_NAME, a.PN_CODE, PN_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, DEPARTMENT_ID,b.PG_CODE,PG_CODE_SALARY
								from 		PER_POS_EMP a, PER_POS_NAME b
								where 	POEM_ID=$POS_POEM_ID and a.PN_CODE=b.PN_CODE ";	
				$db_dpis1->send_cmd($cmd);			
				$data1 = $db_dpis1->get_array();						
				$POS_POEM_NO =  trim($data1[POEM_NO]);
				$POS_POEM_NO_NAME = trim($data1[POEM_NO_NAME]);
				$POS_POEM_NAME = trim($data1[PN_NAME]);
				$PG_CODE = trim($data1[PG_CODE]);
				$PG_CODE_SALARY = trim($data1[PG_CODE_SALARY]);
				if($_POST[PG_CODE_SALARY])	$PG_CODE_SALARY = $_POST[PG_CODE_SALARY];		// เพื่อเวลา update เลือกกลุ่มใหม่ให้ select ค่าใหม่ ถ้าไม่มีมันก็ดึงค่าเดิมใน DB ไป set ก็เลือกกลุ่มเดิมไม่เปลี่ยนแปลง
			}
			
			//  ===== ถ้าเป็นพนักงานราชการ SELECT ข้อมูลตำแหน่งจาก table PER_EMPSER_POS_NAME =====  TR_PER_TYPE=3
			if ($COM_PER_TYPE == 3) {
				//$POEMS_ID = $POS_POEM_ID = trim($data[POEMS_ID]); 
				if($sendPOS)	 $POS_ID_SETNEW = trim($sendPOS);
				else $POS_ID_SETNEW = trim($data[POEMS_ID]);
				$POEMS_ID = $POS_POEM_ID = $POS_ID_SETNEW;
				$PL_PN_CODE_ASSIGN = trim($data[EP_CODE_ASSIGN]); 
				
				$cmd = " select EP_CODE, EP_NAME from PER_EMPSER_POS_NAME 
						where trim(EP_CODE) IN ('$PL_PN_CODE', '$PL_PN_CODE_ASSIGN')";
				$db_dpis1->send_cmd($cmd);
				while ( $data_dpis1 = $db_dpis1->get_array() ) {
					$temp_id = trim($data_dpis1[EP_CODE]);
					$PL_PN_NAME = ($temp_id == $PL_PN_CODE)?  trim($data_dpis1[EP_NAME]) : $PL_PN_NAME;
					$PL_PN_NAME_ASSIGN = ($temp_id == $PL_PN_CODE_ASSIGN)?  trim($data_dpis1[EP_NAME]) : $PL_PN_NAME_ASSIGN;
				}
				
				$cmd = " 	select 	POEMS_NO, POEMS_NO_NAME, a.EP_CODE, EP_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, DEPARTMENT_ID 
						from 	PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
						where 	POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE ";	
				$db_dpis1->send_cmd($cmd);			
				$data1 = $db_dpis1->get_array();						
				$POS_POEM_NO = trim($data1[POEMS_NO]);
				$POS_POEM_NO_NAME = trim($data1[POEMS_NO_NAME]);
				$POS_POEM_NAME = trim($data1[EP_NAME]);
			}			
			
			//  ===== ถ้าเป็นลูกจ้างชั่วคราว SELECT ข้อมูลตำแหน่งจาก table PER_TEMP_POS_NAME =====  TR_PER_TYPE=4
			if ($COM_PER_TYPE == 4) {
				//$POT_ID = $POS_POEM_ID = trim($data[POT_ID]); 
				if($sendPOS)	 $POS_ID_SETNEW = trim($sendPOS);
				else $POS_ID_SETNEW = trim($data[POT_ID]);
				$POT_ID = $POS_POEM_ID = $POS_ID_SETNEW;
				$PL_PN_CODE_ASSIGN = trim($data[TP_CODE_ASSIGN]); 
			
				$cmd = " select TP_CODE, TP_NAME from PER_TEMP_POS_NAME 
						where trim(TP_CODE) IN ('$PL_PN_CODE', '$PL_PN_CODE_ASSIGN')";
				$db_dpis1->send_cmd($cmd);
				while ( $data_dpis1 = $db_dpis1->get_array() ) {
					$temp_id = trim($data_dpis1[TP_CODE]);
					$PL_PN_NAME = ($temp_id == $PL_PN_CODE)?  trim($data_dpis1[TP_NAME]) : $PL_PN_NAME;
					$PL_PN_NAME_ASSIGN = ($temp_id == $PL_PN_CODE_ASSIGN)?  trim($data_dpis1[TP_NAME]) : $PL_PN_NAME_ASSIGN;
				}	
				
				$cmd = " 	select 	POT_NO, POT_NO_NAME, a.TP_CODE, TP_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, a.DEPARTMENT_ID 
						from 	PER_POS_TEMP a, PER_TEMP_POS_NAME b
						where 	POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE ";	
				$db_dpis1->send_cmd($cmd);	
				//$db_dpis1->show_error();
				$data1 = $db_dpis1->get_array();						
				$POS_POEM_NO = trim($data1[POT_NO]);
				$POS_POEM_NO_NAME = trim($data1[POT_NO_NAME]);
				$POS_POEM_NAME = trim($data1[TP_NAME]);
			}
			$DEPARTMENT_ID = trim($data1[DEPARTMENT_ID]);
			$DTLFORM_ORG_ID = trim($data1[ORG_ID]);		
			$DTLFORM_ORG_ID_1 = trim($data1[ORG_ID_1]);
			$DTLFORM_ORG_ID_2 = trim($data1[ORG_ID_2]);
			$DTLFORM_ORG_ID_3 = trim($data1[ORG_ID_3]);		
			$DTLFORM_ORG_ID_4 = trim($data1[ORG_ID_4]);
			$DTLFORM_ORG_ID_5 = trim($data1[ORG_ID_5]);
			
			// === แสดงเงินเดือนในระดับ
		//	echo "<br>[::] ".trim($LEVEL_NO)." && ".$RPT_N." || ".$COM_PER_TYPE." - - $CMD_SALARY - $PG_CODE - $PG_CODE_SALARY";
			if(trim($LEVEL_NO) && (!$RPT_N || ($COM_PER_TYPE==2 && $PG_CODE_SALARY))) {
				$selectfield="";
				if($COM_PER_TYPE==2){
					$selectfield="LAYERE_SALARY";
					$cmd = " select $selectfield FROM PER_LAYEREMP where (PG_CODE='$PG_CODE_SALARY' AND LAYERE_ACTIVE=1) order by $selectfield ";
				}else if($COM_PER_TYPE==3){
					$selectfield="LAYER_SALARY";
					$cmd = " select $selectfield FROM PER_LAYER where (PER_TYPE=3 AND LEVEL_NO like '$LEVEL_NO%' AND LAYER_ACTIVE=1) order by $selectfield ";
				}
				$count_tmp = $db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				//echo $cmd;
				while ( $data_dpis1 = $db_dpis1->get_array() ) {
					$j++;
					$salary_text = number_format($data_dpis1[$selectfield], 2, '.', ',');
					$sel = ($CMD_SALARY == $data_dpis1[$selectfield])? "selected" : "";
					$list_layer_temp.= "<option value='$data_dpis1[$selectfield]' $sel>$salary_text</option>";
				}		// end while
			}			// end if 
		}
		//##########################
		//แสดงทั้งหมด เพิ่ม ดู แก้ไข 
		//##########################
		$CMD_DATE = show_date_format($data[CMD_DATE], 1);
		$MOV_CODE = trim($data[MOV_CODE]); 
/*
		$DEPARTMENT_ID = trim($data1[DEPARTMENT_ID]);
		$DTLFORM_ORG_ID = trim($data1[ORG_ID]);		
		$DTLFORM_ORG_ID_1 = trim($data1[ORG_ID_1]);
		$DTLFORM_ORG_ID_2 = trim($data1[ORG_ID_2]);
		$DTLFORM_ORG_ID_3 = trim($data1[ORG_ID_3]);		
		$DTLFORM_ORG_ID_4 = trim($data1[ORG_ID_4]);
		$DTLFORM_ORG_ID_5 = trim($data1[ORG_ID_5]);
*/		
		if ($DEPARTMENT_ID) $tmp_ORG_ID[] =  $DEPARTMENT_ID;
		if ($DTLFORM_ORG_ID)			$tmp_ORG_ID[] =  $DTLFORM_ORG_ID;
		if ($DTLFORM_ORG_ID_1)		$tmp_ORG_ID[] =  $DTLFORM_ORG_ID_1;
		if ($DTLFORM_ORG_ID_2)		$tmp_ORG_ID[] =  $DTLFORM_ORG_ID_2;
		if ($DTLFORM_ORG_ID_3)		$tmp_ORG_ID[] =  $DTLFORM_ORG_ID_3;
		if ($DTLFORM_ORG_ID_4)		$tmp_ORG_ID[] =  $DTLFORM_ORG_ID_4;
		if ($DTLFORM_ORG_ID_5)		$tmp_ORG_ID[] =  $DTLFORM_ORG_ID_5;
		$dtlform_search_org_id = implode(", ", $tmp_ORG_ID);
		
		$POS_POEM_ORG1 = $POS_POEM_ORG2 = $POS_POEM_ORG3 = $POS_POEM_ORG4 = 
		$POS_POEM_ORG5 = $POS_POEM_ORG6 = $POS_POEM_ORG7 = $POS_POEM_ORG8 = "";
		$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID IN ($dtlform_search_org_id) ";
		$db_dpis->send_cmd($cmd);
		while ( $data1 = $db_dpis->get_array() ) {
			$POS_POEM_ORG2 = ($DEPARTMENT_ID == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$POS_POEM_ORG2";
			$POS_POEM_ORG3 = ($DTLFORM_ORG_ID == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$POS_POEM_ORG3";
			$POS_POEM_ORG4 = ($DTLFORM_ORG_ID_1 == trim($data1[ORG_ID]))? 	trim($data1[ORG_NAME]) : "$POS_POEM_ORG4";
			$POS_POEM_ORG5 = ($DTLFORM_ORG_ID_2 == trim($data1[ORG_ID]))? 	trim($data1[ORG_NAME]) : "$POS_POEM_ORG5";
			$POS_POEM_ORG6 = ($DTLFORM_ORG_ID_3 == trim($data1[ORG_ID]))? 	trim($data1[ORG_NAME]) : "$POS_POEM_ORG6";
			$POS_POEM_ORG7 = ($DTLFORM_ORG_ID_4 == trim($data1[ORG_ID]))? 	trim($data1[ORG_NAME]) : "$POS_POEM_ORG7";
			$POS_POEM_ORG8 = ($DTLFORM_ORG_ID_5 == trim($data1[ORG_ID]))? 	trim($data1[ORG_NAME]) : "$POS_POEM_ORG8";
		}	// while				
		
		$cmd = " select MOV_NAME from PER_MOVMENT where trim(MOV_CODE)='$MOV_CODE' ";		
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$MOV_NAME = trim($data_dpis1[MOV_NAME]);
		
		$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data1 = $db_dpis->get_array();
		$MINISTRY_ID = $data1[ORG_ID_REF];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data1 = $db_dpis->get_array();
		$POS_POEM_ORG1 = $data1[ORG_NAME];
	} 	// 	if($COM_ID)	
	
	//--- set new upddate value to field
/**** // มันเอาไปอัพเดท รายละเอียดซ้ำกับคนที่แก้ไขก่อนหน้า วนแก้ไปแก้มา 2-3 รอบ	
	// print_r($_POST);
	if($_POST && $command!="ADD"){
		foreach($_POST as $key=>$value){
			if(trim($value))	$$key = trim($value);
		} //end foreach
	}
****/
?>