<?
	session_start();
	//กำหนดค่า
	if($db_type=="oci8"){
		$db_obj = $db_dpis;
		$field_size = "FILE_SIZE";
		$update_date = date("Y-m-d H:i:s");
		$update_date = "'$update_date'";
		$update_by = $SESS_USERID;
	}else if($db_type=="odbc"){
		$db_obj = $db;
		$field_size = "file_size";
		$update_date = date("Y-m-d H:i:s");
		$update_date = "'$update_date'";
		$update_by = $SESS_USERID;
	}else if($db_type=="mysql"){
		$db_obj = $db;
		$field_size = "size";
		$update_date = "NOW()";
		$update_by = "'$SESS_USERNAME'";
	} else if($db_type=="mssql") {
		$db_obj = $db;
		$field_size = "size";
		$update_date = "GETDATE()";
		$update_by = $SESS_USERID;
	}
	
	function calsizebyte ($size) {
		if ($size < 1024) {
  			$issize = $size."b";
  		} else {
			$issize = sprintf ("%0.0f",$size / 1024)."KB";
			if ($issize >= 1024) $issize = sprintf ("%0.02f",$issize / 1024)."MB";
		}
		return $issize;
	}

	$URL_IMAGES = "http://$HTTP_HOST/".$virtual_site."attachments/editor_image";
	$PATH_IMAGES = "../attachments/editor_image";
	$URL_ATTACHMENTS = "http://$HTTP_HOST/".$virtual_site."attachments/editor_attachment";
	$PATH_ATTACHMENTS = "../attachments/editor_attachment";
	// =================================================
	if ($PAGE == 1) {
		$f_block = 1;
		// ===========================
		$NumRow_FileList = $NumCol_FileList = 0;
		$fcount = 0;
		if($db_type=="oci8"){
			$cmd = "	select 		SHOW_FILENAME, REAL_FILENAME, DESCRIPTION, WIDTH, HEIGHT 
								from 		EDITOR_IMAGE
								where 	USER_ID = $SESS_USERID
								order by SHOW_FILENAME ";
		}else{
			$cmd = "	select 		show_filename, real_filename, description, width, height 
								from 		editor_image 
								where 	user_id = $SESS_USERID
								order by show_filename ";
		}
		$db_obj->send_cmd($cmd);
		while($data = $db_obj->get_array()) {
			if($db_type=="oci8"){
				$filename = $data[SHOW_FILENAME];
				$filename_encode = $data[REAL_FILENAME];
				$description = ($data[DESCRIPTION] == "") ? $filename : $data[DESCRIPTION];
				$width = $data[WIDTH];
				$height = $data[HEIGHT];
				$pos = strrpos($filename, ".");	
				$file_ext = substr($filename, ($pos+1));
			}else{
				$filename = $data[show_filename];
				$filename_encode = $data[real_filename];
				$description = ($data[description] == "") ? $filename : $data[description];
				$width = $data[width];
				$height = $data[height];
				$pos = strrpos($filename, ".");	
				$file_ext = substr($filename, ($pos+1));
			}
			$FileList[] = "$description|$URL_IMAGES/$filename_encode|$file_ext|$width|$height" ;
		} // while

		$NumRow_FileList = count($FileList);
		if($NumRow_FileList) $NumCol_FileList = 1;
		$PATHIMAGEROOT = "$PATH_IMAGES/";

		$NumRow_AttachFileList = $NumCol_AttachFileList = 0;
		$fcount = 0;
		if($db_type=="oci8"){
			$cmd = "	select 		SHOW_FILENAME, REAL_FILENAME, DESCRIPTION
								from 		EDITOR_ATTACHMENT
								where 	USER_ID = $SESS_USERID
								order by SHOW_FILENAME ";
		}else{
			$cmd = "	select 		show_filename, real_filename, description 
								from 		editor_attachment 
								where 	user_id = $SESS_USERID
								order by show_filename ";
		}
//		$db_obj->send_cmd($cmd);	$db_obj->show_error();
		if ($db_obj->send_cmd($cmd)) {
			while($data = $db_obj->get_array()) {
				//$data = array_change_key_case($data, CASE_LOWER);
				if($db_type=="oci8"){
					$filename = $data[SHOW_FILENAME];
					$filename_encode = $data[REAL_FILENAME];
					$description = ($data[DESCRIPTION] == "") ? $filename : $data[DESCRIPTION];
					$pos = strrpos($filename, ".");	
					$file_ext = substr($filename, ($pos+1));
				}else{
					$filename = $data[show_filename];
					$filename_encode = $data[real_filename];
					$description = ($data[description] == "") ? $filename : $data[description];
					$pos = strrpos($filename, ".");	
					$file_ext = substr($filename, ($pos+1));
				}
				$AttachFileList[] = "$description|$URL_ATTACHMENTS/$filename_encode|$file_ext" ;
			} // while
		} // IF
		$NumRow_AttachFileList = count($AttachFileList);
		if($NumRow_AttachFileList) $NumCol_AttachFileList = 1;
		$PATHATTACHMENTROOT = "$PATH_ATTACHMENTS/";
	} // IF PAGE = 1
	// =================================================
	
	// ===================================================
	if ($PAGE == 2) {
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
					if($db_type=="oci8"){
						//ดึง editor_image ID ล่าสุดมา
						$cmd = " select ID from EDITOR_IMAGE order by ID DESC";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$data = $db_dpis->get_array();
						$max_id = $data[ID] + 1;
						$cmd = 	" 	insert into EDITOR_IMAGE (ID, REAL_FILENAME, SHOW_FILENAME, FILE_TYPE, DESCRIPTION, WIDTH, HEIGHT, $field_size, 
											USER_ID, USER_GROUP_ID, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
											values ($max_id, '$filename_encode', '$filename_name', '$filename_type', '$description', $width, $height, '$size', 
											$SESS_USERID, $SESS_USERGROUP, $update_date, $update_by, $update_date, $update_by) ";
					}else{
						//ดึง editor_image ID ล่าสุดมา
						$cmd = " select max(id) as max_id from editor_image ";
						$db_obj->send_cmd($cmd);
						$data = $db_obj->get_array();
						$max_id = $data[max_id] + 1; 
						$cmd = 	" 	insert into editor_image (id, real_filename, show_filename, file_type, description, width, height, $field_size, 
											user_id, user_group_id, create_date, create_by, update_date, update_by)
											values ($max_id, '$filename_encode', '$filename_name', '$filename_type', '$description', $width, $height, '$size', 
											$SESS_USERID, $SESS_USERGROUP, $update_date, $update_by, $update_date, $update_by) ";
					}
					$db_obj->send_cmd($cmd);
					//$db_obj->show_error();
						
					insert_log("UPLOAD IMAGE FILE $filename_name [$description]");
				} // if success upload file
			} // if
		} // if
		if ($command == "UPDATE" && $file_id) {
			$width += 0;
			$height += 0;
			if ($filename_name != "") {
				$pos = strrpos($filename_name, ".");	
				$file_name = substr($filename_name, 0, ($pos - strlen($filename_name)));
				$file_ext = substr($filename_name, ($pos+1));
				$filename_encode = "usr_image_". $SESS_USERID ."_". date("Ymd") ."_". md5($file_name) .".". $file_ext;

				if($db_type=="oci8"){
					$REAL_FILENAME = "REAL_FILENAME";
					$cmd = " 	select 	$REAL_FILENAME
										from 	EDITOR_IMAGE 
										where USER_ID = $SESS_USERID and ID = '$file_id' ";
					$where_file = " , SHOW_FILENAME = '$filename_name', REAL_FILENAME = '$filename_encode' ,FILE_TYPE = '$filename_type', $field_size = '$size' " ;
				}else{
					$REAL_FILENAME = "real_filename";
					$cmd = " 	select 	$REAL_FILENAME
										from 	editor_image 
										where user_id = $SESS_USERID and id = '$file_id' ";
					$where_file = " , show_filename = '$filename_name', real_filename = '$filename_encode' , file_type = '$filename_type', $field_size = '$size' " ;
				}
				$db_obj->send_cmd($cmd);
				$data = $db_obj->get_array();
				$old_filename_encode = $data[$REAL_FILENAME];
				unlink("$PATH_IMAGES/$old_filename_encode");

				//echo "$filename + $PATH_IMAGES/$filename_encode";
				move_uploaded_file($filename,"$PATH_IMAGES/$filename_encode");
			} // if
			if($db_type=="oci8"){
				$cmd = "	update 	EDITOR_IMAGE set
													DESCRIPTION= '$description', 
													WIDTH = $width, 
													HEIGHT = $height,
													UPDATE_DATE = $update_date 
													$where_file
									where 	USER_ID = $SESS_USERID and ID = '$file_id' ";
			}else{
				$cmd = "	update 	editor_image set
													description = '$description', 
													width = $width, 
													height = $height,
													update_date = $update_date 
													$where_file
									where 	user_id = $SESS_USERID and id = '$file_id' ";
			}
			//echo $cmd;
			$db_obj->send_cmd($cmd);

			insert_log("UPDATE IMAGE FILE $filename_name [$description]");
		} // if

		if ($command == "DELFILE" && count($filedel)) {
			for ($i = 0 ; $i < count($filedel) ; $i++) {
				$file_id = $filedel[$i];
				if($db_type=="oci8"){
					$REAL_FILENAME = "REAL_FILENAME";
					$cmd = " 	select 		$REAL_FILENAME 
									from 		EDITOR_IMAGE
									where 	USER_ID = $SESS_USERID and ID = '$file_id' ";
				}else{	
					$REAL_FILENAME = "real_filename ";
					$cmd = " 	select 		$REAL_FILENAME 
									from 		editor_image 
									where 	user_id = $SESS_USERID and id = '$file_id' ";
				}
				$db_obj->send_cmd($cmd);
				$data = $db_obj->get_array();
				//$data = array_change_key_case($data, CASE_LOWER);
				$old_filename_encode = $data[$REAL_FILENAME];
				unlink("$PATH_IMAGES/$old_filename_encode");
			} // for
			$str_del_file = join($filedel, ",");
			if($db_type=="oci8"){
				$cmd = "delete from EDITOR_IMAGE where USER_ID = $SESS_USERID and ID in ($str_del_file)";
			}else{
				$cmd = "delete from editor_image where user_id = $SESS_USERID and id in ($str_del_file)";
			}
			$db_obj->send_cmd($cmd);
//			$db_obj->show_error();

			insert_log("DELETE IMAGE FILE");
		} // if

		if ($UPD) {
			if($db_type=="oci8"){
				$cmd = " select 	REAL_FILENAME, SHOW_FILENAME, DESCRIPTION, WIDTH, HEIGHT, $field_size 
							 from 		EDITOR_IMAGE
							 where 	USER_ID = $SESS_USERID and ID = '$file_id' ";
			}else{
				$cmd = " select 	real_filename, show_filename, description, width, height, $field_size 
							 from 		editor_image
							 where 	user_id = $SESS_USERID and id = '$file_id' ";
			}
			$db_obj->send_cmd($cmd);
			$data = $db_obj->get_array();
			if($db_type=="oci8"){
				$real_filename = $data[REAL_FILENAME];
				$show_filename = $data[SHOW_FILENAME];
				$description  = $data[DESCRIPTION];
				$width = $data[WIDTH];
				$height = $data[HEIGHT];
				$size  = $data[$field_size ];
			}else{
				$real_filename = $data[real_filename];
				$show_filename = $data[show_filename];
				$description  = $data[description];
				$width = $data[width];
				$height = $data[height];
				$size  = $data[$field_size ];
			}		
		}
	} // if page 2
	// ===================================================

	// ===================================================
	if ($PAGE == 3) {
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
					if($db_type=="oci8"){
						//ดึง editor_aimage ID ล่าสุดมา
						$cmd = " select ID from EDITOR_ATTACHMENT order by ID DESC";
						$db_dpis->send_cmd($cmd);
						//$db_dpis->show_error();
						$data = $db_dpis->get_array();
						$max_id = $data[ID] + 1;
						$cmd = 	" 	insert into EDITOR_ATTACHMENT (ID, REAL_FILENAME, SHOW_FILENAME, FILE_TYPE, DESCRIPTION, $field_size, 
											USER_ID, USER_GROUP_ID, CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_BY)
											values ($max_id, '$filename_encode', '$filename_name', '$filename_type', '$description', '$size', 
											$SESS_USERID, $SESS_USERGROUP, $update_date, $update_by, $update_date, $update_by) ";
					}else{
						//ดึง editor_attachment ID ล่าสุดมา
						$cmd = " select max(id) as max_id from editor_attachment ";
						$db_obj->send_cmd($cmd);
						$data = $db_obj->get_array();
						$max_id = $data[max_id] + 1;
						$cmd = 	" 	insert into editor_attachment (id, real_filename, show_filename, file_type, description, $field_size, 
											user_id, user_group_id, create_date, create_by, update_date, update_by)
											values ($max_id, '$filename_encode', '$filename_name', '$filename_type', '$description', '$size', 
											$SESS_USERID, $SESS_USERGROUP, $update_date, $update_by, $update_date, $update_by) ";
					}
					$db_obj->send_cmd($cmd);
//					$db_obj->show_error();

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

				if($db_type=="oci8"){
					$REAL_FILENAME = "real_filename ";
					$cmd = " 	select $REAL_FILENAME
										from 	EDITOR_ATTACHMENT
										where USER_ID = $SESS_USERID and ID = '$file_id' ";
					$where_file = " , SHOW_FILENAME = '$filename_name', REAL_FILENAME = '$filename_encode' ,FILE_TYPE = '$filename_type', $field_size = '$size' " ;
				}else{
					$REAL_FILENAME = "REAL_FILENAME ";
					$cmd = " 	select $REAL_FILENAME
										from 	editor_attachment 
										where user_id = $SESS_USERID and id = '$file_id' ";
					$where_file = " , show_filename = '$filename_name', real_filename = '$filename_encode' , file_type = '$filename_type', $field_size = '$size' " ;
				}
				$db_obj->send_cmd($cmd);
				$data = $db_obj->get_array();
				$old_filename_encode = $data[$REAL_FILENAME];
				unlink("$PATH_ATTACHMENTS/$old_filename_encode");

				move_uploaded_file($filename,"$PATH_ATTACHMENTS/$filename_encode");
			} // if
			if($db_type=="oci8"){
				$cmd = " update 	EDITOR_ATTACHMENT set
												DESCRIPTION = '$description' $where_file
								 where 	USER_ID = $SESS_USERID and ID = '$file_id' ";
			}else{
				$cmd = " update 	editor_attachment set
												description = '$description' $where_file
								 where 	user_id = $SESS_USERID and id = '$file_id' ";
			}
			$db_obj->send_cmd($cmd);
//			$db_obj->show_error();

			insert_log("UPDATE ATTACHMENT FILE $filename_name [$description]");
		} // if

		if ($command == "DELFILE" && count($filedel)) {
			for ($i = 0 ; $i < count($filedel) ; $i++) {
				$file_id = $filedel[$i];
				if($db_type=="oci8"){
					$REAL_FILENAME = "REAL_FILENAME";
					$cmd = " 	select 	$REAL_FILENAME
										from 	EDITOR_ATTACHMENT
										where USER_ID = $SESS_USERID and ID = '$file_id' ";
				}else{
					$REAL_FILENAME = "real_filename";
					$cmd = " 	select 	$REAL_FILENAME
										from 	editor_attachment 
										where user_id = $SESS_USERID and id = '$file_id' ";
				}
				$db_obj->send_cmd($cmd);
				$data = $db_obj->get_array();
				$old_filename_encode = $data[$REAL_FILENAME];
				unlink("$PATH_ATTACHMENTS/$old_filename_encode");
			} // for
			$str_del_file = join($filedel, ",");
			if($db_type=="oci8"){
				$cmd = "delete from EDITOR_ATTACHMENT where USER_ID = $SESS_USERID and ID in ($str_del_file)";
			}else{
				$cmd = "delete from editor_attachment where user_id = $SESS_USERID and id in ($str_del_file)";
			}
			$db_obj->send_cmd($cmd);
//			$db_obj->show_error();

			insert_log("DELETE ATTACHMENT FILE");
		} // if

		if ($UPD) {
			if($db_type=="oci8"){
				$cmd = " select 	REAL_FILENAME, SHOW_FILENAME, DESCRIPTION, $field_size 
								 from 		EDITOR_ATTACHMENT
								 where 	USER_ID = $SESS_USERID and ID = '$file_id' ";
			}else{
				$cmd = " select 	real_filename, show_filename, description, $field_size 
								 from 		editor_attachment
								 where 	user_id = $SESS_USERID and id = '$file_id' ";
			}
			$db_obj->send_cmd($cmd);
//			$db_obj->show_error();
			$data = $db_obj->get_array();
			if($db_type=="oci8"){
				$real_filename = $data[REAL_FILENAME];
				$show_filename = $data[SHOW_FILENAME];
				$description  = $data[DESCRIPTION];
				$size  = $data[$field_size];
			}else{
				$real_filename = $data[real_filename];
				$show_filename = $data[show_filename];
				$description  = $data[description];
				$size  = $data[$field_size];
			}
		}
	} // if page 3
	// ===================================================

	if ($command) {
		unset($description);
		unset($bg_image_title);
		unset($width);
		unset($height);
		unset($file_id);
	}

?>