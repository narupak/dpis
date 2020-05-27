<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");	

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);		

        $debug=0;/*0=close,1=open*/
        
        
	function list_per_level_move_N ($name, $val, $PL_CODE) {
		global $db_list,$db_dpis, $DPISDB, $COM_PER_TYPE, $PL_PN_CODE_ASSIGN, $RPT_N, $LEVEL_TITLE;
		global $PAGE_AUTH,$UPD,$CH_ADD,$PER_ID, $POS_ID,$SESS_DEPARTMENT_NAME,$levelname;
		
		if($PAGE_AUTH["add"]=="Y" && !trim($PER_ID)){	$CH_ADD=1;	}		//ไม่ให้เปลี่ยนเป็นปุ่ม 'แก้ไข' ยังไม่ได้เลือก'ระดับ'ที่แต่งตั้ง และไม่ submit จึงยังไม่มี PER_ID
		$where = "";
		if ($COM_PER_TYPE==1) {
//			$cmd = "	select LEVEL_NO_MIN, LEVEL_NO_MAX FROM PER_LINE	where PL_CODE = '$PL_PN_CODE_ASSIGN' ";
            $cmd = "	select LEVEL_NO_MIN, LEVEL_NO_MAX FROM PER_CO_LEVEL a, PER_POSITION b where a.CL_NAME = b.CL_NAME and b.POS_ID=$POS_ID ";
			$count_data = $db_list->send_cmd($cmd);
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
			$db_dpis->send_cmd($cmd);
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
//			echo "<select name=\"$name\" class=\"selectbox\" onchange=\"document.all.PROCESS_IFRAME_1.src = 'find_move_N_comdtl_position.html?LEVEL_NO=' + this.value\">
			if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง") { 
				echo "<select name=\"$name\" id=\"$name\"  class=\"selectbox\" onChange=\"javascript:set_PL_NAME_WORK(document.getElementById('lname'+this.value).value,this.id,'$SESS_DEPARTMENT_NAME');\">";		//form1.submit(); setTextbox(this.value,'$SESS_DEPARTMENT_NAME');
			}else{	 //end if
				echo "<select name=\"$name\" id=\"$name\"  class=\"selectbox\" onChange=\"javascript:set_PL_NAME_WORK(document.getElementById('lname'+this.value).value,this.id,'$SESS_DEPARTMENT_NAME');\">";	//form1.submit();
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
	if ( trim($COM_ID) && trim(!$CMD_SEQ) ) {
		$cmd = " select max(CMD_SEQ) as max_id from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$CMD_SEQ_LAST = trim($data[max_id]);	// $CMD_SEQ ตัวล่าสุด
		$CMD_SEQ = $CMD_SEQ_LAST + 1;					//เพิ่มใหม่//(!$VIEW_PER && !$UPD_PER)
	}	
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
    if($command=="ADD" || $command=="UPDATE"){
        $CMD_DATE =  save_date($CMD_DATE);

        $POS_ID = $POEM_ID = $POEMS_ID = $POT_ID = "";
        $PL_CODE = $PL_CODE_ASSIGN = $PN_CODE = $PN_CODE_ASSIGN = $EP_CODE = $EP_CODE_ASSIGN = $TP_CODE = $TP_CODE_ASSIGN = "";
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
        } elseif ($PER_TYPE == 4) {
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
        $LEVEL_NO_PER = trim($CMD_LEVEL)? "'".$CMD_LEVEL."'" : "NULL";			//ระดับบุคคล (เดิม) -> PER_PERSONAL
        $LEVEL_NO_POS = trim($CMD_LEVEL1)? "'".$CMD_LEVEL1."'" : "NULL";		//ระดับตำแหน่ง (เดิม) -> PER_POSITION
        $LEVEL_NO = trim($LEVEL_NO)? "'".$LEVEL_NO."'" : "NULL";							//ระดับตำแหน่ง (ที่แต่งตั้งใหม่) -> PER_POSITION
        $PER_CARDNO = trim($PER_CARDNO)? "'".$PER_CARDNO."'" : "NULL";
        $POS_ID = trim($POS_ID)? $POS_ID : "NULL";
        $POEM_ID = trim($POEM_ID)? $POEM_ID : "NULL";
        $POEMS_ID = trim($POEMS_ID)? $POEMS_ID : "NULL";		
        $POT_ID = trim($POT_ID)? $POT_ID : "NULL";
        $PL_CODE = trim($PL_CODE)? "'".$PL_CODE."'" : "NULL";
        $PN_CODE = trim($PN_CODE)? "'".$PN_CODE."'" : "NULL";
        $EP_CODE = trim($EP_CODE)? "'".$EP_CODE."'" : "NULL";		
        $TP_CODE = trim($TP_CODE)? "'".$TP_CODE."'" : "NULL";		
        $PL_CODE_ASSIGN = trim($PL_CODE_ASSIGN)? "'".$PL_CODE_ASSIGN."'" : "NULL";
        $PN_CODE_ASSIGN = trim($PN_CODE_ASSIGN)? "'".$PN_CODE_ASSIGN."'" : "NULL";	
        $EP_CODE_ASSIGN = trim($EP_CODE_ASSIGN)? "'".$EP_CODE_ASSIGN."'" : "NULL";
        $TP_CODE_ASSIGN = trim($TP_CODE_ASSIGN)? "'".$TP_CODE_ASSIGN."'" : "NULL";
        $PL_NAME_WORK = trim($PL_NAME_WORK)? "".$PL_NAME_WORK."" : "NULL";		
        $ORG_NAME_WORK = trim($ORG_NAME_WORK)? "'".$ORG_NAME_WORK."'" : "NULL";

        if($POS_POEM_NO_NAME)	$CMD_POSITION .= "\|$POS_POEM_NO_NAME$POS_POEM_NO";
        else $CMD_POSITION = "$CMD_POSITION\|$POS_POEM_NO";

        if ($CMD_PM_NAME)$CMD_POSITION .= "\|$CMD_PM_NAME";

        /*เพิ่มเติม*/
        if($PM_NAME){
            $CMD_POSITION .= "\|$PM_NAME\|$PM_CODE";
        }

        $CL_NAME='NULL';
        if($CL_CODE){
            $CL_NAME="'".$CL_CODE."'";
        }
                
    } // end if
	
    if($command=="ADD"){
        $cmd = " select max(CMD_SEQ) as max_id from PER_COMDTL where COM_ID=$COM_ID ";
        $db_dpis->send_cmd($cmd);
        $data = $db_dpis->get_array();
        $data = array_change_key_case($data, CASE_LOWER);
        $CMD_SEQ_LAST = trim($data[max_id]);	// $CMD_SEQ ตัวล่าสุด
        $CMD_SEQ = $CMD_SEQ_LAST + 1;					//เพิ่มใหม่//(!$VIEW_PER && !$UPD_PER)

        /*เดิม*/
        /*$cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, CMD_EDUCATE, CMD_DATE, CMD_POSITION, 
                        CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_ORG6, CMD_ORG7, 
                        CMD_ORG8, CMD_OLD_SALARY, PL_CODE, PN_CODE, EP_CODE, TP_CODE, CMD_AC_NO, CMD_ACCOUNT, 
                        POS_ID, POEM_ID, POEMS_ID, POT_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, PL_CODE_ASSIGN, 
                        PN_CODE_ASSIGN, EP_CODE_ASSIGN, TP_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2, MOV_CODE, 
                        CMD_DATE2, CMD_SAL_CONFIRM, PER_CARDNO, UPDATE_USER, UPDATE_DATE, PL_NAME_WORK, 
                        ORG_NAME_WORK, CMD_LEVEL_POS,CMD_POS_NO_NAME, CMD_POS_NO,CL_NAME)
                values ($COM_ID, $CMD_SEQ, $PER_ID, $EN_CODE, '$CMD_DATE', '$CMD_POSITION', 
                    $LEVEL_NO_PER, '$POS_POEM_ORG1\|$MINISTRY_ID', '$POS_POEM_ORG2\|$DEPARTMENT_ID', '$CMD_ORG3\|$ORGPER_ID', 
                    '$CMD_ORG4\|$ORGPER_ID_1', '$CMD_ORG5\|$ORGPER_ID_2', '$CMD_ORG6\|$ORGPER_ID_3', '$CMD_ORG7\|$ORGPER_ID_4', 
                    '$CMD_ORG8\|$ORGPER_ID_5', $CMD_OLD_SALARY, $PL_CODE, $PN_CODE, $EP_CODE, $TP_CODE, '$CMD_AC_NO', '$CMD_ACCOUNT', 
                    $POS_ID, $POEM_ID, $POEMS_ID, $POT_ID, $LEVEL_NO, $CMD_SALARY, 0, $PL_CODE_ASSIGN, $PN_CODE_ASSIGN, 
                    $EP_CODE_ASSIGN, $TP_CODE_ASSIGN, '$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', '$CMD_DATE2',  0, 
                    $PER_CARDNO, $SESS_USERID, '$UPDATE_DATE', '$PL_NAME_WORK', $ORG_NAME_WORK,$LEVEL_NO_POS , 
                    '$CMD_POS_NO_NAME', '$CMD_POS_NO',$CL_NAME) ";*/
        $cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, CMD_EDUCATE, CMD_DATE, CMD_POSITION, 
                        CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_ORG6, CMD_ORG7, 
                        CMD_ORG8, CMD_OLD_SALARY, PL_CODE, PN_CODE, EP_CODE, TP_CODE, CMD_AC_NO, CMD_ACCOUNT, 
                        POS_ID, POEM_ID, POEMS_ID, POT_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, PL_CODE_ASSIGN, 
                        PN_CODE_ASSIGN, EP_CODE_ASSIGN, TP_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2, MOV_CODE, 
                        CMD_DATE2, CMD_SAL_CONFIRM, PER_CARDNO, UPDATE_USER, UPDATE_DATE, PL_NAME_WORK, 
                        ORG_NAME_WORK, CMD_LEVEL_POS,CMD_POS_NO_NAME, CMD_POS_NO,CL_NAME)
                values ($COM_ID, $CMD_SEQ, $PER_ID, $EN_CODE, '$CMD_DATE', '$CMD_POSITION', 
                    $LEVEL_NO_PER, '$CMD_ORG1\|$POS_POEM_ORG1_ID', '$CMD_ORG2\|$POS_POEM_ORG2_ID', '$CMD_ORG3\|$ORGPER_ID', 
                    '$CMD_ORG4\|$ORGPER_ID_1', '$CMD_ORG5\|$ORGPER_ID_2', '$CMD_ORG6\|$ORGPER_ID_3', '$CMD_ORG7\|$ORGPER_ID_4', 
                    '$CMD_ORG8\|$ORGPER_ID_5', $CMD_OLD_SALARY, $PL_CODE, $PN_CODE, $EP_CODE, $TP_CODE, '$CMD_AC_NO', '$CMD_ACCOUNT', 
                    $POS_ID, $POEM_ID, $POEMS_ID, $POT_ID, $LEVEL_NO, $CMD_SALARY, 0, $PL_CODE_ASSIGN, $PN_CODE_ASSIGN, 
                    $EP_CODE_ASSIGN, $TP_CODE_ASSIGN, '$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', '$CMD_DATE2',  0, 
                    $PER_CARDNO, $SESS_USERID, '$UPDATE_DATE', '$PL_NAME_WORK', $ORG_NAME_WORK,$LEVEL_NO_POS , 
                    '$CMD_POS_NO_NAME', '$CMD_POS_NO',$CL_NAME) ";
        $db_dpis->send_cmd($cmd); 
        if($debug==1){echo "<pre>$cmd";}
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
        $TP_CODE="";
        $CMD_AC_NO="";
        $CMD_ACCOUNT="";
        $POS_ID="";
        $POEM_ID="";
        $POEMS_ID="";
        $POT_ID="";
        $LEVEL_NO="";
        $LEVEL_NAME="";
        $CMD_SALARY="";
        $PL_CODE_ASSIGN="";
        $PN_CODE_ASSIGN="";
        $EP_CODE_ASSIGN="";
        $TP_CODE_ASSIGN="";
        $CMD_NOTE1="";
        $CMD_NOTE2="";
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
        $PL_NAME_WORK = "";
        $ORG_NAME_WORK = "";
        $CMD_ES_CODE = "";
        $CMD_ES_NAME = "";
        $ES_CODE = "";
        $ES_NAME = "";
        $CL_NAME="";
        $CL_CODE="";
                
        $CMD_DATE = show_date_format($CMD_DATE, 1);
        unset($SHOW_UPDATE_USER_DTL);
        unset($SHOW_UPDATE_DATE_DTL);

        insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $ADD_PERSON_TITLE$COM_TYPE_NM [ $COM_ID : $PER_ID ]");
    } // end if

	if($command=="UPDATE" && $COM_ID){
           
                    /*เดิม CMD_ORG1 =$POS_POEM_ORG1\|$MINISTRY_ID
                     * CMD_ORG2 = '$POS_POEM_ORG2\|$DEPARTMENT_ID', 
                     */
						 $cmd = " update PER_COMDTL set
								PER_ID = $PER_ID, 
								CMD_EDUCATE = $EN_CODE, 
								CMD_DATE = '$CMD_DATE', 
								CMD_POSITION = '$CMD_POSITION',
								CMD_LEVEL =$LEVEL_NO_PER, 
								LEVEL_NO  = $LEVEL_NO, 
								CMD_ORG1 = '$CMD_ORG1\|$POS_POEM_ORG1_ID', 
								CMD_ORG2 = '$CMD_ORG2\|$POS_POEM_ORG2_ID', 
								CMD_ORG3 = '$CMD_ORG3\|$ORGPER_ID', 
								CMD_ORG4 = '$CMD_ORG4\|$ORGPER_ID_1', 
								CMD_ORG5 = '$CMD_ORG5\|$ORGPER_ID_2',
								CMD_ORG6 = '$CMD_ORG6\|$ORGPER_ID_3', 
								CMD_ORG7 = '$CMD_ORG7\|$ORGPER_ID_4', 
								CMD_ORG8 = '$CMD_ORG8\|$ORGPER_ID_5',
								CMD_OLD_SALARY = $CMD_OLD_SALARY, 
								PL_CODE = $PL_CODE, 
								PN_CODE = $PN_CODE, 
								EP_CODE = $EP_CODE, 
								TP_CODE = $TP_CODE, 
								CMD_AC_NO = '$CMD_AC_NO', 
								CMD_ACCOUNT = '$CMD_ACCOUNT', 
								CMD_SALARY = $CMD_SALARY, 
								CMD_SPSALARY = 0, 
								PL_CODE_ASSIGN = $PL_CODE_ASSIGN, 
								PN_CODE_ASSIGN = $PN_CODE_ASSIGN, 
								EP_CODE_ASSIGN = $EP_CODE_ASSIGN, 
								TP_CODE_ASSIGN = $TP_CODE_ASSIGN, 
								CMD_NOTE1 = '$CMD_NOTE1', 
								CMD_NOTE2 = '$CMD_NOTE2', 
								MOV_CODE = '$MOV_CODE', 
								CMD_DATE2 = '$CMD_DATE2', 
								CMD_SAL_CONFIRM = 0, 
								PER_CARDNO = $PER_CARDNO, 
								PL_NAME_WORK = '$PL_NAME_WORK', 
								ORG_NAME_WORK = $ORG_NAME_WORK, 
								UPDATE_USER = $SESS_USERID, 
								UPDATE_DATE = '$UPDATE_DATE',
								CMD_LEVEL_POS=$LEVEL_NO_POS,
								CMD_POS_NO_NAME='$CMD_POS_NO_NAME',
								CMD_POS_NO='$CMD_POS_NO',
                                                                CL_NAME=$CL_NAME  
						 where COM_ID=$COM_ID and CMD_SEQ=$CMD_SEQ ";
		$db_dpis->send_cmd($cmd);
		//echo "<pre>".$cmd;
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $EDIT_PERSON_TITLE$COM_TYPE_NM [ $COM_ID : $PER_ID ]");
		
		$UPD_PER="";		$VIEW_PER="";
		// ให้ย้อนกลับไป หน้าที่ 2 
		$show_topic = 2;
		
		$LEVEL_NO = "";
		$PL_PN_CODE_ASSIGN = "";
		$PL_PN_NAME_ASSIGN = "";
		$PL_NAME_WORK = "";
                $CL_NAME="";
                $CL_CODE="";
	} // end if
	
	if($command=="DELETE" && $COM_ID){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID and CMD_SEQ=$CMD_SEQ ";	
		$db_dpis->send_cmd($cmd);
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > $DEL_PERSON_TITLE$COM_TYPE_NM [ $COM_ID : $PER_ID ]");		
	} // end if

	// ค้นหาเพื่อแสดงผลข้อมูล	
//echo $PAGE_AUTH['add']." && ".trim($CMD_SEQ_LAST)." || ".$UPD_PER." || ".$VIEW_PER." && ".trim($COM_ID)." && ".trim($CMD_SEQ)."<=".$_GET[CMD_SEQ];
	if((($PAGE_AUTH["add"]=="Y" && trim($CMD_SEQ_LAST)>0) || $UPD_PER || $VIEW_PER) && trim($COM_ID) && trim($CMD_SEQ)){
		$CH_ADD="";	//เคลียร์ค่า เพื่อให้แสดงปุ่มถูกต้อง
		if($PAGE_AUTH["add"]=="Y" && trim($CMD_SEQ_LAST)){	//กรณีเพิ่มใหม่เอาข้อมูลก่อนหน้ามาแสดง เฉพาะวันที่แต่งตั้ง และประเภทความเคลื่อนไหว ($CMD_DATE/$MOV_CODE/$MOV_NAME)
			$CMD_SEQ_DTL = $CMD_SEQ_LAST;
		}else{
			$CMD_SEQ_DTL = $CMD_SEQ;
		}
		$cmd = "	select	CMD_SEQ, PER_ID, CMD_EDUCATE, CMD_DATE, CMD_POSITION, CMD_LEVEL, 
							CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_ORG6, CMD_ORG7, 
							CMD_ORG8, CMD_OLD_SALARY, PL_CODE, PN_CODE, EP_CODE, TP_CODE, CMD_AC_NO, CMD_ACCOUNT, 
							POS_ID, POEM_ID, POEMS_ID, POT_ID, LEVEL_NO, CMD_SALARY, PL_CODE_ASSIGN, PN_CODE_ASSIGN, 
							EP_CODE_ASSIGN, TP_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_DATE2, PL_NAME_WORK, 
							ORG_NAME_WORK, CMD_LEVEL_POS , CMD_POS_NO_NAME, CMD_POS_NO, UPDATE_USER, UPDATE_DATE,CL_NAME
					from	PER_COMDTL
					where	COM_ID=$COM_ID and CMD_SEQ=$CMD_SEQ_DTL ";
		$db_dpis->send_cmd($cmd);
            if($debug==1){echo '<br><pre>'.$cmd;}
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		if($UPD_PER || $VIEW_PER){		//เฉพาะ ดู และแก้ไข
			$PER_ID = trim($data[PER_ID]);
			$EN_CODE = trim($data[CMD_EDUCATE]);
			$CMD_SEQ = trim($data[CMD_SEQ]); 
			
			if($DPISDB=="mysql"){
				$tmp_data = explode("|", trim($data[CMD_POSITION]));
			}else{
				$tmp_data = explode("\|", trim($data[CMD_POSITION]));
                                //print_r($tmp_data);
			}
			//print_r($tmp_data);
                        //echo '<br>cnt:'.count($tmp_data);
			//ในกรณีที่มี CMD_PM_NAME
			if(is_array($tmp_data)){
				$CMD_POSITION = ($tmp_data[0])? "$tmp_data[0]" : "";
				$POS_POEM_NO = $tmp_data[1];
                               
				if(count($tmp_data)==3 || count($tmp_data)==4){
					$POS_POEM_NO_NAME = $tmp_data[2];		
				}
				if(count($tmp_data)==3){
					$CMD_PM_NAME = $tmp_data[2];
                                        $CMD_PM_NAME2 = '';
                                        $PM_CODE2 = '';
				}else if(count($tmp_data)==4){
					$CMD_PM_NAME = '';
                                        $CMD_PM_NAME2 = $tmp_data[2];
                                        $PM_CODE2 = $tmp_data[3];
				}
                               
                                if(count($tmp_data)==5){/*เพิ่มเติม*/
                                    $CMD_PM_NAME = $tmp_data[2];
                                    $CMD_PM_NAME2 = $tmp_data[3];
                                    $PM_CODE2 = $tmp_data[4];
                                }
                                 
			}else{
				$CMD_POSITION = $data[CMD_POSITION];
			}
			$CMD_POSPOEM_NO_NAME = trim($data[CMD_POS_NO_NAME]); 	//BKK
			$CMD_POSPOEM_NO = trim($data[CMD_POS_NO]);
			
			if($DPISDB=="mysql"){
				$tmp_org1 = explode("|", trim($data[CMD_ORG1]));
			}else{
				$tmp_org1 = explode("\|", trim($data[CMD_ORG1]));
			}
			$CMD_ORG1 = trim($tmp_org1[0]);
			$POS_MINISTRY_ID = trim($tmp_org1[1]); //--MINISTRY_ID
			
			if($DPISDB=="mysql"){
				$tmp_org2 = explode("|", trim($data[CMD_ORG2]));
			}else{
				$tmp_org2 = explode("\|", trim($data[CMD_ORG2]));
			}
			$CMD_ORG2 = trim($tmp_org2[0]);
			$POS_DEPARTMENT_ID = trim($tmp_org2[1]); //--DEPARTMENT_ID
			
			//หา |\ ใน string CMD_ORG3
			if($DPISDB=="mysql"){
				$token_search = "|";
			}else{
				$token_search = "\|";
			}
//			if(substr($token_search,trim($data[CMD_ORG3]))){
//			}
				
			$tmp_org3 = explode($token_search, trim($data[CMD_ORG3]));
			$CMD_ORG3 = trim($tmp_org3[0]);
			$POS_ORG_ID = trim($tmp_org3[1]); //--ORG_ID
	
			if($DPISDB=="mysql"){
				$tmp_org4= explode("|", trim($data[CMD_ORG4]));
			}else{
				$tmp_org4= explode("\|", trim($data[CMD_ORG4]));
			}
			$CMD_ORG4 = trim($tmp_org4[0]);
			$POS_ORG_ID1 = trim($tmp_org4[1]); //--ORG_ID_1
	
			if($DPISDB=="mysql"){
				$tmp_org5 = explode("|", trim($data[CMD_ORG5]));
			}else{
				$tmp_org5 = explode("\|", trim($data[CMD_ORG5]));
			}
			$CMD_ORG5 = trim($tmp_org5[0]);
			$POS_ORG_ID2 = trim($tmp_org5[1]); //--ORG_ID_2
	
			if($DPISDB=="mysql"){
				$tmp_org6 = explode("|", trim($data[CMD_ORG6]));
			}else{
				$tmp_org6 = explode("\|", trim($data[CMD_ORG6]));
			}
			$CMD_ORG6 = trim($tmp_org6[0]);
			$POS_ORG_ID3 = trim($tmp_org6[1]); //--ORG_ID_3
	
			if($DPISDB=="mysql"){
				$tmp_org7= explode("|", trim($data[CMD_ORG7]));
			}else{
				$tmp_org7= explode("\|", trim($data[CMD_ORG7]));
			}
			$CMD_ORG7 = trim($tmp_org7[0]);
			$POS_ORG_ID4 = trim($tmp_org7[1]); //--ORG_ID_4
	
			if($DPISDB=="mysql"){
				$tmp_org8 = explode("|", trim($data[CMD_ORG8]));
			}else{
				$tmp_org8 = explode("\|", trim($data[CMD_ORG8]));
			}
			$CMD_ORG8 = trim($tmp_org8[0]);
			$POS_ORG_ID5 = trim($tmp_org8[1]); //--ORG_ID_5
			
			/*	echo "<br>1. ".$data[POS_ID]."+".$data[PL_CODE_ASSIGN]."<br>";
			echo "<br>2. ".$data[POEM_ID]."+".$data[PN_CODE_ASSIGN]."<br>";
			echo "<br>3. ".$data[POEMS_ID]."+".$data[EP_CODE_ASSIGN]."<br>";*/
			//==================================================	
			
			$CMD_OLD_SALARY = number_format(trim($data[CMD_OLD_SALARY]), 2, '.', ','); 
			$CMD_SALARY = number_format(trim($data[CMD_SALARY]), 2, '.', ','); 
			if($_POST[LEVEL_NO])	$LEVEL_NO=$_POST[LEVEL_NO];	
			if(!$_POST[LEVEL_NO])	$LEVEL_NO = trim($data[LEVEL_NO]); 
			if(!$_POST[PL_NAME_WORK])	$PL_NAME_WORK = trim($data[PL_NAME_WORK]); 
			$ORG_NAME_WORK = trim($data[ORG_NAME_WORK]); 
			$CMD_LEVEL = $data[CMD_LEVEL]; 
			$CMD_LEVEL1 = $data[CMD_LEVEL_POS];
			$CMD_NOTE1 = trim($data[CMD_NOTE1]); 
			$CMD_NOTE2 = trim($data[CMD_NOTE2]); 
			$CMD_DATE2 = show_date_format($data[CMD_DATE2], 1);
                        $CL_NAME = trim($data[CL_NAME]); 
			
			$UPDATE_USER = $data[UPDATE_USER];
			$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
			$db->send_cmd($cmd);
			$data2 = $db->get_array();
			$SHOW_UPDATE_USER_DTL = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
			$SHOW_UPDATE_DATE_DTL = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

			if ($CMD_LEVEL) {
				$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '$CMD_LEVEL' ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$CMD_LEVEL2 = trim($data1[LEVEL_NAME]);
			}
			
			if(trim($CMD_LEVEL1)){	
				$cmd="select LEVEL_NAME from PER_LEVEL where LEVEL_NO='$CMD_LEVEL1'";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$CMD_LEVEL3 = trim($data1[LEVEL_NAME]);
			}
			
			$cmd = " select 	PER_CARDNO, b.PN_NAME, PER_NAME, PER_SURNAME, PER_BIRTHDATE, PER_TYPE, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID 
						  from 	PER_PERSONAL a, PER_PRENAME b 
						  where trim(PER_ID)=$PER_ID and a.PN_CODE=b.PN_CODE ";	
			//echo $cmd;
			$db_dpis1->send_cmd($cmd);
			$data_dpis2 = $db_dpis1->get_array();
			$PER_CARDNO = trim($data_dpis2[PER_CARDNO]);	
			$PER_NAME = trim($data_dpis2[PN_NAME]) . trim($data_dpis2[PER_NAME]) ." ". trim($data_dpis2[PER_SURNAME]);
			$PER_BIRTHDATE = show_date_format($data_dpis2[PER_BIRTHDATE], 1);
			$PER_TYPE = trim($data_dpis2[PER_TYPE]);
			$POS_ID=trim($data_dpis2[POS_ID]);
			$POEM_ID=trim($data_dpis2[POEM_ID]);
			$POEMS_ID=trim($data_dpis2[POEMS_ID]);
			$POT_ID=trim($data_dpis2[POT_ID]);
			
			//  ===== ถ้าเป็นข้าราชการ SELECT ข้อมูลตำแหน่งจาก table PER_LINE =====  TR_PER_TYPE=1
			//!!!!!!=== PL_PN_CODE_ASSIGN / PL_PN_NAME_ASSIGNเวลา แก้ไข ไม่ให้มันดึงค่าเดิมจาก db มา แต่ให้เป็นค่าที่เกิดจากการเลือกใหม่ ก่อนจะกดปุ่ม แก้ไข ไป และ submit form ไปให้ list ของ LEVEL_NO
			if ($PER_TYPE == 1) {			
				if(!$_POST[PL_PN_CODE_ASSIGN]) $PL_PN_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]); 		
				
				$cmd = " select PL_CODE, PL_NAME from PER_LINE where trim(PL_CODE) = '$PL_PN_CODE_ASSIGN' ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				if(!$_POST[PL_PN_NAME_ASSIGN]) $PL_PN_NAME_ASSIGN = trim($data1[PL_NAME]);
			}
	
			//  ===== ถ้าเป็นลูกจ้างประจำ SELECT ข้อมูลตำแหน่งจาก table PER_POS_NAME =====  TR_PER_TYPE=2
			if ($PER_TYPE == 2) {
				if(!$_POST[PL_PN_CODE_ASSIGN]) $PL_PN_CODE_ASSIGN = trim($data[PN_CODE_ASSIGN]); 		
						
				$cmd = " select PN_CODE, PN_NAME from PER_POS_NAME where trim(PN_CODE) = '$PL_PN_CODE_ASSIGN' ";
				$db_dpis1->send_cmd($cmd);
				$data_dpis1 = $db_dpis1->get_array();
				if(!$_POST[PL_PN_NAME_ASSIGN]) $PL_PN_NAME_ASSIGN = trim($data_dpis1[PN_NAME]);
			}
			
			//  ===== ถ้าเป็นพนักงานราชการ SELECT ข้อมูลตำแหน่งจาก table PER_EMPSER_POS_NAME =====  TR_PER_TYPE=3
			if ($PER_TYPE == 3) {
				if(!$_POST[PL_PN_CODE_ASSIGN]) $PL_PN_CODE_ASSIGN = trim($data[EP_CODE_ASSIGN]); 
				
				$cmd = " select EP_CODE, EP_NAME from PER_EMPSER_POS_NAME 
						where trim(EP_CODE) IN ('$PL_PN_CODE', '$PL_PN_CODE_ASSIGN')";
				$db_dpis1->send_cmd($cmd);
				while ( $data_dpis1 = $db_dpis1->get_array() ) {
					$temp_id = trim($data_dpis1[EP_CODE]);
					$PL_PN_NAME = ($temp_id == $PL_PN_CODE)?  trim($data_dpis1[EP_NAME]) : $PL_PN_NAME;
					if(!$_POST[PL_PN_NAME_ASSIGN]) $PL_PN_NAME_ASSIGN = ($temp_id == $PL_PN_CODE_ASSIGN)?  trim($data_dpis1[EP_NAME]) : $PL_PN_NAME_ASSIGN;
				}
			}
	
			//  ===== ถ้าเป็นลูกจ้างชั่วคราว SELECT ข้อมูลตำแหน่งจาก table PER_TEMP_POS_NAME =====  TR_PER_TYPE=4
			if ($PER_TYPE == 4) {
				if(!$_POST[PL_PN_CODE_ASSIGN]) $PL_PN_CODE_ASSIGN = trim($data[TP_CODE_ASSIGN]); 
						
				$cmd = " select TP_CODE, TP_NAME from PER_TEMP_POS_NAME where trim(TP_CODE) = '$PL_PN_CODE_ASSIGN' ";
				$db_dpis1->send_cmd($cmd);
				$data_dpis1 = $db_dpis1->get_array();
				if(!$_POST[PL_PN_NAME_ASSIGN]) $PL_PN_NAME_ASSIGN = trim($data_dpis1[TP_NAME]);
			}
//		}else if($PAGE_AUTH["add"]){	 //end UPD / VIEW
//			$CMD_SEQ = $CMD_SEQ + 1;				//เพิ่มให้เป็นลำดับใหม่
		}
                /*ปรับเพิ่มเติม*/
                /*$CL_NAME='';
                $cmdlvl="   SELECT CL_NAME 
                            FROM PER_CO_LEVEL 
                            WHERE '$LEVEL_NO' BETWEEN LEVEL_NO_MIN AND LEVEL_NO_MAX AND LEVEL_NO_MIN='$LEVEL_NO' AND LEVEL_NO_MAX='$LEVEL_NO' ";
                //echo $cmdlvl;
                $db_dpis1->send_cmd($cmdlvl);
                $data_lvl = $db_dpis1->get_array();
                $CL_NAME = $data_lvl[CL_NAME];*/
                /*ปรับเพิ่มเติม*/
		//##########################
		//แสดงทั้งหมด เพิ่ม ดู แก้ไข 
		//##########################
		$CMD_DATE = show_date_format($data[CMD_DATE], 1);
		$MOV_CODE = trim($data[MOV_CODE]); 

		//---หา กอง / ต่ำ1 / ต่ำ 2
		//$DEPARTMENT_ID = trim($data1[DEPARTMENT_ID]);
		$ORGPER_ID = trim($POS_ORG_ID);
		$ORGPER_ID_1 = trim($POS_ORG_ID1);
		$ORGPER_ID_2 = trim($POS_ORG_ID2);
		$ORGPER_ID_3 = trim($POS_ORG_ID3);
		$ORGPER_ID_4 = trim($POS_ORG_ID4);
		$ORGPER_ID_5 = trim($POS_ORG_ID5);
		
		//if ($DEPARTMENT_ID) $tmp_ORG_ID[] =  $DEPARTMENT_ID;
		if ($ORGPER_ID)			$tmp_ORG_ID[] =  $ORGPER_ID;
		if ($ORGPER_ID_1)		$tmp_ORG_ID[] =  $ORGPER_ID_1;
		if ($ORGPER_ID_2)		$tmp_ORG_ID[] =  $ORGPER_ID_2;
		if ($ORGPER_ID_3)		$tmp_ORG_ID[] =  $ORGPER_ID_3;
		if ($ORGPER_ID_4)		$tmp_ORG_ID[] =  $ORGPER_ID_4;
		if ($ORGPER_ID_5)		$tmp_ORG_ID[] =  $ORGPER_ID_5;
		$search_org_id = implode(", ", $tmp_ORG_ID);
				
		//$POS_POEM_ORG1 = $POS_POEM_ORG2 = $POS_POEM_ORG3 = $POS_POEM_ORG4 = $POS_POEM_ORG5 = "";
		$POS_POEM_ORG3 = $POS_POEM_ORG4 = $POS_POEM_ORG5 = $POS_POEM_ORG6 = $POS_POEM_ORG7 = $POS_POEM_ORG8 = "";
		$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID IN ($search_org_id) ";
		$db_dpis->send_cmd($cmd);
		//echo "=> $cmd<br>";
		while ( $data1 = $db_dpis->get_array() ) {
			//$POS_POEM_ORG2 = ($DEPARTMENT_ID == trim($data1[ORG_ID]))?trim($data1[ORG_NAME]) : "$POS_POEM_ORG2";
			$POS_POEM_ORG3 = ($ORGPER_ID == trim($data1[ORG_ID]))?trim($data1[ORG_NAME]) : "$POS_POEM_ORG3";
			$POS_POEM_ORG4 = ($ORGPER_ID_1 == trim($data1[ORG_ID]))?trim($data1[ORG_NAME]) : "$POS_POEM_ORG4";
			$POS_POEM_ORG5 = ($ORGPER_ID_2 == trim($data1[ORG_ID]))?trim($data1[ORG_NAME]) : "$POS_POEM_ORG5";
			$POS_POEM_ORG6 = ($ORGPER_ID_3 == trim($data1[ORG_ID]))?trim($data1[ORG_NAME]) : "$POS_POEM_ORG6";
			$POS_POEM_ORG7 = ($ORGPER_ID_4 == trim($data1[ORG_ID]))?trim($data1[ORG_NAME]) : "$POS_POEM_ORG7";
			$POS_POEM_ORG8 = ($ORGPER_ID_5 == trim($data1[ORG_ID]))?trim($data1[ORG_NAME]) : "$POS_POEM_ORG8";
		}	// while
		
		$cmd = " select MOV_NAME from PER_MOVMENT where trim(MOV_CODE)='$MOV_CODE' ";		
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$MOV_NAME = trim($data_dpis1[MOV_NAME]);
			
		/*$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data1 = $db_dpis->get_array();
		//$MINISTRY_ID = $data1[ORG_ID_REF];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data1 = $db_dpis->get_array();
		$POS_POEM_ORG1 = $data1[ORG_NAME];*/		

/***
//!!!!!!===set ค่า ให้ ORG_NAME_WORK
if($POS_POEM_ORG8)	$ORG_NAME_WORK = $POS_POEM_ORG8;
if($POS_POEM_ORG7)	$ORG_NAME_WORK = $POS_POEM_ORG7." ".$ORG_NAME_WORK;
if($POS_POEM_ORG6)	$ORG_NAME_WORK = $POS_POEM_ORG6." ".$ORG_NAME_WORK;
if($POS_POEM_ORG5)	$ORG_NAME_WORK = $POS_POEM_ORG5." ".$ORG_NAME_WORK;
if($POS_POEM_ORG4)	$ORG_NAME_WORK = $POS_POEM_ORG4." ".$ORG_NAME_WORK;
if($POS_POEM_ORG3)	$ORG_NAME_WORK = $POS_POEM_ORG3." ".$ORG_NAME_WORK;
***/
	} 	// 	if($COM_ID)
?>