<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");

//	$db_dpis_n = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	// ==== use for testing phase =====
	$db_dpis_n = new connect_db($db_host, $db_name, $db_user, $db_pwd);
	// ==========================

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if(!$TITLE_ID) $TITLE_NAME = "ข้อมูลโดยสรุป";		
	$IMG_PATH = "../attachment/pic_personal/";	

	$arr_selected_list = explode(",", trim($SELECTED_LIST));
	$count_checked = 	count($arr_selected_list);
	
	for($i=0; $i<$count_checked; $i++){
		$PER_ID = $arr_selected_list[$i];
		
		if($DPISDB=="odbc"){
			$cmd = " select 	a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_CARDNO, a.LEVEL_NO,
											a.PER_TYPE, a.POS_ID, a.POEM_ID, a.POEMS_ID
							 from		(
							 					PER_PERSONAL a
							 					inner join PER_PRENAME b on (a.PN_CODE=b.PN_CODE)
											)
							 where 	a.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_CARDNO, a.LEVEL_NO,
											a.PER_TYPE, a.POS_ID, a.POEM_ID, a.POEMS_ID
							 from		PER_PERSONAL a, PER_PRENAME b
							 where 	a.PN_CODE=b.PN_CODE and a.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_CARDNO, a.LEVEL_NO,
											a.PER_TYPE, a.POS_ID, a.POEM_ID, a.POEMS_ID
							 from		(
							 					PER_PERSONAL a
							 					inner join PER_PRENAME b on (a.PN_CODE=b.PN_CODE)
											)
							 where 	a.PER_ID=$PER_ID ";
		} // end if
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$PER_TYPE = $data[PER_TYPE];
			if($PER_TYPE == 1){
				$position_field = "POS_ID";
			}elseif($PER_TYPE == 2){
				$position_field = "POEM_ID";
			}elseif($PER_TYPE == 3){
				$position_field = "POEMS_ID";
			}
//			$POS_POEM_ID=$data[$position_field];
//			ให้ POS_POEM_ID เป็นตำแหน่งที่จะเลื่อนไป	
//			echo "ตำแหน่งที่จะเลื่อนไป-->POS_POEM_ID=$POS_POEM_ID<br>";

			$POS_ID = $data[POS_ID];
			$POEM_ID = $data[POEM_ID];
			$POEMS_ID = $data[POEMS_ID];
			//$PRO_DATE = "";
			${"PER_NAME_".($i + 1)} = "$data[PN_NAME]$data[PER_NAME] $data[PER_SURNAME]";
			${"PER_CARDNO_".($i + 1)} = trim($data[PER_CARDNO]);
			${"IFRAME_SRC_".($i + 1)} = "data_promote_e_p_presentation_1.html?PER_ID=$PER_ID&PRO_DATE=$PRO_DATE&POS_POEM_ID=$POS_POEM_ID&MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2&MENU_ID_LV3=$MENU_ID_LV3&getdate=". date("YmdHis");

		// กำหนด การแสดงรูปภาพ หาจาก DB
			$next_search_pic = 0;
//			$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID AND PIC_SHOW = '1' ";
			$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID order by PER_PICSAVEDATE asc ";
//echo "IMG:$cmd<br>";
			$piccnt = $db_dpis2->send_cmd($cmd);
			if ($piccnt > 0) { 
				while ($data2 = $db_dpis2->get_array()) {
					$PIC_SHOW = trim($data2[PIC_SHOW]);
					$TMP_PER_CARDNO = trim($data2[PER_CARDNO]);
					$PER_GENNAME = trim($data2[PER_GENNAME]);
					$PIC_PATH = trim($data2[PER_PICPATH]);
					$PIC_SEQ = trim($data2[PER_PICSEQ]);
					$T_PIC_SEQ = substr("000",0,3-strlen("$PIC_SEQ"))."$PIC_SEQ";
					$PIC_SERVER_ID = trim($data2[PIC_SERVER_ID]);
				
$next_search_pic = 1;					
if($next_search_pic==1){
					if ($PIC_SHOW == '1') {		// เฉพาะที่ กำหนด แสดงรูปภาพ เท่านั้น
						if($PIC_SERVER_ID > 0){
							if($PIC_SERVER_ID==99){		// $PIC_SERVER_ID 99 = ip จากตั้งค่าระบบ C06				 ใช้ \ 
								// หา # กรณี server อื่น เปลี่ยน # ให้เป็น \ เพื่อใช้ในการอัพโหลดรูป
								$PIC_PATH = $IMG_PATH_DISPLAY."#".$PIC_PATH;
								$PIC_PATH = str_replace("#","'",$PIC_PATH);
								$PIC_PATH = addslashes($PIC_PATH);
								$PIC_PATH = str_replace("'","",$PIC_PATH);
							
								$img_file = $PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
								$arr_img[] = $img_file;
								$arr_imgshow[] = 1;
								$PER_PICSAVEDATE = trim($data2[PER_PICSAVEDATE]);
							}else{  // other server
								$cmd = " select * from OTH_SERVER where SERVER_ID=$PIC_SERVER_ID ";
								if ($db_dpis2->send_cmd($cmd)) { 
									$data2 = $db_dpis2->get_array();
									$SERVER_NAME = trim($data2[SERVER_NAME]);
									$ftp_server = trim($data2[FTP_SERVER]);
									$ftp_username = trim($data2[FTP_USERNAME]);
									$ftp_password = trim($data2[FTP_PASSWORD]);
									$main_path = trim($data2[MAIN_PATH]);
									$http_server = trim($data2[HTTP_SERVER]);
										if ($http_server) {
											//echo "1.".$http_server."/".$img_file."<br>";
											$fp = @fopen($http_server."/".$img_file, "r");
											if ($fp !== false) $img_file = $http_server."/".$img_file;
											else $img_file=$IMG_PATH."shadow.jpg";
											fclose($fp);
										} else {
					//						echo "2.".$img_file."<br>";
											$img_file = file_exists("../".$img_file)?("../".$img_file):$IMG_PATH."shadow.jpg";
										}
									} // end db_dpis2
							}
						}else{ // localhost  $PIC_SERVER_ID == 0
							$img_file = $PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
							$arr_img[] = "../".$PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
							$arr_imgshow[] = 1;
							$PER_PICSAVEDATE = trim($data2[PER_PICSAVEDATE]);
						}
					} else { // PIC_SHOW==1
						/*
						$arr_img[] = "../".$PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
						$arr_imgsavedate[] = trim($data2[PER_PICSAVEDATE]);
						$arr_imgshow[] = 0;
						*/
					}
} // end if($next_search_pic==1)
				} // end while loop
			} else { // $piccnt
				//$img_file="";
				$img_file=$IMG_PATH."shadow.jpg";
			}
		
			//echo "($PIC_PATH) -> img_file=$img_file // $PIC_SERVER_ID<br>";
			if ($i==0) $img_file_1 = $img_file;
			elseif ($i==1) $img_file_2 = $img_file;
			elseif ($i==2) $img_file_3 = $img_file;

			$LEVEL_NO = trim($data[LEVEL_NO]);	
			$cmd="select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO'";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$Level=$db_dpis->get_array();
			$LEVEL_NAME=$Level[LEVEL_NAME];
			$POSITION_LEVEL = $Level[POSITION_LEVEL];
			if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
			
			if($PER_TYPE==1){
				$cmd = " select		a.POS_NO, b.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2, c.PL_NAME, a.PT_CODE
								 from		PER_POSITION a, PER_ORG b, PER_LINE c
								 where	a.POS_ID=$POS_ID and a.ORG_ID=b.ORG_ID and a.PL_CODE=c.PL_CODE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				${"POS_NO_".($i + 1)} = trim($data2[POS_NO]);
				$PL_NAME = trim($data2[PL_NAME]);
				$PT_CODE = trim($data2[PT_CODE]);
				$cmd = " select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE' ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$PT_NAME = trim($data1[PT_NAME]);
				
				${"POSITION_".($i + 1)} = trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):" ".$LEVEL_NAME;
				${"ORG_".($i + 1)} = $data2[ORG_NAME];
				
				$ORG_ID_1 = $data2[ORG_ID_1];
				$ORG_ID_2 = $data2[ORG_ID_2];				
			}elseif($PER_TYPE==2){
				$cmd = " select		a.POEM_NO as POS_NO, b.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2, c.PN_NAME
								 from		PER_POS_EMP a, PER_ORG b, PER_POS_NAME c
								 where	a.POEM_ID=$POEM_ID and a.ORG_ID=b.ORG_ID and a.PN_CODE=c.PN_CODE ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				${"POS_NO_".($i + 1)} = trim($data2[POS_NO]);
				$PN_NAME = trim($data2[PN_NAME]);
				
				${"POSITION_".($i + 1)} = trim($PN_NAME)?($PN_NAME . $POSITION_LEVEL):" ".$LEVEL_NAME;
				${"ORG_".($i + 1)} = $data2[ORG_NAME];
				
				$ORG_ID_1 = $data2[ORG_ID_1];
				$ORG_ID_2 = $data2[ORG_ID_2];
			}elseif($PER_TYPE==3){
				$cmd = " select		a.POEMS_NO as POS_NO, b.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2, c.EP_NAME
								 from		PER_POS_EMPSER a, PER_ORG b, PER_EMPSER_POS_NAME c
								 where	a.POEMS_ID=$POEMS_ID and a.ORG_ID=b.ORG_ID and a.EP_CODE=c.EP_CODE ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				${"POS_NO_".($i + 1)} = trim($data2[POS_NO]);
				$EP_NAME = trim($data2[EP_NAME]);
				
				${"POSITION_".($i + 1)} = trim($EP_NAME)?($EP_NAME . $POSITION_LEVEL):" ".$LEVEL_NAME;
				${"ORG_".($i + 1)} = $data2[ORG_NAME];
				
				$ORG_ID_1 = $data2[ORG_ID_1];
				$ORG_ID_2 = $data2[ORG_ID_2];
			} elseif($PER_TYPE==4){
				$cmd = " select		a.POT_NO as POS_NO, b.ORG_NAME, a.ORG_ID_1, a.ORG_ID_2, c.TP_NAME
								 from		PER_POS_TEMP a, PER_ORG b, PER_TEMP_POS_NAME c
								 where	a.POT_ID=$POT_ID and a.ORG_ID=b.ORG_ID and a.TP_CODE=c.TP_CODE ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
				$data2 = $db_dpis2->get_array();
				${"POS_NO_".($i + 1)} = trim($data2[POS_NO]);
				$TP_NAME = trim($data2[TP_NAME]);
				
				${"POSITION_".($i + 1)} = trim($TP_NAME)?($TP_NAME . $POSITION_LEVEL):" ".$LEVEL_NAME;
				${"ORG_".($i + 1)} = $data2[ORG_NAME];
				
				$ORG_ID_1 = $data2[ORG_ID_1];
				$ORG_ID_2 = $data2[ORG_ID_2];
			} // end if

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			if($ORG_ID_1) ${"ORG_".($i + 1)} .= "<br>".$data2[ORG_NAME];

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			if($ORG_ID_2) ${"ORG_".($i + 1)} .= "<br>".$data2[ORG_NAME];
		} // end while
	} // end if
?>