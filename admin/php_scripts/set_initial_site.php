<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	

	$BG_PATH = "images/";	

	$f_search = true;

	$CTRL_TYPE = $CH_CTRL_TYPE;
	$PROVINCE_CODE = $CH_PROVINCE_CODE;
	$MINISTRY_ID = $CH_MINISTRY_ID;
	$DEPARTMENT_ID = $CH_DEPARTMENT_ID;
	$ORG_ID = $CH_ORG_ID;
	$ORG_ID_1 = $CH_ORG_ID_1;

	switch($CH_CTRL_TYPE){
		case 1 :
			$CH_PROVINCE_CODE = "";
			$CH_ORG_ID = 0;
			break;
		case 2 :
			$CH_ORG_ID = 0;
			break;
		case 3 :
			$CH_PROVINCE_CODE = "";
			$CH_ORG_ID = $CH_MINISTRY_ID;
			break;
		case 4 :
			$CH_PROVINCE_CODE = "";
			$CH_ORG_ID = $CH_DEPARTMENT_ID;
			break;
		case 5 :
			$CH_PROVINCE_CODE = "";
			$CH_ORG_ID = $CH_ORG_ID;
			break;
		case 6 :
			$CH_PROVINCE_CODE = "";
			$CH_ORG_ID = $CH_ORG_ID_1;
			break;
	} // end switch case
		
	if(trim($CH_PROVINCE_CODE)) $CH_PROVINCE_CODE = "'$CH_PROVINCE_CODE'";
	else $CH_PROVINCE_CODE = "NULL";
	if(!$CH_ORG_ID) $CH_ORG_ID = "NULL";

	$prov_ui = "'NULL'";
	if ($SITE_ID) {
		$cond = " SITE_ID = $SITE_ID ";
	} else if ($CH_CTRL_TYPE == 1){
		$cond = " SITE_LEVEL = 1 ";
	}else if ($CH_CTRL_TYPE == 2){
		if (is_null($CH_PROVINCE_CODE) || $CH_PROVINCE_CODE=="NULL" || $CH_PROVINCE_CODE=="'NULL'"){
			$cond = " SITE_LEVEL = 2 and PV_CODE = 'NULL' ";
		}else{
			$cond = " SITE_LEVEL = 2 and PV_CODE = '$CH_PROVINCE_CODE' ";
			$prov_ui = "'$CH_PROVINCE_CODE'";
		}
	}else if ($CH_CTRL_TYPE && $CH_ORG_ID!="NULL"){
		$cond = " SITE_LEVEL = $CH_CTRL_TYPE and ORG_ID = $CH_ORG_ID ";
	}else{
		$cond = "";
	}

	$f_search = true;
	
	if($command == "UPDATE"){
		$UPDATE_DATE = date("Y-m-d H:i:s");
		if(is_uploaded_file($SITE_BG_LEFT)){
			$tmp_filename = $BG_PATH."tmp_1.jpg";
			move_uploaded_file($SITE_BG_LEFT, $tmp_filename);	
			$arr_img_attr = getimagesize($tmp_filename);
//			echo "<pre>"; print_r($arr_img_attr); echo "</pre>";
			switch($arr_img_attr[2]){
				case IMAGETYPE_GIF :																	// 1
					$img = @imagecreatefromgif($tmp_filename);
					break;
				case IMAGETYPE_JPEG :																// 2
					$img = @imagecreatefromjpeg($tmp_filename);
					break;						
				case IMAGETYPE_PNG :																// 3
					$img = @imagecreatefrompng($tmp_filename);
					break;
				case IMAGETYPE_BMP :																// 6
					$img = @imagecreatefrombmp($tmp_filename);
					break;
				case IMAGETYPE_WBMP :															// 15
					$img = @imagecreatefromwbmp($tmp_filename);
					break;
				default : 
//					echo "NOT SUPPORT IMAGE FORMAT<br>";
					$img = imagecreatefromstring(file_get_contents($tmp_filename));
					unlink($tmp_filename);
					imagepng($img, $tmp_filename);
					imagedestroy($img);
					$img = @imagecreatefrompng($tmp_filename);
			} // end switch case
//			echo "image resource :: $img<br>";
			unlink($tmp_filename);
			$filename = $BG_PATH.$_FILES["SITE_BG_LEFT"]["name"];
			$convert_success = imagejpeg($img, $filename, 100);
//			echo "convert result :: $convert_success<br>";
			imagedestroy($img);
			$SITE_BG_LEFT = $filename; 
//			echo "in php  1 L..SITE_BG_LEFT=$SITE_BG_LEFT<br>";
		} else {
			$SITE_BG_LEFT = $h_BG_LEFT;
//			echo "in php  2 L..SITE_BG_LEFT=$SITE_BG_LEFT<br>";
//			echo "h_BG_LEFT=$h_BG_LEFT<br>";
		} // end if is upload

		if(is_uploaded_file($SITE_BG)){
			$tmp_filename = $BG_PATH."tmp_2.jpg";
			move_uploaded_file($SITE_BG, $tmp_filename);	
			$arr_img_attr = getimagesize($tmp_filename);
//			echo "<pre>"; print_r($arr_img_attr); echo "</pre>";
			switch($arr_img_attr[2]){
				case IMAGETYPE_GIF :																	// 1
					$img = @imagecreatefromgif($tmp_filename);
					break;
				case IMAGETYPE_JPEG :																// 2
					$img = @imagecreatefromjpeg($tmp_filename);
					break;						
				case IMAGETYPE_PNG :																// 3
					$img = @imagecreatefrompng($tmp_filename);
					break;
				case IMAGETYPE_BMP :																// 6
					$img = @imagecreatefrombmp($tmp_filename);
					break;
				case IMAGETYPE_WBMP :															// 15
					$img = @imagecreatefromwbmp($tmp_filename);
					break;
				default : 
//					echo "NOT SUPPORT IMAGE FORMAT<br>";
					$img = imagecreatefromstring(file_get_contents($tmp_filename));
					unlink($tmp_filename);
					imagepng($img, $tmp_filename);
					imagedestroy($img);
					$img = @imagecreatefrompng($tmp_filename);
			} // end switch case
//			echo "image resource :: $img<br>";
			unlink($tmp_filename);
			$filename = $BG_PATH.$_FILES["SITE_BG"]["name"];
			$convert_success = imagejpeg($img, $filename, 100);
//			echo "convert result :: $convert_success<br>";
			imagedestroy($img);
			$SITE_BG = $filename; 
		} else {
			$SITE_BG = $h_BG;
//			echo "h_BG=$h_BG<br>";
		} // end if is upload
		
		if(is_uploaded_file($SITE_BG_RIGHT)){
			$tmp_filename = $BG_PATH."tmp_3.jpg";
			move_uploaded_file($SITE_BG_RIGHT, $tmp_filename);	
			$arr_img_attr = getimagesize($tmp_filename);
//			echo "<pre>"; print_r($arr_img_attr); echo "</pre>";
			switch($arr_img_attr[2]){
				case IMAGETYPE_GIF :																	// 1
					$img = @imagecreatefromgif($tmp_filename);
					break;
				case IMAGETYPE_JPEG :																// 2
					$img = @imagecreatefromjpeg($tmp_filename);
					break;						
				case IMAGETYPE_PNG :																// 3
					$img = @imagecreatefrompng($tmp_filename);
					break;
				case IMAGETYPE_BMP :																// 6
					$img = @imagecreatefrombmp($tmp_filename);
					break;
				case IMAGETYPE_WBMP :															// 15
					$img = @imagecreatefromwbmp($tmp_filename);
					break;
				default : 
//					echo "NOT SUPPORT IMAGE FORMAT<br>";
					$img = imagecreatefromstring(file_get_contents($tmp_filename));
					unlink($tmp_filename);
					imagepng($img, $tmp_filename);
					imagedestroy($img);
					$img = @imagecreatefrompng($tmp_filename);
			} // end switch case
//			echo "image resource :: $img<br>";
			unlink($tmp_filename);
			$filename = $BG_PATH.$_FILES["SITE_BG_RIGHT"]["name"];
			$convert_success = imagejpeg($img, $filename, 100);
//			echo "convert result :: $convert_success<br>";
			imagedestroy($img);
			$SITE_BG_RIGHT = $filename; 
		} else {
			$SITE_BG_RIGHT = $h_BG_RIGHT;
//			echo "h_BG_RIGHT=$h_BG_RIGHT<br>";
		} // end if is upload

		if ($_FILES["CSS_NAME"]["name"])
			$CSS_NAME = $_FILES["CSS_NAME"]["name"];
		else 
			$CSS_NAME = $h_CSS_NAME;
//		echo "CSS_NAME=$CSS_NAME<br>";

		if (!$SITE_BG_LEFT_X) $SITE_BG_LEFT_X="0";
		if (!$SITE_BG_LEFT_Y) $SITE_BG_LEFT_Y="0";
		if (file_exists($SITE_BG_LEFT)) {
			list($width, $height, $type, $attr) = getimagesize($SITE_BG_LEFT);
		} else {
			$width = 80; $height = 80;
		}
		if ($check_wh_LEFT=="on") {
			$SITE_BG_LEFT_W=$width;
			$SITE_BG_LEFT_H=$height;
		} else {
			if (!$SITE_BG_LEFT_W) $SITE_BG_LEFT_W=$width;
			if (!$SITE_BG_LEFT_H) $SITE_BG_LEFT_H=$height;
		}
		if (!$SITE_BG_LEFT_ALPHA) $SITE_BG_LEFT_ALPHA=.8;

		if (!$SITE_BG_X) $SITE_BG_X="0";
		if (!$SITE_BG_Y) $SITE_BG_Y="0";
		if (file_exists($SITE_BG)) {
			list($width, $height, $type, $attr) = getimagesize($SITE_BG);
		} else {
			$width = 80; $height = 80;
		}
		if ($check_wh_BG=="on") {
			$SITE_BG_W=$width;
			$SITE_BG_H=$height;
		} else {
			if (!$SITE_BG_W) $SITE_BG_W=$width;
			if (!$SITE_BG_H) $SITE_BG_H=$height;
		}
		if (!$SITE_BG_ALPHA) $SITE_BG_ALPHA=.5;

		if (!$SITE_BG_RIGHT_X) $SITE_BG_RIGHT_X="0";
		if (!$SITE_BG_RIGHT_Y) $SITE_BG_RIGHT_Y="0";
		if (file_exists($SITE_BG_RIGHT)) {
			list($width, $height, $type, $attr) = getimagesize($SITE_BG_RIGHT);
		} else {
			$width = 80; $height = 80;
		}
		if ($check_wh_RIGHT=="on") {
			$SITE_BG_RIGHT_W=$width;
			$SITE_BG_RIGHT_H=$height;
		} else {
			if (!$SITE_BG_RIGHT_W) $SITE_BG_RIGHT_W=$width;
			if (!$SITE_BG_RIGHT_H) $SITE_BG_RIGHT_H=$height;
		}
		if (!$SITE_BG_RIGHT_ALPHA) $SITE_BG_RIGHT_ALPHA=.8;

		if (!$HEAD_HEIGHT) $HEAD_HEIGHT=80;

//		echo "check_wh_LEFT=$check_wh_LEFT, BG=$check_wh_BG, RIGHT=$check_wh_RIGHT<br>";
//		echo "SITE_BG_LEFT=$SITE_BG_LEFT, L=$SITE_BG_LEFT_X, T=$SITE_BG_LEFT_Y, W=$SITE_BG_LEFT_W, H=$SITE_BG_LEFT_H, A=$SITE_BG_LEFT_ALPHA<br>";

		if ($cond) {
			$cmd = " select * from SITE_INFO where $cond ";
			$count_ctrl = $db->send_cmd($cmd);
	//		echo "upd-$cmd [$count_ctrl]<br>";
		}
		$arr_upd = (array) null;
		if($count_ctrl){
			if ($CH_CTRL_TYPE<=2)  $arr_upd[] = "ORG_ID = 0"; 
			elseif ($CH_ORG_ID) $arr_upd[] = "ORG_ID = $CH_ORG_ID";
			$arr_upd[] = "SITE_NAME = '$SITE_NAME'";
			$arr_upd[] = "SITE_BG_LEFT = '$SITE_BG_LEFT'";
			if ($SITE_BG_LEFT_X)
				$arr_upd[] = "SITE_BG_LEFT_X = $SITE_BG_LEFT_X";
			else
				$arr_upd[] = "SITE_BG_LEFT_X = NULL";
			if ($SITE_BG_LEFT_Y)
				$arr_upd[] = "SITE_BG_LEFT_Y = $SITE_BG_LEFT_Y";
			else
				$arr_upd[] = "SITE_BG_LEFT_Y = NULL";
			if ($SITE_BG_LEFT_W)
				$arr_upd[] = "SITE_BG_LEFT_W = $SITE_BG_LEFT_W";
			else
				$arr_upd[] = "SITE_BG_LEFT_W = NULL";
			if ($SITE_BG_LEFT_H)
				$arr_upd[] = "SITE_BG_LEFT_H = $SITE_BG_LEFT_H";
			else
				$arr_upd[] = "SITE_BG_LEFT_H = NULL";
			if ($SITE_BG_LEFT_ALPHA)
				$arr_upd[] = "SITE_BG_LEFT_ALPHA = $SITE_BG_LEFT_ALPHA";
			else
				$arr_upd[] = "SITE_BG_LEFT_ALPHA = NULL";
			$arr_upd[] = "SITE_BG = '$SITE_BG'";
			if ($SITE_BG_X)
				$arr_upd[] = "SITE_BG_X = $SITE_BG_X";
			else
				$arr_upd[] = "SITE_BG_X = NULL";
			if ($SITE_BG_Y)
				$arr_upd[] = "SITE_BG_Y = $SITE_BG_Y";
			else
				$arr_upd[] = "SITE_BG_Y = NULL";
			if ($SITE_BG_W)
				$arr_upd[] = "SITE_BG_W = $SITE_BG_W";
			else
				$arr_upd[] = "SITE_BG_W = NULL";
			if ($SITE_BG_H)
				$arr_upd[] = "SITE_BG_H = $SITE_BG_H";
			else
				$arr_upd[] = "SITE_BG_H = NULL";
			if ($SITE_BG_ALPHA)
				$arr_upd[] = "SITE_BG_ALPHA = $SITE_BG_ALPHA";
			else
				$arr_upd[] = "SITE_BG_ALPHA = NULL";
			$arr_upd[] = "SITE_BG_RIGHT = '$SITE_BG_RIGHT'";
			if ($SITE_BG_RIGHT_X)
				$arr_upd[] = "SITE_BG_RIGHT_X = $SITE_BG_RIGHT_X";
			else
				$arr_upd[] = "SITE_BG_RIGHT_X = NULL";
			if ($SITE_BG_RIGHT_Y)
				$arr_upd[] = "SITE_BG_RIGHT_Y = $SITE_BG_RIGHT_Y";
			else
				$arr_upd[] = "SITE_BG_RIGHT_Y = NULL";
			if ($SITE_BG_RIGHT_W)
				$arr_upd[] = "SITE_BG_RIGHT_W = $SITE_BG_RIGHT_W";
			else
				$arr_upd[] = "SITE_BG_RIGHT_W = NULL";
			if ($SITE_BG_RIGHT_H)
				$arr_upd[] = "SITE_BG_RIGHT_H = $SITE_BG_RIGHT_H";
			else
				$arr_upd[] = "SITE_BG_RIGHT_H = NULL";
			if ($SITE_BG_RIGHT_ALPHA)
				$arr_upd[] = "SITE_BG_RIGHT_ALPHA = $SITE_BG_RIGHT_ALPHA";
			else
				$arr_upd[] = "SITE_BG_RIGHT_ALPHA = NULL";
			$arr_upd[] = "CSS_NAME = '$CSS_NAME'";
			$arr_upd[] = "PV_CODE = $prov_ui";
			if ($HEAD_HEIGHT)
				$arr_upd[] = "HEAD_HEIGHT = $HEAD_HEIGHT";
			else
				$arr_upd[] = "HEAD_HEIGHT = NULL";
			$lselect = implode(", ", $arr_upd);
			if ($lselect) $lselect .= ", ";
			$cmd = " update SITE_INFO set SITE_LEVEL = $CH_CTRL_TYPE, 
								$lselect
								UPDATE_USER = $SESS_USERID,
								UPDATE_DATE = '$UPDATE_DATE'
							where $cond ";

			$db->send_cmd($cmd);
//			echo "update-$cmd<br>";
			
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [$PROVINCE_CODE => $CH_PROVINCE_CODE ; $MINISTRY_ID => $CH_MINISTRY_ID ; $DEPARTMENT_ID => $CH_DEPARTMENT_ID]");	
		}else{
			$cmd = " select max(SITE_ID) as MAXSITE from SITE_INFO ";
			$db->send_cmd($cmd);
//			echo "max-$cmd<br>";
			if ($data = $db->get_array()) {
				if ($data[MAXSITE])
					$newid = $data[MAXSITE]+1;
				else
					$newid = 1;
			} else
				$newid = 1;

			$cmd = " insert into SITE_INFO (SITE_ID, SITE_LEVEL, ORG_ID, SITE_NAME, SITE_BG_LEFT, SITE_BG_LEFT_X, 
							SITE_BG_LEFT_Y, SITE_BG_LEFT_W, SITE_BG_LEFT_H, SITE_BG_LEFT_ALPHA,
							SITE_BG, SITE_BG_X, SITE_BG_Y, SITE_BG_W, SITE_BG_H, SITE_BG_ALPHA, SITE_BG_RIGHT, 
							SITE_BG_RIGHT_X, SITE_BG_RIGHT_Y, SITE_BG_RIGHT_W, SITE_BG_RIGHT_H, 
							SITE_BG_RIGHT_ALPHA, CSS_NAME, PV_CODE, HEAD_HEIGHT, UPDATE_USER, UPDATE_DATE)
							values ($newid, $CH_CTRL_TYPE, $CH_ORG_ID, '$SITE_NAME', '$SITE_BG_LEFT', $SITE_BG_LEFT_X, 
							$SITE_BG_LEFT_Y, $SITE_BG_LEFT_W, $SITE_BG_LEFT_H, $SITE_BG_LEFT_ALPHA, '$SITE_BG', 
							$SITE_BG_X, $SITE_BG_Y, $SITE_BG_W, $SITE_BG_H, $SITE_BG_ALPHA, 
							'$SITE_BG_RIGHT', $SITE_BG_RIGHT_X, $SITE_BG_RIGHT_Y, $SITE_BG_RIGHT_W, 
							$SITE_BG_RIGHT_H, $SITE_BG_RIGHT_ALPHA, '$CSS_NAME', $prov_ui, $HEAD_HEIGHT, 
							$SESS_USERID, '$UPDATE_DATE') ";

			$db->send_cmd($cmd);
//			echo "insert-$cmd<br>";
			
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [$PROVINCE_CODE => $CH_PROVINCE_CODE ; $MINISTRY_ID => $CH_MINISTRY_ID ; $DEPARTMENT_ID => $CH_DEPARTMENT_ID]");	
		} // end if
//		$db->show_error();
	} // end if ($command=="UPDATE")

	if ($command=="DELETE") {
		$cmd = " delete from SITE_INFO where $cond ";
		$db->send_cmd($cmd);
//		$db->show_error();
		$f_search = false;
		$CTRL_TYPE = $CH_CTRL_TYPE = "0";
	} // end if $command=="DELETE"


	if ($command=="CLEAR") {
		$f_search = false;
		$CTRL_TYPE = $CH_CTRL_TYPE = "0";
		$SITE_ID = "";
	} // end if $command=="CLEAR"

	$PROVINCE_CODE = "";
	$CH_PROVINCE_CODE = "";
	$PROVINCE_NAME = "";
	$MINISTRY_ID = "";
	$CH_MINISTRY_ID = "";
	$MINISTRY_NAME = "";
	$DEPARTMENT_ID = "";
	$CH_DEPARTMENT_ID = "";
	$DEPARTMENT_NAME = "";
	$CH_ORG_ID = "";
	$ORG_ID = "";
	$ORG_NAME = "";
	$CH_ORG_ID_1 = "";
	$ORG_ID_1 = "";
	$ORG_NAME_1 = "";
	$SITE_NAME = "";
	$SITE_BG_LEFT = "";
	$SITE_BG_LEFT_X = "";
	$SITE_BG_LEFT_Y = "";
	$SITE_BG_LEFT_W = "";
	$SITE_BG_LEFT_H = "";
	$SITE_BG_LEFT_ALPHA = "";
	$SITE_BG = "";
	$SITE_BG_X = "";
	$SITE_BG_Y = "";
	$SITE_BG_W = "";
	$SITE_BG_H = "";
	$SITE_BG_ALPHA = "";
	$SITE_BG_RIGHT = "";
	$SITE_BG_RIGHT_X = "";
	$SITE_BG_RIGHT_Y = "";
	$SITE_BG_RIGHT_W = "";
	$SITE_BG_RIGHT_H = "";
	$SITE_BG_RIGHT_ALPHA = "";
	$CSS_NAME = "";
	$BG_LEFT_w = "";
	$BG_LEFT_h = "";
	$BG_RIGHT_w = "";
	$BG_RIGHT_h = "";
	$HEAD_HEIGHT = "";
	
	if ($f_search && $cond)	 {
		$cmd = " select * from SITE_INFO where $cond ";
		$db->send_cmd($cmd);
//		$db->show_error();
//		echo "search-$cmd<br>";
		if ($data = $db->get_array()) {
			$data = array_change_key_case($data, CASE_LOWER);
			switch($data[site_level]){
				case 2 :
					$PROVINCE_CODE = $data[pv_code];
					break;
				case 3 :
					$MINISTRY_ID = $data[org_id];
					break;
				case 4 :
					$DEPARTMENT_ID = $data[org_id];
					break;
				case 5 :
					$ORG_ID = $data[org_id];
					break;
				case 6 :
					$ORG_ID_1 = $data[org_id];
					break;
			} // end switch case

			if($ORG_ID_1){
				$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID_1 ";
				$db_dpis1->send_cmd($cmd);
//				$db_dpis1->show_error();
				$data1 = $db_dpis1->get_array();
				$ORG_NAME_1 = $data1[ORG_NAME];
				$ORG_ID = $data1[ORG_ID_REF];	
			} // end if

			if($ORG_ID){
				$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID ";
				$db_dpis1->send_cmd($cmd);
//				$db_dpis1->show_error();
				$data1 = $db_dpis1->get_array();
				$ORG_NAME = $data1[ORG_NAME];
				$DEPARTMENT_ID = $data1[ORG_ID_REF];	
			} // end if

			if($DEPARTMENT_ID){
				$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
				$db_dpis1->send_cmd($cmd);
//				$db_dpis1->show_error();
				$data1 = $db_dpis1->get_array();
				$DEPARTMENT_NAME = $data1[ORG_NAME];
				$MINISTRY_ID = $data1[ORG_ID_REF];	
			} // end if

			if($MINISTRY_ID){
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
				$db_dpis1->send_cmd($cmd);
//				$db_dpis1->show_error();
				$data1 = $db_dpis1->get_array();
				$MINISTRY_NAME = $data1[ORG_NAME];
			} // end if

			if($PROVINCE_CODE){
				$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$PROVINCE_CODE' ";
				$db_dpis1->send_cmd($cmd);
//				$db_dpis1->show_error();
				$data1 = $db_dpis1->get_array();
				$PROVINCE_NAME = $data1[PV_NAME];
			} // end if
	
			$CTRL_TYPE = $CH_CTRL_TYPE = $data[site_level];
			$SITE_NAME = $data[site_name];
			$SITE_BG_LEFT = $data[site_bg_left];
			$SITE_BG_LEFT_X = $data[site_bg_left_x];
			$SITE_BG_LEFT_Y = $data[site_bg_left_y];
			$SITE_BG_LEFT_W = $data[site_bg_left_w];
			$SITE_BG_LEFT_H = $data[site_bg_left_h];
			$SITE_BG_LEFT_ALPHA = $data[site_bg_left_alpha];
			$SITE_BG = $data[site_bg];
			$SITE_BG_X = $data[site_bg_x];
			$SITE_BG_Y = $data[site_bg_y];
			$SITE_BG_W = $data[site_bg_w];
			$SITE_BG_H = $data[site_bg_h];
			$SITE_BG_ALPHA = $data[site_bg_alpha];
			$SITE_BG_RIGHT = $data[site_bg_right];
			$SITE_BG_RIGHT_X = $data[site_bg_right_x];
			$SITE_BG_RIGHT_Y = $data[site_bg_right_y];
			$SITE_BG_RIGHT_W = $data[site_bg_right_w];
			$SITE_BG_RIGHT_H = $data[site_bg_right_h];
			$SITE_BG_RIGHT_ALPHA = $data[site_bg_right_alpha];
			$CSS_NAME = $data[css_name];
			$HEAD_HEIGHT = $data[head_height];
//			echo "**** end search $CTRL_TYPE SITE_BG_LEFT=$SITE_BG_LEFT, L=$SITE_BG_LEFT_X, T=$SITE_BG_LEFT_Y, W=$SITE_BG_LEFT_W, H=$SITE_BG_LEFT_H, A=$SITE_BG_LEFT_ALPHA<br>";
		} // end if ($data = $db->get_array())
	} // end if ($f_search)	
?>