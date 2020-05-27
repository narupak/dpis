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
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO, PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
							PER_PERSONAL.PER_TYPE, PER_PERSONAL.PER_CARDNO, PER_PERSONAL.DEPARTMENT_ID 
					from		PER_PERSONAL, PER_PRENAME
					where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO, PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		} // end if
		
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_CARDNO = (trim($data[PER_CARDNO]))?trim($data[PER_CARDNO]): "NULL";		
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);

		if (!$ABS_ID) {
			$cmd = "	select  ABS_ID  from  PER_WORKFLOW_ABSENTHIS
							where  PER_ID=$PER_ID and ABS_WF_STATUS!='04'
							order by ABS_ID DESC ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$ABS_ID = $data[ABS_ID];
			if ($ABS_ID) {
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
	
	$F_REFRESH = "";
	if($command=="ADD" && $PER_ID){
		$ABS_STARTDATE =  save_date($ABS_STARTDATE);
		$ABS_ENDDATE =  save_date($ABS_ENDDATE);
		
		$cmd2 = " select AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY 
							from 	PER_WORKFLOW_ABSENTHIS  
							where PER_ID=$PER_ID and AB_CODE='$AB_CODE' and ABS_STARTDATE='$ABS_STARTDATE' and  
								ABS_STARTPERIOD='$ABS_STARTPERIOD' and ABS_ENDDATE='$ABS_ENDDATE' and 
								ABS_ENDPERIOD='$ABS_ENDPERIOD' and ABS_DAY='$ABS_DAY' ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
				alert("�������ö������������ ���ͧ�ҡ�س�к� ������ѹ��� �֧�ѹ��� ����������� �ӹǹ�ѹ ��� !!!");
		-->   </script>	<? }  
		else {	  	
		
			$cmd = " select max(ABS_ID) as max_id from PER_WORKFLOW_ABSENTHIS ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$ABS_ID = $data[max_id] + 1;
		
			$cmd = " insert into PER_WORKFLOW_ABSENTHIS (ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, 
							ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, ABS_REMARK, PER_CARDNO, UPDATE_USER, UPDATE_DATE, ABS_WF_STATUS)
							values ($ABS_ID, $PER_ID, '$AB_CODE', '$ABS_STARTDATE', '$ABS_STARTPERIOD', '$ABS_ENDDATE', 
							'$ABS_ENDPERIOD', '$ABS_DAY', '$ABS_REMARK', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE', '01')   ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "<br>$cmd<br>";

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ���������Ż���ѵԡ����ŧ���ҧ���ͧ [$PER_ID : $ABS_ID : $AB_CODE]");
			$F_REFRESH = "1";
		} // end if
	} //end if �礢����ū��
	
	if($command=="UPDATE" && $PER_ID && $ABS_ID){
		$ABS_STARTDATE =  save_date($ABS_STARTDATE);
		$ABS_ENDDATE =  save_date($ABS_ENDDATE);
		
		$cmd2 = " select AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY 
						from 	PER_WORKFLOW_ABSENTHIS  
						where PER_ID=$PER_ID and AB_CODE='$AB_CODE' and ABS_STARTDATE='$ABS_STARTDATE' and  
							ABS_STARTPERIOD='$ABS_STARTPERIOD' and ABS_ENDDATE='$ABS_ENDDATE' and 
							ABS_ENDPERIOD='$ABS_ENDPERIOD' and ABS_DAY='$ABS_DAY' ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("�������ö��䢢������� ���ͧ�ҡ�س�к� ������ѹ��� �֧�ѹ��� ����������� �ӹǹ�ѹ ��� !!!");
		-->   </script>	<? }  
		else {	  	
	
			$cmd = " UPDATE PER_WORKFLOW_ABSENTHIS SET
							AB_CODE='$AB_CODE', 
							ABS_STARTDATE='$ABS_STARTDATE', 
							ABS_STARTPERIOD='$ABS_STARTPERIOD', 
							ABS_ENDDATE='$ABS_ENDDATE', 
							ABS_ENDPERIOD='$ABS_ENDPERIOD', 
							ABS_DAY='$ABS_DAY',  
							ABS_REMARK='$ABS_REMARK',  
							PER_CARDNO='$PER_CARDNO', 
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE'
							WHERE ABS_ID=$ABS_ID ";
			$db_dpis->send_cmd($cmd);
		
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢢����Ż���ѵԡ���� [$PER_ID : $ABS_ID : $AB_CODE]");
		} // end if
	} // end if �礢����ū��
	
	if($command=="DELETE" && $PER_ID && $ABS_ID){
		$cmd = " select AB_CODE from PER_WORKFLOW_ABSENTHIS where ABS_ID=$ABS_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$AB_CODE = $data[AB_CODE];
		
		$cmd = " delete from PER_WORKFLOW_ABSENTHIS where ABS_ID=$ABS_ID ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź�����Ż���ѵԡ���� [$PER_ID : $ABS_ID : $AB_CODE]");
	} // end if

//  UPDATE ABS_WF_STATUS
	$updtype=explode("-",$command);
	$F_REFRESH = "";
	if($updtype[0]=="UPD" && $PER_ID && $ABS_ID){
		$ABS_WF_STATUS = $updtype[1];
		$cmd2="select * from PER_WORKFLOW_ABSENTHIS where PER_ID=$PER_ID and ABS_ID=$ABS_ID";
		$count=$db_dpis->send_cmd($cmd2);
//		$db_dpis->show_error(); echo "<hr><br>";
		if($count) { 
			$cmd = " 	update PER_WORKFLOW_ABSENTHIS set
								ABS_WF_STATUS='$updtype[1]', 
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE'
							where PER_ID=$PER_ID and ABS_ID=$ABS_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$F_REFRESH = "1";
		
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢻���ѵԡ���֡�� [$PER_ID : $PER_NAME : $EN_CODE]");
			
			if ($ABS_WF_STATUS=="04") {
				$command ="COMMAND"; 	// �ó� 04 ͹��ѵ� - �����͹��������������ԧ ��ѧ�ҡ update status ��� ��� temp
															// ���� $command ����� = "COMMAND" ���ͨ������ loop � if ��ͨҡ���
			}
		} // end if
	} // end if  UPDATE ABS_WF_STATUS
	
	if($command=="COMMAND" && $PER_ID){
		$ABS_STARTDATE =  save_date($ABS_STARTDATE);
		$ABS_ENDDATE =  save_date($ABS_ENDDATE);
		
		$cmd2="select  AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY from PER_ABSENTHIS  where PER_ID=$PER_ID and AB_CODE='$AB_CODE' and ABS_STARTDATE='$ABS_STARTDATE' and  ABS_STARTPERIOD='$ABS_STARTPERIOD' and ABS_ENDDATE='$ABS_ENDDATE' and ABS_ENDPERIOD='$ABS_ENDPERIOD' and ABS_DAY='$ABS_DAY' ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
				alert("�������ö������������ ���ͧ�ҡ�س�к� ������ѹ��� �֧�ѹ��� ����������� �ӹǹ�ѹ ��� !!!");
		-->   </script>	<? }  
		else {	  	
		
			$cmd = " select max(ABS_ID) as max_id from PER_ABSENTHIS ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$ABS_ID = $data[max_id] + 1;
		
			$cmd = " insert into PER_ABSENTHIS
								(ABS_ID, PER_ID, AB_CODE, ABS_STARTDATE, ABS_STARTPERIOD, ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
							values
								($ABS_ID, $PER_ID, '$AB_CODE', '$ABS_STARTDATE', '$ABS_STARTPERIOD', '$ABS_ENDDATE', '$ABS_ENDPERIOD', '$ABS_DAY', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE')   ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "<br>$cmd<br>";

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
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/62';
				$MOVERENAME = "PER_ATTACHMENT62";
			}else{
				$MOVESECOND_SUBDIR = $MOVEFIRST_SUBDIR.'/'.$MOVE_CATEGORY;
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/'.$ABS_ID;
				$MOVERENAME = $MOVE_CATEGORY.$ABS_ID;
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
			//___echo "TEST : [ $ABS_ID ] <$FILE_PATH ~~ $MOVE_PATH> $FILE_PATH => $MOVEFINAL_PATH<br>$cmdup<br>";
			//###################################

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ���������Ż���ѵԡ����ŧ���ҧ��ԧ [$PER_ID : $ABS_ID : $AB_CODE]");
			$F_REFRESH = "1";
		} // end if
	} // end if �礢����ū��

	if(($UPD && $PER_ID && $ABS_ID) || ($VIEW && $PER_ID && $ABS_ID)){
		$cmd = "	SELECT 	ABS_ID, pah.AB_CODE, pat.AB_NAME,pat.AB_COUNT, ABS_STARTDATE, ABS_STARTPERIOD, 
												ABS_ENDDATE, ABS_ENDPERIOD, ABS_DAY, ABS_REMARK, pah.UPDATE_USER, pah.UPDATE_DATE, ABS_WF_STATUS
							FROM		PER_WORKFLOW_ABSENTHIS pah, PER_ABSENTTYPE pat  
							WHERE	ABS_ID=$ABS_ID and pah.AB_CODE=pat.AB_CODE and ABS_WF_STATUS!='04'  ";	
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$ABS_STARTDATE = show_date_format($data[ABS_STARTDATE], 1);
		$ABS_ENDDATE = show_date_format($data[ABS_ENDDATE], 1);
		$ABS_STARTPERIOD = $data[ABS_STARTPERIOD];		
		$ABS_ENDPERIOD = $data[ABS_ENDPERIOD];		
		$ABS_DAY = $data[ABS_DAY];
		$ABS_REMARK = $data[ABS_REMARK];
		$ABS_WF_STATUS = $data[ABS_WF_STATUS];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		$AB_CODE = $data[AB_CODE];
		$AB_NAME = $data[AB_NAME];
		$AB_COUNT = $data[AB_COUNT];

	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($ABS_ID);
		unset($ABS_STARTDATE);
		unset($ABS_STARTPERIOD);		
		unset($ABS_ENDDATE);
		unset($ABS_ENDPERIOD);
		unset($ABS_DAY);		
		unset($ABS_REMARK);		

		unset($AB_CODE);
		unset($AB_NAME);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
		
		$ABS_STARTPERIOD = 3;
		$ABS_ENDPERIOD = 3;
		$ABS_LETTER = 3;
	} // end if
?>