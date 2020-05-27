<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");
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
		
		if (!$REH_ID) {
			$cmd = "	select  REH_ID  from  PER_WORKFLOW_REWARDHIS
							where  PER_ID=$PER_ID and REH_WF_STATUS!='04'
							order by REH_ID DESC ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$REH_ID = $data[REH_ID];
			if ($REH_ID) {
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
		$REH_DATE =  save_date($REH_DATE);

		$cmd = " select max(REH_ID) as max_id from PER_WORKFLOW_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$REH_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_WORKFLOW_REWARDHIS (REH_ID, PER_ID, REH_DATE, REW_CODE, REH_ORG, REH_DOCNO, UPDATE_USER, 
						UPDATE_DATE, PER_CARDNO, REH_YEAR, REH_PERFORMANCE, REH_OTHER_PERFORMANCE, REH_REMARK, REH_WF_STATUS)
						values ($REH_ID, $PER_ID, '$REH_DATE', '$REW_CODE', '$REH_ORG', '$REH_DOCNO', $SESS_USERID, '$UPDATE_DATE', 
						'$PER_CARDNO', '$REH_YEAR', '$REH_PERFORMANCE', '$REH_OTHER_PERFORMANCE', '$REH_REMARK', '01') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��������ѵԤ����դ����ͺ [$PER_ID : $REH_ID : $REW_CODE]");
	} // end if

	if($command=="UPDATE" && $PER_ID && $REH_ID){
		$REH_DATE =  save_date($REH_DATE);
	
		$cmd = " UPDATE PER_WORKFLOW_REWARDHIS SET
						REH_DATE='$REH_DATE', 
						REW_CODE='$REW_CODE', 
						REH_ORG='$REH_ORG', 
						REH_DOCNO='$REH_DOCNO', 
						REH_YEAR='$REH_YEAR', 
						REH_PERFORMANCE='$REH_PERFORMANCE', 
						REH_OTHER_PERFORMANCE='$REH_OTHER_PERFORMANCE', 
						REH_REMARK='$REH_REMARK', 
						UPDATE_USER=$SESS_USERID, 
						UPDATE_DATE='$UPDATE_DATE', 
						PER_CARDNO='$PER_CARDNO' 
					WHERE REH_ID=$REH_ID ";
		$db_dpis->send_cmd($cmd);
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢻���ѵԤ����դ����ͺ [$PER_ID : $REH_ID : $REW_CODE]");
	} // end if
	
	if($command=="DELETE" && $PER_ID && $REH_ID){
		$cmd = " select REW_CODE from PER_WORKFLOW_REWARDHIS where REH_ID=$REH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REW_CODE = $data[REW_CODE];
		
		$cmd = " delete from PER_WORKFLOW_REWARDHIS where REH_ID=$REH_ID ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź����ѵԤ����դ����ͺ [$PER_ID : $REH_ID : $REW_CODE]");
	} // end if

//  UPDATE REH_WF_STATUS
	$updtype=explode("-",$command);
	$F_REFRESH = "";
	if($updtype[0]=="UPD" && $PER_ID && $REH_ID){
		$REH_WF_STATUS = $updtype[1];
		$cmd2="select * from PER_WORKFLOW_REWARDHIS where PER_ID=$PER_ID and REH_ID=$REH_ID";
		$count=$db_dpis->send_cmd($cmd2);
//		$db_dpis->show_error(); echo "<hr><br>";
		if($count) { 
			$cmd = " 	update PER_WORKFLOW_REWARDHIS set
								REH_WF_STATUS='$updtype[1]', 
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE'
							where PER_ID=$PER_ID and REH_ID=$REH_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$F_REFRESH = "1";
		
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢻���ѵԡ���֡�� [$PER_ID : $PER_NAME : $EN_CODE]");
			
			if ($REH_WF_STATUS=="04") {
				$command ="COMMAND"; 	// �ó� 04 ͹��ѵ� - �����͹��������������ԧ ��ѧ�ҡ update status ��� ��� temp
															// ���� $command ����� = "COMMAND" ���ͨ������ loop � if ��ͨҡ���
			}
		} // end if
	} // end if  UPDATE EDU_WF_STATUS
	
	if($command=="COMMAND" && $PER_ID){
		$REH_DATE =  save_date($REH_DATE);

		$cmd = " select max(REH_ID) as max_id from PER_REWARDHIS ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$REH_ID = $data[max_id] + 1;
		
		$cmd = " insert into PER_REWARDHIS (REH_ID, PER_ID, REH_DATE, REW_CODE, REH_ORG, REH_DOCNO, UPDATE_USER, 
						UPDATE_DATE, PER_CARDNO, REH_YEAR, REH_PERFORMANCE, REH_OTHER_PERFORMANCE, REH_REMARK)
						values ($REH_ID, $PER_ID, '$REH_DATE', '$REW_CODE', '$REH_ORG', '$REH_DOCNO', $SESS_USERID, '$UPDATE_DATE', 
						'$PER_CARDNO', '$REH_YEAR', '$REH_PERFORMANCE', '$REH_OTHER_PERFORMANCE', '$REH_REMARK') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

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
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/43';
				$MOVERENAME = "PER_ATTACHMENT43";
			}else{
				$MOVESECOND_SUBDIR = $MOVEFIRST_SUBDIR.'/'.$MOVE_CATEGORY;
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/'.$REH_ID;
				$MOVERENAME = $MOVE_CATEGORY.$REH_ID;
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
			//___echo "TEST : [ $REH_ID ] <$FILE_PATH ~~ $MOVE_PATH> $FILE_PATH => $MOVEFINAL_PATH<br>$cmdup<br>";
			//###################################

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��������ѵԤ����դ����ͺ [$PER_ID : $REH_ID : $REW_CODE]");
	} // end if

	if(($UPD && $PER_ID && $REH_ID) || ($VIEW && $PER_ID && $REH_ID)){
		$cmd = " SELECT		prh.REW_CODE, pr.REW_NAME, REH_ORG, REH_DOCNO, REH_DATE, prh.UPDATE_USER, prh.UPDATE_DATE, 
												REH_YEAR, REH_PERFORMANCE, REH_OTHER_PERFORMANCE, REH_REMARK , REH_WF_STATUS
							FROM		PER_WORKFLOW_REWARDHIS prh, PER_REWARD pr
							WHERE	REH_ID=$REH_ID and prh.REW_CODE=pr.REW_CODE and REH_WF_STATUS!='04' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REH_DATE = show_date_format($data[REH_DATE], 1);

		$REH_ORG = trim($data[REH_ORG]);
		$REH_DOCNO = trim($data[REH_DOCNO]);
		$REH_YEAR = trim($data[REH_YEAR]);
		$REH_PERFORMANCE = trim($data[REH_PERFORMANCE]);
		$REH_OTHER_PERFORMANCE = trim($data[REH_OTHER_PERFORMANCE]);
		$REH_REMARK = trim($data[REH_REMARK]);
		$REW_CODE = trim($data[REW_CODE]);
		$REW_NAME = trim($data[REW_NAME]);
		$REH_WF_STATUS = trim($data[REH_WF_STATUS]);
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($REH_ID);
		unset($REH_DATE);
		unset($REH_ORG);		
		unset($REH_DOCNO);
		unset($REH_YEAR);
		unset($REH_PERFORMANCE);
		unset($REH_OTHER_PERFORMANCE);
		unset($REH_REMARK);
	
		unset($REW_CODE);
		unset($REW_NAME);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>