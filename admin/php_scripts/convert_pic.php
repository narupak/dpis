<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_bmp.php");

	$IMG_PATH = "../attachment/pic_personal";	
	$success_pic = $error_pic = $total_pic = 0;
	$err_text = "";

	if(!isset($SRC_DIR)) $SRC_DIR = "C:\Program Files\SmartDoc\FileDbs";
	if($command=="CONVERTPIC") {
		// Open a known directory, and proceed to read its contents
		if (is_dir($SRC_DIR)) trace_dir($SRC_DIR);
	} // end if
		
	function trace_dir($dir){
		global $db_dpis, $IMG_PATH;
		global $success_pic, $error_pic, $total_pic, $err_text;
		
		if ($dir_handler = opendir($dir)) {
			while (($file = readdir($dir_handler)) !== false) {
				if($file == '.' || $file == '..' || $file == 'Thumbs.db' ) continue;
				if(is_dir(($dir."/".$file))){
					trace_dir(($dir."/".$file));
				}else{
					$PER_ID = str_replace(".", "", $file) + 0;
					
					$cmd = " select PER_CARDNO from PER_PERSONAL where PER_ID=$PER_ID ";
					$db_dpis->send_cmd($cmd);
					$db_dpis->show_error();
					$data = $db_dpis->get_array();
					$PER_CARDNO = trim($data[PER_CARDNO]);
					
					$total_pic++;
//					echo "$total_pic => $PER_ID :: $PER_CARDNO<br>";
					if($PER_CARDNO){
//						echo "CONVERT FILE FROM $file ==> $PER_CARDNO.jpg<br>";

						copy(($dir."/".$file), ($IMG_PATH."/tmp_".$PER_CARDNO.".jpg"));

						// =========================== convert file into real jpeg ================================//
						$tmp_filename = $IMG_PATH."/tmp_".$PER_CARDNO.".jpg";

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
						$filename = $IMG_PATH."/".$PER_CARDNO.".jpg";
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
