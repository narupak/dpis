<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	if($PER_ID && $command != "CANCEL"){
		if($DPISDB=="odbc"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
							PER_PERSONAL.PER_TYPE, PER_PERSONAL.PER_CARDNO,PER_PERSONAL.DEPARTMENT_ID
 					from		PER_PERSONAL, PER_PRENAME
					where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and 
							PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		} // end if
		
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_CARDNO = (trim($data[PER_CARDNO]))?trim($data[PER_CARDNO]): "NULL";		
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
		
		if (!$ABI_ID) {
			$cmd = "	select  ABI_ID  from  PER_WORKFLOW_ABILITY
							where  PER_ID=$PER_ID and ABI_WF_STATUS!='04'
							order by ABI_ID DESC ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$ABI_ID = $data[ABI_ID];
			if ($ABI_ID) {
				$VIEW = 1;
			}
		}
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");

	if($db_type=="mysql") {
		$update_date = "NOW()";
		$update_by = "'$SESS_USERNAME'";
	} elseif($db_type=="mssql") {
		$update_date = "GETDATE()";
		$update_by = $SESS_USERID;
	} elseif($db_type=="oci8" || $db_type=="odbc") {
		$update_date = date("Y-m-d H:i:s");
		$update_date = "'$update_date'";
		$update_by = $SESS_USERID;
	}
	
	if($command=="ADD" && $PER_ID){
		$cmd2="select AL_CODE from PER_WORKFLOW_ABILITY  where PER_ID=$PER_ID and AL_CODE='$AL_CODE' and ABI_DESC='$ABI_DESC' ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("�������ö������������ ���ͧ�ҡ�س�кش�ҹ��������ö�������Ф�������ö����ɫ�� !!!");
				-->   </script>	<? 
		} else {	  
			$cmd = " select max(ABI_ID) as max_id from PER_WORKFLOW_ABILITY ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$ABI_ID = $data[max_id] + 1;
		
			$ABI_DESC = trim($ABI_DESC);
			$cmd = " insert into PER_WORKFLOW_ABILITY (ABI_ID, PER_ID, AL_CODE, ABI_DESC, ABI_REMARK, PER_CARDNO, UPDATE_USER, UPDATE_DATE, ABI_WF_STATUS )
							values ($ABI_ID, $PER_ID, '$AL_CODE', '$ABI_DESC', '$ABI_REMARK', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE', '01' )   ";
			$db_dpis->send_cmd($cmd);

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��������ѵԤ�������ö����� [$PER_ID : $ABI_ID : $AL_CODE]");
		} // end if
	}

	if($command=="UPDATE" && $PER_ID && $ABI_ID){
		$cmd2="select AL_CODE from PER_WORKFLOW_ABILITY  where PER_ID=$PER_ID and ABI_ID!=$ABI_ID and AL_CODE='$AL_CODE' and ABI_DESC='$ABI_DESC' ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("�������ö��䢢������� ���ͧ�ҡ�س�кش�ҹ��������ö�������Ф�������ö����ɫ�� !!!");
				-->   </script>	<? 
		} else {	  
			$cmd = " UPDATE PER_WORKFLOW_ABILITY SET
							AL_CODE='$AL_CODE', 
							ABI_DESC='$ABI_DESC', 
							ABI_REMARK='$ABI_REMARK', 
							PER_CARDNO='$PER_CARDNO', 
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'
						WHERE ABI_ID=$ABI_ID ";
			$db_dpis->send_cmd($cmd);
		
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢻���ѵԤ�������ö����� [$PER_ID : $ABI_ID : $AL_CODE]");
		} // end if
	}
	
	if($command=="DELETE" && $PER_ID && $ABI_ID){
		$cmd = " select AL_CODE from PER_WORKFLOW_ABILITY where ABI_ID=$ABI_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$AL_CODE = $data[AL_CODE];
		
		$cmd = " delete from PER_WORKFLOW_ABILITY where ABI_ID=$ABI_ID ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź����ѵԤ�������ö����� [$PER_ID : $ABI_ID : $AL_CODE]");
	} // end if

//  UPDATE ABI_WF_STATUS
	$updtype=explode("-",$command);
	$F_REFRESH = "";
	if($updtype[0]=="UPD" && $PER_ID && $ABI_ID){
		$ABI_WF_STATUS = $updtype[1];
		$cmd2="select * from PER_WORKFLOW_ABILITY where PER_ID=$PER_ID and ABI_ID=$ABI_ID";
		$count=$db_dpis->send_cmd($cmd2);
//		$db_dpis->show_error(); echo "<hr><br>";
		if($count) { 
			$cmd = " 	update PER_WORKFLOW_ABILITY set
								ABI_WF_STATUS='$updtype[1]', 
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE'
							where PER_ID=$PER_ID and ABI_ID=$ABI_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$F_REFRESH = "1";
		
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢻���ѵԡ���֡�� [$PER_ID : $PER_NAME : $EN_CODE]");
			
			if ($ABI_WF_STATUS=="04") {
				$command ="COMMAND"; 	// �ó� 04 ͹��ѵ� - �����͹��������������ԧ ��ѧ�ҡ update status ��� ��� temp
															// ���� $command ����� = "COMMAND" ���ͨ������ loop � if ��ͨҡ���
			}
		} // end if
	} // end if  UPDATE ABI_WF_STATUS
	
	if($command=="COMMAND" && $PER_ID){
		$cmd2="select AL_CODE from PER_ABILITY  where PER_ID=$PER_ID and AL_CODE='$AL_CODE' and ABI_DESC='$ABI_DESC' ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("�������ö������������ ���ͧ�ҡ�س�кش�ҹ��������ö�������Ф�������ö����ɫ�� !!!");
				-->   </script>	<? 
		} else {	  
			$cmd = " select max(ABI_ID) as max_id from PER_ABILITY ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$ABI_ID = $data[max_id] + 1;
		
			$ABI_DESC = trim($ABI_DESC);
			$cmd = " insert into PER_ABILITY (ABI_ID, PER_ID, AL_CODE, ABI_DESC, ABI_REMARK, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
							values ($ABI_ID, $PER_ID, '$AL_CODE', '$ABI_DESC', '$ABI_REMARK', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE')   ";
			$db_dpis->send_cmd($cmd);

			//################################################
			//---�����͹��ѵ����� �ӡ�������������������������ա������֧
			//if($ATTACH_FILE==1){
			$PATH_MAIN = "../attachments";		$mode = 0755;		$MOVERENAME = "";
			$MOVE_CATEGORY = str_replace("_WORKFLOW","",$CATEGORY);	// �Ѵ�� _WORKFLOW �Ҫ��� category ���������
			$FILE_PATH = $PATH_MAIN."/".$PER_CARDNO."/".$CATEGORY."/".$LAST_SUBDIR;		//�����������ͧ workflow ��������������
			//$FILE_PATH_HOST = "$PATH_MAIN\$PER_CARDNO\$CATEGORY\$LAST_SUBDIR";		//�����������ͧ workflow ��������������
			
			//��Ǩ�ͺ������	(��������������������������)
			$MOVEFIRST_SUBDIR = $PATH_MAIN.'/'.$PER_CARDNO;
			if($ATTACH_FILE==1){
				$MOVESECOND_SUBDIR = $MOVEFIRST_SUBDIR.'/PER_ATTACHMENT';
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/70';
				$MOVERENAME = "PER_ATTACHMENT70";
			}else{
				$MOVESECOND_SUBDIR = $MOVEFIRST_SUBDIR.'/'.$MOVE_CATEGORY;
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/'.$ABI_ID;
				$MOVERENAME = $MOVE_CATEGORY.$ABI_ID;
			}
			echo "$MOVEFINAL_PATH with $MOVERENAME";
				//==================================================
				//---������ժ�������������������������� ?
				//---���ҧ���������� ����ѧ���������
				if (!is_dir($MOVEFINAL_PATH)) {		//---1 : ����������������� ���ҧ���������
					$msgresult1 = $msgresult2 = $msgresult3 = "";

					//��������á
					if (!is_dir($MOVEFIRST_SUBDIR)) {
						$result1= mkdir($MOVEFIRST_SUBDIR,$mode);
					}
					//����������ͧ
					if (!is_dir($MOVESECOND_SUBDIR)) {
						if($result1 || is_dir($MOVEFIRST_SUBDIR)){
							$result2 = mkdir($MOVESECOND_SUBDIR,$mode);
						}else{
							$msgresult1 = " <br><span style='color:#FF0000'>���ҧ������ $MOVEFIRST_SUBDIR ����� !!!</span><br>";
						}
					}					
					//��������ش����
					if($result2 || is_dir($MOVESECOND_SUBDIR)){
						$result3= mkdir($MOVEFINAL_PATH,$mode);
					}else{
						$msgresult1="";
						$msgresult2 = "<span style='color:#FF0000'>���ҧ������ $MOVESECOND_SUBDIR ����� !!!</span><br>";
					}
					//umask(0);		echo umask();  //test
					if(!$result3){
						$msgresult2="";
						$msgresult3 = "<span style='color:#FF0000'>���ҧ������ $MOVEFINAL_PATH ����� !!!</span><br>";
					}
				if($msgresult3){	echo $msgresult3;	}
				if($result3 || is_dir($MOVEFINAL_PATH)){		//--->���ҧ������������ ��������������//rename
					//---ǹ loop ��ҹ���������������� ��������������������
					if (is_dir($FILE_PATH)) {
						if ($dh = opendir($FILE_PATH)) {
							while (($file = readdir($dh)) !== false) {
							if ($file != "." && $file != "..") {
								$movefile = str_replace($CATEGORY.$LAST_SUBDIR,$MOVERENAME,$file);	//�Ѵ�ӷ�� �������������
								if(rename("$FILE_PATH/$file","$MOVEFINAL_PATH/$movefile")){
									//echo "���ҧ/���������<br>";		unlink("$FINAL_PATH/$file");		//---������������������� ��ź����鹷��
									//--- �Ѿഷ��������� db ���������������§�ʴ���¡����
									$cmdup = " update 	editor_attachment set real_filename = '$movefile',update_date=$update_date,update_by=$update_by where real_filename='$file' ";
									$db->send_cmd($cmdup);
								}
							} // end if
							} // while loop 
							closedir($dh);
						} // end if
						//__rmdir($FINAL_PATH);	//ź��������鹷��
					} // end if is_dir
				} // end if result
			}else{				//---2 : ���������١���ҧ������� ������������������
					//---ǹ loop ��ҹ���������������� ��������������������
					if (is_dir($FILE_PATH)) {
						if ($dh = opendir($FILE_PATH)) {
							while (($file = readdir($dh)) !== false) {
							if ($file != "." && $file != "..") {
								$movefile = str_replace($CATEGORY.$LAST_SUBDIR,$MOVERENAME,$file);	//�Ѵ�ӷ�� �������������
								if(rename("$FILE_PATH/$file","$MOVEFINAL_PATH/$movefile")){
									//echo "���������<br>";		unlink("$FINAL_PATH/$file");		//---������������������� ��ź����鹷��
									//--- �Ѿഷ��������� db ���������������§�ʴ���¡����
									$cmdup = " update 	editor_attachment set real_filename = '$movefile',update_date=$update_date,update_by=$update_by where real_filename='$file' ";
									$db->send_cmd($cmdup);
								}
							} // end if
							} // while loop 
							closedir($dh);
						} // end if
						//__rmdir($FINAL_PATH);	//ź��������鹷��
					} // end if is_dir
			}
			//___echo "TEST : [ $ABI_ID ] <$FILE_PATH ~~ $MOVE_PATH> $FILE_PATH => $MOVEFINAL_PATH<br>$cmdup<br>";
			//###################################

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��������ѵԤ�������ö����� [$PER_ID : $ABI_ID : $AL_CODE]");
		} // end if
	} // end if �礢����ū��

	if(($UPD && $PER_ID && $ABI_ID) || ($VIEW && $PER_ID && $ABI_ID)){
		$cmd = " SELECT 	ABI_ID, pa.AL_CODE, pag.AL_NAME, ABI_DESC, ABI_REMARK, pa.UPDATE_USER, pa.UPDATE_DATE, pa.ABI_WF_STATUS  
						FROM			PER_WORKFLOW_ABILITY pa, PER_ABILITYGRP pag 
						WHERE		pa.ABI_ID=$ABI_ID and pa.AL_CODE=pag.AL_CODE and ABI_WF_STATUS!='04' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$ABI_ID = $data[ABI_ID];
		$AL_CODE = $data[AL_CODE];
		$AL_NAME = $data[AL_NAME];
		$ABI_DESC = $data[ABI_DESC];
		$ABI_REMARK = $data[ABI_REMARK];
		$UPDATE_USER = $data[UPDATE_USER];
		$ABI_WF_STATUS = $data[ABI_WF_STATUS];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($ABI_ID);
		unset($ABI_DESC);
		unset($ABI_REMARK);
	
		unset($AL_CODE);
		unset($AL_NAME);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>