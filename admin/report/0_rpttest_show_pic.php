<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
    
	if ($FLAG_RTF) {
		include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
		require("../../RTF/rtf_class.php");
	  } else{
		define('FPDF_FONTPATH','../../PDF/font/');
		include ("../../PDF/fpdf.php");
		include ("../../PDF/pdf_extends_DPIS.php");
	}

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	ini_set("max_execution_time", $max_execution_time);
	$report_title = trim($report_title);
	$report_code = "";
	$company_name = "";
	
//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	if ($FLAG_RTF) {
		$fname= "0_rpttest_show_pic.rtf";
	//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
		$paper_size="a4";
		$orientation='P';
		$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);
		$RTF->set_default_font($font, 14);
	//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

		$RTF->set_report_code($report_code);
		$RTF->set_report_title($report_title);
	} else {
		$unit="mm";
		$paper_size="A4";
		$lang_code="TH";
		$orientation='P';
		$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
		$pdf->Open();
		$pdf->SetMargins(5,5,5);
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetTextColor(0, 0, 0);
		$pdf->SetFont($font,'',14);
		$pdf->SetAutoPageBreak(true,10);
    } 

	$head_text1 = ",,";	// ไม่พิมพ์ head
	if ($FLAG_RTF) {
		$head_width1 = "30,40,30";
	} else {
		$head_width1 = "63,77,60";
	}
	$head_align1 = "C,C,C";
	$col_function = implode(",", $column_function[NOTPAID]);
//	$column_function = array((($NUMBER_DISPLAY==2)?"TNUM":"ENUM"),(($NUMBER_DISPLAY==2)?"TNUM":"ENUM"),(($NUMBER_DISPLAY==2)?"TNUM":"ENUM"));
//	$col_function = implode(",", $column_function);
	$COLUMN_FORMAT = do_COLUMN_FORMAT(explode(",",$head_text1), explode(",",$head_width1), explode(",",$head_align1));
//	echo "$HISTORY_NAME--text:$head_text1, width:$head_width1, align:$head_align1, col:$col_function, format:$COLUMN_FORMAT<br>";

	$img_file = "../../attachment/pic_personal/3609900687557-004.jpg";
	
	if ($FLAG_RTF) {
		$RTF->add_header("", 0, false);	// header default
		$RTF->add_footer("", 0, false);		// footer default
		
//		echo "$head_text1<br>";
		$tab_align = "center";
		$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, "", false, $tab_align);
	} else {
		$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function, true);
		if (!$result) echo "****** error ****** on open table for $table<br>";
	}
	
	$arr_data = (array) null;
	$arr_data[0] = "บรรทัด 1 column 1";
	$arr_data[1] = "<&&row&&><*img*".$img_file."*img*>";
	$arr_data[2] = "บรรทัด 1 column 2";

	$data_align = array("L","C","L");

	if ($FLAG_RTF) {
		$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
	} else {
		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "", $at_end_up);
	}
	if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

	$arr_data = (array) null;
	$arr_data[0] = "บรรทัด 2 column 1";
	$arr_data[1] = "<&&row&&><*img*".$img_file."*img*>";
	$arr_data[2] = "บรรทัด 2 column 2";

	$data_align = array("L","C","L");
	
	if ($FLAG_RTF) {
		$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
	} else {
		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "", $at_end_up);
	}
	if (!$result) echo "****** error ****** add data to table at record count = 2 <br>";

	$arr_data = (array) null;
	$arr_data[0] = "บรรทัด 3 column 1";
	$arr_data[1] = "<&&row&&><*img*".$img_file."*img*>";
	$arr_data[2] = "บรรทัด 3 column 2";

	$data_align = array("L","C","L");
	
	if ($FLAG_RTF) {
		$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
	} else {
		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "", $at_end_up);
	}
	if (!$result) echo "****** error ****** add data to table at record count = 4 <br>";

	$arr_data = (array) null;
	$arr_data[0] = "บรรทัด 4 column 1";
	$arr_data[1] = "<&&row&&><*img*".$img_file."*img*>";
	$arr_data[2] = "บรรทัด 4 column 2";

	$data_align = array("L","C","L");
	
	if ($FLAG_RTF) {
		$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
	} else {
		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "", $at_end_up);
	}
	if (!$result) echo "****** error ****** add data to table at record count = 5 <br>";

	$arr_data = (array) null;
	$arr_data[0] = "บรรทัด 5 column 1";
	$arr_data[1] = "<&&end&&><*img*".$img_file."*img*>";
	$arr_data[2] = "บรรทัด 5 column 2";

	$data_align = array("L","C","L");
	
	if ($FLAG_RTF) {
		$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
	} else {
		$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "", $at_end_up);
	}
	if (!$result) echo "****** error ****** add data to table at record count = 6 <br>";

	if ($FLAG_RTF) {
		$RTF->close_tab(); 
		$RTF->close_section(); 
	} else {
		$pdf->close_tab(""); 
	}

   	if ($FLAG_RTF) {
		$heading_width[0] = "5";
		$heading_width[1] = "10";
		$heading_width[2] = "10";
		$heading_width[3] = "10";
		$heading_width[4] = "10";
		$heading_width[5] = "5";
	}else {
		$heading_width[0] = "20";
		$heading_width[1] = "50";
		$heading_width[2] = "50";
		$heading_width[3] = "40";
		$heading_width[4] = "30";
		$heading_width[5] = "10";
	}

	$heading_text[0] = "รหัส";
	$heading_text[1] = "ชื่อ";
	$heading_text[2] = "สังกัด";
	$heading_text[3] = "ตำแหน่ง";
	$heading_text[4] = "รูป";
	$heading_text[5] = "check";
		
	$heading_align = array('C','C','C','C','C','C');

	$cmd = "	select			PER_ID, PER_NAME||' '||PER_SURNAME as PNAME, DEPARTMENT_ID||':'||ORG_ID as ORG, POS_ID
						from 		PER_PERSONAL
						where 	PER_ID>7000 and PER_ID<8000 order by PER_ID ";

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo "count_data=$count_data<br>";

	if($count_data){
		if ($FLAG_RTF){ 
			$company_name .= "ทดสอบภาพ";
			$RTF->set_company_name($company_name);
		}else{
			$pdf->Cell(array_sum($heading_width), 7, "ทดสอบภาพ", "", 1, 'L', 0);
		}
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		if ($FLAG_RTF) {
			$RTF->add_header("", 0, false);	// header default
			$RTF->add_footer("", 0, false);		// footer default
			
	//		echo "$head_text1<br>";
			$tab_align = "center";
			$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, "", false, $tab_align);
		} else {
		    $pdf->SetFont($font,'',14);
		    $pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
			$pdf->AutoPageBreak = false; 
		    $result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", 0);
		}
		if (!$result) echo "****** error ****** on open table for $table<br>";

		$data_count = 0;
		while($data = $db_dpis->get_array()){
			$data_count++;

			$PER_ID = trim($data[PER_ID]);
			$PER_NAME = trim($data[PNAME]);
			$PER_ORG = trim($data[ORG]);
			$POS_ID = trim($data[POS_ID]);

			$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID and PIC_SHOW='1' and (PIC_SIGN = 0 or PIC_SIGN is NULL)";
//			echo "IMG:$cmd<br>";
			$cnt = $db_dpis2->send_cmd($cmd);
			if ($cnt) {
				$data2 = $db_dpis2->get_array();
				$TMP_PER_CARDNO = trim($data2[PER_CARDNO]);
				$PER_GENNAME = trim($data2[PER_GENNAME]);
				$PIC_PATH = trim($data2[PER_PICPATH]);
				$PIC_SEQ = trim($data2[PER_PICSEQ]);
				$T_PIC_SEQ = substr("000",0,3-strlen("$PIC_SEQ"))."$PIC_SEQ";
				$PIC_SERVER_ID = trim($data2[PIC_SERVER_ID]);
				$PER_PICSAVEDATE = trim($data2[PER_PICSAVEDATE]);
				if($PIC_SERVER_ID > 0){
					if($PIC_SERVER_ID==99){		// $PIC_SERVER_ID 99 = ip จากตั้งค่าระบบ C06				 ใช้ \ 
						// หา # กรณี server อื่น เปลี่ยน # ให้เป็น \ เพื่อใช้ในการอัพโหลดรูป
						$PIC_PATH = $IMG_PATH_DISPLAY."#".$PIC_PATH;
						$PIC_PATH = str_replace("#","'",$PIC_PATH);
						$PIC_PATH = addslashes($PIC_PATH);
						$PIC_PATH = str_replace("'","",$PIC_PATH);

						$img_file = $PIC_PATH;
					}else{  // other server
						$cmd = " select * from OTH_SERVER where SERVER_ID=$PIC_SERVER_ID ";
						if ($db_dpis3->send_cmd($cmd)) { 
							$data3 = $db_dpis3->get_array();
							$PIC_SERVER_NAME = trim($data3[SERVER_NAME]);
							$ftp_server = trim($data3[FTP_SERVER]);
							$ftp_username = trim($data3[FTP_USERNAME]);
							$ftp_password = trim($data3[FTP_PASSWORD]);
							$main_path = trim($data3[MAIN_PATH]);
							$http_server = trim($data3[HTTP_SERVER]);
							if ($http_server) {
								//echo "1.".$http_server."/".$img_file."<br>";
								$fp = @fopen($http_server."/".$img_file, "r");
								if ($fp !== false) $img_file = $http_server."/".$img_file;
								else $img_file=$IMG_PATH."shadow.jpg";
								fclose($fp);
							} else {
//								echo "2.".$img_file."<br>";
								$img_file = file_exists("../".$img_file)?("../".$img_file):$IMG_PATH."shadow.jpg";
							}
						}
					}
				}else{ // localhost  $PIC_SERVER_ID == 0
					$img_file =  "../".$PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";		//$PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
				}
			}

			$arr_data = (array) null;
			$arr_data[] = "$PER_ID";
			$arr_data[] = "$PER_NAME";
			$arr_data[] = "$PER_ORG";
			$arr_data[] = "$POS_ID";
			if ($img_file) {
				if ($FLAG_RTF)
					$arr_data[] = "<*img*".$img_file.",15*img*>";	// ,ตัวเลขหลัง comma คือ image ratio
				else
					$arr_data[] = "<*img*".$img_file.",4*img*>";		// , ตัวเลขหลัง comma คือ จำนวนบรรทัดที่จะกำหนดให้ในแต่ละบรรทัด
			}
			if ($FLAG_RTF)
				$arr_data[] = "<*img*".(($PER_ID%3==0)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg")."*img*>";
			else
				$arr_data[] = "<*img*".(($PER_ID%3==0)?"../images/checkbox_check.jpg":"../images/checkbox_blank.jpg").",fixsize*img*>";

			$data_align = array("C", "L", "L", "L", "C", "C");
			if ($FLAG_RTF)
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			else
				$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "", "000000", "");
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

		} // end while
	}else{
		$arr_data = (array) null;
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";
		$arr_data[] = "<**1**>********** ไม่มีข้อมูล **********";

		$data_align = array("C", "C", "C", "C", "C", "C");
		  if ($FLAG_RTF)
	     	$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "b", "16", "", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		 else	
			$result = $pdf->add_data_tab($arr_data, 7, "TRHBL", $data_align, "b", "16", "", "000000", "");
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
	} // end if
	if ($FLAG_RTF) {
		$RTF->close_tab(); 
		$RTF->close_section(); 
		$RTF->display($fname);
	} else {
		$pdf->close_tab(""); 
		$pdf->close();
		$pdf->Output();	
	}

	ini_set("max_execution_time", 30);
	
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