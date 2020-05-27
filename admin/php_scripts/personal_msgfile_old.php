<?	
	include("php_scripts/session_start.php");

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	$UPDATE_DATE = date("Y-m-d H:i:s");

	//กำหนด PATH สำหรับเก็บไฟล์
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

	//--กรณี : อัพไฟล์ใหม่เข้าระบบ ส่งมาจากหน้า personal_message_form.php 
	if (trim($MSG_ID)){		//---UPLOADFILE
		//สร้างโฟลเดอร์ใหม่ ถ้ายังไม่มีอยู่
		if (!is_dir($FILE_PATH)) {
			$msgresult = "";		$mode = 0755;
			//โฟล์เดอร์แรก
			if (!is_dir($FIRST_SUBDIR)) {
				$result1= mkdir($FIRST_SUBDIR,$mode);
			}
			//โฟล์เดอร์เก็บไฟล์สุดท้าย
			if(is_dir($FIRST_SUBDIR)){
				$result2= mkdir($FILE_PATH,$mode);
			}else{
					$msgresult = "<span style='color:#FF0000'>สร้างโฟลเดอร์ $FIRST_SUBDIR ไม่ได้ !!!</span><br>";
			}
			//umask(0);		echo umask();  //test
			if(!$result2){
					$msgresult .= "<span style='color:#FF0000'>สร้างโฟลเดอร์ $FILE_PATH ไม่ได้ !!!</span><br>";
			}
		}
		if($msgresult){	echo $msgresult;	}
				
		//เมื่อสร้างโฟลเดอร์ได้แล้ว ก็เอาไฟล์นำเข้าไป
		if (is_dir($FILE_PATH) && $_FILES && $temp_document_name){
			if(move_uploaded_file($_FILES['MSG_DOCUMENT']['tmp_name'],"$FILE_PATH/$temp_document_name")){
				echo "<script language='JavaScript'>alert('นำเข้าไฟล์ $temp_document_name สำหรับ MSG_ID:$MSG_ID แล้ว'); self.close();</script>";
			insert_log("UPLOAD $file_namedb FILE $temp_document_name สำหรับ MSG_ID : $MSG_ID ");
			} // if success upload file
			else{
				//echo "<span style='color:#FF0000'>ไม่สามารถนำเข้าไฟล์ได้</span><br>";
			}
		}
	} //end if (trim($MSG_ID)

	//***สำหรับ personal_msgfile.html เท่านั้น 
	//ลบไฟล์ของ MSG_ID นี้
	if ($command == "DELFILE" && count($filedel)) {
			//---เพราะไม่ได้ถูก include มาจาก personal_message_form.php เลยไม่มี function นี้ ใช้เพื่อเก็บ log
			include("php_scripts/function_share.php");
			//--------------------------------------------------------	
			for ($i = 0 ; $i < count($filedel) ; $i++) {
				$filename = $filedel[$i];
				unlink("$FILE_PATH/$filename");
			insert_log("DELETE $file_namedb FILE");
			} // for
	} // if
?>