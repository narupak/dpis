<?	
	include("php_scripts/session_start.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	$UPDATE_DATE = date("Y-m-d H:i:s");

	//��˹� PATH ����Ѻ�����
	$PATH_MAIN = "../attachments";
	$FIRST_SUBDIR =	$PATH_MAIN."/PER_MESSAGE"; 
	$FILE_PATH = $FIRST_SUBDIR."/".$MSG_ID;
	function calsizebyte ($size) {
		if ($size < 1024) {
  			$issize = $size."b";
  		} else {
			$issize = sprintf ("%0.0f",$size / 1024)."KB";
			if ($issize >= 1024) $issize = sprintf ("%0.02f",$issize / 1024)."MB";
		}
	return $issize;
	}

	//--�ó� : �Ѿ�����������к� ���Ҩҡ˹�� personal_message_form.php 
	if (trim($MSG_ID)){		//---UPLOADFILE
		//���ҧ���������� ����ѧ���������
		if (!is_dir($FILE_PATH)) {
			$msgresult = "";		$mode = 0755;
			//��������á
			if (!is_dir($FIRST_SUBDIR)) {
				$result1= mkdir($FIRST_SUBDIR,$mode);
			}
			//�������������ش����
			if(is_dir($FIRST_SUBDIR)){
				$result2= mkdir($FILE_PATH,$mode);
			}else{
					$msgresult = "<span style='color:#FF0000'>���ҧ������ $FIRST_SUBDIR ����� !!!</span><br>";
			}
			//umask(0);		echo umask();  //test
			if(!$result2){
					$msgresult .= "<span style='color:#FF0000'>���ҧ������ $FILE_PATH ����� !!!</span><br>";
			}
		}
		if($msgresult){	echo $msgresult;	}
				
		//��������ҧ������������ �������������
		if (is_dir($FILE_PATH) && $_FILES && $temp_document_name){
			if(move_uploaded_file($_FILES['MSG_DOCUMENT']['tmp_name'],"$FILE_PATH/$temp_document_name")){
				echo "<script language='JavaScript'>alert('�������� $temp_document_name ����Ѻ MSG_ID:$MSG_ID ����'); self.close();</script>";
			insert_log("UPLOAD $file_namedb FILE $temp_document_name ����Ѻ MSG_ID : $MSG_ID ");
			} // if success upload file
			else{
				//echo "<span style='color:#FF0000'>�������ö����������</span><br>";
			}
		}
	} //end if (trim($MSG_ID)

	//***����Ѻ personal_msgfile.html ��ҹ�� 
	//ź���ͧ MSG_ID ���
	if ($command == "DELFILE" && count($filedel)) {
			//---���������١ include �Ҩҡ personal_message_form.php �������� function ��� �������� log
			include("php_scripts/function_share.php");
			//--------------------------------------------------------	
			for ($i = 0 ; $i < count($filedel) ; $i++) {
				$filename = $filedel[$i];
				unlink("$FILE_PATH/$filename");
			insert_log("DELETE $file_namedb FILE");
			} // for
	} // if
?>