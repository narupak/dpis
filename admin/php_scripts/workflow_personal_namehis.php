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
			$cmd = " select 	PER.PN_CODE, PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PERSONAL.PN_CODE, PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
							PER_PERSONAL.PER_TYPE, PER_PERSONAL.PER_CARDNO,PER_PERSONAL.DEPARTMENT_ID
 					from		PER_PERSONAL, PER_PRENAME
					where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and 
							PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PER.PN_CODE, PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		} // end if
		
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_CODE_OLD = $data[PN_CODE];
		$PN_NAME_OLD = $data[PN_NAME];
		$NH_NAME_OLD = $data[PER_NAME];
		$NH_SURNAME_OLD = $data[PER_SURNAME];
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_CARDNO = (trim($data[PER_CARDNO]))?trim($data[PER_CARDNO]): "NULL";		
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
		
		if (!$NH_ID) {
			$cmd = "	select  NH_ID  from  PER_WORKFLOW_NAMEHIS
							where  PER_ID=$PER_ID and NH_WF_STATUS!='04'
							order by NH_ID DESC ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$NH_ID = $data[NH_ID];
			if ($NH_ID) {
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
		$NH_DATE =  save_date($NH_DATE);
		$NH_BOOK_DATE =  save_date($NH_BOOK_DATE);
		
		$cmd2 = " select   NH_DATE, PN_CODE, NH_NAME, NH_SURNAME, NH_DOCNO from PER_WORKFLOW_NAMEHIS 
							where PER_ID=$PER_ID and NH_DATE='$NH_DATE' and PN_CODE='$PN_CODE' and  NH_NAME='$NH_NAME' and 
							NH_SURNAME='$NH_SURNAME' and NH_DOCNO='$NH_DOCNO' ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("�������ö������������ ���ͧ�ҡ�س�к� �ѹ�������¹�ŧ, �ӹ�˹��, �������, ʡ����� �����ѡ�ҹ�������¹�ŧ��� !!!");
				-->   </script>	<? 
		} else {			
			$cmd = " select max(NH_ID) as max_id from PER_WORKFLOW_NAMEHIS ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$NH_ID = $data[max_id] + 1;

			$cmd = " insert into PER_WORKFLOW_NAMEHIS (NH_ID, PER_ID, NH_DATE, PN_CODE, NH_NAME, NH_SURNAME, NH_DOCNO, 
								UPDATE_USER, UPDATE_DATE, PER_CARDNO, NH_ORG, PN_CODE_NEW, NH_NAME_NEW, 
								NH_SURNAME_NEW, NH_BOOK_NO, NH_BOOK_DATE, NH_REMARK, NH_WF_STATUS)
								values ($NH_ID, $PER_ID, '$NH_DATE', '$PN_CODE', '$NH_NAME', '$NH_SURNAME', '$NH_DOCNO', 
								$SESS_USERID, '$UPDATE_DATE', '$PER_CARDNO', '$NH_ORG', '$PN_CODE_NEW', '$NH_NAME_NEW', 
								'$NH_SURNAME_NEW', '$NH_BOOK_NO', '$NH_BOOK_DATE', '$NH_REMARK', '01') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��������ѵԡ������¹�ŧ����-ʡ�� [$PER_ID : $NH_ID : $PN_CODE]");
		} // end if
	} //end if �礢����ū��

	if($command=="UPDATE" && $PER_ID && $NH_ID){
		$NH_DATE =  save_date($NH_DATE);
		$NH_BOOK_DATE =  save_date($NH_BOOK_DATE);
	
		$cmd2 = " select   NH_DATE, PN_CODE, NH_NAME, NH_SURNAME, NH_DOCNO from PER_WORKFLOW_NAMEHIS 
							where PER_ID=$PER_ID and NH_DATE='$NH_DATE' and PN_CODE='$PN_CODE' and  NH_NAME='$NH_NAME' and 
							NH_SURNAME='$NH_SURNAME' and NH_DOCNO='$NH_DOCNO' and  NH_ID <> $NH_ID ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("�������ö��䢢������� ���ͧ�ҡ�س�к� �ѹ�������¹�ŧ, �ӹ�˹��, �������, ʡ����� �����ѡ�ҹ�������¹�ŧ��� !!!");
				-->   </script>	<? 
		} else {		
			$cmd = " UPDATE PER_WORKFLOW_NAMEHIS SET
							NH_DATE='$NH_DATE', 
							PN_CODE='$PN_CODE', 
							NH_DOCNO='$NH_DOCNO',  
							NH_NAME='$NH_NAME', 
							NH_SURNAME='$NH_SURNAME', 
							NH_ORG='$NH_ORG', 
							PN_CODE_NEW='$PN_CODE_NEW', 
							NH_NAME_NEW='$NH_NAME_NEW', 
							NH_SURNAME_NEW='$NH_SURNAME_NEW', 
							NH_BOOK_NO='$NH_BOOK_NO',  
							NH_BOOK_DATE='$NH_BOOK_DATE',  
							NH_REMARK='$NH_REMARK',  
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE', 
							PER_CARDNO='$PER_CARDNO' 
						WHERE NH_ID=$NH_ID ";
			$db_dpis->send_cmd($cmd);
		
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢻���ѵԡ������¹�ŧ����-ʡ�� [$PER_ID : $NH_ID : $PN_CODE]");
		} // end if
	} // end if �礢����ū��
	
	if($command=="DELETE" && $PER_ID && $NH_ID){
		$cmd = " select PN_CODE from PER_WORKFLOW_NAMEHIS where NH_ID=$NH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_CODE = $data[PN_CODE];
		
		$cmd = " delete from PER_WORKFLOW_NAMEHIS where NH_ID=$NH_ID ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź����ѵԡ������¹�ŧ����-ʡ�� [$PER_ID : $NH_ID : $PN_CODE]");
	} // end if
	
//  UPDATE NH_WF_STATUS
	$updtype=explode("-",$command);
	$F_REFRESH = "";
	if($updtype[0]=="UPD" && $PER_ID && $NH_ID){
		$NH_WF_STATUS = $updtype[1];
		$cmd2="select * from PER_WORKFLOW_NAMEHIS where PER_ID=$PER_ID and NH_ID=$NH_ID ";
		$count=$db_dpis->send_cmd($cmd2);
//		$db_dpis->show_error(); echo "<hr><br>";
		if($count) { 
			$cmd = " 	update PER_WORKFLOW_NAMEHIS set
								NH_WF_STATUS='$updtype[1]', 
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE'
							where PER_ID=$PER_ID and NH_ID=$NH_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$F_REFRESH = "1";
		
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢻���ѵԡ���֡�� [$PER_ID : $PER_NAME : $EN_CODE]");
			
			if ($NH_WF_STATUS=="04") {
				$command ="COMMAND"; 	// �ó� 04 ͹��ѵ� - �����͹��������������ԧ ��ѧ�ҡ update status ��� ��� temp
															// ���� $command ����� = "COMMAND" ���ͨ������ loop � if ��ͨҡ���
			}
		} // end if
	} // end if  UPDATE EDU_WF_STATUS
	
	if($command=="COMMAND" && $PER_ID){
		$NH_DATE =  save_date($NH_DATE);
		$NH_BOOK_DATE =  save_date($NH_BOOK_DATE);
		
		$cmd2 = " select   NH_DATE, PN_CODE, NH_NAME, NH_SURNAME, NH_DOCNO from PER_NAMEHIS 
							where PER_ID=$PER_ID and NH_DATE='$NH_DATE' and PN_CODE='$PN_CODE' and  NH_NAME='$NH_NAME' and 
							NH_SURNAME='$NH_SURNAME' and NH_DOCNO='$NH_DOCNO' ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("�������ö������������ ���ͧ�ҡ�س�к� �ѹ�������¹�ŧ, �ӹ�˹��, �������, ʡ����� �����ѡ�ҹ�������¹�ŧ��� !!!");
				-->   </script>	<? 
		} else {			
			$cmd = " select max(NH_ID) as max_id from PER_NAMEHIS ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$NH_ID = $data[max_id] + 1;

			$cmd = " insert into PER_NAMEHIS (NH_ID, PER_ID, NH_DATE, PN_CODE, NH_NAME, NH_SURNAME, NH_DOCNO, 
							UPDATE_USER, UPDATE_DATE, PER_CARDNO, NH_ORG, PN_CODE_NEW, NH_NAME_NEW, 
							NH_SURNAME_NEW, NH_BOOK_NO, NH_BOOK_DATE, NH_REMARK)
							values ($NH_ID, $PER_ID, '$NH_DATE', '$PN_CODE', '$NH_NAME', '$NH_SURNAME', '$NH_DOCNO', 
							$SESS_USERID, '$UPDATE_DATE', '$PER_CARDNO', '$NH_ORG', '$PN_CODE_NEW', '$NH_NAME_NEW', 
							'$NH_SURNAME_NEW', '$NH_BOOK_NO', '$NH_BOOK_DATE', '$NH_REMARK') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			
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
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/34';
				$MOVERENAME = "PER_ATTACHMENT34";
			}else{
				$MOVESECOND_SUBDIR = $MOVEFIRST_SUBDIR.'/'.$MOVE_CATEGORY;
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/'.$NH_ID;
				$MOVERENAME = $MOVE_CATEGORY.$NH_ID;
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
			//___echo "TEST : [ $NH_ID ] <$FILE_PATH ~~ $MOVE_PATH> $FILE_PATH => $MOVEFINAL_PATH<br>$cmdup<br>";
			//###################################

			if ($PN_CODE_NEW) {
				$cmd = " UPDATE PER_PERSONAL SET PN_CODE = '$PN_CODE_NEW' WHERE PER_ID = $PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}

			if ($NH_NAME_NEW) {
				$cmd = " UPDATE PER_PERSONAL SET PER_NAME = '$NH_NAME_NEW' WHERE PER_ID = $PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}

			if ($NH_SURNAME_NEW) {
				$cmd = " UPDATE PER_PERSONAL SET PER_SURNAME = '$NH_SURNAME_NEW' WHERE PER_ID = $PER_ID ";
				$db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			}

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��������ѵԡ������¹�ŧ����-ʡ�� [$PER_ID : $NH_ID : $PN_CODE]");
		} // end if
	} //end if �礢����ū��

	if(($UPD && $PER_ID && $NH_ID) || ($VIEW && $PER_ID && $NH_ID)){
		$cmd = " select		NH_DATE, pnh.PN_CODE, ppn.PN_NAME, NH_NAME, NH_SURNAME, NH_DOCNO, 
											pnh.UPDATE_USER, pnh.UPDATE_DATE, NH_ORG, PN_CODE_NEW, NH_NAME_NEW, NH_SURNAME_NEW, 
											NH_BOOK_NO, NH_BOOK_DATE, NH_REMARK, NH_WF_STATUS
						from			PER_WORKFLOW_NAMEHIS pnh, PER_PRENAME ppn
						where		NH_ID = $NH_ID and pnh.PN_CODE = ppn.PN_CODE and NH_WF_STATUS!='04' ";	
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$NH_DATE = show_date_format($data[NH_DATE], 1);
		$PN_CODE = $data[PN_CODE];
		$PN_NAME = $data[PN_NAME];
	
		$NH_NAME = $data[NH_NAME];
		$NH_SURNAME = $data[NH_SURNAME];
		$NH_DOCNO = $data[NH_DOCNO];
		$NH_ORG = trim($data[NH_ORG]);
		$NH_NAME_NEW = trim($data[NH_NAME_NEW]);
		$NH_SURNAME_NEW = trim($data[NH_SURNAME_NEW]);
		$NH_BOOK_NO = trim($data[NH_BOOK_NO]);
		$NH_BOOK_DATE = show_date_format($data[NH_BOOK_DATE], 1);
		$NH_REMARK = trim($data[NH_REMARK]);
		$NH_WF_STATUS = trim($data[NH_WF_STATUS]);
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);

		$PN_CODE_NEW = trim($data[PN_CODE_NEW]);
		if($PN_CODE_NEW){
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE_NEW' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PN_NAME_NEW = $data2[PN_NAME];
		} // end if
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($NH_ID);
		unset($NH_DATE);
		unset($NH_DOCNO);		
		unset($NH_ORG);
		$PN_CODE = $PN_CODE_NEW = $PN_CODE_OLD;
		$PN_NAME = $PN_NAME_NEW = $PN_NAME_OLD;
		$NH_NAME = $NH_NAME_NEW = $NH_NAME_OLD;
		$NH_SURNAME = $NH_SURNAME_NEW = $NH_SURNAME_OLD;
		unset($NH_BOOK_NO);
		unset($NH_BOOK_DATE);
		unset($NH_REMARK);
	} // end if
?>