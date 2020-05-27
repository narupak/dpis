<?php	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");	

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);		
	//กำหนดค่า
	if($db_type=="oci8"){
		$db_obj = $db_dpis;
	}else{
		$db_obj = $db;
	}
	$UPDATE_DATE = date("Y-m-d H:i:s");
	if(!isset($MSG_START_DATE)){
		$MSG_START_DATE = date("Y-m-d");
		$MSG_FINISH_DATE = date("Y-m-d",mktime(0, 0, 0, date("m")  , date("d")+15, date("Y")));
		$MSG_START_DATE = show_date_format($MSG_START_DATE, 1);
		$MSG_FINISH_DATE = show_date_format($MSG_FINISH_DATE, 1);
	} // end if

/****	
  //สำหรับอัพไฟล์เอกสารเข้าระบบ โดยผู้โพสต์หรือ Admin
  if($commandupfile == "UPLOADFILE") {
  	if($_FILES){
  		//___print_r($_FILES);
		if($_FILES['MSG_DOCUMENT']['name']) {
			$temp_document_name = $_FILES['MSG_DOCUMENT']['name'];
		}
		include("personal_msgfile.php");	//ส่วนของการนำเข้าไฟล์
		//เซตเพื่อกลับมาแล้วปุ่มแก้ไข และค่ายังอยู่เหมือนเดิม
		$UPD =	1;
		$VIEW = 1;	  
	 }
   }


   if($command=="ADD" && trim($SELECTED_PER_ID) && $commandupfile!= "UPLOADFILE"){****/
   if($command=="ADD" && trim($SELECTED_PER_ID)){
		//ดึง MSG_ID ล่าสุดมา
		$cmd = " select max(MSG_ID) as max_id from PER_MESSAGE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$MSG_ID = $data[max_id] + 1;
		
		$MSG_POST_DATE = $UPDATE_DATE;
		if($MSG_START_DATE){
			$arr_temp = explode("/", $MSG_START_DATE);
			$MSG_START_DATE = ($arr_temp[2] - 543) ."-". str_pad($arr_temp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($arr_temp[0], 2, "0", STR_PAD_LEFT)." ".date("H:i:s");
		} // end if
		if($MSG_FINISH_DATE){
			$arr_temp = explode("/", $MSG_FINISH_DATE);
			$MSG_FINISH_DATE = ($arr_temp[2] - 543) ."-". str_pad($arr_temp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($arr_temp[0], 2, "0", STR_PAD_LEFT)." ".date("H:i:s");
		} // end if
		
		$cmd = " insert into PER_MESSAGE(MSG_ID, MSG_SOURCE,MSG_HEADER, MSG_DETAIL, MSG_POST_DATE, 
						  MSG_START_DATE, MSG_FINISH_DATE, USER_ID, MSG_TYPE,MSG_SHOW, MSG_DOCUMENT, 
						  MSG_ORG_NAME,UPDATE_USER, UPDATE_DATE)  
						  values ($MSG_ID, '$MSG_SOURCE',  '$MSG_HEADER', '$MSG_DETAIL', '$MSG_POST_DATE', 
						  '$MSG_START_DATE', '$MSG_FINISH_DATE', $SESS_USERID, $MSG_TYPE, 0, '$temp_document_name', 
						  '$MSG_ORG_NAME', $SESS_USERID, '$UPDATE_DATE')";
		$resultadd = $db_dpis->send_cmd($cmd);
//		echo "+++ $resultadd - $commandupfile : $cmd ++ <br>";
		
		//นำค่า MSG_ID เพื่อเชื่อโยงกับผู่ที่ได้รับข่าวสารนั้น $MSG_ID
		$ARRSELECTED_PER_ID=explode(",",$SELECTED_PER_ID);
		$f_admin = 0;
		$arr_a = (array) null;
		for($i=0; $i <= count($ARRSELECTED_PER_ID); $i++) {
			if ($ARRSELECTED_PER_ID[$i] == 1)  $f_admin = 1;
			if ($i == count($ARRSELECTED_PER_ID) && !$f_admin) {
				$USER_ID_SELECTED = 1;
			} else if ($i < count($ARRSELECTED_PER_ID)) {
				$USER_ID_SELECTED = $ARRSELECTED_PER_ID[$i];
			}
			if (($i < count($ARRSELECTED_PER_ID)) || ($i == count($ARRSELECTED_PER_ID) && !$f_admin)) {
				if (!in_array($USER_ID_SELECTED,$arr_a)) {
					$arr_a[] = $USER_ID_SELECTED;
					$cmd = "Insert into PER_MESSAGE_USER(MSG_ID,USER_ID,MSG_STATUS,UPDATE_USER,UPDATE_DATE )  
									values ($MSG_ID, $USER_ID_SELECTED, 0, $SESS_USERID, '$UPDATE_DATE') "; 
					$db_dpis->send_cmd($cmd);
//					$db_dpis->show_error();
//					if ($USER_ID_SELECTED==1) echo "insert : $cmd<br>";
//					echo "1..insert : $cmd<br>";
				}
			}
			//___echo "<br><br>msg user :: $cmd <br>";
		} //end for
		// insert owner message to message_user 
                /* http://dpis.ocsc.go.th/Service/node/2117 แก้ไขให้ส่งหา owner อัตตโนมัต  */
                $USER_ID_SELECTED = $SESS_USERID; /* เดิม ไม่มีบรรทัดนี้ */
		if (!in_array($USER_ID_SELECTED,$arr_a))  {
			$arr_a[] = $SESS_USERID;
			$cmd = "Insert into PER_MESSAGE_USER(MSG_ID,USER_ID,MSG_STATUS,UPDATE_USER,UPDATE_DATE )  
							values ($MSG_ID, $SESS_USERID, 0, $SESS_USERID, '$UPDATE_DATE') "; 
			$db_dpis->send_cmd($cmd);
//			echo "2..insert : $cmd<br>";
//			sort($arr_a);
//			for($a=0; $a < count($arr_a); $a++) {
//				echo $arr_a[$a]."(".$a."} , ";
//				if ($a > 0 && $arr_a[$a-1]==$arr_a[$a])
//					echo $arr_a[$a]."(".$a."} , ";
//			}
   		}
		$VIEW=1;	//เซตค่าที่ได้เพิ่มไป

	  //สำหรับอัพไฟล์เอกสารเข้าระบบ โดยผู้โพสต์หรือ Admin
	  if($commandupfile == "UPLOADFILE") {
		if($_FILES){
                    //___print_r($_FILES);
                    for($i=1;$i<=10;$i++){
                        if($i==1){
                            if($_FILES['MSG_DOCUMENT']['name']) {
                                $temp_document_name = $_FILES['MSG_DOCUMENT']['name'];
                            }
                        }else{
                            if($_FILES['MSG_DOCUMENT'.$i]['name']) {
                                $temp_document_name = $_FILES['MSG_DOCUMENT'.$i]['name'];
                            }
                        }
                        include("personal_msgfile.php");	//ส่วนของการนำเข้าไฟล์
                    }
			//เซตเพื่อกลับมาแล้วปุ่มแก้ไข และค่ายังอยู่เหมือนเดิม
			$UPD =	1;			$VIEW = 1;	  
		 }
	  }
	  //-----------------------------------------------------------------

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 >เพิ่มข้อมูลวิธีการใช้งานระบบ/ข่าวสารและประชาสัมพันธ์/หนังสือเวียนอิเล็กทรอนิกส์-หนังสือคำสั่งอิเล็กทรอนิกส์ [ $MSG_ID : $SESS_USERID ]");
  }else if($command=="UPDATE" && $MSG_ID){
		$MSG_POST_DATE = $UPDATE_DATE;
		if($MSG_START_DATE){
			$arr_temp = explode("/", $MSG_START_DATE);
			$MSG_START_DATE = ($arr_temp[2] - 543) ."-". str_pad($arr_temp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($arr_temp[0], 2, "0", STR_PAD_LEFT)." ".date("H:i:s");
		} // end if
		if($MSG_FINISH_DATE){
			$arr_temp = explode("/", $MSG_FINISH_DATE);
			$MSG_FINISH_DATE = ($arr_temp[2] - 543) ."-". str_pad($arr_temp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($arr_temp[0], 2, "0", STR_PAD_LEFT)." ".date("H:i:s");
		} // end if
		$cmd = " update PER_MESSAGE set  MSG_SOURCE = '$MSG_SOURCE' ,MSG_HEADER = '$MSG_HEADER', 
						  MSG_DETAIL = '$MSG_DETAIL', MSG_POST_DATE =  '$MSG_POST_DATE', 
						  MSG_START_DATE = '$MSG_START_DATE', MSG_FINISH_DATE =  '$MSG_FINISH_DATE', 
						  MSG_TYPE = $MSG_TYPE, MSG_SHOW = 0, MSG_DOCUMENT = '$MSG_DOCUMENT', 
						  MSG_ORG_NAME='$MSG_ORG_NAME', UPDATE_USER = $SESS_USERID, UPDATE_DATE = '$UPDATE_DATE'	
		where MSG_ID = '$MSG_ID'";
		// USER_ID = $SESS_USERID, ไม่ต้อง Update
		$resultup = $db_dpis->send_cmd($cmd);
		//___echo "+++ $resultup - $commandupfile : $cmd ++ <br>";
		
	  //สำหรับอัพไฟล์เอกสารเข้าระบบ โดยผู้โพสต์หรือ Admin    ???????????????????????????
	  if($commandupfile == "UPLOADFILE") {
		if($_FILES){
                    //___print_r($_FILES);
                    for($i=1;$i<=10;$i++){
                        if($i==1){
                            if($_FILES['MSG_DOCUMENT']['name']) {
                                $temp_document_name = $_FILES['MSG_DOCUMENT']['name'];
                            }
                        }else{
                            if($_FILES['MSG_DOCUMENT'.$i]['name']) {
                                $temp_document_name = $_FILES['MSG_DOCUMENT'.$i]['name'];
                            }
                        }
                        include("personal_msgfile.php");	//ส่วนของการนำเข้าไฟล์
                    }    
                    //เซตเพื่อกลับมาแล้วปุ่มแก้ไข และค่ายังอยู่เหมือนเดิม
                    $UPD =	1;			$VIEW = 1;	  
		 }
	  }
	  //-----------------------------------------------------------------
		
		//นำค่า MSG_ID เพื่อเชื่อโยงกับผู่ที่ได้รับข่าวสารนั้น $MSG_ID
		$ARRSELECTED_PER_ID=explode(",",$SELECTED_PER_ID);
		//เช็คผู้รับที่มีการเปลี่ยนแปลง ว่ามีในตารางแล้วหรือไม่ ?
		// กรณี 1. ถ้าเปลี่ยนแปลงผู้รับคนก่อนที่เคยได้รับข้อความนี้ แล้วมาอัพเดทไม่มีคนนี้อีกจะลบข้อมูลคนๆนี้ทั้งไปจาก PER_MESSAGE_USER
		// กรณี 2. ถ้ายังมีข้อมูลคนก่อนอยู่ก็จะอัพเดท MSG_STATUS=0 เพราะมีการแก้ไขก็มีค่าเท่ากับการโพสต์เปลี่ยนแปลงมาใหม่นั่นเอง
		
		$cmd = "select USER_ID from  PER_MESSAGE_USER where MSG_ID='$MSG_ID' order by MSG_ID";
		$db_dpis->send_cmd($cmd);		
		while($data = $db_dpis->get_array()){
			$temp[] = $userid_data[USER_ID];
		}
		$OLD_MESSAGE_USER = implode(',',$temp);
		if(is_array($OLD_MESSAGE_USER)){
		
		}
		if(is_array($ARRSELECTED_PER_ID)){
			$f_admin = false;
			for($i=0; $i <= count($ARRSELECTED_PER_ID); $i++) {
				if ($ARRSELECTED_PER_ID[$i] == 1)  $f_admin = true;
				if ($i == count($ARRSELECTED_PER_ID) && !$f_admin) {
					$USER_ID_SELECTED = 1;
				} else if ($i < count($ARRSELECTED_PER_ID)) {
					$USER_ID_SELECTED = $ARRSELECTED_PER_ID[$i];
				}
				if (($i < count($ARRSELECTED_PER_ID)) || ($i == count($ARRSELECTED_PER_ID) && !$f_admin)) {
					$cmd = "select * from  PER_MESSAGE_USER where MSG_ID=$MSG_ID AND USER_ID=$USER_ID_SELECTED order by MSG_ID";
					$count = $db_dpis->send_cmd($cmd);
		//	       echo "$cmd <br>";
					if($count>0){  //ยังมีอยู่ กรณี 2 :
						$cmd = " update PER_MESSAGE_USER set MSG_STATUS=0, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
										  where MSG_ID=$MSG_ID AND USER_ID=$USER_ID_SELECTED "; 
						$db_dpis->send_cmd($cmd);
//						$db_dpis->show_error();
					//___echo "<br><br>update msg user :: $cmd <br>";
					}else{ //คนที่ยังไม่มี
						$cmd = "Insert into PER_MESSAGE_USER(MSG_ID,USER_ID,MSG_STATUS,UPDATE_USER,UPDATE_DATE )  
										values ($MSG_ID, $USER_ID_SELECTED, 0, $SESS_USERID, '$UPDATE_DATE')"; 
						$db_dpis->send_cmd($cmd);
//						$db_dpis->show_error();
						//___echo "<br><br>insert msg user :: $cmd <br>";
					} 
				} // end if ($i < count($ARRSELECTED_PER_ID) || ($i == count($ARRSELECTED_PER_ID) && !f_admin))
			} //end for
		} //end is_array

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูลวิธีการใช้งานระบบ/ข่าวสารและประชาสัมพันธ์/หนังสือเวียนอิเล็กทรอนิกส์-หนังสือคำสั่งอิเล็กทรอนิกส์ [ $MSG_ID : $SESS_USERID ]");
		//เซตเพื่อกลับมาแล้วปุ่มแก้ไข และค่ายังอยู่เหมือนเดิม
		$UPD =	1;			$VIEW = 1;	  
   }else if($command=="DELETE" && $MSG_ID){
		//ลบจากตารางผู้รับ
		$cmd = " delete from PER_MESSAGE_USER where MSG_ID=$MSG_ID";
		$db_dpis->send_cmd($cmd); 
		
		//ลบจากตารางข้อความ
		$cmd = " delete from PER_MESSAGE where MSG_ID=$MSG_ID";
		$db_dpis->send_cmd($cmd); 
		
		//ลบไฟล์เอกสารใน folder PER_MESSAGE/?MSG_ID? ทิ้งทั้งหมด
		$FILE_PATH = "../attachments/PER_MESSAGE/$MSG_ID";
		if (is_dir($FILE_PATH)) {
			if ($dh = opendir($FILE_PATH)) {
				while (($file = readdir($dh)) !== false) {
					if ($file != "." && $file != "..") {
						unlink("$FILE_PATH/$file");		//ลบไฟล์ภายใน folder
						$count_data++;
					}
				}
				//จะลบได้ต้องลบไฟล์ข้างในทิ้งทั้งหมดก่อน
				$remresult=rmdir($FILE_PATH);
				closedir($dh);
			}
		}

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ลบข้อมูลวิธีการใช้งานระบบ/ข่าวสารและประชาสัมพันธ์/หนังสือเวียนอิเล็กทรอนิกส์-หนังสือคำสั่งอิเล็กทรอนิกส์ [$MSG_ID]");		
	}

	// ค้นหาเพื่อแสดงผลข้อมูล
	//echo $UPD." || ".$VIEW.") && ".trim($MSG_ID);
	if(($UPD || $VIEW) && trim($MSG_ID)){
			$cmd = "select * from PER_MESSAGE where MSG_ID=".$MSG_ID;
			$db_dpis->send_cmd($cmd);
			//echo "$cmd<br>";
			$update_data = $db_dpis->get_array();
			$MSG_ID = $update_data[MSG_ID];
			$MSG_POSTUSER_ID= $update_data[USER_ID];
			$MSG_SOURCE=$update_data[MSG_SOURCE];
			$MSG_HEADER=$update_data[MSG_HEADER];
			$MSG_DETAIL =trim($update_data[MSG_DETAIL]);
			$MSG_START_DATE = show_date_format($update_data[MSG_START_DATE], 1);
			$MSG_FINISH_DATE = show_date_format($update_data[MSG_FINISH_DATE], 1);
			$MSG_TYPE = $update_data[MSG_TYPE];
			$MSG_ORG_NAME = $update_data[MSG_ORG_NAME];
			$MSG_SHOW = $update_data[MSG_SHOW];
			
			if(empty($SELECTED_PER_ID) && trim($MSG_ID)) {  //ดึง USER_ID ของผู้รับมา
				$cmd = "select USER_ID from PER_MESSAGE_USER where MSG_ID='$MSG_ID'";
				$count = $db_dpis->send_cmd($cmd);
			   //$db_dpis->show_error();
				if($count>0){
					while($userid_data = $db_dpis->get_array()) {
						$temp[] = $userid_data[USER_ID];
					}
					$SELECTED_PER_ID = implode(',',$temp);
					$SELECTED_PER_ID = substr($SELECTED_PER_ID,0,100);
				//	echo "<br>>>> ".$SELECTED_PER_ID;
				}
			}
			/****************************************
			else if(trim($SELECTED_PER_ID)){
				if(trim($SELECTED_PER_ID)) $arr_search_condition[] = "(user_link_id in ($SELECTED_PER_ID))";
				$search_condition = "";
				if(count($arr_search_condition)) $search_condition = " where " . implode(" and ", $arr_search_condition);

				$cmd1 ="select username,user_link_id,fullname from user_detail 
								$search_condition
								order by username
								$limit_data";
				//echo "$cmd1<br>";
		//		$db_obj->show_error();
				$count_page_data = $db_obj->send_cmd($cmd1);
				if($count_page_data){
					$current_list = "";
					$data_count = 0;
					$count_selected = 0;
					while($data = $db_obj->get_array()) :
						$data_count++;
						$data = array_change_key_case($data, CASE_LOWER);
						$TMP_PER_ID = $data[user_link_id];
						$TMP_PER_FULLNAME=$data[fullname];
		 // 			echo "<br>$data_count : ".$TMP_PER_FULLNAME;
				} //end while
				} // end if
		} //end  if($SELECTED_PER_ID)
		****************************************/

			//ดึงชื่อผู้โพสต์จาก ตาราง user_detail ของ mysql
			if($db_type=="oci8"){
				$cmd1 ="select FULLNAME from USER_DETAIL where ID=$MSG_POSTUSER_ID";
			}else{
				$cmd1 ="select fullname from user_detail where id=$MSG_POSTUSER_ID";
			}
			$db_obj->send_cmd($cmd1);
			//$db_obj->show_error();
			$datausr = $db_obj->get_array();
			//$datausr = array_change_key_case($datausr, CASE_LOWER);
			$MSG_POSTUSER = $datausr[fullname];
	} 	//end if(($UPD || $VIEW) && trim($MSG_ID))
?>