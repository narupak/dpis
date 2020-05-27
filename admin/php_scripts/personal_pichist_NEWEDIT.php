<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	//$PIC_IP
	//$PIC_PATH
	//$PIC_NAME
	
	$PIC_REMARK = $PIC_IP;
	$IMG_PATH = $PIC_IP.$PIC_PATH;
	//___$IMG_PATH = "../attachment/pic_personal/";	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	if($command=="DELETE" && $PER_ID && $PIC_SEQ){
		$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID AND PER_PICSEQ=$PIC_SEQ ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_GENNAME = trim($data[PER_GENNAME]);
		$PIC_PATH = trim($data[PER_PICPATH]);
		$T_PIC_SEQ = substr("000",0,3-strlen("$PIC_SEQ"))."$PIC_SEQ";
		$N_PIC_NAME=$PIC_PATH.($PER_CARDNO=="NULL"?$PER_GENNAME:$PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
		
		$cmd = " DELETE from PER_PERSONALPIC where PER_ID=$PER_ID AND PER_PICSEQ=$PIC_SEQ ";
		$db_dpis->send_cmd($cmd);
		if($N_PIC_NAME){
			unlink($N_PIC_NAME);
			$img_error = "ลบรูป [$N_PIC_NAME]";
		}else{
			$img_error = "ไม่สามารถลบรูปได้";
		} // end if
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลรูปภาพ [$PER_ID : $PIC_SEQ]");
	} // end if
	
	if($PER_ID){
		if($DPISDB=="odbc"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
							PER_PERSONAL.PER_TYPE, PER_PERSONAL.PER_CARDNO,PER_PERSONAL.DEPARTMENT_ID
 					from		PER_PERSONAL, PER_PRENAME
					where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and 
							PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		} // end if
		
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_GENNAME = personal_gen_name($data[PER_NAME],$data[PER_SURNAME]);
		$PER_GENNAME = "9999".$PER_GENNAME;
		$PER_CARDNO = (trim($data[PER_CARDNO]))? trim($data[PER_CARDNO]) : "NULL";
	} // end if

	if($command=="UPDATE" && $PER_ID && $PIC_SEQ){
		$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID AND PER_PICSEQ=$PIC_SEQ ";
		if ($db_dpis->send_cmd($cmd)) { 
			$data = $db_dpis->get_array();
			$O_PIC_SHOW = trim($data[PIC_SHOW]);
			if ($O_PIC_SHOW=="1" && $PIC_SHOW!="1")  $change_show="NoShow";
			else if ($O_PIC_SHOW==$PIC_SHOW) 	$change_show="NoChange";
			else $change_show="Show";
			
			if ($change_show=="Show") {
				$cmd = " UPDATE PER_PERSONALPIC SET PIC_SHOW=''	
								WHERE PER_ID=$PER_ID AND PIC_SHOW='1' ";
//			} else if ($change_show=="NoShow") {
//				$cmd = " UPDATE PER_PERSONALPIC SET PIC_SHOW='1'
//								WHERE PER_ID=$PER_ID AND last(PER_PICSAVEDATE) ";
			}
			$db_dpis->send_cmd($cmd);

			$cmd = " UPDATE PER_PERSONALPIC SET PIC_SHOW='$PIC_SHOW', PIC_REMARK='$PIC_REMARK', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 	WHERE PER_ID=$PER_ID AND PER_PICSEQ=$PIC_SEQ ";

			$db_dpis->send_cmd($cmd);		
		} else {
			$img_error = "ไม่สามารถแก้ไขข้อมูลรูปได้";
		}
		$UPD=1;
		$command="UPDATEIMG";	//ถ้าเลือกรูปมาแล้วกดปุ่มแก้ไขข้อมูล
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติรูปภาพ [$PER_ID : $PIC_SEQ]");
	} // end if

	if($command=="ADD" && $PER_ID){
		$cmd2="select PER_PICSEQ from PER_PERSONALPIC  where PER_ID=$PER_ID and PER_PICSEQ='$PIC_SEQ' ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจากคุณระบุลำดับที่รูปภาพซ้ำ !!!");
				-->   </script>	<? 
		} else {	  
			$cmd = " select max(PER_PICSEQ) as max_id from PER_PERSONALPIC where PER_ID=$PER_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$PIC_SEQ = $data[max_id] + 1;

			$T_PIC_SEQ = substr("000",0,3-strlen("$PIC_SEQ"))."$PIC_SEQ";
			$PIC_NAME = $IMG_PATH.($PER_CARDNO=="NULL"?$PER_GENNAME:$PER_CARDNO).$T_PIC_SEQ.".jpg";
		
			if ($PIC_SHOW=="1") $change_show="Show"; else $change_show="NoShow";

			if ($change_show=="Show") {
				$cmd = " UPDATE PER_PERSONALPIC SET PIC_SHOW=''	
								WHERE PER_ID=$PER_ID AND PIC_SHOW='1' ";
//			} else if ($change_show=="NoShow") {
//				$cmd = " UPDATE PER_PERSONALPIC SET PIC_SHOW='1'
//								WHERE PER_ID=$PER_ID AND last(PER_PICSAVEDATE) ";
			}
			$db_dpis->send_cmd($cmd);

			$cmd = " insert into PER_PERSONALPIC (PER_ID, PER_PICSEQ, PER_CARDNO, PER_PICPATH, PER_PICSAVEDATE, PIC_REMARK, PIC_SHOW, PER_GENNAME, UPDATE_USER, UPDATE_DATE)
							values ($PER_ID, $PIC_SEQ, $PER_CARDNO, '$IMG_PATH', '', '$PIC_REMARK', '$PIC_SHOW', '$PER_GENNAME', $SESS_USERID, '$UPDATE_DATE')   ";
			$db_dpis->send_cmd($cmd);
			
//			echo "ADD $cmd<br>";
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติรูปภาพ [$PER_ID : $PIC_SEQ]");
		} // end if
		$UPD=1;
		$command="UPDATEIMG";	//ถ้าเลือกรูปใส่มาตั้งแต่ตอนแรกที่เพิ่มข้อมูล
	}
	
	if($command=="UPDATEIMG" && $PER_ID){
		$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID AND PER_PICSEQ=$PIC_SEQ ";
//		echo "IMG:$cmd<br>";
		if ($db_dpis->send_cmd($cmd)) { 
			$data = $db_dpis->get_array();
			$PER_CARDNO = trim($data[PER_CARDNO]);
			$PER_GENNAME = trim($data[PER_GENNAME]);
			$PIC_PATH = trim($data[PER_PICPATH]);
			$O_PIC_SHOW = trim($data[PIC_SHOW]);
			if ($O_PIC_SHOW=="1" && $PIC_SHOW!="1")  $change_show="NoShow";
			else if ($O_PIC_SHOW==$PIC_SHOW) 	$change_show="NoChange";
			else $change_show="Show";
			$T_PIC_SEQ = substr("000",0,3-strlen("$PIC_SEQ"))."$PIC_SEQ";

			$PIC_PATH =$IMG_PATH;	// สำหรับทดสอบ TesTTTTTTTTTT
			$N_PIC_NAME=$PIC_PATH.($PER_CARDNO=="NULL"?$PER_GENNAME:$PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
			$T_PIC_NAME=$PIC_PATH."tmp_".($PER_CARDNO=="NULL"?$PER_GENNAME:$PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
			$f_upd=true;
		} else {
			$cmd = " select max(PER_PICSEQ) as max_id from PER_PERSONALPIC where PER_ID=$PER_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$PIC_SEQ = $data[max_id] + 1;
			if ($PIC_SHOW=="1") $change_show="Show"; else $change_show="NoShow";
			$T_PIC_SEQ = substr("000",0,3-strlen("$PIC_SEQ"))."$PIC_SEQ";
			$N_PIC_NAME=$IMG_PATH.($PER_CARDNO=="NULL"?$PER_GENNAME:$PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
			$T_PIC_NAME=$IMG_PATH."tmp_".($PER_CARDNO=="NULL"?$PER_GENNAME:$PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
//			echo "N_PIC_NAME=$N_PIC_NAME:pic_show=$PIC_SHOW<br>";
			$f_upd=false;
		}
		if($N_PIC_NAME){
			if(is_file($PERSONAL_PIC)&&is_uploaded_file($PERSONAL_PIC)){
				$tmp_filename = $T_PIC_NAME;
				move_uploaded_file($PERSONAL_PIC, $tmp_filename);	
				$arr_img_attr = getimagesize($tmp_filename);
				
				echo "==> $PERSONAL_PIC  +++  $tmp_filename ";
//echo "<pre>$tmp_filename===>"; print_r($arr_img_attr); echo "</pre>";
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
				$filename = $N_PIC_NAME;
				$convert_success = imagejpeg($img, $filename, 100);
//				echo "convert result :: $convert_success<br>";
				imagedestroy($img);
				$img_error = "เปลี่ยนรูป [$N_PIC_NAME]";
				$PIC_SAVEDATE = date("Y-m-d");
			}else{
				$img_error = "ไม่สามารถเปลี่ยนรูปได้";
			} // end if
		}else{
			$img_error = "ไม่สามารถเปลี่ยนรูปได้";
		} // end if
		
		if ($change_show=="Show") {
			$cmd = " UPDATE PER_PERSONALPIC SET PIC_SHOW=''	
							WHERE PER_ID=$PER_ID AND PIC_SHOW='1' ";
//		} else if ($change_show=="NoShow") {
//			$cmd = " UPDATE PER_PERSONALPIC SET PIC_SHOW='1'
//							WHERE PER_ID=$PER_ID AND last(PER_PICSAVEDATE) ";
		}
		$db_dpis->send_cmd($cmd);
		
		if ($f_upd) {
			$cmd = " UPDATE PER_PERSONALPIC SET PIC_SHOW='$PIC_SHOW', PIC_REMARK='$PIC_REMARK', PER_PICSAVEDATE='$PIC_SAVEDATE', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 	WHERE PER_ID=$PER_ID AND PER_PICSEQ=$PIC_SEQ ";
		} else {
			$cmd = " insert into PER_PERSONALPIC (PER_ID, PER_PICSEQ, PER_CARDNO, PER_PICPATH, PER_PICSAVEDATE, PIC_REMARK, PIC_SHOW, PER_GENNAME, UPDATE_USER, UPDATE_DATE)
							values ($PER_ID, $PIC_SEQ, $PER_CARDNO, '$IMG_PATH', '$PIC_SAVEDATE', '$PIC_REMARK', '$PIC_SHOW', '$PER_GENNAME', $SESS_USERID, '$UPDATE_DATE') ";
		}
		$db_dpis->send_cmd($cmd);

//		echo "$cmd<br>";
		$UPD=1;
		//รีเฟรชเพจ
//_____________		echo "<meta http-equiv=\"refresh\" content=\"0;URL=personal_pichist.html?PER_ID=".$PER_ID."&MENU_ID_LV0=".$MENU_ID_LV0."&MENU_ID_LV1=".$MENU_ID_LV1."&MENU_ID_LV2=".$MENU_ID_LV2."&MENU_ID_LV3=".$MENU_ID_LV3."&HIDE_HEADER=1".($MAIN_VIEW?"&MAIN_VIEW=1":"")."&getdate=".date('YmdHis')."\">";	
	} // end if UPDATEIMG
	
	if(($UPD && $PER_ID && $PIC_SEQ) || ($VIEW && $PER_ID && $PIC_SEQ)){
		$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID AND PER_PICSEQ=$PIC_SEQ ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_GENNAME = trim($data[PER_GENNAME]);
		$PIC_PATH = trim($data[PER_PICPATH]);
		$T_PIC_SEQ = substr("000",0,3-strlen("$PIC_SEQ"))."$PIC_SEQ";
//		$PIC_NAME = $PIC_PATH.($PER_CARDNO=="NULL"?$PER_GENNAME:$PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
		$PIC_NAME = ($PER_CARDNO=="NULL"?$PER_GENNAME:$PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
		$PIC_REMARK = trim($data[PIC_REMARK]);
		$PIC_SAVEDATE = show_date_format($data[PER_PICSAVEDATE], 1);
		$PIC_SHOW = $data[PIC_SHOW];
		$UPDATE_USER = $data[UPDATE_USER];
		
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($PIC_SEQ);
		unset($PIC_SHOW);
		unset($PIC_SAVEDATE);
		unset($PIC_REMARK);
		unset($PIC_PATH);
	
		unset($PIC_NAME);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
?>