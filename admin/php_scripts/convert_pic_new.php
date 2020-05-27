<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_bmp.php");

	$IMG_PATH = "../attachment/pic_personal";	
	$success_pic = $error_pic = $total_pic = $found001 = 0;
	$err_text = "";

	if(!isset($SRC_DIR)) $SRC_DIR = $IMG_PATH;
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$UPDATE_BY = $SESS_USERID;

	if($command=="CONVERTPIC") {
		// Open a known directory, and proceed to read its contents
		$cmd = " SELECT PER_ID, PER_CARDNO, PER_NAME, PER_SURNAME FROM PER_PERSONAL WHERE PER_STATUS <> 3 ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$PER_ID = $data[PER_ID];
			$PER_CARDNO = $data[PER_CARDNO];
			$PER_GENNAME = personal_gen_name($data[PER_NAME],$data[PER_SURNAME]);
			$PER_GENNAME = "9999".$PER_GENNAME;

			if ($PER_CARDNO) {
				$cmd = " INSERT INTO PER_PERSONALPIC (PER_ID, PER_PICSEQ, PER_CARDNO, PER_GENNAME, 
								PER_PICPATH, PER_PICSAVEDATE, PIC_SHOW, PIC_REMARK,PIC_SERVER_ID, PIC_SIGN, UPDATE_USER, UPDATE_DATE)
								VALUES ($PER_ID, 1, '$PER_CARDNO', '$PER_GENNAME', '../attachment/pic_personal/', '$UPDATE_DATE', 1, 
								NULL, 0, 0 ,$SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis1->send_cmd($cmd);
//				$db_dpis1->show_error();
			}
		} // end while						

		$cmd = " SELECT PER_PICSEQ FROM PER_PERSONALPIC ";
		$count_data = $db_dpis->send_cmd($cmd);
//		if ($count_data)
//			$err_text .= "มีการ Rename ชื่อรูปไปแล้ว<br>";
//		else
			if (is_dir($SRC_DIR)) trace_dir($SRC_DIR);
	} // end if
		
	function trace_dir($dir){
		global $db_dpis, $IMG_PATH;
		global $success_pic, $error_pic, $total_pic, $found001, $err_text;
		
		if ($dir_handler = opendir($dir)) {
			while (($file = readdir($dir_handler)) !== false) {
				if(!($file == '.' || $file == '..' || $file == 'Thumbs.db'  || $file == 'shadow.png' )) {
					if(is_dir(($dir."/".$file))){
						trace_dir(($dir."/".$file));
					}else{
//						echo "$success_pic-$file<br>";
						$c=strpos($file,".");
						$c1=strpos($file,"-001.");
						if ($c !== false && $c1 === false) {
							$newfile = substr($file,0,$c)."-001".substr($file,$c);
//							echo "$file==>$newfile<br>";
							$flgRename = rename($dir."/".$file, $dir."/".$newfile); 
							if($flgRename) { 
//								echo "___File [$file] Rename To [$newfile]<br>"; 
								$success_pic++;					
							} else { 
//								echo "***File [$file] can not Rename To [$newfile]<br>"; 
								$error_pic++;
							} // end if 
							$total_pic++;
						} else {
							if ($c1 !== false) $found001++;
						} // end if ($c !== false && $c1 === false)
					} // end if
				} // end  if(!(c == '.' || $file == '..' || $file == 'Thumbs.db' ))
			} // end while
			closedir($dir_handler);
		}else{
			$err_text .= "CANNOT OPEN DIRECTORY :: $dir<br>";
		} // end if
		
		return;
	} // end if

?>
