<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_bmp.php");

	$IMG_PATH = "../attachment/pic_personal";	
	$success_pic = $error_pic = $total_pic = 0;
	$err_text = "";

	if(!isset($SRC_DIR)) $SRC_DIR = "D:\image";
	if($command=="CONVERTPIC1") {
		// Open a known directory, and proceed to read its contents
		if (is_dir($SRC_DIR)) trace_dir($SRC_DIR);
	} // end if
		
	if($command=="CONVERTPIC2") {
		// Open a known directory, and proceed to read its contents
		if (is_dir($SRC_DIR)) trace_dir($SRC_DIR);
	} // end if
		
	function trace_dir($dir){
		global $db_dpis, $IMG_PATH;
		global $success_pic, $error_pic, $total_pic, $err_text, $command;
		
		if ($dir_handler = opendir($dir)) {
			while (($file = readdir($dir_handler)) !== false) {
				if($file == '.' || $file == '..' || $file == 'Thumbs.db' ) continue;
				if(is_dir(($dir."/".$file))){
					trace_dir(($dir."/".$file));
				}else{
					if($command=="CONVERTPIC1") {
//						$POS_NO = substr($file,0,3);
//						$cmd = " select PER_CARDNO from PER_PERSONAL where POS_ID in (select POS_ID from PER_POSITION where POS_NO_NAME='รพจ.' and POS_NO='$POS_NO') ";
						$arr_temp1 = explode(".jpg", $file);
						$arr_temp2 = explode("_", $arr_temp1[0]);
						$arr_temp = explode(" ", $arr_temp2[1]);
						$PER_NAME = trim($arr_temp[0]);
						$PER_SURNAME = trim($arr_temp[1].$arr_temp[2]);
						$cmd = " select PER_CARDNO from PER_PERSONAL a, PER_PRENAME b where a.PN_CODE=b.PN_CODE and 
										(trim(PN_NAME)||PER_NAME='$PER_NAME' or trim(PN_SHORTNAME)||PER_NAME='$PER_NAME') and PER_SURNAME='$PER_SURNAME' ";
					} elseif($command=="CONVERTPIC2") {
						$arr_temp1 = explode(".jpg", $file);
						$arr_temp = explode(" ", $arr_temp1[0]);
						$PER_NAME = trim($arr_temp[0]);
						$PER_SURNAME = trim($arr_temp[1].$arr_temp[2].$arr_temp[3]);
						$PER_NAME = str_replace("10." ,"", $PER_NAME);
						$PER_NAME = str_replace("11." ,"", $PER_NAME);
						$PER_NAME = str_replace("12." ,"", $PER_NAME);
						$PER_NAME = str_replace("13." ,"", $PER_NAME);
						$PER_NAME = str_replace("14." ,"", $PER_NAME);
						$PER_NAME = str_replace("15." ,"", $PER_NAME);
						$PER_NAME = str_replace("16." ,"", $PER_NAME);
						$PER_NAME = str_replace("17." ,"", $PER_NAME);
						$PER_NAME = str_replace("18." ,"", $PER_NAME);
						$PER_NAME = str_replace("19." ,"", $PER_NAME);
						$PER_NAME = str_replace("20." ,"", $PER_NAME);
						$PER_NAME = str_replace("21." ,"", $PER_NAME);
						$PER_NAME = str_replace("22." ,"", $PER_NAME);
						$PER_NAME = str_replace("23." ,"", $PER_NAME);
						$PER_NAME = str_replace("24." ,"", $PER_NAME);
						$PER_NAME = str_replace("25." ,"", $PER_NAME);
						$PER_NAME = str_replace("26." ,"", $PER_NAME);
						$PER_NAME = str_replace("27." ,"", $PER_NAME);
						$PER_NAME = str_replace("28." ,"", $PER_NAME);
						$PER_NAME = str_replace("29." ,"", $PER_NAME);
						$PER_NAME = str_replace("30." ,"", $PER_NAME);
						$PER_NAME = str_replace("31." ,"", $PER_NAME);
						$PER_NAME = str_replace("32." ,"", $PER_NAME);
						$PER_NAME = str_replace("33." ,"", $PER_NAME);
						$PER_NAME = str_replace("34." ,"", $PER_NAME);
						$PER_NAME = str_replace("35." ,"", $PER_NAME);
						$PER_NAME = str_replace("36." ,"", $PER_NAME);
						$PER_NAME = str_replace("37." ,"", $PER_NAME);
						$PER_NAME = str_replace("38." ,"", $PER_NAME);
						$PER_NAME = str_replace("39." ,"", $PER_NAME);
						$PER_NAME = str_replace("40." ,"", $PER_NAME);
						$PER_NAME = str_replace("41." ,"", $PER_NAME);
						$PER_NAME = str_replace("42." ,"", $PER_NAME);
						$PER_NAME = str_replace("43." ,"", $PER_NAME);
						$PER_NAME = str_replace("44." ,"", $PER_NAME);
						$PER_NAME = str_replace("45." ,"", $PER_NAME);
						$PER_NAME = str_replace("46." ,"", $PER_NAME);
						$PER_NAME = str_replace("47." ,"", $PER_NAME);
						$PER_NAME = str_replace("48." ,"", $PER_NAME);
						$PER_NAME = str_replace("49." ,"", $PER_NAME);
						$PER_NAME = str_replace("50." ,"", $PER_NAME);
						$PER_NAME = str_replace("51." ,"", $PER_NAME);
						$PER_NAME = str_replace("52." ,"", $PER_NAME);
						$PER_NAME = str_replace("53." ,"", $PER_NAME);
						$PER_NAME = str_replace("54." ,"", $PER_NAME);
						$PER_NAME = str_replace("55." ,"", $PER_NAME);
						$PER_NAME = str_replace("56." ,"", $PER_NAME);
						$PER_NAME = str_replace("57." ,"", $PER_NAME);
						$PER_NAME = str_replace("58." ,"", $PER_NAME);
						$PER_NAME = str_replace("59." ,"", $PER_NAME);
						$PER_NAME = str_replace("1." ,"", $PER_NAME);
						$PER_NAME = str_replace("2." ,"", $PER_NAME);
						$PER_NAME = str_replace("3." ,"", $PER_NAME);
						$PER_NAME = str_replace("4." ,"", $PER_NAME);
						$PER_NAME = str_replace("5." ,"", $PER_NAME);
						$PER_NAME = str_replace("6." ,"", $PER_NAME);
						$PER_NAME = str_replace("7." ,"", $PER_NAME);
						$PER_NAME = str_replace("8." ,"", $PER_NAME);
						$PER_NAME = str_replace("9." ,"", $PER_NAME);
						if (substr($PER_NAME,0,4)=="น.ส.") $PER_NAME = substr($PER_NAME,4);
						if (substr($PER_NAME,0,6)=="นางสาว") $PER_NAME = substr($PER_NAME,6);
						if (substr($PER_NAME,0,3)=="นาง") $PER_NAME = substr($PER_NAME,3);
						if (substr($PER_NAME,0,3)=="นาย") $PER_NAME = substr($PER_NAME,3);
						$PER_SURNAME = str_replace(".png" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace(".JPG" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.10" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.11" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.12" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.13" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.14" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.15" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.16" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.17" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.18" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.19" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.20" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.21" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.22" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.23" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.24" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.25" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.26" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.27" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.1" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.2" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.3" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.4" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.5" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.6" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.7" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.8" ,"", $PER_SURNAME);
						$PER_SURNAME = str_replace("กรง.9" ,"", $PER_SURNAME);
						if (substr($PER_NAME,0,3)=="กสบ" || substr($PER_NAME,0,3)=="กวส") {
							$POS_NO_NAME = substr($PER_NAME,0,3);
							$arr_temp = explode($POS_NO_NAME, $PER_NAME);
							$POS_NO = trim($arr_temp[1]);
							$cmd = " select PER_CARDNO from PER_PERSONAL where POS_ID in (select POS_ID from PER_POSITION 
											where POS_NO_NAME='$POS_NO_NAME.' and POS_NO='$POS_NO') ";
						} else {
							$cmd = " select PER_CARDNO from PER_PERSONAL where PER_NAME='$PER_NAME' and PER_SURNAME='$PER_SURNAME' ";
						}
					}
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
					$data = $db_dpis->get_array();
					$PER_CARDNO = trim($data[PER_CARDNO]);
					if (!$PER_CARDNO) echo "ไม่พบข้อมูล => $PER_NAME :: $PER_SURNAME :: $cmd<br>";
					
					$total_pic++;
					if($PER_CARDNO){
//						echo "CONVERT FILE FROM $file ==> $PER_CARDNO.jpg<br>";

						copy(($dir."/".$file), ($IMG_PATH."/tmp_".$PER_CARDNO."-001.jpg"));

						// =========================== convert file into real jpeg ================================//
						$tmp_filename = $IMG_PATH."/tmp_".$PER_CARDNO."-001.jpg";

						$arr_img_attr = getimagesize($tmp_filename);
//						echo "<pre>"; print_r($arr_img_attr); echo "</pre>";
						switch($arr_img_attr[2]){
							case IMAGETYPE_GIF :																// 1
//								echo "TYPE => IMAGETYPE_GIF<br>";
								$img = @imagecreatefromgif($tmp_filename);
								break;
							case IMAGETYPE_JPEG :																// 2
//								echo "TYPE => IMAGETYPE_JPEG<br>";
								$img = @imagecreatefromjpeg($tmp_filename);
								break;						
							case IMAGETYPE_PNG :																// 3
//								echo "TYPE => IMAGETYPE_PNG<br>";
								$img = @imagecreatefrompng($tmp_filename);
								break;
							case IMAGETYPE_BMP :																// 6
//								echo "TYPE => IMAGETYPE_BMP<br>";
								$img = @imagecreatefrombmp($tmp_filename);
								break;
							case IMAGETYPE_WBMP :															// 15
//								echo "TYPE => IMAGETYPE_WBMP<br>";
								$img = @imagecreatefromwbmp($tmp_filename);
								break;
							default : 
//								echo "NOT SUPPORT IMAGE FORMAT<br>";
								$img = imagecreatefromstring(file_get_contents($tmp_filename));
								unlink($tmp_filename);
								imagepng($img, $tmp_filename);
								imagedestroy($img);
								$img = @imagecreatefrompng($tmp_filename);
						} // end switch case
//						echo "image resource :: $img<br>";
						unlink($tmp_filename);
						$filename = $IMG_PATH."/".$PER_CARDNO."-001.jpg";
						$convert_success = imagejpeg($img, $filename, 100);
//						echo "convert result :: $convert_success<br>";
						imagedestroy($img);
						// =========================== convert file into real jpeg ================================//

						$success_pic++;					
					}else{
						$err_text .= "CANNOT CONVERT FILE :: $file<br>";
						$error_pic++;
					} // end if 
				} // end if
			} // end while
			closedir($dir_handler);
		}else{
			$err_text .= "CANNOT OPEN DIRECTORY :: $dir<br>";
		} // end if
		
		return;
	} // end if

?>
