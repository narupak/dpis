<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");	

	$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
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

		if (!$PUN_ID) {
			$cmd = "	select  PUN_ID  from  PER_WORKFLOW_PUNISHMENT
							where  PER_ID=$PER_ID and PUN_WF_STATUS!='04'
							order by PUN_ID DESC ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PUN_ID = $data[PUN_ID];
			if ($PUN_ID) {
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
		$PUN_STARTDATE =  save_date($PUN_STARTDATE);
		$PUN_ENDDATE =  save_date($PUN_ENDDATE);
		
		$cmd2 = " select  CRD_CODE, PUN_TYPE, PEN_CODE, PUN_STARTDATE, PUN_ENDDATE, PUN_WF_STATUS from PER_WORKFLOW_PUNISHMENT 
							where PER_ID=$PER_ID and trim(CRD_CODE)='".trim($CRD_CODE)."' and PUN_TYPE=$PUN_TYPE and  
							trim(PEN_CODE)='".trim($PEN_CODE)."' and PUN_STARTDATE='$PUN_STARTDATE' and PUN_ENDDATE='$PUN_ENDDATE' ";
		$count=$db_dpis->send_cmd($cmd2);
                //echo "1:".$cmd2."<br><br>";
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("�������ö������������ ���ͧ�ҡ�س�кءóդ����Դ, ������, �ѹ����Ѻ�� ����ѹ�������ش�� ��� !!!");
		-->   </script>	<? }  
		else {	  	
			//$cmd = " select max(PUN_ID) as max_id from PER_WORKFLOW_PUNISHMENT "; /*���*/
                        $cmd = " select NVL(max(PUN_ID),0) as max_id from PER_WORKFLOW_PUNISHMENT ";
                    
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$PUN_ID = $data[max_id] + 1;
		
			$cmd = " 	insert into PER_WORKFLOW_PUNISHMENT (PUN_ID, PER_ID, INV_NO, PUN_NO, PUN_REF_NO, PUN_TYPE, 
								PUN_STARTDATE, PUN_ENDDATE, CRD_CODE, PEN_CODE, PUN_PAY, PUN_SALARY, 
								PUN_REMARK, PER_CARDNO, UPDATE_USER, UPDATE_DATE, PUN_WF_STATUS)
								values ($PUN_ID, $PER_ID, '$INV_NO', '$PUN_NO', '$PUN_REF_NO', '$PUN_TYPE', 
								'$PUN_STARTDATE', '$PUN_ENDDATE', '".trim($CRD_CODE)."', '".trim($PEN_CODE)."', '0', '0', 
								'$PUN_REMARK', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE', '01')  ";
			//echo "2:".$cmd."<br><br>";
                        $db_dpis->send_cmd($cmd);
                        //echo $cmd;
			//$db_dpis->show_error();
		
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��������ѵԷҧ�Թ�� [$PER_ID : $PUN_ID : $CRD_CODE]");
			$F_REFRESH = "1";
		} // end if
	}// end if �礢����ū��
	
	if($command=="UPDATE" && $PER_ID && $PUN_ID){
		$PUN_STARTDATE =  save_date($PUN_STARTDATE);
		$PUN_ENDDATE =  save_date($PUN_ENDDATE);
		
		$cmd2 = " select  CRD_CODE, PUN_TYPE, PEN_CODE, PUN_STARTDATE, PUN_ENDDATE from PER_WORKFLOW_PUNISHMENT 
							where PER_ID=$PER_ID and CRD_CODE='$CRD_CODE' and PUN_TYPE=$PUN_TYPE and  
							PEN_CODE='$PEN_CODE' and PUN_STARTDATE='$PUN_STARTDATE' and PUN_ENDDATE='$PUN_ENDDATE' and PUN_ID != $PUN_ID ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("�������ö��䢢������� ���ͧ�ҡ�س�кءóդ����Դ, ������, �ѹ����Ѻ�� ����ѹ�������ش�� ��� !!!");
		-->   </script>	<? }  
		else {	
			$cmd = " UPDATE PER_WORKFLOW_PUNISHMENT SET
								INV_NO='$INV_NO', 
								PUN_NO='$PUN_NO', 
								PUN_REF_NO='$PUN_REF_NO', 
								PUN_TYPE='$PUN_TYPE', 
								PUN_STARTDATE='$PUN_STARTDATE', 
								PUN_ENDDATE='$PUN_ENDDATE', 
								CRD_CODE='$CRD_CODE', 
								PEN_CODE='$PEN_CODE', 
								PUN_REMARK='$PUN_REMARK', 
								PER_CARDNO='$PER_CARDNO', 
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE'
							WHERE PUN_ID=$PUN_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();		
	
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢻���ѵԷҧ�Թ�� [$PER_ID : $PUN_ID : $CRD_CODE]");
		} // end if
	}// end if �礢����ū��
	
	if($command=="DELETE" && $PER_ID && $PUN_ID){
		$cmd = " select CRD_CODE from PER_WORKFLOW_PUNISHMENT where PUN_ID=$PUN_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$CRD_CODE = $data[CRD_CODE];
		
		$cmd = " delete from PER_WORKFLOW_PUNISHMENT where PUN_ID=$PUN_ID ";
		$db_dpis->send_cmd($cmd);

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź����ѵԷҧ�Թ�� [$PER_ID : $PUN_ID : $CRD_CODE]");
	} // end if

//  UPDATE PUN_WF_STATUS
	$updtype=explode("-",$command);
	$F_REFRESH = "";
	if($updtype[0]=="UPD" && $PER_ID && $PUN_ID){
		$PUN_WF_STATUS = $updtype[1];
		$cmd2="select * from PER_WORKFLOW_PUNISHMENT where PER_ID=$PER_ID and PUN_ID=$PUN_ID";
                //echo "1:".$cmd2."<br><br>";
		$count=$db_dpis->send_cmd($cmd2);
//		$db_dpis->show_error(); echo "<hr><br>";
		if($count) { 
			$cmd = " 	update PER_WORKFLOW_PUNISHMENT set
								PUN_WF_STATUS='$updtype[1]', 
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE'
							where PER_ID=$PER_ID and PUN_ID=$PUN_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$F_REFRESH = "1";
		
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢻���ѵԡ���֡�� [$PER_ID : $PER_NAME : $EN_CODE]");
			
			if ($PUN_WF_STATUS=="04") {
				$command ="COMMAND"; 	// �ó� 04 ͹��ѵ� - �����͹��������������ԧ ��ѧ�ҡ update status ��� ��� temp
															// ���� $command ����� = "COMMAND" ���ͨ������ loop � if ��ͨҡ���
			}
		} // end if
                //echo "2:".$command."<br><br>";
	} // end if  UPDATE PUN_WF_STATUS
	
	if($command=="COMMAND" && $PER_ID){
		$PUN_STARTDATE =  save_date($PUN_STARTDATE);
		$PUN_ENDDATE =  save_date($PUN_ENDDATE);
		/*���*/
		/*$cmd2="	select  CRD_CODE, PUN_TYPE, PEN_CODE, PUN_STARTDATE, PUN_ENDDATE, PUN_WF_STATUS 
						from PER_PUNISHMENT 
						where PER_ID=$PER_ID and CRD_CODE='$CRD_CODE' and PUN_TYPE=$PUN_TYPE 
									and  PEN_CODE='$PEN_CODE' and PUN_STARTDATE='$PUN_STARTDATE' and PUN_ENDDATE='$PUN_ENDDATE' ";*/
		/* Release 5.1.0.7(ź PUN_WF_STATUS �͡)*/
                $cmd2="	select  CRD_CODE, PUN_TYPE, PEN_CODE, PUN_STARTDATE, PUN_ENDDATE 
						from PER_PUNISHMENT 
						where PER_ID=$PER_ID and trim(CRD_CODE)='".trim($CRD_CODE)."' and PUN_TYPE=$PUN_TYPE 
									and  trim(PEN_CODE)='".trim($PEN_CODE)."' and PUN_STARTDATE='$PUN_STARTDATE' and PUN_ENDDATE='$PUN_ENDDATE' ";
                /*Release 5.1.0.7 end*/
                //echo "3:".$cmd2."<br><br>";
                $count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("�������ö������������ ���ͧ�ҡ�س�кءóդ����Դ, ������, �ѹ����Ѻ�� ����ѹ�������ش�� ��� !!!");
		-->   </script>	<? }  
		else {	  	
			$cmd = " select max(PUN_ID) as max_id from PER_PUNISHMENT ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$PUN_ID = $data[max_id] + 1;
		
			$cmd = " 	insert into PER_PUNISHMENT (PUN_ID, PER_ID, INV_NO, PUN_NO, PUN_REF_NO, PUN_TYPE, 
								PUN_STARTDATE, PUN_ENDDATE, CRD_CODE, PEN_CODE, PUN_PAY, PUN_SALARY, 
								PUN_REMARK, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
								values ($PUN_ID, $PER_ID, '$INV_NO', '$PUN_NO', '$PUN_REF_NO', '$PUN_TYPE', 
								'$PUN_STARTDATE', '$PUN_ENDDATE', '".trim($CRD_CODE)."', '".trim($PEN_CODE)."', '0', '0', 
								'$PUN_REMARK', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE')  ";
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
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/54';
				$MOVERENAME = "PER_ATTACHMENT54";
			}else{
				$MOVESECOND_SUBDIR = $MOVEFIRST_SUBDIR.'/'.$MOVE_CATEGORY;
				$MOVEFINAL_PATH = $MOVESECOND_SUBDIR.'/'.$PUN_ID;
				$MOVERENAME = $MOVE_CATEGORY.$PUN_ID;
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
			//___echo "TEST : [ $PUN_ID ] <$FILE_PATH ~~ $MOVE_PATH> $FILE_PATH => $MOVEFINAL_PATH<br>$cmdup<br>";
			//###################################

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��������ѵԷҧ�Թ�� [$PER_ID : $PUN_ID : $CRD_CODE]");
		} // end if
	} // end if �礢����ū��

	if(($UPD && $PER_ID && $PUN_ID) || ($VIEW && $PER_ID && $PUN_ID)){
		$cmd = "	SELECT PUN_ID, INV_NO, PUN_NO, PUN_REF_NO, ppm.CRD_CODE, pcd.CRD_NAME, PUN_STARTDATE, PUN_ENDDATE,  
							ppm.PEN_CODE, pp.PEN_NAME, PUN_TYPE, PUN_REMARK, ppm.UPDATE_USER, ppm.UPDATE_DATE, PUN_WF_STATUS
				FROM		PER_WORKFLOW_PUNISHMENT ppm, PER_CRIME_DTL pcd, PER_PENALTY pp   
				WHERE		PUN_ID=$PUN_ID and ppm.CRD_CODE=pcd.CRD_CODE and ppm.PEN_CODE=pp.PEN_CODE and PUN_WF_STATUS!='04' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		
		$INV_NO = $data[INV_NO];
		$PUN_NO = $data[PUN_NO];
		$PUN_REF_NO = $data[PUN_REF_NO];
		$PUN_TYPE = $data[PUN_TYPE];
		$PUN_REMARK = $data[PUN_REMARK];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		
		$PUN_STARTDATE = show_date_format($data[PUN_STARTDATE], 1);
		$PUN_ENDDATE = show_date_format($data[PUN_ENDDATE], 1);

		$CRD_CODE = $data[CRD_CODE];
		$CRD_NAME = $data[CRD_NAME];
		$PEN_CODE = $data[PEN_CODE];
		$PEN_NAME = $data[PEN_NAME];		
		$PUN_WF_STATUS = $data[PUN_WF_STATUS];

		if($CRD_CODE){
			$cmd = " select CR_NAME from PER_CRIME pc, PER_CRIME_DTL pcd where CRD_CODE='$CRD_CODE' and pc.CR_CODE=pcd.CR_CODE";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$CR_NAME = $data2[CR_NAME];
		} // end if
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($PUN_ID);
		unset($INV_NO);
		unset($PUN_NO);
		unset($PUN_REF_NO);
		unset($PUN_TYPE);
		unset($PUN_REMARK);
		unset($PUN_STARTDATE);
		unset($PUN_ENDDATE);

		unset($CRD_CODE);
		unset($CRD_NAME);
		unset($CR_NAME);
		unset($PEN_CODE);
		unset($PEN_NAME);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
		
		//$PUN_TYPE = 1;
	} // end if
?>