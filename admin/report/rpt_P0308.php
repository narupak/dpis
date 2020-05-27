<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$IMG_PATH = "../../attachment/pic_personal/";
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	// sort หัวข้อที่เลือก เพื่อจัดลำดับการแสดงก่อนหลัง ของ $arr_history_name ที่เลือกเข้ามา
	$arr_history_sort = array("PERINFO", "POSITIONHIS", "EDUCATE", 'TRAINING', "SERVICEHIS", "SPROM");
	// sort หัวข้อที่เลือก
	if (!$HISTORY_LIST) {
		$arr_history_name = $arr_history_sort;
	} else {
		$arr_history_name = explode("|", $HISTORY_LIST);
	}
	
	$arr_selected_list = explode(",", trim($SELECTED_LIST));
	$num_zone = 	count($arr_selected_list);
//	echo "selected_list : ".trim($SELECTED_LIST)."<br>";
	
	for($i=0; $i<$num_zone; $i++){
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
		$cnt = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		echo "$cmd ($cnt)<br>";
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
//			${"IFRAME_SRC_".($i + 1)} = "data_promote_e_p_presentation_1.html?PER_ID=$PER_ID&PRO_DATE=$PRO_DATE&POS_POEM_ID=$POS_POEM_ID&MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2&MENU_ID_LV3=$MENU_ID_LV3&getdate=". date("YmdHis");
			${"POS_ID_".($i + 1)} = $POS_ID;
			${"POEM_ID_".($i + 1)} = $POEM_ID;
			${"POEMS_ID_".($i + 1)} = $POEMS_ID;

			if($POS_POEM_ID && $PRO_DATE){
				if($PER_TYPE==1){
					$cmd = " select PRO_SUMMARY from PER_PROMOTE_P where SUBSTR(PRO_DATE, 1, 10)='$PRO_DATE' and POS_ID=$POS_POEM_ID and PER_ID=$PER_ID ";
				}elseif($PER_TYPE==2){
					$cmd = " select PRO_SUMMARY from PER_PROMOTE_E where SUBSTR(PRO_DATE, 1, 10)='$PRO_DATE' and POEM_ID=$POS_POEM_ID and PER_ID=$PER_ID ";
				}elseif($PER_TYPE==3){
					$cmd = " select PRO_SUMMARY from PER_PROMOTE_E where SUBSTR(PRO_DATE, 1, 10)='$PRO_DATE' and POEM_ID=$POS_POEM_ID and PER_ID=$PER_ID ";
				} // end if
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				${"PRO_SUMMARY".($i + 1)} = stripslashes(trim($data[PRO_SUMMARY]));
		//		echo "-> ( ".$PRO_SUMMARY." ) +".$command.":".$cmd;
			}

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
				$cmd = "	select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE'";
				$db_dpis3->send_cmd($cmd);
				$data3 = $db_dpis3->get_array();
				$PT_NAME = trim($data3[PT_NAME]);
				
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
	} // end for loop
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "";
	$report_code = (($NUMBER_DISPLAY==2)?convert2thaidigit("RP0308"):"RP0308");
	$orientation='L';

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	
	$page_start_x = $pdf->x;	  $page_start_y = $pdf->y;

	$zone_wide = floor(290 / $num_zone);	// ความกว้างต่อบุคคล

	$heading_width[PERINFO][0] = floor(75/$num_zone);
	$heading_width[PERINFO][1] = floor(50/$num_zone);
	$heading_width[PERINFO][2] = floor(165/$num_zone);
	
	$heading_text[PERINFO][0] = "";			$column_function[PERINFO][0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[PERINFO][1] = "";			$column_function[PERINFO][1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[PERINFO][2] = "";			$column_function[PERINFO][2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");

	$heading_align[PERINFO] = array('C', 'C', 'C');

	$heading_width[POSITIONHIS][0] = floor(50/$num_zone);
	$heading_width[POSITIONHIS][1] = floor(45/$num_zone);
	$heading_width[POSITIONHIS][2] = floor(65/$num_zone);
	$heading_width[POSITIONHIS][3] = floor(65/$num_zone);
	$heading_width[POSITIONHIS][4] = floor(65/$num_zone);
	
	$heading_text[POSITIONHIS][0] = "วันเข้าสู่ตำแหน่ง";			$column_function[POSITIONHIS][0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[POSITIONHIS][1] = "$POS_NO_TITLE";			$column_function[POSITIONHIS][1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[POSITIONHIS][2] = "ชื่อตำแหน่ง";					$column_function[POSITIONHIS][2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[POSITIONHIS][3] = "$ORG_TITLE";				$column_function[POSITIONHIS][3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[POSITIONHIS][4] = "$ORG_TITLE1";				$column_function[POSITIONHIS][4] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");

	$heading_align[POSITIONHIS] = array('C', 'C', 'C', 'C', 'C');

	$heading_width[EDUCATE][0] = floor(70/$num_zone);
	$heading_width[EDUCATE][1] = floor(70/$num_zone);
	$heading_width[EDUCATE][2] = floor(80/$num_zone);
	$heading_width[EDUCATE][3] = floor(70/$num_zone);
	
	$heading_text[EDUCATE][0] = $EN_TITLE;			$column_function[EDUCATE][0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[EDUCATE][1] = $EM_TITLE;			$column_function[EDUCATE][1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[EDUCATE][2] = $INS_TITLE;			$column_function[EDUCATE][2] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[EDUCATE][3] = $CT_TITLE;			$column_function[EDUCATE][3] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");

	$heading_align[EDUCATE] = array('C', 'C', 'C', 'C');

	$heading_width[TRAINING][0] = floor(45/$num_zone);
	$heading_width[TRAINING][1] = floor(245/$num_zone);

	$heading_text[TRAINING][0] = "วันที่เข้ารับการอบรม";		$column_function[TRAINING][0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[TRAINING][1] = "ชื่อหลักสูตร";						$column_function[TRAINING][1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	
	$heading_align[TRAINING] = array('C', 'C');

	$heading_width[SERVICEHIS][0] = floor(45/$num_zone);
	$heading_width[SERVICEHIS][1] = floor(245/$num_zone);
	
	$heading_text[SERVICEHIS][0] = "วันที่";									$column_function[SERVICEHIS][0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[SERVICEHIS][1] = "หัวข้อโครงการ";					$column_function[SERVICEHIS][1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");

	$heading_align[SERVICEHIS] = array('C', 'C');

	$heading_width[SPROM][0] = floor(45/$num_zone);
	$heading_width[SPROM][1] = floor(245/$num_zone);

	$heading_text[SPROM][0] = "วันที่ได้รับ";					$column_function[SPROM][0] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
	$heading_text[SPROM][1] = "ความดีความชอบ";		$column_function[SPROM][1] = (($NUMBER_DISPLAY==2)?"TNUM":"ENUM");

	$heading_align[SPROM] = array('C', 'C');

	$pdf->AutoPageBreak = false;

	for($history_index=0; $history_index<count($arr_history_sort); $history_index++){
			if (in_array($arr_history_sort[$history_index],$arr_history_name)) {
				$HISTORY_NAME = $arr_history_sort[$history_index];
			} else {
				$HISTORY_NAME = "";
			}
//			echo "HISTORY_NAME=$HISTORY_NAME<br>";
			switch($HISTORY_NAME){
					case "PERINFO" :
						$pdf->SetFont($font,'b',14);
						$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

//						$pdf->Cell(290,7,"",1,1,"C");
//						$pdf->Cell(290,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("ข้อมูลโดยสรุป"):"ข้อมูลโดยสรุป"),1,1,"C");
						
						$head_text = implode(",", $heading_text[$HISTORY_NAME]);	// ไม่พิมพ์ head
						$head_width = implode(",", $heading_width[$HISTORY_NAME]);
						$head_align = implode(",", $heading_align[$HISTORY_NAME]);
						$col_function = implode(",", $column_function[$HISTORY_NAME]);
						$COLUMN_FORMAT = do_COLUMN_FORMAT($heading_text[$HISTORY_NAME], $heading_width[$HISTORY_NAME], $heading_align[$HISTORY_NAME]);
						$a_format = explode("|", $COLUMN_FORMAT);
						$a_column_map = (array) null; $a_column_sel = (array) null; $a_column_width = (array) null; $a_column_align = (array) null;
						$a_head_text1 = (array) null;	$a_head_width1 = (array) null; $a_head_align1 = (array) null; $a_col_function1 = (array) null;
						for($ii=0; $ii < $num_zone; $ii++) {
							$a_head_text1[] = $head_text;
							$a_head_width1[] = $head_width;
							$a_head_align1[] = $head_align;
							$a_col_function1[] = $col_function;
//							$a_column_map[] = $a_format[0];
							$a_column_sel[] = $a_format[1];
							$a_column_width[] = $a_format[2];
							$a_column_align[] = $a_format[3];
						}
						$head_text1 = implode(",,",$a_head_text1);
						$head_width1 = implode(",1,",$a_head_width1);
						$head_align1 = implode(",C,",$a_head_align1);
						$col_function1 = implode(",,",$a_col_function1);
						$t_column_sel1 = implode(",1,",$a_column_sel);
						$arr_column_sel = explode(",", $t_column_sel1);
						$t_column_width1 = implode(",1,",$a_column_width);
						$arr_column_width = explode(",", $t_column_width1);
						$t_column_align1 = implode(",C,",$a_column_align);
						$arr_column_align = explode(",", $t_column_align1);
						$a_column_map1 = (array) null;
						for($iii = 0; $iii < count($arr_column_sel); $iii++) {
							$a_column_map1[] = $iii;
						}
						$arr_column_map = $a_column_map1;
						$COLUMN_FORMAT1 = implode(",",$arr_column_map)."|".implode(",",$arr_column_sel)."|".implode(",",$arr_column_width)."|".implode(",",$arr_column_align);
//						echo "$HISTORY_NAME--text:$head_text1, width:$head_width1, col:$col_function1, format:$COLUMN_FORMAT1<br>";
						$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT1, $col_function1, true);
						if (!$result) echo "****** error ****** on open table for $table<br>";

 						$arr_data = (array) null;
 						$data_align = (array) null;
						for($ii=0; $ii < $num_zone; $ii++) {
							$POS_ID = ${"POS_ID_".($ii + 1)};
							$POEM_ID = ${"POEM_ID_".($ii + 1)};
							$POEMS_ID = ${"POEMS_ID_".($ii + 1)};
							$PER_NAME = ${"PER_NAME_".($ii + 1)};
							$PER_CARDNO = ${"PER_CARDNO_".($ii + 1)};
							$POSITION = ${"POSITION_".($ii + 1)};
							$ORG = ${"ORG_".($ii + 1)};
							$PRO_SUMMARY = ${"PRO_SUMMARY".($ii + 1)};

							$picname = (($PER_CARDNO && file_exists($IMG_PATH.$PER_CARDNO.".jpg"))?($IMG_PATH.$PER_CARDNO.".jpg"):"../images/my_preview.jpg");
							if ($ii==0) $picname = "../".$img_file_1;
							elseif ($ii==1) $picname = "../".$img_file_2;
							elseif ($ii==2) $picname = "../".$img_file_3;

							$arr_data[1][] = "<&&row&&><*img*".$picname."*img*>";
							$arr_data[1][] = $NAME_TITLE;
							$arr_data[1][] = $PER_NAME;

							$data_align[1][] = "C";
							$data_align[1][] = "R";
							$data_align[1][] = "L";
							if ($ii < $num_zone-1) {
								$arr_data[1][] = "";
								$data_align[1][] = "C";
							}

							$arr_data[2][] = "<&&row&&><*img*".$picname."*img*>";
							$arr_data[2][] = "สังกัด";
							$arr_data[2][] = $ORG;

							$data_align[2][] = "C";
							$data_align[2][] = "R";
							$data_align[2][] = "L";
							if ($ii < $num_zone-1) {
								$arr_data[2][] = "";
								$data_align[2][] = "C";
							}

							$arr_data[3][] = "<&&row&&><*img*".$picname."*img*>";
							$arr_data[3][] = "";
							$arr_data[3][] = "";

							$data_align[3][] = "C";
							$data_align[3][] = "R";
							$data_align[3][] = "L";
							if ($ii < $num_zone-1) {
								$arr_data[3][] = "";
								$data_align[3][] = "C";
							}

							$arr_data[4][] = "<&&row&&><*img*".$picname."*img*>";
							$arr_data[4][] = "";
							$arr_data[4][] = "";

							$data_align[4][] = "C";
							$data_align[4][] = "R";
							$data_align[4][] = "L";
							if ($ii < $num_zone-1) {
								$arr_data[4][] = "";
								$data_align[4][] = "C";
							}

							$arr_data[5][] = "<&&row&&><*img*".$picname."*img*>";
							$arr_data[5][] = "";
							$arr_data[5][] = "";

							$data_align[5][] = "C";
							$data_align[5][] = "R";
							$data_align[5][] = "L";
							if ($ii < $num_zone-1) {
								$arr_data[5][] = "";
								$data_align[5][] = "C";
							}

							$arr_data[6][] = "<&&end&&><*img*".$picname."*img*>";
							$arr_data[6][] = "";
							$arr_data[6][] = "";

							$data_align[6][] = "C";
							$data_align[6][] = "R";
							$data_align[6][] = "L";
							if ($ii < $num_zone-1) {
								$arr_data[6][] = "";
								$data_align[6][] = "C";
							}

							$arr_data[7][] = "<**1**> ตำแหน่ง : $POSITION";
							$arr_data[7][] = "<**1**>";
							$arr_data[7][] = "<**1**>";

							$data_align[7][] = "L";
							$data_align[7][] = "L";
							$data_align[7][] = "L";
							if ($ii < $num_zone-1) {
								$arr_data[7][] = "";
								$data_align[7][] = "C";
							}

							$arr_data[8][] = "";
							$arr_data[8][] = "";
							$arr_data[8][] = "";

							$data_align[8][] = "L";
							$data_align[8][] = "L";
							$data_align[8][] = "L";
							if ($ii < $num_zone-1) {
								$arr_data[8][] = "";
								$data_align[8][] = "C";
							}

							$arr_data[9][] = "<**1**> --$PRO_SUMMARY";
							$arr_data[9][] = "<**1**> --$PRO_SUMMARY";
							$arr_data[9][] = "<**1**> --$PRO_SUMMARY";

							$data_align[9][] = "L";
							$data_align[9][] = "L";
							$data_align[9][] = "L";
							if ($ii < $num_zone-1) {
								$arr_data[9][] = "";
								$data_align[9][] = "C";
							}
						} // end for loop $ii

						for($kk=1; $kk < 10; $kk++) {
//							echo "$HISTORY_NAME -- data[$kk] : ".implode(",",$arr_data[$kk])."<br>";
							if (($kk == 9 && $PRO_SUMMARY) || $kk != 9)  {
								$result = $pdf->add_data_tab($arr_data[$kk], 7, "", $data_align[$kk], "", "14", "", "000000", "");
								if (!$result) echo "****** error ****** add data to table for $HISTORY_NAME line 1<br>";
							}
						}

						$pdf->close_tab("", "no"); 
						break;
					case "POSITIONHIS" :
//						$pdf->AddPage();

						$pdf->SetFont($font,'b',14);
						$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

						$pdf->Cell(285,7,"",0,1,"C");
						$pdf->Cell(285,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("1. การดำรงตำแหน่ง"):"1. การดำรงตำแหน่ง"),0,1,"C");

						$head_text = implode(",", $heading_text[$HISTORY_NAME]);	// ไม่พิมพ์ head
						$head_width = implode(",", $heading_width[$HISTORY_NAME]);
						$head_align = implode(",", $heading_align[$HISTORY_NAME]);
						$col_function = implode(",", $column_function[$HISTORY_NAME]);
						$COLUMN_FORMAT = do_COLUMN_FORMAT($heading_text[$HISTORY_NAME], $heading_width[$HISTORY_NAME], $heading_align[$HISTORY_NAME]);
						$a_format = explode("|", $COLUMN_FORMAT);
						$a_column_map = (array) null; $a_column_sel = (array) null; $a_column_width = (array) null; $a_column_align = (array) null;
						$a_head_text1 = (array) null;	$a_head_width1 = (array) null; $a_head_align1 = (array) null; $a_col_function1 = (array) null; $a_COLUMN_FORMAT1 = (array) null;
						for($ii=0; $ii < $num_zone; $ii++) {
							$a_head_text1[] = $head_text;
							$a_head_width1[] = $head_width;
							$a_head_align1[] = $head_align;
							$a_col_function1[] = $col_function;
//							$a_column_map[] = $a_format[0];
							$a_column_sel[] = $a_format[1];
							$a_column_width[] = $a_format[2];
							$a_column_align[] = $a_format[3];
						}
						$head_text1 = implode(",,",$a_head_text1);
						$head_width1 = implode(",1,",$a_head_width1);
						$head_align1 = implode(",C,",$a_head_align1);
						$col_function1 = implode(",,",$a_col_function1);
						$t_column_sel1 = implode(",1,",$a_column_sel);
						$arr_column_sel = explode(",", $t_column_sel1);
						$t_column_width1 = implode(",1,",$a_column_width);
						$arr_column_width = explode(",", $t_column_width1);
						$t_column_align1 = implode(",C,",$a_column_align);
						$arr_column_align = explode(",", $t_column_align1);
						$a_column_map1 = (array) null;
						for($iii = 0; $iii < count($arr_column_sel); $iii++) {
							$a_column_map1[] = $iii;
						}
						$arr_column_map = $a_column_map1;
						$COLUMN_FORMAT1 = implode(",",$arr_column_map)."|".implode(",",$arr_column_sel)."|".implode(",",$arr_column_width)."|".implode(",",$arr_column_align);
//						echo "$HISTORY_NAME--text:$head_text1, width:$head_width1, col:$col_function1, format:$COLUMN_FORMAT1<br>";
						$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT1, $col_function1);
						if (!$result) echo "****** error ****** on open table for $table<br>";

						$arr_data = (array) null;
 						$data_align = (array) null;
						$max_rec = 0;
						for($ii=0; $ii < $num_zone; $ii++) {
							$PER_ID = $arr_selected_list[$ii];
							
							if($PER_TYPE==1){
								if($DPISDB=="odbc"){
									$cmd = "  select		a.POH_EFFECTIVEDATE, a.POH_POS_NO_NAME, a.POH_POS_NO, a.POH_ORG3, a.POH_UNDER_ORG1, a.LEVEL_NO,
																		a.POH_PL_NAME as PL_NAME, a.PT_CODE
													  from			PER_POSITIONHIS a, PER_LINE c
													  where		a.PER_ID=$PER_ID and a.PL_CODE=c.PL_CODE 
													  order by	a.POH_EFFECTIVEDATE ";
								}elseif($DPISDB=="oci8"){
									$cmd = "  select		a.POH_EFFECTIVEDATE, a.POH_POS_NO_NAME, a.POH_POS_NO, a.POH_ORG3, a.POH_UNDER_ORG1, a.LEVEL_NO,
																		a.POH_PL_NAME as PL_NAME, a.PT_CODE
													  from			PER_POSITIONHIS a, PER_LINE c
													  where		a.PER_ID=$PER_ID and a.PL_CODE=c.PL_CODE(+)
													  order by	a.POH_EFFECTIVEDATE ";
								}elseif($DPISDB=="mysql"){
									$cmd = "  select		a.POH_EFFECTIVEDATE, a.POH_POS_NO_NAME, a.POH_POS_NO, a.POH_ORG3, a.POH_UNDER_ORG1, a.LEVEL_NO,
																		a.POH_PL_NAME as PL_NAME, a.PT_CODE
													  from			PER_POSITIONHIS a, PER_LINE c
													  where		a.PER_ID=$PER_ID and a.PL_CODE=c.PL_CODE 
													  order by	a.POH_EFFECTIVEDATE ";
								} // end if
							}elseif($PER_TYPE==2){
								if($DPISDB=="odbc"){
									$cmd = "  select		a.POH_EFFECTIVEDATE, a.POH_POS_NO_NAME, a.POH_POS_NO, a.POH_ORG3, a.POH_UNDER_ORG1, a.LEVEL_NO, c.PN_NAME as PL_NAME
													  from			PER_POSITIONHIS a, PER_POS_NAME c
													  where		a.PER_ID=$PER_ID and a.PN_CODE=c.PN_CODE
													  order by	a.POH_EFFECTIVEDATE ";
								}elseif($DPISDB=="oci8"){
									$cmd = "  select		a.POH_EFFECTIVEDATE, a.POH_POS_NO_NAME, a.POH_POS_NO, a.POH_ORG3, a.POH_UNDER_ORG1, a.LEVEL_NO, c.PN_NAME as PL_NAME
													  from			PER_POSITIONHIS a, PER_POS_NAME c
													  where		a.PER_ID=$PER_ID and a.PN_CODE=c.PN_CODE(+)
													  order by	a.POH_EFFECTIVEDATE ";
								}elseif($DPISDB=="mysql"){
									$cmd = "  select		a.POH_EFFECTIVEDATE, a.POH_POS_NO_NAME, a.POH_POS_NO, a.POH_ORG3, a.POH_UNDER_ORG1, a.LEVEL_NO, c.PN_NAME as PL_NAME
													  from			PER_POSITIONHIS a, PER_POS_NAME c
													  where		a.PER_ID=$PER_ID and a.PN_CODE=c.PN_CODE
													  order by	a.POH_EFFECTIVEDATE ";
								} // end if
							}elseif($PER_TYPE==3){
								if($DPISDB=="odbc"){
									$cmd = "  select		a.POH_EFFECTIVEDATE, a.POH_POS_NO_NAME, a.POH_POS_NO, a.POH_ORG3, a.POH_UNDER_ORG1, a.LEVEL_NO, c.EP_NAME as PL_NAME
													  from			PER_POSITIONHIS a, PER_EMPSER_POS_NAME c
													  where		a.PER_ID=$PER_ID and a.EP_CODE=c.EP_CODE
													  order by	a.POH_EFFECTIVEDATE ";
								}elseif($DPISDB=="oci8"){
									$cmd = "  select		a.POH_EFFECTIVEDATE, a.POH_POS_NO_NAME, a.POH_POS_NO, a.POH_ORG3, a.POH_UNDER_ORG1, a.LEVEL_NO, c.EP_NAME as PL_NAME
													  from			PER_POSITIONHIS a, PER_EMPSER_POS_NAME c
													  where		a.PER_ID=$PER_ID and a.EP_CODE=c.EP_CODE(+)
													  order by	a.POH_EFFECTIVEDATE ";
								}elseif($DPISDB=="mysql"){
									$cmd = "  select		a.POH_EFFECTIVEDATE, a.POH_POS_NO_NAME, a.POH_POS_NO, a.POH_ORG3, a.POH_UNDER_ORG1, a.LEVEL_NO, c.EP_NAME as PL_NAME
													  from			PER_POSITIONHIS a, PER_EMPSER_POS_NAME c
													  where		a.PER_ID=$PER_ID and a.EP_CODE=c.EP_CODE
													  order by	a.POH_EFFECTIVEDATE ";
								} // end if
							} // end if
							
							$count_data = $db_dpis->send_cmd($cmd);
						//	$db_dpis->show_error();
//							echo "$cmd ($count_data)<br>";
							if ($count_data > $max_rec) $max_rec = $count_data;
	
							if ($count_data) {
								$data_count = 0;
								while ($data = $db_dpis->get_array()) {
									$POH_EFFECTIVEDATE = show_date_format($data[POH_EFFECTIVEDATE], $DATE_DISPLAY);
									$POH_POS_NO = trim($data[POH_POS_NO_NAME]).trim($data[POH_POS_NO]);
									$ORG_NAME = trim($data[POH_ORG3]);
									$LEVEL_NO = trim($data[LEVEL_NO]);
									$PL_NAME = trim($data[PL_NAME]);
									$POH_UNDER_ORG1 = trim($data[POH_UNDER_ORG1]);
									
									if($PER_TYPE == 1){
										$PT_CODE = trim($data[PT_CODE]);
										$cmd = "	select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE'";
										$db_dpis2->send_cmd($cmd);
										$data2 = $db_dpis2->get_array();
										$PT_NAME = trim($data2[PT_NAME]);
											
										$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$POH_UNDER_ORG1 ";
										$db_dpis2->send_cmd($cmd);
										$data2 = $db_dpis2->get_array();
										$POH_UNDER_ORG_NAME1 = $data2[ORG_NAME];
										
										$cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
										$db_dpis2->send_cmd($cmd);
										$data2 = $db_dpis2->get_array();
										$LEVEL_NAME = $data2[LEVEL_NAME];
										$POSITION_LEVEL = $data2[POSITION_LEVEL];
									
										$POSITION = trim($PL_NAME)?($PL_NAME."".$POSITION_LEVEL. (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"$LEVEL_NAME";
										
									}elseif($PER_TYPE == 2){
										$POSITION = trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO)):"ระดับ ".level_no_format($LEVEL_NO);
									}elseif($PER_TYPE == 3){
										$POSITION = trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO)):"ระดับ ".level_no_format($LEVEL_NO);
									} 

									$arr_data[$data_count][] = $POH_EFFECTIVEDATE;
									$arr_data[$data_count][] = $POH_POS_NO;
									$arr_data[$data_count][] = $POSITION;
									$arr_data[$data_count][] = $ORG_NAME;
									$arr_data[$data_count][] = $POH_UNDER_ORG_NAME1;
			
									$data_align[$data_count][] = "C";
									$data_align[$data_count][] = "L";
									$data_align[$data_count][] = "L";
									$data_align[$data_count][] = "L";
									$data_align[$data_count][] = "L";
									if ($ii < $num_zone-1) {
										$arr_data[$data_count][] = "";
										$data_align[$data_count][] = "";
									}
									$data_count++;
								} // end while
							} // end if
						} // end for loop

						if ($max_rec > 0) {
							for($kk = 0; $kk < $max_rec; $kk++) {
//								echo "$HISTORY_NAME -- data[$kk] : ".implode(",",$arr_data[$kk])."<br>";
								$result = $pdf->add_data_tab($arr_data[$kk], 7, "TRHBL", $data_align[$kk], "", "14", "", "000000", "");
								if (!$result) echo "****** error ****** add data to table at record count = 5 <br>";
							}
						} else {
							$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "TRBL", "C", "", "14", "b", 0, 0);
							if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
						}
									
						$pdf->close_tab("");
						break;
					case "EDUCATE" : // การศึกษา
						if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();

						$pdf->SetFont($font,'b',14);
						$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

						$pdf->Cell(285,7,"",0,1,"C");
						$pdf->Cell(285,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("2. การศึกษา"):"2. การศึกษา"),0,1,"C");

						$head_text = implode(",", $heading_text[$HISTORY_NAME]);	// ไม่พิมพ์ head
						$head_width = implode(",", $heading_width[$HISTORY_NAME]);
						$head_align = implode(",", $heading_align[$HISTORY_NAME]);
						$col_function = implode(",", $column_function[$HISTORY_NAME]);
						$COLUMN_FORMAT = do_COLUMN_FORMAT($heading_text[$HISTORY_NAME], $heading_width[$HISTORY_NAME], $heading_align[$HISTORY_NAME]);
						$a_format = explode("|", $COLUMN_FORMAT);
						$a_column_map = (array) null; $a_column_sel = (array) null; $a_column_width = (array) null; $a_column_align = (array) null;
						$a_head_text1 = (array) null;	$a_head_width1 = (array) null; $a_head_align1 = (array) null; $a_col_function1 = (array) null; $a_COLUMN_FORMAT1 = (array) null;
						for($ii=0; $ii < $num_zone; $ii++) {
							$a_head_text1[] = $head_text;
							$a_head_width1[] = $head_width;
							$a_head_align1[] = $head_align;
							$a_col_function1[] = $col_function;
//							$a_column_map[] = $a_format[0];
							$a_column_sel[] = $a_format[1];
							$a_column_width[] = $a_format[2];
							$a_column_align[] = $a_format[3];
						}
						$head_text1 = implode(",,",$a_head_text1);
						$head_width1 = implode(",1,",$a_head_width1);
						$head_align1 = implode(",C,",$a_head_align1);
						$col_function1 = implode(",,",$a_col_function1);
						$t_column_sel1 = implode(",1,",$a_column_sel);
						$arr_column_sel = explode(",", $t_column_sel1);
						$t_column_width1 = implode(",1,",$a_column_width);
						$arr_column_width = explode(",", $t_column_width1);
						$t_column_align1 = implode(",C,",$a_column_align);
						$arr_column_align = explode(",", $t_column_align1);
						$a_column_map1 = (array) null;
						for($iii = 0; $iii < count($arr_column_sel); $iii++) {
							$a_column_map1[] = $iii;
						}
						$arr_column_map = $a_column_map1;
						$COLUMN_FORMAT1 = implode(",",$arr_column_map)."|".implode(",",$arr_column_sel)."|".implode(",",$arr_column_width)."|".implode(",",$arr_column_align);
//						echo "$HISTORY_NAME--text:$head_text1, width:$head_width1, col:$col_function1, format:$COLUMN_FORMAT1<br>";
						$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT1, $col_function1);
						if (!$result) echo "****** error ****** on open table for $table<br>";

						$arr_data = (array) null;
 						$data_align = (array) null;
						$max_rec = 0;
						for($ii=0; $ii < $num_zone; $ii++) {
							$PER_ID = $arr_selected_list[$ii];

							if($DPISDB=="odbc"){
								$cmd = "  select		b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME
												  from			(
																		(
																			(
																				PER_EDUCATE a
																				left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
																			) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
																		) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
																	) left join PER_COUNTRY e on (d.CT_CODE=e.CT_CODE)
												  where		a.PER_ID=$PER_ID
												  order by	a.EDU_SEQ
											   ";
							}elseif($DPISDB=="oci8"){
								$cmd = "  select		b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME
												  from			PER_EDUCATE a, PER_EDUCNAME b, PER_EDUCMAJOR c, PER_INSTITUTE d, PER_COUNTRY e
												  where		a.PER_ID=$PER_ID and a.EN_CODE=b.EN_CODE(+) and a.EM_CODE=c.EM_CODE(+) and a.INS_CODE=d.INS_CODE(+) and d.CT_CODE=e.CT_CODE(+)
												  order by	a.EDU_SEQ
											   ";
							}elseif($DPISDB=="mysql"){
								$cmd = "  select		b.EN_NAME, c.EM_NAME, d.INS_NAME, e.CT_NAME
												  from			(
																		(
																			(
																				PER_EDUCATE a
																				left join PER_EDUCNAME b on (a.EN_CODE=b.EN_CODE)
																			) left join PER_EDUCMAJOR c on (a.EM_CODE=c.EM_CODE)
																		) left join PER_INSTITUTE d on (a.INS_CODE=d.INS_CODE)
																	) left join PER_COUNTRY e on (d.CT_CODE=e.CT_CODE)
												  where		a.PER_ID=$PER_ID
												  order by	a.EDU_SEQ
											   ";
							} // end if
							
							$count_data = $db_dpis->send_cmd($cmd);
//							echo "$ii--$cmd ($count_data)<br>";
							if ($count_data > $max_rec) $max_rec = $count_data;

							if ($count_data) {
								$data_count = 0;
								while ($data = $db_dpis->get_array()) {
									$EN_NAME = trim($data[EN_NAME]);
									$EM_NAME = trim($data[EM_NAME]);
									$INS_NAME = trim($data[INS_NAME]);
									$INS_COUNTRY = trim($data[CT_NAME]);

									$arr_data[$data_count][] = $EN_NAME;
									$arr_data[$data_count][] = $EM_NAME;
									$arr_data[$data_count][] = $INS_NAME;
									$arr_data[$data_count][] = $INS_COUNTRY;
			
									$data_align[$data_count][] = "L";
									$data_align[$data_count][] = "L";
									$data_align[$data_count][] = "L";
									$data_align[$data_count][] = "L";
									if ($ii < $num_zone-1) {
										$arr_data[$data_count][] = "";
										$data_align[$data_count][] = "C";
									}
									$data_count++;
								} // end while 
							} // end if ($count_data)
						} // end for $ii

						if ($max_rec > 0) {
							for($kk = 0; $kk < $max_rec; $kk++) {
//								echo "$HISTORY_NAME -- data[$kk] : ".implode(",",$arr_data[$kk])."<br>";
								$result = $pdf->add_data_tab($arr_data[$kk], 7, "TRHBL", $data_align[$kk], "", "14", "", "000000", "");
								if (!$result) echo "****** error ****** add data to table at record count = 5 <br>";
							}
						} else {
							$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "TRBL", "C", "", "14", "b", 0, 0);
							if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
						}
									
						$pdf->close_tab("");
						break;
					case "TRAINING" :
						if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();

						$pdf->SetFont($font,'b',14);
						$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

						$pdf->Cell(285,7,"",0,1,"C");
						$pdf->Cell(285,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("3. การอบรม/สัมมนา/ดูงาน"):"3. การอบรม/สัมมนา/ดูงาน"),0,1,"C");

						$head_text = implode(",", $heading_text[$HISTORY_NAME]);	// ไม่พิมพ์ head
						$head_width = implode(",", $heading_width[$HISTORY_NAME]);
						$head_align = implode(",", $heading_align[$HISTORY_NAME]);
						$col_function = implode(",", $column_function[$HISTORY_NAME]);
						$COLUMN_FORMAT = do_COLUMN_FORMAT($heading_text[$HISTORY_NAME], $heading_width[$HISTORY_NAME], $heading_align[$HISTORY_NAME]);
						$a_format = explode("|", $COLUMN_FORMAT);
						$a_column_map = (array) null; $a_column_sel = (array) null; $a_column_width = (array) null; $a_column_align = (array) null;
						$a_head_text1 = (array) null;	$a_head_width1 = (array) null; $a_head_align1 = (array) null; $a_col_function1 = (array) null; $a_COLUMN_FORMAT1 = (array) null;
						for($ii=0; $ii < $num_zone; $ii++) {
							$a_head_text1[] = $head_text;
							$a_head_width1[] = $head_width;
							$a_head_align1[] = $head_align;
							$a_col_function1[] = $col_function;
//							$a_column_map[] = $a_format[0];
							$a_column_sel[] = $a_format[1];
							$a_column_width[] = $a_format[2];
							$a_column_align[] = $a_format[3];
						}
						$head_text1 = implode(",,",$a_head_text1);
						$head_width1 = implode(",1,",$a_head_width1);
						$head_align1 = implode(",C,",$a_head_align1);
						$col_function1 = implode(",,",$a_col_function1);
						$t_column_sel1 = implode(",1,",$a_column_sel);
						$arr_column_sel = explode(",", $t_column_sel1);
						$t_column_width1 = implode(",1,",$a_column_width);
						$arr_column_width = explode(",", $t_column_width1);
						$t_column_align1 = implode(",C,",$a_column_align);
						$arr_column_align = explode(",", $t_column_align1);
						$a_column_map1 = (array) null;
						for($iii = 0; $iii < count($arr_column_sel); $iii++) {
							$a_column_map1[] = $iii;
						}
						$arr_column_map = $a_column_map1;
						$COLUMN_FORMAT1 = implode(",",$arr_column_map)."|".implode(",",$arr_column_sel)."|".implode(",",$arr_column_width)."|".implode(",",$arr_column_align);
//						echo "$HISTORY_NAME--text:$head_text1, width:$head_width1, col:$col_function1, format:$COLUMN_FORMAT1<br>";
						$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT1, $col_function1);
						if (!$result) echo "****** error ****** on open table for $table<br>";

						$arr_data = (array) null;
 						$data_align = (array) null;
						$max_rec = 0;
						for($ii=0; $ii < $num_zone; $ii++) {
							$PER_ID = $arr_selected_list[$ii];

							if($DPISDB=="odbc"){
								$cmd = "  select		a.TRN_STARTDATE, b.TR_NAME
												  from			(
																		PER_TRAINING a
																		left join PER_TRAIN b on (a.TR_CODE=b.TR_CODE)
																	)
												  where		a.PER_ID=$PER_ID
												  order by	a.TRN_STARTDATE, a.TRN_ENDDATE
											   ";
							}elseif($DPISDB=="oci8"){
								$cmd = "  select		a.TRN_STARTDATE, b.TR_NAME
												  from			PER_TRAINING a, PER_TRAIN b
												  where		a.PER_ID=$PER_ID and a.TR_CODE=b.TR_CODE(+)
												  order by	a.TRN_STARTDATE, a.TRN_ENDDATE
											   ";
							}elseif($DPISDB=="mysql"){
								$cmd = "  select		a.TRN_STARTDATE, b.TR_NAME
												  from			(
																		PER_TRAINING a
																		left join PER_TRAIN b on (a.TR_CODE=b.TR_CODE)
																	)
												  where		a.PER_ID=$PER_ID
												  order by	a.TRN_STARTDATE, a.TRN_ENDDATE
											   ";
							} // end if
							
							$count_data = $db_dpis->send_cmd($cmd);
						//	$db_dpis->show_error();
//							echo "$ii--$cmd ($count_data)<br>";
							if ($count_data > $max_rec) $max_rec = $count_data;

							if ($count_data) {
								$data_count = 0;
								while ($data = $db_dpis->get_array()) {
									$TRN_STARTDATE = show_date_format($data[TRN_STARTDATE], $DATE_DISPLAY);
									$TR_NAME = trim($data[TR_NAME]);

									$arr_data[$data_count][] = $TRN_STARTDATE;
									$arr_data[$data_count][] = $TR_NAME;
			
									$data_align[$data_count][] = "C";
									$data_align[$data_count][] = "L";
									if ($ii < $num_zone-1) {
										$arr_data[$data_count][] = "";
										$data_align[$data_count][] = "C";
									}
									$data_count++;
                                } // end while
							} // end if
						} // end for $ii

						if ($max_rec > 0) {
							for($kk = 0; $kk < $max_rec; $kk++) {
//								echo "$HISTORY_NAME -- data[$kk] : ".implode(",",$arr_data[$kk])."<br>";
								$result = $pdf->add_data_tab($arr_data[$kk], 7, "TRHBL", $data_align[$kk], "", "14", "", "000000", "");
								if (!$result) echo "****** error ****** add data to table at record count = 5 <br>";
							}
						} else {
							$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "TRBL", "C", "", "14", "b", 0, 0);
							if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
						}

						$pdf->close_tab("");
						break;
					case "SERVICEHIS" :
						if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();

						$pdf->SetFont($font,'b',14);
						$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

						$pdf->Cell(285,7,"",0,1,"C");
						$pdf->Cell(285,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("4. ราชการพิเศษ"):"4. ราชการพิเศษ"),0,1,"C");

						$head_text = implode(",", $heading_text[$HISTORY_NAME]);	// ไม่พิมพ์ head
						$head_width = implode(",", $heading_width[$HISTORY_NAME]);
						$head_align = implode(",", $heading_align[$HISTORY_NAME]);
						$col_function = implode(",", $column_function[$HISTORY_NAME]);
						$COLUMN_FORMAT = do_COLUMN_FORMAT($heading_text[$HISTORY_NAME], $heading_width[$HISTORY_NAME], $heading_align[$HISTORY_NAME]);
						$a_format = explode("|", $COLUMN_FORMAT);
						$a_column_map = (array) null; $a_column_sel = (array) null; $a_column_width = (array) null; $a_column_align = (array) null;
						$a_head_text1 = (array) null;	$a_head_width1 = (array) null; $a_head_align1 = (array) null; $a_col_function1 = (array) null; $a_COLUMN_FORMAT1 = (array) null;
						for($ii=0; $ii < $num_zone; $ii++) {
							$a_head_text1[] = $head_text;
							$a_head_width1[] = $head_width;
							$a_head_align1[] = $head_align;
							$a_col_function1[] = $col_function;
//							$a_column_map[] = $a_format[0];
							$a_column_sel[] = $a_format[1];
							$a_column_width[] = $a_format[2];
							$a_column_align[] = $a_format[3];
						}
						$head_text1 = implode(",,",$a_head_text1);
						$head_width1 = implode(",1,",$a_head_width1);
						$head_align1 = implode(",C,",$a_head_align1);
						$col_function1 = implode(",,",$a_col_function1);
						$t_column_sel1 = implode(",1,",$a_column_sel);
						$arr_column_sel = explode(",", $t_column_sel1);
						$t_column_width1 = implode(",1,",$a_column_width);
						$arr_column_width = explode(",", $t_column_width1);
						$t_column_align1 = implode(",C,",$a_column_align);
						$arr_column_align = explode(",", $t_column_align1);
						$a_column_map1 = (array) null;
						for($iii = 0; $iii < count($arr_column_sel); $iii++) {
							$a_column_map1[] = $iii;
						}
						$arr_column_map = $a_column_map1;
						$COLUMN_FORMAT1 = implode(",",$arr_column_map)."|".implode(",",$arr_column_sel)."|".implode(",",$arr_column_width)."|".implode(",",$arr_column_align);
//						echo "$HISTORY_NAME--text:$head_text1, width:$head_width1, col:$col_function1, format:$COLUMN_FORMAT1<br>";
						$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT1, $col_function1);
						if (!$result) echo "****** error ****** on open table for $table<br>";

						$arr_data = (array) null;
 						$data_align = (array) null;
						$max_rec = 0;
						for($ii=0; $ii < $num_zone; $ii++) {
							$PER_ID = $arr_selected_list[$ii];

							if($DPISDB=="odbc"){
								$cmd = "  select		a.SRH_STARTDATE, b.SRT_NAME
												  from			(
																		PER_SERVICEHIS a
																		inner join PER_SERVICETITLE b on (a.SRT_CODE=b.SRT_CODE)
																	)
												  where		a.PER_ID=$PER_ID
												  order by	a.SRH_STARTDATE, a.SRH_ENDDATE
											   ";
							}elseif($DPISDB=="oci8"){
								$cmd = "  select		a.SRH_STARTDATE, b.SRT_NAME
												  from			PER_SERVICEHIS a, PER_SERVICETITLE b
												  where		a.PER_ID=$PER_ID and a.SRT_CODE=b.SRT_CODE
												  order by	a.SRH_STARTDATE, a.SRH_ENDDATE
											   ";
							}elseif($DPISDB=="mysql"){
								$cmd = "  select		a.SRH_STARTDATE, b.SRT_NAME
												  from			(
																		PER_SERVICEHIS a
																		inner join PER_SERVICETITLE b on (a.SRT_CODE=b.SRT_CODE)
																	)
												  where		a.PER_ID=$PER_ID
												  order by	a.SRH_STARTDATE, a.SRH_ENDDATE
											   ";
							} // end if
							
							$count_data = $db_dpis->send_cmd($cmd);
						//	$db_dpis->show_error();
//							echo "$ii--$cmd ($count_data)<br>";
							if ($count_data > $max_rec) $max_rec = $count_data;

							if ($count_data) {
								$data_count = 0;
								while ($data = $db_dpis->get_array()) {
									$SRH_STARTDATE = show_date_format($data[SRH_STARTDATE], $DATE_DISPLAY);
									$SRT_NAME = trim($data[SRT_NAME]);

									$arr_data[$data_count][] = $SRH_STARTDATE;
									$arr_data[$data_count][] = $SRT_NAME;
			
									$data_align[$data_count][] = "C";
									$data_align[$data_count][] = "L";
									if ($ii < $num_zone-1) {
										$arr_data[$data_count][] = "";
										$data_align[$data_count][] = "C";
									}
									$data_count++;
                                } // end while
							} // end if
						} // end for $ii

						if ($max_rec > 0) {
							for($kk = 0; $kk < $max_rec; $kk++) {
//								echo "$HISTORY_NAME -- data[$kk] : ".implode(",",$arr_data[$kk])."<br>";
								$result = $pdf->add_data_tab($arr_data[$kk], 7, "TRHBL", $data_align[$kk], "", "14", "", "000000", "");
								if (!$result) echo "****** error ****** add data to table at record count = 5 <br>";
							}
						} else {
							$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "TRBL", "C", "", "14", "b", 0, 0);
							if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
						}

						$pdf->close_tab("");
						break;
					case "SPROM" :
						if(($pdf->h - $pdf->y - 10) < 30) $pdf->AddPage();

						$pdf->SetFont($font,'b',14);
						$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

						$pdf->Cell(285,7,"",0,1,"C");
						$pdf->Cell(285,7,(($NUMBER_DISPLAY==2)?convert2thaidigit("5. ความดีความชอบ"):"5. ความดีความชอบ"),0,1,"C");

						$head_text = implode(",", $heading_text[$HISTORY_NAME]);	// ไม่พิมพ์ head
						$head_width = implode(",", $heading_width[$HISTORY_NAME]);
						$head_align = implode(",", $heading_align[$HISTORY_NAME]);
						$col_function = implode(",", $column_function[$HISTORY_NAME]);
						$COLUMN_FORMAT = do_COLUMN_FORMAT($heading_text[$HISTORY_NAME], $heading_width[$HISTORY_NAME], $heading_align[$HISTORY_NAME]);
						$a_format = explode("|", $COLUMN_FORMAT);
						$a_column_map = (array) null; $a_column_sel = (array) null; $a_column_width = (array) null; $a_column_align = (array) null;
						$a_head_text1 = (array) null;	$a_head_width1 = (array) null; $a_head_align1 = (array) null; $a_col_function1 = (array) null; $a_COLUMN_FORMAT1 = (array) null;
						for($ii=0; $ii < $num_zone; $ii++) {
							$a_head_text1[] = $head_text;
							$a_head_width1[] = $head_width;
							$a_head_align1[] = $head_align;
							$a_col_function1[] = $col_function;
//							$a_column_map[] = $a_format[0];
							$a_column_sel[] = $a_format[1];
							$a_column_width[] = $a_format[2];
							$a_column_align[] = $a_format[3];
						}
						$head_text1 = implode(",,",$a_head_text1);
						$head_width1 = implode(",1,",$a_head_width1);
						$head_align1 = implode(",C,",$a_head_align1);
						$col_function1 = implode(",,",$a_col_function1);
						$t_column_sel1 = implode(",1,",$a_column_sel);
						$arr_column_sel = explode(",", $t_column_sel1);
						$t_column_width1 = implode(",1,",$a_column_width);
						$arr_column_width = explode(",", $t_column_width1);
						$t_column_align1 = implode(",C,",$a_column_align);
						$arr_column_align = explode(",", $t_column_align1);
						$a_column_map1 = (array) null;
						for($iii = 0; $iii < count($arr_column_sel); $iii++) {
							$a_column_map1[] = $iii;
						}
						$arr_column_map = $a_column_map1;
						$COLUMN_FORMAT1 = implode(",",$arr_column_map)."|".implode(",",$arr_column_sel)."|".implode(",",$arr_column_width)."|".implode(",",$arr_column_align);
//						echo "$HISTORY_NAME--text:$head_text1, width:$head_width1, col:$col_function1, format:$COLUMN_FORMAT1<br>";
						$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT1, $col_function1);
						if (!$result) echo "****** error ****** on open table for $table<br>";

						$arr_data = (array) null;
 						$data_align = (array) null;
						$max_rec = 0;
						for($ii=0; $ii < $num_zone; $ii++) {
							$PER_ID = $arr_selected_list[$ii];

							if($DPISDB=="odbc"){
								$cmd = "  select		a.REH_DATE, b.REW_NAME
												  from			(
																		PER_REWARDHIS a
																		inner join PER_REWARD b on (a.REW_CODE=b.REW_CODE)
																	)
												  where		a.PER_ID=$PER_ID
												  order by	a.REH_DATE
											   ";
							}elseif($DPISDB=="oci8"){
								$cmd = "  select		a.REH_DATE, b.REW_NAME
												  from			PER_REWARDHIS a, PER_REWARD b
												  where		a.PER_ID=$PER_ID and a.REW_CODE=b.REW_CODE
												  order by	a.REH_DATE
											   ";
							}elseif($DPISDB=="mysql"){
								$cmd = "  select		a.REH_DATE, b.REW_NAME
												  from			(
																		PER_REWARDHIS a
																		inner join PER_REWARD b on (a.REW_CODE=b.REW_CODE)
																	)
												  where		a.PER_ID=$PER_ID
												  order by	a.REH_DATE
											   ";
							} // end if
							
							$count_data = $db_dpis->send_cmd($cmd);
						//	$db_dpis->show_error();
//							echo "$ii--$cmd ($count_data)<br>";
							if ($count_data > $max_rec) $max_rec = $count_data;


							if ($count_data) {
								$data_count = 0;
								while ($data = $db_dpis->get_array()) {
									$REH_DATE = show_date_format($data[REH_DATE], $DATE_DISPLAY);
									$REW_NAME = trim($data[REW_NAME]);
									
									$arr_data[$data_count][] = $REH_DATE;
									$arr_data[$data_count][] = $REW_NAME;
			
									$data_align[$data_count][] = "C";
									$data_align[$data_count][] = "L";
									if ($ii < $num_zone-1) {
										$arr_data[$data_count][] = "";
										$data_align[$data_count][] = "C";
									}
									$data_count++;
                                } // end while
							} // end if
						} // end for $ii

						if ($max_rec > 0) {
							for($kk = 0; $kk < $max_rec; $kk++) {
//								echo "$HISTORY_NAME -- data[$kk] : ".implode(",",$arr_data[$kk])."<br>";
								$result = $pdf->add_data_tab($arr_data[$kk], 7, "TRHBL", $data_align[$kk], "", "14", "", "000000", "");
								if (!$result) echo "****** error ****** add data to table at record count = 5 <br>";
							}
						} else {
							$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "TRBL", "C", "", "14", "b", 0, 0);
							if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
						}

						$pdf->close_tab("");
						break;
		} // end switch
		if($data_count < $count_data) $pdf->AddPage();
	} // end for

	$pdf->close();
	$pdf->Output();
	
	function 	do_COLUMN_FORMAT($heading_text, $heading_width, $data_align) {
		$total_head_width = 0;
		for($iii=0; $iii < count($heading_width); $iii++) 	$total_head_width += (int)$heading_width[$iii];
	
		$arr_column_map = (array) null;
		$arr_column_sel = (array) null;
		for($i=0; $i < count($heading_text); $i++) {
			$arr_column_map[] = $i;		// link index ของ head 
			$arr_column_sel[] = 1;			// 1=แสดง	0=ไม่แสดง   ถ้าไม่มีการปรับแต่งรายงาน ก็เป็นแสดงหมด
		}
		$arr_column_width = $heading_width;	// ความกว้าง
		$arr_column_align = $data_align;		// align
		$COLUMN_FORMAT = implode(",",$arr_column_map)."|".implode(",",$arr_column_sel)."|".implode(",",$arr_column_width)."|".implode(",",$arr_column_align);
		
		return $COLUMN_FORMAT;
	}
?>