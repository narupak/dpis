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

		if (!$DEH_ID) {
			$cmd = "	select  DEH_ID  from  PER_WORKFLOW_DECORATEHIS
							where  PER_ID=$PER_ID and DEH_WF_STATUS!='04'
							order by DEH_ID DESC ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$DEH_ID = $data[DEH_ID];
			if ($DEH_ID) {
				$VIEW = 1;
			}
		}
	} // end if

	$UPDATE_DATE = date("Y-m-d H:i:s");
	if (!$DEH_RECEIVE_FLAG) $DEH_RECEIVE_FLAG = "NULL";
	if (!$DEH_RETURN_FLAG) $DEH_RETURN_FLAG = "NULL";
	if (!$DEH_RETURN_TYPE) $DEH_RETURN_TYPE = "NULL";

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
		$DEH_DATE =  save_date($DEH_DATE);
		$DEH_RETURN_DATE =  save_date($DEH_RETURN_DATE);
		$DEH_RECEIVE_DATE =  save_date($DEH_RECEIVE_DATE);
		$DEH_BOOK_DATE =  save_date($DEH_BOOK_DATE);
		
		$cmd2="select   DC_CODE, DEH_DATE from PER_WORKFLOW_DECORATEHIS where PER_ID=$PER_ID and DC_CODE='$DC_CODE' ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("�������ö������������ ���ͧ�ҡ�س�к� ����ͧ�Ҫ/����­��� ��� !!!");
				-->   </script>	<? 
		} else {			
			$cmd = " select max(DEH_ID) as max_id from PER_WORKFLOW_DECORATEHIS ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$DEH_ID = $data[max_id] + 1;	
		
			$cmd = " insert into PER_WORKFLOW_DECORATEHIS (DEH_ID, PER_ID, DC_CODE, DEH_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE, 
							DEH_GAZETTE, DEH_RECEIVE_FLAG, DEH_RETURN_FLAG, DEH_RETURN_DATE, DEH_RETURN_TYPE, 
							DEH_RECEIVE_DATE, DEH_BOOK_NO, DEH_BOOK_DATE, DEH_REMARK, DEH_POSITION, DEH_ORG, DEH_WF_STATUS) 
							values	($DEH_ID, $PER_ID, '$DC_CODE', '$DEH_DATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE', '$DEH_GAZETTE', 
							$DEH_RECEIVE_FLAG, $DEH_RETURN_FLAG, '$DEH_RETURN_DATE', $DEH_RETURN_TYPE, 
							'$DEH_RECEIVE_DATE', '$DEH_BOOK_NO', '$DEH_BOOK_DATE', '$DEH_REMARK', '$DEH_POSITION', '$DEH_ORG', '01') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��������ѵԡ���Ѻ����ͧ�Ҫ� [$PER_ID : $DEH_ID : $DC_CODE]");
		} // end if
	} // end if �礢����ū��

	if($command=="UPDATE" && $PER_ID && $DEH_ID){
		$DEH_DATE =  save_date($DEH_DATE);
		$DEH_RETURN_DATE =  save_date($DEH_RETURN_DATE);
		$DEH_RECEIVE_DATE =  save_date($DEH_RECEIVE_DATE);
		$DEH_BOOK_DATE =  save_date($DEH_BOOK_DATE);
		
		$cmd2="select   DC_CODE, DEH_DATE from PER_WORKFLOW_DECORATEHIS where PER_ID=$PER_ID and DC_CODE='$DC_CODE' and DEH_ID<>$DEH_ID";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("�������ö��䢢������� ���ͧ�ҡ�س�к� ����ͧ�Ҫ/����­��� ��� !!!");
				-->   </script>	<? 
		} else {		
			$cmd = "	update PER_WORKFLOW_DECORATEHIS set
							DC_CODE='$DC_CODE',  
							DEH_DATE='$DEH_DATE', 
							PER_CARDNO='$PER_CARDNO', 
							DEH_GAZETTE='$DEH_GAZETTE',
							DEH_RECEIVE_FLAG=$DEH_RECEIVE_FLAG,
							DEH_RETURN_FLAG=$DEH_RETURN_FLAG,
							DEH_RETURN_DATE='$DEH_RETURN_DATE',
							DEH_RETURN_TYPE=$DEH_RETURN_TYPE,
							DEH_RECEIVE_DATE='$DEH_RECEIVE_DATE',
							DEH_BOOK_NO='$DEH_BOOK_NO', 
							DEH_BOOK_DATE='$DEH_BOOK_DATE', 
							DEH_REMARK='$DEH_REMARK', 
							DEH_POSITION='$DEH_POSITION', 
							DEH_ORG='$DEH_ORG', 
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE' 
					where DEH_ID=$DEH_ID  ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢻���ѵԡ���Ѻ����ͧ�Ҫ� [$PER_ID : $DEH_ID : $DC_CODE]");
		} // end if
	} // end if �礢����ū��
	
	if($command=="DELETE" && $PER_ID && $DEH_ID){
		$cmd = " select DC_CODE from PER_WORKFLOW_DECORATEHIS where DEH_ID=$DEH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DC_CODE = $data[DC_CODE];
		
		$cmd = " delete from PER_WORKFLOW_DECORATEHIS where DEH_ID=$DEH_ID ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź����ѵԡ���Ѻ����ͧ�Ҫ� [$PER_ID : $DEH_ID : $DC_CODE]");
	} // end if

//  UPDATE DEH_WF_STATUS
	$updtype=explode("-",$command);
	$F_REFRESH = "";
	if($updtype[0]=="UPD" && $PER_ID && $DEH_ID){
		$DEH_WF_STATUS = $updtype[1];
		$cmd2="select * from PER_WORKFLOW_DECORATEHIS where PER_ID=$PER_ID and DEH_ID=$DEH_ID";
                //echo "1:".$cmd2."<br><br>";
		$count=$db_dpis->send_cmd($cmd2);
//		$db_dpis->show_error(); echo "<hr><br>";
		if($count) { 
			$cmd = " 	update PER_WORKFLOW_DECORATEHIS set
								DEH_WF_STATUS='$updtype[1]', 
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE'
							where PER_ID=$PER_ID and DEH_ID=$DEH_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$F_REFRESH = "1";
		
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢻���ѵԡ���֡�� [$PER_ID : $PER_NAME : $EN_CODE]");
			
			if ($DEH_WF_STATUS=="04") {
				$command ="COMMAND"; 	// �ó� 04 ͹��ѵ� - �����͹��������������ԧ ��ѧ�ҡ update status ��� ��� temp
															// ���� $command ����� = "COMMAND" ���ͨ������ loop � if ��ͨҡ���
			}
                        //echo "2:".$command."<br><br>";
		} // end if
	} // end if  UPDATE DEH_WF_STATUS

	if($command=="COMMAND" && $PER_ID){
		$DEH_DATE =  save_date($DEH_DATE);
		$DEH_RETURN_DATE =  save_date($DEH_RETURN_DATE);
		$DEH_RECEIVE_DATE =  save_date($DEH_RECEIVE_DATE);
		$DEH_BOOK_DATE =  save_date($DEH_BOOK_DATE);
		
		$cmd2="select   DC_CODE, DEH_DATE from PER_DECORATEHIS where PER_ID=$PER_ID and DC_CODE='$DC_CODE' ";
		echo "3:".$cmd2."<br><br>";
                $count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("�������ö������������ ���ͧ�ҡ�س�к� ����ͧ�Ҫ/����­��� ��� !!!");
				-->   </script>	<? 
		} else {			
			$cmd = " select max(DEH_ID) as max_id from PER_DECORATEHIS ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$DEH_ID = $data[max_id] + 1;	
		
                        /*��� �� DEH_REMARK �ͧ ���*/
			/*$cmd = " insert into PER_DECORATEHIS (DEH_ID, PER_ID, DC_CODE, DEH_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE, 
							DEH_GAZETTE, DEH_RECEIVE_FLAG, DEH_RETURN_FLAG, DEH_RETURN_DATE, DEH_RETURN_TYPE, 
							DEH_RECEIVE_DATE, DEH_BOOK_NO, DEH_BOOK_DATE, DEH_REMARK, DEH_REMARK, DEH_POSITION) 
							values	($DEH_ID, $PER_ID, '$DC_CODE', '$DEH_DATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE', '$DEH_GAZETTE', 
							$DEH_RECEIVE_FLAG, $DEH_RETURN_FLAG, '$DEH_RETURN_DATE', $DEH_RETURN_TYPE, 
							'$DEH_RECEIVE_DATE', '$DEH_BOOK_NO', '$DEH_BOOK_DATE', '$DEH_REMARK', '$DEH_REMARK', '$DEH_POSITION') ";*/
                        
			/*Release 5.1.0.7 �Ѵ DEH_REMARK 1 ���*/
                        $cmd = " insert into PER_DECORATEHIS (DEH_ID, PER_ID, DC_CODE, DEH_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE, 
							DEH_GAZETTE, DEH_RECEIVE_FLAG, DEH_RETURN_FLAG, DEH_RETURN_DATE, DEH_RETURN_TYPE, 
							DEH_RECEIVE_DATE, DEH_BOOK_NO, DEH_BOOK_DATE, DEH_REMARK, DEH_POSITION) 
							values	($DEH_ID, $PER_ID, '$DC_CODE', '$DEH_DATE', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE', '$DEH_GAZETTE', 
							$DEH_RECEIVE_FLAG, $DEH_RETURN_FLAG, '$DEH_RETURN_DATE', $DEH_RETURN_TYPE, 
							'$DEH_RECEIVE_DATE', '$DEH_BOOK_NO', '$DEH_BOOK_DATE',  '$DEH_REMARK', '$DEH_POSITION') ";
                        /*Release 5.1.0.7 End*/
                        //echo "4:".$cmd."<br><br>";
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
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/44';
				$MOVERENAME = "PER_ATTACHMENT44";
			}else{
				$MOVESECOND_SUBDIR = $MOVEFIRST_SUBDIR.'/'.$MOVE_CATEGORY;
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/'.$DEH_ID;
				$MOVERENAME = $MOVE_CATEGORY.$DEH_ID;
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
			//___echo "TEST : [ $DEH_ID ] <$FILE_PATH ~~ $MOVE_PATH> $FILE_PATH => $MOVEFINAL_PATH<br>$cmdup<br>";
			//###################################

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��������ѵԡ���Ѻ����ͧ�Ҫ� [$PER_ID : $DEH_ID : $DC_CODE]");
		} // end if
	} // end if �礢����ū��
//die('x');
	if(($UPD && $PER_ID && $DEH_ID) || ($VIEW && $PER_ID && $DEH_ID)){
		$cmd = 	" select 	DEH_ID, pdh.DC_CODE, pd.DC_NAME, DEH_DATE, DEH_GAZETTE, DEH_RECEIVE_FLAG, 
											DEH_RETURN_FLAG, DEH_RETURN_DATE, DEH_RETURN_TYPE, pdh.UPDATE_USER, pdh.UPDATE_DATE, 
											DEH_RECEIVE_DATE, DEH_BOOK_NO, DEH_BOOK_DATE, DEH_REMARK, DEH_REMARK, DEH_POSITION, DEH_WF_STATUS
							from		PER_WORKFLOW_DECORATEHIS pdh,  PER_DECORATION pd  
							where	DEH_ID=$DEH_ID and pdh.DC_CODE=pd.DC_CODE and DEH_WF_STATUS!='04' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEH_ID = $data[DEH_ID];
		$DEH_GAZETTE = trim($data[DEH_GAZETTE]);
		$DEH_RECEIVE_FLAG = $data[DEH_RECEIVE_FLAG];
		$DEH_RETURN_FLAG = $data[DEH_RETURN_FLAG];
		$DEH_RETURN_TYPE = $data[DEH_RETURN_TYPE];
		$DEH_DATE = show_date_format($data[DEH_DATE], 1);
		$DEH_RETURN_DATE = show_date_format($data[DEH_RETURN_DATE], 1);
		$DEH_RECEIVE_DATE = show_date_format($data[DEH_RECEIVE_DATE], 1);
		$DEH_BOOK_DATE = show_date_format($data[DEH_BOOK_DATE], 1);
				
		$DC_CODE = $data[DC_CODE];
		$DC_NAME = $data[DC_NAME];		
		$DEH_BOOK_NO = trim($data[DEH_BOOK_NO]);
		$DEH_REMARK = trim($data[DEH_REMARK]);
		$DEH_POSITION = trim($data[DEH_POSITION]);
		$DEH_ORG = trim($data[DEH_ORG]);
		$DEH_WF_STATUS = trim($data[DEH_WF_STATUS]);
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($DEH_ID);
		unset($DEH_DATE);

		unset($DC_CODE);
		unset($DC_NAME);
		unset($DEH_GAZETTE);
		unset($DEH_RECEIVE_FLAG);
		unset($DEH_RETURN_FLAG);
		unset($DEH_RETURN_DATE);
		unset($DEH_RETURN_TYPE);
		unset($DEH_RECEIVE_DATE);
		unset($DEH_BOOK_NO);
		unset($DEH_BOOK_DATE);
		unset($DEH_REMARK);
		unset($DEH_POSITION);
		unset($DEH_ORG);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>