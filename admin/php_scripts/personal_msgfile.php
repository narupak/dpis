<?	
	include("php_scripts/session_start.php");
	if(strstr($_SERVER['HTTP_REFERER'],"personal_msgfile")){
		//---เพราะไม่ได้ถูก include มาจาก personal_message_form.php เลยไม่มี function นี้ ใช้เพื่อเก็บ log
		include("php_scripts/function_share.php");
	}
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_obj = $db_dpis2;
	//echo "	$db_dpis / $db_obj ";
	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	$UPDATE_DATE = date("Y-m-d H:i:s");

	//กำหนด PATH สำหรับเก็บไฟล์
	$PATH_MAIN = "../attachments";
	$FIRST_SUBDIR =	$PATH_MAIN."/PER_MESSAGE"; 
	$FILE_PATH = $FIRST_SUBDIR."/".$MSG_ID;
	//กำหนดวันที่
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
	
	// =================================================
/********
	function calsizebyte ($size) {
		if ($size < 1024) {
  			$issize = $size."b";
  		} else {
			$issize = sprintf ("%0.0f",$size / 1024)."KB";
			if ($issize >= 1024) $issize = sprintf ("%0.02f",$issize / 1024)."MB";
		}
	return $issize;
	}**********/
	
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
/*********
		//แบบตอนแรก ใช้กับชื่อไฟล์ภาษาไทยไม่ได้
		if (is_dir($FILE_PATH) && $_FILES && $temp_document_name) {
			foreach($_FILES as $row) {
				$temp_document_name = $row['name'];
				echo "$temp_document_name <br>";
				if(move_uploaded_file($row['tmp_name'],"$FILE_PATH/$temp_document_name")){
					echo "<script language='JavaScript'>alert('นำเข้าไฟล์ $temp_document_name สำหรับ MSG_ID:$MSG_ID แล้ว'); self.close();</script>";
				insert_log("UPLOAD $file_namedb FILE $temp_document_name สำหรับ MSG_ID : $MSG_ID ");
				} // if success upload file
				else{
					//echo "<span style='color:#FF0000'>ไม่สามารถนำเข้าไฟล์ได้</span><br>";
				}
			} // foreach
		}
********/		
		
//เมื่อสร้างโฟลเดอร์ได้แล้ว ก็เอาไฟล์นำเข้าไป
if (is_dir($FILE_PATH) && $_FILES && $temp_document_name) {
			foreach($_FILES as $row) {
				$temp_document_name = $row['name'];	//ชื่อไฟล์จริง	
				$file_name = $row['tmp_name'];					//$file_name = substr($temp_document_name, 0, ($pos - strlen($temp_document_name)));
//				echo "$temp_document_name + $file_name<br>";
				$pos = strrpos($temp_document_name, ".");	
				$temp_document_ext = substr($temp_document_name, ($pos+1));
				$temp_document_encode = $SESS_USERID."_". date("Ymd") ."_". md5($temp_document_name) .".". $temp_document_ext;
				$size = filesize($temp_document_name);	//$size = calsizebyte($temp_document_size);

//				echo $temp_document_encode."<br>";
				//เมื่อสร้างโฟลเดอร์ได้แล้ว ก็เอาไฟล์นำเข้าไป
				if(move_uploaded_file($file_name,"$FILE_PATH/$temp_document_encode")){	
					//เก็บข้อมูลลง database
					if($db_type=="oci8"){
						//ดึง editor_attachment ID ล่าสุดมา
						$cmd = " select ID from EDITOR_ATTACHMENT order by ID DESC";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$data = $db_dpis->get_array();
						$EDT_ID = $data[ID]+1;	
						$cmd = 	" 	insert into EDITOR_ATTACHMENT
											(ID,REAL_FILENAME, SHOW_FILENAME, FILE_TYPE, DESCRIPTION, FILE_SIZE, USER_ID, USER_GROUP_ID, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
											values 
											($EDT_ID,'$temp_document_encode', '$temp_document_name', '$temp_document_type', '$description', '$size', $SESS_USERID, $SESS_USERGROUP, $update_date, $SESS_USERID, $update_date, $SESS_USERID) ";
					}else{
						$cmd = 	" 	insert into editor_attachment 
											(real_filename, show_filename, file_type, description, size, user_id, user_group_id, create_date, update_date, update_by)
											values 
											('$temp_document_encode', '$temp_document_name', '$temp_document_type', '$description', '$size', $SESS_USERID, $SESS_USERGROUP, $update_date, $update_date, '$SESS_USERNAME') ";
					}
					$db_obj->send_cmd($cmd);
					//$db_obj->show_error();
//echo "<br>ins : $cmd<br>";
					
					/*//สั่งให้ปิดวินโดวส์เมื่ออัพโหลดเสร็จสิ้น 
					echo "<script language='JavaScript'>alert('นำเข้าไฟล์ $temp_document_name สำหรับ MSG_ID:$MSG_ID แล้ว'); self.close();</script>"; */
					insert_log("UPLOAD $file_namedb FILE $temp_document_name สำหรับ MSG_ID : $MSG_ID ");
				} // if success upload file
				else{
					//echo "<span style='color:#FF0000'>ไม่สามารถนำเข้าไฟล์ได้</span><br>";
				}
			} // foreach
	} //end if (is_dir($FILE_PATH) && $_FILES && $temp_document_name)

	} //end if (trim($MSG_ID)

	//***สำหรับ personal_msgfile.html เท่านั้น 
	//ลบไฟล์ของ MSG_ID นี้
	if ($command == "DELFILE" && (is_array($filedel) && !empty($filedel))) {
			$str_del_file="";
			for ($i = 0 ; $i < count($filedel) ; $i++) {
					$file_id = trim($filedel[$i]);
					if($file_id){
						$str_del_file .= $file_id.","; 
						if($db_type=="oci8"){
							$REAL_FILENAME = "REAL_FILENAME";		$SHOW_FILENAME = "SHOW_FILENAME";			$USER_ID = "USER_ID";
							$cmd = "select $REAL_FILENAME, $SHOW_FILENAME,$USER_ID
											from 	EDITOR_ATTACHMENT
											where ID = $file_id";
						}else{
							$REAL_FILENAME = "real_filename";			$SHOW_FILENAME = "show_filename";			$USER_ID = "user_id";
							$cmd = " 	select $REAL_FILENAME, $SHOW_FILENAME,$USER_ID
												from 	editor_attachment 
												where  id='$file_id' ";
						}
						$db_obj->send_cmd($cmd);
						$data = $db_obj->get_array();
						$old_filename_encode = trim($data[$REAL_FILENAME]);
						$owner_file = trim($data[$USER_ID]); 
//echo "$cmd<br>$old_filename_encode<br>$owner_file";
						if($SESS_USERID==1 || $SESS_USERID==$owner_file){	//ลบได้เฉพาะเจ้าของไฟล์ที่อัพมา และ Admin
								//___echo $cmd."<br>$FILE_PATH/$old_filename_encode";
								unlink("$FILE_PATH/$old_filename_encode");
								insert_log("DELETE $old_filename_encode FILE");
						} //end if SESS_USERID
					} //end if
			} // for
			if(trim($str_del_file) && ($SESS_USERID==1 || $SESS_USERID==$owner_file)){	//ลบได้เฉพาะเจ้าของไฟล์ที่อัพมา และ Admin
				$str_del_file = substr($str_del_file,0,-1);
				if($db_type=="oci8"){
					$cmd = "delete from EDITOR_ATTACHMENT where ID in ($str_del_file)";
				}else{
					$cmd = "delete from editor_attachment where id in ($str_del_file)";
				}			
				$db_obj->send_cmd($cmd);
	//			$db_obj->show_error();
//echo "<br>$cmd<br>";
	
				insert_log("DELETE $file_namedb FILE");
			}	//end if
	} // if
?>