<?
	//echo "->".$command;
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	//กำหนดค่า
	if($db_type=="oci8"){
		$db_obj = $db_dpis;
	}else{
		$db_obj = $db;
	}	
	//session_start();
	function calsizebyte ($size) {
		if ($size < 1024) {
  			$issize = $size."b";
  		} else {
			$issize = sprintf ("%0.0f",$size / 1024)."KB";
			if ($issize >= 1024) $issize = sprintf ("%0.02f",$issize / 1024)."MB";
		}
	return $issize;
	}

	$PATH_MAIN = "../attachments";
	//รูปภาพ
	$URL_IMAGES = "http://$HTTP_HOST/".$virtual_site."attachments/editor_image";
	$PATH_IMAGES = "$PATH_MAIN/editor_image";
	//ไฟล์แนบ
	$URL_ATTACHMENTS = "http://$HTTP_HOST/".$virtual_site."attachments/editor_attachment";
	$PATH_ATTACHMENTS = "$PATH_MAIN/editor_attachment";
	// =================================================
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
	
	if ($PAGE == 1) { //สร้างข้อมูลไฟล์ของบุคคลแต่ละคน
		//สำหรับ :  อัพโหลดไฟล์
		$description = trim($description);
		$size = calsizebyte($filename_size);
		//ชื่อที่เก็บลงใน database
		$file_namedb = $PER_CARDNO."_".$CATEGORY.$LAST_SUBDIR;
		//ตรวจสอบโฟลเดอร์
		$FIRST_SUBDIR = $PATH_MAIN.'/'.$PER_CARDNO;
		$SECOND_SUBDIR = $FIRST_SUBDIR.'/'.$CATEGORY;
		$FINAL_PATH = $SECOND_SUBDIR.'/'.$LAST_SUBDIR;
                
                
//		echo "file_namedb=$file_namedb , FIRST_SUBDIR=$FIRST_SUBDIR , SECOND_SUBDIR=$SECOND_SUBDIR , FINAL_PATH=$FINAL_PATH command=$command<br>";

		if ($command == "UPLOAD") {
//			echo "filename_name :: $filename_name<br>";
			if ($filename_name != "") {
				$pos = strrpos($filename_name, ".");	
				$file_name = substr($filename_name, 0, ($pos - strlen($filename_name)));
				$file_ext = substr($filename_name, ($pos+1));
				$filename_encode = $file_namedb."_". $SESS_USERID ."_". date("Ymd") ."_". md5($file_name) .".". $file_ext;

				//สร้างโฟลเดอร์ใหม่ ถ้ายังไม่มีอยู่
				if (!is_dir($FINAL_PATH)) {
					$msgresult1 = $msgresult2 = $msgresult3 = "";
					$mode = 0755;
					
					//โฟล์เดอร์แรก
					if (!is_dir($FIRST_SUBDIR)) {
						$result1= mkdir($FIRST_SUBDIR,$mode);
					}
					//โฟล์เดอร์ที่สอง
					if (!is_dir($SECOND_SUBDIR)) {
						if($result1 || is_dir($FIRST_SUBDIR)){
							$result2 = mkdir($SECOND_SUBDIR,$mode);
						}else{
							$msgresult1 = " <br><span style='color:#FF0000'>สร้างโฟลเดอร์ $FIRST_SUBDIR ไม่ได้ !!!</span><br>";
						}
					}					
					//โฟล์เดอร์สุดท้าย
					if($result2 || is_dir($SECOND_SUBDIR)){
						$result3= mkdir($FINAL_PATH,$mode);
					}else{
						$msgresult1="";
						$msgresult2 = "<span style='color:#FF0000'>สร้างโฟลเดอร์ $SECOND_SUBDIR ไม่ได้ !!!</span><br>";
					}
					//umask(0);		echo umask();  //test
					if(!$result3){
						$msgresult2="";
						$msgresult3 = "<span style='color:#FF0000'>สร้างโฟลเดอร์ $FINAL_PATH ไม่ได้ !!!</span><br>";
					}
				}
				if($msgresult3){	echo $msgresult3;	}
				
				//เมื่อสร้างโฟลเดอร์ได้แล้ว ก็เอาไฟล์นำเข้าไป
				if (is_dir($FINAL_PATH)){
					if(move_uploaded_file($filename,"$FINAL_PATH/$filename_encode") ){
						//เก็บข้อมูลลง database
						if($db_type=="oci8") {	
							//ดึง editor_attachment ID ล่าสุดมา
							$cmd = " select ID from EDITOR_ATTACHMENT order by ID DESC";			//  $cmd = " select max(ID) as max_id from EDITOR_ATTACHMENT";
							$db_dpis->send_cmd($cmd);
							//$db_dpis->show_error();
							$data = $db_dpis->get_array();
							$EA_ID = $data[ID]+1;						
							$cmd = 	" 	insert into EDITOR_ATTACHMENT
											(ID,REAL_FILENAME, SHOW_FILENAME, FILE_TYPE, DESCRIPTION, FILE_SIZE, USER_ID, USER_GROUP_ID, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
											values 
											($EA_ID,'$filename_encode', '$filename_name', '$filename_type', '$description', '$size', $SESS_USERID, $SESS_USERGROUP, $update_date, $update_by, $update_date, $update_by) ";
						}else{	
							$cmd = 	" 	insert into editor_attachment 
												(real_filename, show_filename, file_type, description, size, user_id, user_group_id, create_date, update_date, update_by)
												values 
												('$filename_encode', '$filename_name', '$filename_type', '$description', '$size', $SESS_USERID, $SESS_USERGROUP, $update_date, $update_date, $update_by) ";
						}
						$db_obj->send_cmd($cmd);
//						$db_obj->show_error();
//						echo "insert=$cmd<br>";

						echo "นำเข้าไฟล์ $filename_name [$description] แล้ว<br>";
					insert_log("UPLOAD $file_namedb FILE $filename_name [$description]");
					} // if success upload file
					else{
						//echo "<span style='color:#FF0000'>ไม่สามารถนำเข้าไฟล์ได้</span><br>";
					}
				}
				
			} // if filename_name != ""
		} // if command
	
		if ($command == "UPDATE") {
			if ($filename_name != "") {
				if($db_type=="oci8"){
					$REAL_FILENAME="REAL_FILENAME";
					$SHOW_FILENAME="SHOW_FILENAME";
					if($SESS_USERID != 1){	$search_userid = "and USER_ID = ".$SESS_USERID;		}	//ที่ไม่ใช่ ADMIN
					$cmd = " select $REAL_FILENAME, $SHOW_FILENAME
									from 	EDITOR_ATTACHMENT
									where ID = $file_id $search_userid
							  ";
				}else{
					$REAL_FILENAME="real_filename";
					$SHOW_FILENAME="show_filename";
					if($SESS_USERID != 1){	$search_userid = "and user_id = ".$SESS_USERID;		}	//ที่ไม่ใช่ ADMIN
					$cmd = " select $REAL_FILENAME, $SHOW_FILENAME
									from 	editor_attachment 
									where id = '$file_id' $search_userid
							  ";
				}  
				$db_obj->send_cmd($cmd);
				$data = $db_obj->get_array();
				//$data = array_change_key_case($data, CASE_LOWER);
				//echo "<br>$up_dbsamefile ::  $cmd<br>";
				$old_filename_encode = $data[$REAL_FILENAME];
				$old_show_filename = $data[$SHOW_FILENAME];
				
				if($up_dbsamefile==1){
					if($filename_name != $old_show_filename){ //ซ้ำกับชื่อไฟล์ของเรคคอร์ดอื่นใน database
						$filename_name = $old_show_filename;
					}
				}
				$pos = strrpos($filename_name, ".");	
				$file_name = substr($filename_name, 0, ($pos - strlen($filename_name)));
				$file_ext = substr($filename_name, ($pos+1));
				$filename_encode = $file_namedb."_". $SESS_USERID ."_". date("Ymd") ."_". md5($file_name) .".". $file_ext;

				unlink("$FINAL_PATH/$old_filename_encode");		//เอาชื่อไฟล์เดิมออก
				move_uploaded_file($filename,"$FINAL_PATH/$filename_encode");		//ใส่ชื่อใหม่ไป
				if($db_type=="oci8"){
					$where_file = " , SHOW_FILENAME = '$filename_name', REAL_FILENAME = '$filename_encode' , FILE_TYPE = '$filename_type', FILE_SIZE = '$size' " ;
				}else{
					$where_file = " , show_filename = '$filename_name', real_filename = '$filename_encode' , file_type = '$filename_type', size = '$size' " ;
				}
			} // if
			if($db_type=="oci8"){
				if($SESS_USERID != 1){	$search_userid = "and USER_ID = ".$SESS_USERID;		}	//ที่ไม่ใช่ ADMIN
				$cmd = " update 	EDITOR_ATTACHMENT set
												DESCRIPTION = '$description' $where_file
								 where 	ID = $file_id $search_userid
							  ";
			}else{
				if($SESS_USERID != 1){	$search_userid = "and user_id = ".$SESS_USERID;		}	//ที่ไม่ใช่ ADMIN
				$cmd = " update 	editor_attachment set
												description = '$description' $where_file
								 where 	id = '$file_id' $search_userid
							  ";
			}
			$db_obj->send_cmd($cmd);
//			$db->show_error();
//			echo "<br>$up_dbsamefile ::  $cmd<br>";
//			echo "update=$cmd<br>";

			insert_log("UPDATE $file_namedb FILE $filename_name [$description]");
		} // if

		if ($command == "REPLACE") { //ชื่อไฟล์ซ้ำ อัพเดทข้อมูลใหม่แทนที่ข้อมูลเดิมทั้งหมด
			if ($filename_name != "") {
				$pos = strrpos($filename_name, ".");	
				$file_name = substr($filename_name, 0, ($pos - strlen($filename_name)));
				$file_ext = substr($filename_name, ($pos+1));
				//$filename_encode = $file_namedb."_". $SESS_USERID ."_". date("Ymd") ."_". md5($file_name) .".". $file_ext;
			
				if($db_type=="oci8"){
					$FILE_ID="ID";
					$REAL_FILENAME="REAL_FILENAME";
					if($SESS_USERID != 1){	$search_userid = "and USER_ID = ".$SESS_USERID;		}	//ที่ไม่ใช่ ADMIN
					$cmd = " select $FILE_ID,$REAL_FILENAME
									from 	EDITOR_ATTACHMENT
									where SHOW_FILENAME = '$filename_name' $search_userid
								  ";
				}else{
					$FILE_ID="id";
					$REAL_FILENAME="real_filename";
					if($SESS_USERID != 1){	$search_userid = "and user_id = ".$SESS_USERID;		}	//ที่ไม่ใช่ ADMIN
					$cmd = " select $FILE_ID,$REAL_FILENAME
									from 	editor_attachment 
									where show_filename = '$filename_name' $search_userid
								  ";
				}
				$db_obj->send_cmd($cmd);
				$data = $db_obj->get_array();
				//$data = array_change_key_case($data, CASE_LOWER);
				$file_id = $data[$FILE_ID];
				$old_filename_encode = $data[$REAL_FILENAME];
				//unlink("$FINAL_PATH/$old_filename_encode");
				move_uploaded_file($filename,"$FINAL_PATH/$old_filename_encode");	 //แทนที่ไฟล์เดิม	
				if($db_type=="oci8"){
					$where_file = " , SHOW_FILENAME = '$filename_name', REAL_FILENAME = '$old_filename_encode' , FILE_TYPE = '$filename_type', FILE_SIZE = '$size' " ;
				}else{
					$where_file = " , show_filename = '$filename_name', real_filename = '$old_filename_encode' , file_type = '$filename_type', size = '$size' " ;
				}
			} // if
			if($db_type=="oci8"){
				if($SESS_USERID != 1){	$search_userid = "and USER_ID = ".$SESS_USERID;		}	//ที่ไม่ใช่ ADMIN
				$cmd = " update 	EDITOR_ATTACHMENT set
											DESCRIPTION = '$description' $where_file
							 where 	ID = $file_id  $search_userid
						  ";
			}else{
				if($SESS_USERID != 1){	$search_userid = "and user_id = ".$SESS_USERID;		}	//ที่ไม่ใช่ ADMIN
				$cmd = " update 	editor_attachment set
											description = '$description' $where_file
							 where 	id = '$file_id' $search_userid
						  ";
			}
			$db_obj->send_cmd($cmd);
//			$db_obj->show_error();
//			echo "Replace:<br>$cmd<br>";

			insert_log("REPLACE $file_namedb FILE $filename_name [$description]");
		}

		if ($command == "DELFILE" && count($filedel)) {
                    /*if($CATEGORY=="PER_ATTACHMENT"){
                        $FINAL_PATH="../attachments/PER_COMMAND/".$COM_ID;
                    }*/
			for ($i = 0 ; $i < count($filedel) ; $i++) {
				$file_id = $filedel[$i];
				if(strstr($file_id,"convert")){
					$old_filename_encode = str_replace("convert", "", $file_id);
					unlink("$FINAL_PATH/$old_filename_encode");
                                        //echo "$FINAL_PATH/$old_filename_encode";
				}else{
					$str_del_file .= $file_id.",";
					if($db_type=="oci8"){
						$REAL_FILENAME = "REAL_FILENAME";
						if($SESS_USERID != 1){	$search_userid = "and USER_ID = ".$SESS_USERID;		}	//ที่ไม่ใช่ ADMIN
						$cmd = "select 	$REAL_FILENAME
										from 	EDITOR_ATTACHMENT
										where ID = $file_id $search_userid";
					}else{
						$REAL_FILENAME = "real_filename";
						if($SESS_USERID != 1){	$search_userid = "and user_id = ".$SESS_USERID;		}	//ที่ไม่ใช่ ADMIN
						$cmd = "select 	 $REAL_FILENAME
										from 	editor_attachment 
										where id = '$file_id' $search_userid";
					}
					$db_obj->send_cmd($cmd);
					$data = $db_obj->get_array();
					//$data = array_change_key_case($data, CASE_LOWER);
					$old_filename_encode = $data[$REAL_FILENAME];
					unlink("$FINAL_PATH/$old_filename_encode");
                                        //echo "$FINAL_PATH/$old_filename_encode";
				}
			} // for
			//___$str_del_file = join($filedel, ",");
			if(trim($str_del_file)){
				$str_del_file = substr($str_del_file,0,-1);
				if($db_type=="oci8"){
					if($SESS_USERID != 1){	$search_userid = "and USER_ID = ".$SESS_USERID;		}	//ที่ไม่ใช่ ADMIN
					$cmd = "delete from EDITOR_ATTACHMENT where ID in ($str_del_file) $search_userid";
				}else{
					if($SESS_USERID != 1){	$search_userid = "and user_id = ".$SESS_USERID;		}	//ที่ไม่ใช่ ADMIN
					$cmd = "delete from editor_attachment where id in ($str_del_file) $search_userid";
				}	
                                //echo $cmd;
				$db_obj->send_cmd($cmd);
	//			$db_obj->show_error();
	//			echo "<br>$cmd<br>";
			}

			insert_log("DELETE $file_namedb FILE");
		} // if

		//ดึงข้อมูลมาแก้ไข
		if ($UPD) {
			if($db_type=="oci8") {	
				$REAL_FILENAME="REAL_FILENAME";
				$SHOW_FILENAME="SHOW_FILENAME";
				$DESCRIPTION="DESCRIPTION";
				$FILE_SIZE="FILE_SIZE";
				$FILE_UPDATE_BY="UPDATE_BY";
				if($SESS_USERID != 1){	$search_userid = "and USER_ID = ".$SESS_USERID;		}	//ที่ไม่ใช่ ADMIN
				$cmd = " 	select 	$REAL_FILENAME, $SHOW_FILENAME, $DESCRIPTION, $FILE_SIZE, $FILE_UPDATE_BY
									from 		EDITOR_ATTACHMENT
									where 	ID = $file_id $search_userid
							   ";			
			}else{	
				$REAL_FILENAME="real_filename";
				$SHOW_FILENAME="show_filename";
				$DESCRIPTION="description";
				$FILE_SIZE="size";
				$FILE_UPDATE_BY="update_by";
				if($SESS_USERID != 1){	$search_userid = "and user_id = ".$SESS_USERID;		}	//ที่ไม่ใช่ ADMIN
				$cmd = " select  $REAL_FILENAME, $SHOW_FILENAME, $DESCRIPTION, $FILE_SIZE, $FILE_UPDATE_BY
							 from 		editor_attachment
							 where 	id = '$file_id' $search_userid
						  ";
			}
			$db_obj->send_cmd($cmd);
			//echo $cmd;
			//$db_obj->show_error();
			$data = $db_obj->get_array();
			$real_filename = $data[$REAL_FILENAME];
			$show_filename = $data[$SHOW_FILENAME];
			$description  = $data[$DESCRIPTION];
			$size  = $data[$FILE_SIZE];
			$update_by = $data[$FILE_UPDATE_BY];
			if($db_type=="oci8") {	
				//ดึงชื่อผู้โพสต์จาก ตาราง USER_DETAIL
				$cmd1 ="select FULLNAME from USER_DETAIL where ID=$update_by";	
				$db_dpis2->send_cmd($cmd1);
				$datausr = $db_dpis2->get_array();
				//$datausr = array_change_key_case($datausr, CASE_LOWER);
				$update_by = $datausr[FULLNAME];		//$SESS_USERID;	
			}
//echo "$db_obj [ $data ] -> $cmd <br><pre><u>$REAL_FILENAME</u> -> $real_filename  + <u>$SHOW_FILENAME</u> -> $show_filename + <u>$DESCRIPTION</u> -> $description + <u>$FILE_SIZE</u> -> $size + <u>$FILE_UPDATE_BY</u> -> $update_by</pre> ";
		}
	} // IF PAGE = 1
	// =================================================

/*********************	
	// ===================================================
	if ($PAGE == 2) { //นำเข้ารูปภาพ
		$description = trim($description);
		$size = calsizebyte($filename_size);
		if ($command == "UPLOAD") {
//			echo " :: $filename_name";
			if ($filename_name != "") {
				$width += 0;
				$height += 0;
				$pos = strrpos($filename_name, ".");	
				$file_name = substr($filename_name, 0, ($pos - strlen($filename_name)));
				$file_ext = substr($filename_name, ($pos+1));
				$filename_encode = "usr_image_". $SESS_USERID ."_". date("Ymd") ."_". md5($file_name) .".". $file_ext;
				if( move_uploaded_file($filename,"$PATH_IMAGES/$filename_encode") ){
					$cmd = 	" 	insert into editor_image 
										(real_filename, show_filename, file_type, description, width, height, size, user_id, user_group_id, create_date, update_date, update_by)
										values 
										('$filename_encode', '$filename_name', '$filename_type', '$description', $width, $height, '$size', $SESS_USERID, $SESS_USERGROUP, $update_date, $update_date, $update_by) ";
					$db->send_cmd($cmd);
//					$db->show_error();
					
					insert_log("UPLOAD IMAGE FILE $filename_name [$description]");
				} // if success upload file
			} // if
		} // if
		if ($command == "UPDATE") {
			$width += 0;
			$height += 0;
			if ($filename_name != "") {
				$pos = strrpos($filename_name, ".");	
				$file_name = substr($filename_name, 0, ($pos - strlen($filename_name)));
				$file_ext = substr($filename_name, ($pos+1));
				$filename_encode = "usr_image_". $SESS_USERID ."_". date("Ymd") ."_". md5($file_name) .".". $file_ext;

				$cmd = " 	select 	real_filename 
									from 	editor_image 
									where user_id = $SESS_USERID and id = '$file_id' ";
				$db->send_cmd($cmd);
				$data = $db->get_data();
				$data = array_change_key_case($data, CASE_LOWER);
				$old_filename_encode = $data[0];
				unlink("$PATH_IMAGES/$old_filename_encode");

				move_uploaded_file($filename,"$PATH_IMAGES/$filename_encode");
				$where_file = " , show_filename = '$filename_name', real_filename = '$filename_encode' , file_type = '$filename_type', size = '$size' " ;
			} // if
			$cmd = "	update 	editor_image set
												description = '$description', 
												width = $width, 
												height = $height,
												update_date = $update_date 
												$where_file
								where 	user_id = $SESS_USERID and id = '$file_id' 
						  ";
			$db->send_cmd($cmd);

			insert_log("UPDATE IMAGE FILE $filename_name [$description]");
		} // if

		if ($command == "DELFILE" && count($filedel)) {
			for ($i = 0 ; $i < count($filedel) ; $i++) {
				$file_id = $filedel[$i];
				$cmd = " 	select 		real_filename 
									from 		editor_image 
									where 	user_id = $SESS_USERID and id = '$file_id' 
							  ";
				$db->send_cmd($cmd);
				$data = $db->get_data();
				$data = array_change_key_case($data, CASE_LOWER);
				$old_filename_encode = $data[0];
				unlink("$PATH_IMAGES/$old_filename_encode");
			} // for
			$str_del_file = join($filedel, ",");
			$cmd = "delete from editor_image where user_id = $SESS_USERID and id in ($str_del_file)";
			$db->send_cmd($cmd);
//			$db->show_error();

			insert_log("DELETE IMAGE FILE");
		} // if

		if ($UPD) {
			$cmd = " select 	real_filename, show_filename, description, width, height, size 
							 from 		editor_image
							 where 	user_id = $SESS_USERID and id = '$file_id' 
						  ";
			$db->send_cmd($cmd);
			$data = $db->get_data();
			$data = array_change_key_case($data, CASE_LOWER);
			$real_filename = $data[0];
			$show_filename = $data[1];
			$description  = $data[2];
			$width = $data[3];
			$height = $data[4];
			$size  = $data[5];
		}
	} // if page 2
	// ===================================================

	// ===================================================
	if ($PAGE == 3) { //นำเข้าไฟล์เพิ่มเติม
		$description = trim($description);
		$size = calsizebyte($filename_size);
		if ($command == "UPLOAD") {
//			echo " :: $filename_name";
			if ($filename_name != "") {
				$pos = strrpos($filename_name, ".");	
				$file_name = substr($filename_name, 0, ($pos - strlen($filename_name)));
				$file_ext = substr($filename_name, ($pos+1));
				$filename_encode = "usr_attachment_". $SESS_USERID ."_". date("Ymd") ."_". md5($file_name) .".". $file_ext;
				if( move_uploaded_file($filename,"$PATH_ATTACHMENTS/$filename_encode") ){
//					echo "$PATH_ATTACHMENTS/$filename_encode";
					$cmd = 	" 	insert into editor_attachment 
										(real_filename, show_filename, file_type, description, size, user_id, user_group_id, create_date, update_date, update_by)
										values 
										('$filename_encode', '$filename_name', '$filename_type', '$description', '$size', $SESS_USERID, $SESS_USERGROUP, $update_date, $update_date,$update_by) ";
					$db->send_cmd($cmd);
//					$db->show_error();

					insert_log("UPLOAD ATTACHMENT FILE $filename_name [$description]");
				}else{ // if success upload file
//					echo "<br>Can't upload file $file_name<br>";
//					print_r($_FILES);
				}
			} // if
		} // if
		if ($command == "UPDATE") {
			$width += 0;
			$height += 0;
			if ($filename_name != "") {
				$pos = strrpos($filename_name, ".");	
				$file_name = substr($filename_name, 0, ($pos - strlen($filename_name)));
				$file_ext = substr($filename_name, ($pos+1));
				$filename_encode = "usr_attachment_". $SESS_USERID ."_". date("Ymd") ."_". md5($file_name) .".". $file_ext;

				$cmd = " 	select real_filename 
									from 	editor_attachment 
									where user_id = $SESS_USERID and id = '$file_id' 
							  ";
				$db->send_cmd($cmd);
				$data = $db->get_data();
				$data = array_change_key_case($data, CASE_LOWER);
				$old_filename_encode = $data[0];
				unlink("$PATH_ATTACHMENTS/$old_filename_encode");

				move_uploaded_file($filename,"$PATH_ATTACHMENTS/$filename_encode");
				$where_file = " , show_filename = '$filename_name', real_filename = '$filename_encode' , file_type = '$filename_type', size = '$size' " ;
			} // if
			$cmd = " update 	editor_attachment set
											description = '$description' $where_file
							 where 	user_id = $SESS_USERID and id = '$file_id' 
						  ";
			$db->send_cmd($cmd);
//			$db->show_error();

			insert_log("UPDATE ATTACHMENT FILE $filename_name [$description]");
		} // if

		if ($command == "DELFILE" && count($filedel)) {
			for ($i = 0 ; $i < count($filedel) ; $i++) {
				$file_id = $filedel[$i];
				$cmd = " 	select 	real_filename 
									from 	editor_attachment 
									where user_id = $SESS_USERID and id = '$file_id' ";
				$db->send_cmd($cmd);
				$data = $db->get_data();
				$data = array_change_key_case($data, CASE_LOWER);
				$old_filename_encode = $data[0];
				unlink("$PATH_ATTACHMENTS/$old_filename_encode");
			} // for
			$str_del_file = join($filedel, ",");
			$cmd = "delete from editor_attachment where user_id = $SESS_USERID and id in ($str_del_file)";
			$db->send_cmd($cmd);
//			$db->show_error();

			insert_log("DELETE ATTACHMENT FILE");
		} // if

		if ($UPD) {
			$cmd = " select 	real_filename, show_filename, description, size 
							 from 		editor_attachment
							 where 	user_id = $SESS_USERID and id = '$file_id' 
						  ";
			$db->send_cmd($cmd);
//			$db->show_error();
			$data = $db->get_data();
			$data = array_change_key_case($data, CASE_LOWER);
			$real_filename = $data[0];
			$show_filename = $data[1];
			$description  = $data[2];
			$size  = $data[3];
		}
	} // if page 3
	// ===================================================
*********************/

	if ($command) {
		unset($description);
		unset($bg_image_title);
		unset($width);
		unset($height);
		unset($file_id);
	}

?>