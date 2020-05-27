<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/function_bmp.php");
	include("php_scripts/load_per_control.php");

	//ฟังก์ชันอัพโหลดรูป
	function upload_picture($IMG_NAME,$FILE_UP){
		$IMG_PATH = "./images/";  //โฟลเดอร์เก็บรูป
		if($IMG_NAME){
			$filename = $IMG_PATH.$IMG_NAME.".jpg";
			unlink($filename);
						
			if(is_uploaded_file($FILE_UP)){
				$tmp_filename = $IMG_PATH."tmp_".$IMG_NAME.".jpg";
				move_uploaded_file($FILE_UP, $tmp_filename);	
				$arr_img_attr = getimagesize($tmp_filename);
//				echo "<pre>"; print_r($arr_img_attr); echo "</pre>";
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
//						echo "NOT SUPPORT IMAGE FORMAT<br>";
						$img = imagecreatefromstring(file_get_contents($tmp_filename));
						unlink($tmp_filename);
						imagepng($img, $tmp_filename);
						imagedestroy($img);
						$img = @imagecreatefrompng($tmp_filename);
				} // end switch case
//				echo "image resource :: $img<br>";
				unlink($tmp_filename);
				$filename = $IMG_PATH.$IMG_NAME.".jpg";
				$convert_success = imagejpeg($img, $filename, 100);
//				echo "convert result :: $convert_success<br>";
				imagedestroy($img);
				///$img_error = "เปลี่ยนรูป $filename";
				///$img_error = "เปลี่ยนรูป [$IMG_NAME.jpg]";
				$img_error = "[$IMG_NAME.jpg]";
			}else{
				$img_error = "ไม่สามารถเปลี่ยนรูปได้";
			} // end if
		}else{
			$img_error = "ไม่สามารถเปลี่ยนรูปได้";
		} // end if	
	return $img_error;
	}

//คำสั่งอัพโหลดรูปภาพ
if($command=="UPDATEIMG" ){
		$kpilogo_msg = $topbg_msg = $topleft_msg = $topright_msg = "";	$sign_up_salary_msg = "";
		if(is_file($KPI_LOGO)){	//อัพโหลดโลโก้แบบสรุปผลการปฏิบัติราชการ (KPI)
			$IMG_NAME="logo_ocsc";
			$kpilogo_msg = upload_picture($IMG_NAME,$KPI_LOGO);
		}
		if(is_file($TOP_BG)){	//อัพโหลด BG แบนเนอร์
			$IMG_NAME="top_bg$RPT_N";
			$topbg_msg = upload_picture($IMG_NAME,$TOP_BG);
		}
		if(is_file($TOP_LEFT)){ //อัพโหลด LEFT แบนเนอร์
			$IMG_NAME="top_left$RPT_N";
			$topleft_msg = upload_picture($IMG_NAME,$TOP_LEFT);
		}
		if(is_file($TOP_RIGHT)){ //อัพโหลด RIGHT แบนเนอร์
			$IMG_NAME="top_right$RPT_N";
			$topright_msg = upload_picture($IMG_NAME,$TOP_RIGHT);
		}
		if(is_file($SIGN_UP_SALARY)){ //อัพโหลดรูปผู้ลงนาม และลายเซ็น
			$IMG_NAME="sign_up_salary$RPT_N";
			$sign_up_salary_msg = upload_picture($IMG_NAME,$SIGN_UP_SALARY);
		}
		//รีเฟรชเพจ
		//echo "<meta http-equiv=\"refresh\" content=\"0;URL=upload_pic.html\">";	
		echo "<meta http-equiv=\"refresh\" content=\"0;URL=upload_pic.html?kpilogo_msg=".$kpilogo_msg."&topbg_msg=".$topbg_msg."&topleft_msg=".$topleft_msg."&topright_msg=".$topright_msg."&sign_up_salary_msg=".$sign_up_salary_msg."\">";	
} // end if

?>