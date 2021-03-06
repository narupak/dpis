<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);		

	// �����ӴѺ�ͧ PER_COMDTL �����ʴ�㹡ó�������������´
	if ( trim($COM_ID) && trim(!$CMD_SEQ) ) {
		$cmd = " select max(CMD_SEQ) as max_id from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$CMD_SEQ_LAST = trim($data[max_id]);	// $CMD_SEQ �������ش
		$CMD_SEQ = $CMD_SEQ_LAST + 1;	
	}	
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	if(!$TR_GENDER) 	$TR_GENDER = 1;
	if(!$TR_PER_TYPE) 	$TR_PER_TYPE = 1;	
	if (!$PL_NAME_WORK) $PL_NAME_WORK = "�����Ҫ���";
	
	if($command=="ADD" || $command=="UPDATE"){
		$CMD_DATE = save_date($CMD_DATE); 
		$CMD_DATE2 = save_date($CMD_DATE2); 
		
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
		$CMD_NOTE1 = str_replace("'", "&rsquo;", $CMD_NOTE1);
		$CMD_NOTE2 = str_replace("'", "&rsquo;", $CMD_NOTE2);
		
		$EN_CODE = trim($EN_CODE)? "'".$EN_CODE."'" : "NULL";
		$LEVEL_NO = trim($CMD_LEVEL)? "'".$CMD_LEVEL."'" : "NULL";
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
		if ($CMD_ORG1) $CMD_ORG1 = "$CMD_ORG1\|$POS_ORG_ID1";
		if ($CMD_ORG2) $CMD_ORG2 = "$CMD_ORG2\|$POS_ORG_ID2";
		if ($CMD_ORG3) $CMD_ORG3 = "$CMD_ORG3\|$POS_ORG_ID3";
		if ($CMD_ORG4) $CMD_ORG4 = "$CMD_ORG4\|$POS_ORG_ID4";
		if ($CMD_ORG5) $CMD_ORG5 = "$CMD_ORG5\|$POS_ORG_ID5";
		if ($CMD_ORG6) $CMD_ORG6 = "$CMD_ORG6\|$POS_ORG_ID6";
		if ($CMD_ORG7) $CMD_ORG7 = "$CMD_ORG7\|$POS_ORG_ID7";
		if ($CMD_ORG8) $CMD_ORG8 = "$CMD_ORG8\|$POS_ORG_ID8";
		$cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, CMD_EDUCATE, CMD_DATE, 
				CMD_POSITION, CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, 
				CMD_ORG6, CMD_ORG7, CMD_ORG8, CMD_OLD_SALARY, PL_CODE, PN_CODE, EP_CODE, 
				CMD_AC_NO, CMD_ACCOUNT, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, 
				CMD_SPSALARY, PL_CODE_ASSIGN, PN_CODE_ASSIGN, EP_CODE_ASSIGN, 
				CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_DATE2, CMD_SAL_CONFIRM,   
				PER_CARDNO, UPDATE_USER, UPDATE_DATE, PL_NAME_WORK, ORG_NAME_WORK, 
				CMD_LEVEL_POS, CMD_POS_NO_NAME, CMD_POS_NO, CMD_ES_CODE, ES_CODE)
				values ($COM_ID, $CMD_SEQ, $PER_ID, $EN_CODE, '$CMD_DATE', '$CMD_POSITION', 
				'$CMD_LEVEL', '$CMD_ORG1', '$CMD_ORG2', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', 
				'$CMD_ORG6', '$CMD_ORG7', '$CMD_ORG8', 
				$CMD_OLD_SALARY, $PL_CODE, $PN_CODE, $EP_CODE, '$CMD_AC_NO', '$CMD_ACCOUNT', 
				$POS_ID, $POEM_ID, $POEMS_ID, $LEVEL_NO, $CMD_SALARY, 0, $PL_CODE_ASSIGN, 
				$PN_CODE_ASSIGN, $EP_CODE_ASSIGN, '$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', 
				'$CMD_DATE2',  0, $PER_CARDNO, $SESS_USERID, '$UPDATE_DATE', $PL_NAME_WORK, 
				$ORG_NAME_WORK, '$CMD_LEVEL1', '$CMD_POS_NO_NAME', '$CMD_POS_NO', '$CMD_ES_CODE', '$ES_CODE') ";
		$db_dpis->send_cmd($cmd);
		//echo "$cmd<br>";
		//$db_dpis->show_error();

		//---��˹�����ѧ������˹������������ �ӴѺ�Ѵ������������Ѵ� / ���������������ҧ�������͡����������
		$CH_ADD="";	//�������� ��������ʴ������١��ͧ
		$UPD=1;		//���������������ѧ������
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
		$CMD_SALARY="";
		$PL_CODE_ASSIGN="";
		$PN_CODE_ASSIGN="";
		$EP_CODE_ASSIGN="";
		$CMD_NOTE1="";
		$CMD_NOTE2="";
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
		$PL_PN_NAME_ASSIGN = "";
		$PL_NAME_WORK = $MOV_NAME;
		$ORG_NAME_WORK = "";
		$CMD_ES_CODE = "";
		$CMD_ES_NAME = "";
		$ES_CODE = "";
		$ES_NAME = "";
		$POS_PM_CODE = "";
		$POS_PM_NAME = "";
		$CMD_DATE = show_date_format(trim($CMD_DATE), 1);
		$CMD_DATE2 = show_date_format(trim($CMD_DATE2), 1);

//------------------------------------------------------------------------------------------------------------------------------------
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ���������Ţ���Ҫ���/�١��ҧṺ���ºѭ�դ���觨Ѵ��ŧ����ç���ҧ��ǹ�Ҫ������� [ $COM_ID : $PER_ID ]");

		$cmd = " select * from PER_COMDTL where COM_ID = $COM_ID ";
		$count_comdtl = $db_dpis->send_cmd($cmd);
		// ��������������� ���º���� ���觤�ҡ�Ѻ�������ѡ �������� mode ������ǵ���
		echo "<script>";
		echo "parent.refresh_opener('3<::>!<::>!<::>!<::>!<::><::>$count_comdtl<::><::>!<::>')";
		echo "</script>";
	} // end if

	if($command=="UPDATE" && $COM_ID){
		if ($CMD_ORG1) $CMD_ORG1 = "$CMD_ORG1\|$POS_ORG_ID1";
		if ($CMD_ORG2) $CMD_ORG2 = "$CMD_ORG2\|$POS_ORG_ID2";
		if ($CMD_ORG3) $CMD_ORG3 = "$CMD_ORG3\|$POS_ORG_ID3";
		if ($CMD_ORG4) $CMD_ORG4 = "$CMD_ORG4\|$POS_ORG_ID4";
		if ($CMD_ORG5) $CMD_ORG5 = "$CMD_ORG5\|$POS_ORG_ID5";
		if ($CMD_ORG6) $CMD_ORG6 = "$CMD_ORG6\|$POS_ORG_ID6";
		if ($CMD_ORG7) $CMD_ORG7 = "$CMD_ORG7\|$POS_ORG_ID7";
		if ($CMD_ORG8) $CMD_ORG8 = "$CMD_ORG8\|$POS_ORG_ID8";
		 $cmd = " update PER_COMDTL set
				PER_ID = $PER_ID, 
				CMD_EDUCATE = $EN_CODE, 
				CMD_DATE = '$CMD_DATE', 
				CMD_POSITION = '$CMD_POSITION',
				CMD_LEVEL = '$CMD_LEVEL', 
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
				PL_NAME_WORK = $PL_NAME_WORK, 
				ORG_NAME_WORK = $ORG_NAME_WORK, 
				UPDATE_USER = $SESS_USERID, 
				UPDATE_DATE = '$UPDATE_DATE',
				CMD_LEVEL_POS='$CMD_LEVEL1',
				CMD_POS_NO_NAME='$CMD_POS_NO_NAME',
				CMD_POS_NO='$CMD_POS_NO',
				CMD_ES_CODE='$CMD_ES_CODE',
				ES_CODE='$ES_CODE'
		 where COM_ID=$COM_ID and CMD_SEQ=$CMD_SEQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$UPD=1;
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ��䢢����Ţ���Ҫ���/�١��ҧṺ���ºѭ�դ���觨Ѵ��ŧ����ç���ҧ��ǹ�Ҫ������� [ $COM_ID : $PER_ID ]");
	} // end if
	
	if($command=="DELETE" && $COM_ID){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID and PER_ID=$PER_ID ";	
		$db_dpis->send_cmd($cmd);
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ź�����Ţ���Ҫ���/�١��ҧṺ���ºѭ�դ���觨Ѵ��ŧ����ç���ҧ��ǹ�Ҫ������� [ $COM_ID : $PER_ID ]");		
	} // end if

	// ���������ʴ��Ţ�����	
	if( (($PAGE_AUTH["add"] && trim($CMD_SEQ_LAST)>0) || $UPD || $VIEW) && trim($COM_ID) && trim($CMD_SEQ) ){
		$CH_ADD="";	//�������� ��������ʴ������١��ͧ
		if($PAGE_AUTH["add"] && trim($CMD_SEQ_LAST)){	//�ó�����������Ң����š�͹˹�����ʴ� ੾���ѹ����觵�� ��л�������������͹��� ($CMD_DATE/$MOV_CODE/$MOV_NAME)
			$CMD_SEQ = $CMD_SEQ_LAST;
		}
		$cmd = "	select	CMD_SEQ, PER_ID, CMD_EDUCATE, CMD_DATE, CMD_POSITION, CMD_LEVEL, 
							CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_ORG6, CMD_ORG7, 
							CMD_ORG8, CMD_OLD_SALARY, PL_CODE, PN_CODE, EP_CODE, CMD_AC_NO, 
							CMD_ACCOUNT, POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, 
							PL_CODE_ASSIGN, PN_CODE_ASSIGN, EP_CODE_ASSIGN, 
							CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_DATE2, PL_NAME_WORK, 
							ORG_NAME_WORK ,CMD_LEVEL_POS, CMD_POS_NO_NAME, CMD_POS_NO, CMD_ES_CODE, ES_CODE 
					from	PER_COMDTL
					where	COM_ID=$COM_ID and CMD_SEQ=$CMD_SEQ ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		if($UPD || $VIEW){		//੾�� �� ������
			if($DPISDB=="mysql"){
				$tmp_data = explode("|", trim($data[CMD_POSITION]));
			}else{
				$tmp_data = explode("\|", trim($data[CMD_POSITION]));
			}		
			//㹡óշ���� CMD_PM_NAME
			if(is_array($tmp_data)){
				$CMD_POSITION = $tmp_data[0];
				$CMD_PM_NAME = $tmp_data[1];
			}else{
				$CMD_POSITION = $data[CMD_POSITION];
			}
			$CMD_POSPOEM_NO_NAME = trim($data[CMD_POS_NO_NAME]); 
			$CMD_POSPOEM_NO = trim($data[CMD_POS_NO]); 
	
			if($DPISDB=="mysql"){
				$tmp_org = explode("|", trim($data[CMD_ORG1]));
			}else{
				$tmp_org = explode("\|", trim($data[CMD_ORG1]));
			}
			$CMD_ORG1 = trim($tmp_org[0]);
			$POS_ORG_ID1 = trim($tmp_org[1]);
			if ($POS_ORG_ID1) {
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$POS_ORG_ID1 ";		
				$db_dpis1->send_cmd($cmd);
				$data_dpis1 = $db_dpis1->get_array();
				$POS_POEM_ORG1 = trim($data_dpis1[ORG_NAME]);		
			}
	
			if($DPISDB=="mysql"){
				$tmp_org = explode("|", trim($data[CMD_ORG2]));
			}else{
				$tmp_org = explode("\|", trim($data[CMD_ORG2]));
			}
			$CMD_ORG2 = trim($tmp_org[0]);
			$POS_ORG_ID2 = trim($tmp_org[1]);
			if ($POS_ORG_ID2) {
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$POS_ORG_ID2 ";		
				$db_dpis1->send_cmd($cmd);
				$data_dpis1 = $db_dpis1->get_array();
				$POS_POEM_ORG2 = trim($data_dpis1[ORG_NAME]);		
			}
	
			if($DPISDB=="mysql"){
				$tmp_org = explode("|", trim($data[CMD_ORG3]));
			}else{
				$tmp_org = explode("\|", trim($data[CMD_ORG3]));
			}
			$CMD_ORG3 = trim($tmp_org[0]);
			$POS_ORG_ID3 = trim($tmp_org[1]);
			if ($POS_ORG_ID3) {
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$POS_ORG_ID3 ";		
				$db_dpis1->send_cmd($cmd);
				$data_dpis1 = $db_dpis1->get_array();
				$POS_POEM_ORG3 = trim($data_dpis1[ORG_NAME]);		
			}
	
			if($DPISDB=="mysql"){
				$tmp_org = explode("|", trim($data[CMD_ORG4]));
			}else{
				$tmp_org = explode("\|", trim($data[CMD_ORG4]));
			}
			$CMD_ORG4 = trim($tmp_org[0]);
			$POS_ORG_ID4 = trim($tmp_org[1]);
			if ($POS_ORG_ID4) {
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$POS_ORG_ID4 ";		
				$db_dpis1->send_cmd($cmd);
				$data_dpis1 = $db_dpis1->get_array();
				$POS_POEM_ORG4 = trim($data_dpis1[ORG_NAME]);		
			}
	
			if($DPISDB=="mysql"){
				$tmp_org = explode("|", trim($data[CMD_ORG5]));
			}else{
				$tmp_org = explode("\|", trim($data[CMD_ORG5]));
			}
			$CMD_ORG5 = trim($tmp_org[0]);
			$POS_ORG_ID5 = trim($tmp_org[1]);
			if ($POS_ORG_ID5) {
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$POS_ORG_ID5 ";		
				$db_dpis1->send_cmd($cmd);
				$data_dpis1 = $db_dpis1->get_array();
				$POS_POEM_ORG5 = trim($data_dpis1[ORG_NAME]);		
			}
	
			if($DPISDB=="mysql"){
				$tmp_org = explode("|", trim($data[CMD_ORG6]));
			}else{
				$tmp_org = explode("\|", trim($data[CMD_ORG6]));
			}
			$CMD_ORG6 = trim($tmp_org[0]);
			$POS_ORG_ID6 = trim($tmp_org[1]);
			if ($POS_ORG_ID6) {
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$POS_ORG_ID6 ";		
				$db_dpis1->send_cmd($cmd);
				$data_dpis1 = $db_dpis1->get_array();
				$POS_POEM_ORG6 = trim($data_dpis1[ORG_NAME]);		
			}
	
			if($DPISDB=="mysql"){
				$tmp_org = explode("|", trim($data[CMD_ORG7]));
			}else{
				$tmp_org = explode("\|", trim($data[CMD_ORG7]));
			}
			$CMD_ORG7 = trim($tmp_org[0]);
			$POS_ORG_ID7 = trim($tmp_org[1]);
			if ($POS_ORG_ID7) {
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$POS_ORG_ID7 ";		
				$db_dpis1->send_cmd($cmd);
				$data_dpis1 = $db_dpis1->get_array();
				$POS_POEM_ORG7 = trim($data_dpis1[ORG_NAME]);		
			}
	
			if($DPISDB=="mysql"){
				$tmp_org = explode("|", trim($data[CMD_ORG8]));
			}else{
				$tmp_org = explode("\|", trim($data[CMD_ORG8]));
			}
			$CMD_ORG8 = trim($tmp_org[0]);
			$POS_ORG_ID8 = trim($tmp_org[1]);
			if ($POS_ORG_ID8) {
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$POS_ORG_ID8 ";		
				$db_dpis1->send_cmd($cmd);
				$data_dpis1 = $db_dpis1->get_array();
				$POS_POEM_ORG8 = trim($data_dpis1[ORG_NAME]);		
			}
	
			$CMD_OLD_SALARY = number_format(trim($data[CMD_OLD_SALARY]), 2, '.', ','); 
			$CMD_SALARY = number_format(trim($data[CMD_SALARY]), 2, '.', ','); 
			$CMD_NOTE1 = trim($data[CMD_NOTE1]); 
			$CMD_NOTE2 = trim($data[CMD_NOTE2]); 
			$PL_NAME_WORK = trim($data[PL_NAME_WORK]); 
			$ORG_NAME_WORK = trim($data[ORG_NAME_WORK]); 
			$CMD_ES_CODE = trim($data[CMD_ES_CODE]); 
			$cmd = " select ES_NAME from PER_EMP_STATUS where ES_CODE='$CMD_ES_CODE' ";		
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$CMD_ES_NAME = trim($data_dpis1[ES_NAME]);		
	
			$ES_CODE = trim($data[ES_CODE]); 
			$cmd = " select ES_NAME from PER_EMP_STATUS where ES_CODE='$ES_CODE' ";		
			$db_dpis1->send_cmd($cmd);
			$data_dpis1 = $db_dpis1->get_array();
			$ES_NAME = trim($data_dpis1[ES_NAME]);		

			$CMD_LEVEL = level_no_format($data[CMD_LEVEL]); 
			$LEVEL_NO = trim($data[LEVEL_NO]); 
			if ($CMD_LEVEL) {
				$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '$CMD_LEVEL' ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$CMD_LEVEL2 = trim($data1[LEVEL_NAME]);
			}
			$CMD_LEVEL1 = level_no_format($data[CMD_LEVEL_POS]);
			if ($CMD_LEVEL1) {
				$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '$CMD_LEVEL1' ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$CMD_LEVEL3 = trim($data1[LEVEL_NAME]);
			}
	
			$CMD_DATE2 = show_date_format(trim($data[CMD_DATE2]), 1);
			
			$PER_ID = trim($data[PER_ID]);
			$cmd = " select 	PER_CARDNO, b.PN_NAME, PER_NAME, PER_SURNAME, PER_BIRTHDATE, PER_TYPE 
						  from 	PER_PERSONAL a, PER_PRENAME b 
						  where trim(PER_ID)=$PER_ID and a.PN_CODE=b.PN_CODE ";	
			$db_dpis1->send_cmd($cmd);
			$data_dpis2 = $db_dpis1->get_array();
			$PER_CARDNO = trim($data_dpis2[PER_CARDNO]);	
			$PER_NAME = trim($data_dpis2[PN_NAME]) . trim($data_dpis2[PER_NAME]) ." ". trim($data_dpis2[PER_SURNAME]);
			$PER_BIRTHDATE = show_date_format($data_dpis2[PER_BIRTHDATE], 1);
			
			$PER_TYPE = trim($data_dpis2[PER_TYPE]);
				
			$EN_CODE = trim($data[CMD_EDUCATE]);
		}else if($PAGE_AUTH["add"]){	 //end UPD / VIEW
			$CMD_SEQ = $CMD_SEQ + 1;				//����������ӴѺ����
		}
		//##########################
		//�ʴ������� ���� �� ��� 
		//##########################
		$CMD_DATE = show_date_format(trim($data[CMD_DATE]), 1);
		
		$MOV_CODE = trim($data[MOV_CODE]); 
		$cmd = " select MOV_NAME from PER_MOVMENT where trim(MOV_CODE)='$MOV_CODE' ";		
		$db_dpis1->send_cmd($cmd);
		$data_dpis1 = $db_dpis1->get_array();
		$MOV_NAME = trim($data_dpis1[MOV_NAME]);		
	} 	// 	if($COM_ID)	
?>