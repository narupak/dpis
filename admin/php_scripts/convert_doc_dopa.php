<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_bmp.php");

	$IMG_PATH = "../attachments";	
	$success_pic = $error_pic = $total_pic = 0;
	$err_text = "";

	if(!isset($SRC_DIR)) $SRC_DIR = "E:\Seagate Sync\VOL\VOL00\Tomcat5Fam\webapps\ROOT\Docs";
	if($command=="CONVERTPIC") {
		// Open a known directory, and proceed to read its contents
		if (is_dir($SRC_DIR)) trace_dir($SRC_DIR);
	} // end if
		
	function trace_dir($dir){
		global $db_dpis, $IMG_PATH;
		global $success_pic, $error_pic, $total_pic, $err_text;
		
		$first_dir="";
		if ($dir_handler = opendir($dir)) {
			while (($file = readdir($dir_handler)) !== false) {
				if($file == '.' || $file == '..' || $file == 'Thumbs.db' ) continue;
				if(is_dir(($dir."/".$file))){
					trace_dir(($dir."/".$file));
				}else{
					$dir_part=explode("/",$dir);
					$first_dir=$dir_part[count($dir_part)-1];
//					echo "read file=$file - ($first_dir)<br>";
					$fname_part = explode("_",$first_dir);
					$PER_OFFNO = $fname_part[0];
					$fname_subpart = explode("-", $fname_part[1]);
					$sub_dir = $fname_subpart[0];
					$file_seq = ($fname_subpart[1]?$fname_subpart[1]:"");
					
					$extpart = explode(".",$file);
					$fext = $extpart[1]; // หา extention ของ file image

					$cmd = " select PER_CARDNO from PER_PERSONAL where PER_OFFNO='$PER_OFFNO' ";
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
						if (!chmod($IMG_PATH,0777)) echo "**ERROR** cannot chmod /dum<br>";
						$savepath = $IMG_PATH."/".$PER_CARDNO."/PER_ATTACHMENT/".$sub_dir;
//						$filename = $IMG_PATH."/".$PER_CARDNO.($file_seq?"_".$file_seq:"").".jpg";
						$filename = $savepath."/".$PER_CARDNO.($file_seq?"_".$file_seq:"").".jpg";
						$oldfilename = $savepath."/".$PER_CARDNO.($file_seq?"_".$file_seq:"").".".$fext;
						$cnt=0;
						while (file_exists($oldfilename)) {
							$cnt++;
							$subseq=($cnt < 10 ? "00".$cnt : ($cnt < 100 ? "0".$cnt : $cnt));
//							$filename = $savepath."/".$PER_CARDNO.($file_seq?"_".$file_seq:"")."-".$subseq.".jpg";
							$oldfilename = $savepath."/".$PER_CARDNO.($file_seq?"_".$file_seq:"")."-".$subseq.".".$fext;
						}
//						echo "filename=$oldfilename<br>";
						if (!file_exists($IMG_PATH."/".$PER_CARDNO)) { // ถ้าไม่มี $PER_CARDNO
							if (!mkdir($IMG_PATH."/".$PER_CARDNO, 0777)) {
								echo "**ERROR** can't make dir $PER_CARDNO<br>";
							} else { // ถ้าสร้าง $PER_CARDNO ได้
								if (!mkdir($IMG_PATH."/".$PER_CARDNO."/PER_ATTACHMENT", 0777)) {
									echo "**ERROR** can't make dir PER_ATTACHMENT<br>";
								} else { // ถ้าสร้าง PER_ARRACHMENT ได้
									if (!mkdir($IMG_PATH."/".$PER_CARDNO."/PER_ATTACHMENT/".$sub_dir, 0777)) {
										echo "**ERROR** can't make sub dir $sub_dir<br>";
									}
								} // mkdir PER_ATTACHMENT
							} // $PER_CATDNO
						} else { // ถ้า มี dir PER_CARD แล้ว
							if (!file_exists($IMG_PATH."/".$PER_CARDNO."/PER_ATTACHMENT")) { // ถ้าไม่มี PER_ATTACHMENT
								if (!mkdir($IMG_PATH."/".$PER_CARDNO."/PER_ATTACHMENT", 0777)) { // ถ้าสร้าง PER_ATT ไม่ได้
									echo "**ERROR** can't make PER_ATTACHMENT dir<br>";
								} else { // ถ้าสร้าง PER_ATTACHMENT ได้
									if (!mkdir($IMG_PATH."/".$PER_CARDNO."/PER_ATTACHMENT/".$sub_dir, 0777)) {
										echo "**ERROR** can't make dir $sub_dir<br>";
									}
								}
							} else { // ถ้ามี PER_ATTACHMENT แล้ว จึง check $sub_dir
								if (!file_exists($IMG_PATH."/".$PER_CARDNO."/PER_ATTACHMENT/".$sub_dir)) {
									if (!mkdir($IMG_PATH."/".$PER_CARDNO."/PER_ATTACHMENT/".$sub_dir, 0777)) {
										echo "**ERROR** can't make dir $sub_dir<br>";
									}
								}
							}
						}
//						if ($convert_success = imagejpeg($img, $filename, 100)) {
//							echo "convert success<br>";
//						} else {
//							echo "convert fail<br>";
//						}
						if (!copy(($dir."/".$file), ($oldfilename))) {
							echo "**ERROR** copy fail<br>";
						}
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
