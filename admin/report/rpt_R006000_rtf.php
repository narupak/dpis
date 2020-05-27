<?
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require("../../RTF/rtf_class.php");

	ini_set("max_execution_time", $max_execution_time);
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if (!$PER_ID) 	$PER_ID = $_GET[PER_ID];
	if (!$PER_ID) 	$PER_ID = $_POST[PER_ID];

	if (!$SAH_KF_YEAR) 	$SAH_KF_YEAR = $_GET[SAH_KF_YEAR];
	if (!$SAH_KF_YEAR) 	$SAH_KF_YEAR = $_POST[SAH_KF_YEAR];
	if ($SAH_KF_YEAR) 		$search_budget_year = $SAH_KF_YEAR;
	if (!$SAH_KF_CYCLE) 	$SAH_KF_CYCLE = $_GET[SAH_KF_CYCLE];
	if (!$SAH_KF_CYCLE) 	$SAH_KF_CYCLE = $_POST[SAH_KF_CYCLE];
	if ($SAH_KF_CYCLE) 		$search_kf_cycle = $SAH_KF_CYCLE;
//	echo "search_budget_year=$search_budget_year ,  search_kf_cycle=$search_kf_cycle<br>";

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
//	echo "NUMBER_DISPLAY=".$NUMBER_DISPLAY."<br>";
	$mgt_code = "";
	if($search_per_type == 1 || $search_per_type == 5){
		$position_table = "PER_POSITION";
		$position_join = "a.POS_ID=b.POS_ID";
		$line_table = "PER_LINE";
		$line_code = "b.PL_CODE";
		$line_name = "PL_NAME";
		$position_no_name = "b.POS_NO_NAME";
		$position_no = "b.POS_NO";
		$mgt_code = ", PM_CODE";
	}elseif($search_per_type == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "a.POEM_ID=b.POEM_ID";
		$line_table = "PER_POS_NAME";
		$line_code = "b.PN_CODE";
		$line_name = "PN_NAME";	
		$position_no_name = "b.POEM_NO_NAME";
		$position_no = "b.POEM_NO";
	}elseif($search_per_type == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "a.POEMS_ID=b.POEMS_ID";
		$line_table = "PER_EMPSER_POS_NAME";
		$line_code = "b.EP_CODE";
		$line_name = "EP_NAME";	
		$position_no_name = "b.POEMS_NO_NAME";
		$position_no = "b.POEMS_NO";
	}elseif($search_per_type == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "a.POT_ID=b.POT_ID";
		$line_table = "PER_TEMP_POS_NAME";
		$line_code = "b.TP_CODE";
		$line_name = "TP_NAME";	
		$position_no_name = "b.POT_NO_NAME";
		$position_no = "b.POT_NO";
	} // end if

	$KF_CYCLE = $search_kf_cycle;
	if(trim($search_budget_year)){ 
		if($KF_CYCLE == 1){
			$START_DATE = ($search_budget_year - 543) . '-04-01';
			$KF_START_DATE = '1 ต.ค. ' . ($search_budget_year - 1);
			$KF_END_DATE = '31 มี.ค. ' . $search_budget_year;
			$SIGN_START_DATE1 = (($search_budget_year -1) - 543) . '-10-01';
			$SIGN_END_DATE1 = ($search_budget_year - 543) . '-03-31';
		}elseif($KF_CYCLE == 2){
			$START_DATE = ($search_budget_year - 542) . '-10-01';
			$KF_START_DATE = '1 เม.ย. ' . $search_budget_year;
			$KF_END_DATE = '30 ก.ย. ' . $search_budget_year;
			$SIGN_START_DATE1 = ($search_budget_year - 543) . '-04-01';
			$SIGN_END_DATE1 = ($search_budget_year - 543) . '-09-30';
		} // end if
	} // end if

	if(in_array("PER_ORG", $list_type)){
		if($select_org_structure==1){	//สำหรับมอบหมายงาน
			if(trim($search_org_ass_id)){	$search_org_id=trim($search_org_ass_id);		}	
			if(trim($search_org_ass_id_1)){	$search_org_id_1=trim($search_org_ass_id_1);		}
			if(trim($search_org_ass_id_2)){	$search_org_id_2=trim($search_org_ass_id_2);		}
		}
		if(trim($search_org_id)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID = $search_org_id)";
			else if($select_org_structure==1) $arr_search_condition[] = "(a.ORG_ID = $search_org_id)";
		} // end if
		if(trim($search_org_id_1)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_1 = $search_org_id_1)";
			else if($select_org_structure==1) $arr_search_condition[] = "(a.ORG_ID_1 = $search_org_id_1)"; 
		} // end if
		if(trim($search_org_id_2)){ 
			if($select_org_structure==0) $arr_search_condition[] = "(b.ORG_ID_2 = $search_org_id_2)";
			else if($select_org_structure==1) $arr_search_condition[] = "(a.ORG_ID_2 = $search_org_id_2)";
		} // end if
	}else{
		if($DEPARTMENT_ID){
			$arr_search_condition[] = "(a.DEPARTMENT_ID = $DEPARTMENT_ID)";
		}elseif($MINISTRY_ID){
			$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
			if($select_org_structure==1) $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
			$db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()) $arr_org_ref[] = $data[ORG_ID];

			$arr_search_condition[] = "(a.DEPARTMENT_ID in (". implode($arr_org_ref, ",") ."))";
		}elseif($PROVINCE_CODE){
			$PROVINCE_CODE = trim($PROVINCE_CODE);
			$arr_search_condition[] = "(trim(b.PV_CODE) = '$PROVINCE_CODE')";
		} // end if
	} // end if
	if(count($arr_search_condition)) $search_condition = " and ". implode(" and ", $arr_search_condition);
	
//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();
	
	$fname= "rpt_R006000_rtf.rtf";

	if (!$font) $font = "AngsanaUPC";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='P';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	
	$RTF->set_table_font($font, 18);
	$RTF->color("0");	// 0=BLACK

	$company_name = "";
	$report_title = "";
	$report_code = "R006000";
	$RTF->set_report_code($report_code);
	$RTF->set_report_title($report_title);
	$RTF->set_company_name($company_name);
	
	//#### ดึงลายเซ็น ####//
	function getPIC_SIGN($PER_ID,$PER_CARDNO){
		global $db_dpis , $db_dpis2;
	
		$PIC_SIGN="";
		//หารูปที่เป็นลายเซ็น
		$cmd = "	select 		*
									  from 		PER_PERSONALPIC
									  where 		PER_ID=$PER_ID and PER_CARDNO = '$PER_CARDNO' and  PIC_SIGN=1
									  order by 	PER_PICSEQ ";
		$count_pic_sign=$db_dpis2->send_cmd($cmd);
		if($count_pic_sign>0){	
		$data2 = $db_dpis2->get_array();
		$TMP_PIC_SEQ = $data2[PER_PICSEQ];
		$current_list .= ((trim($current_list))?",":"") . $TMP_PIC_SEQ;
		$T_PIC_SEQ = substr("000",0,3-strlen("$TMP_PIC_SEQ"))."$TMP_PIC_SEQ";
		$TMP_SERVER = $data2[PIC_SERVER_ID];
		$TMP_PIC_SIGN= $data2[PIC_SIGN];
		
		if ($TMP_SERVER) {
			$cmd1 = " SELECT * FROM OTH_SERVER WHERE SERVER_ID=$TMP_SERVER ";
			$db_dpis2->send_cmd($cmd1);
			$data2 = $db_dpis2->get_array();
			$tmp_SERVER_NAME = trim($data2[SERVER_NAME]);
			$tmp_ftp_server = trim($data2[FTP_SERVER]);
			$tmp_ftp_username = trim($data2[FTP_USERNAME]);
			$tmp_ftp_password = trim($data2[FTP_PASSWORD]);
			$tmp_main_path = trim($data2[MAIN_PATH]);
			$tmp_http_server = trim($data2[HTTP_SERVER]);
		} else {
			$TMP_SERVER = 0;
			$tmp_SERVER_NAME = "";
			$tmp_ftp_server = "";
			$tmp_ftp_username = "";
			$tmp_ftp_password = "";
			$tmp_main_path = "";
			$tmp_http_server = "";
		}
		$SIGN_NAME = "";
		if($TMP_PIC_SIGN==1){ $SIGN_NAME = "SIGN"; }
		if (trim($PER_ID) && trim($PER_CARDNO) != "NULL") {
			$TMP_PIC_NAME = $data2[PER_PICPATH].$PER_CARDNO."-".$SIGN_NAME.$T_PIC_SEQ.".jpg".($tmp_SERVER_NAME ? " [".$tmp_SERVER_NAME."]" : "");
			//$TMP_SHOW_PIC = ($tmp_http_server ? $tmp_http_server."/" : "").$data[PER_PICPATH].$PER_CARDNO."-".$SIGN_NAME.$T_PIC_SEQ.".jpg";
		} else {
			$TMP_PIC_NAME = $data2[PER_PICPATH].$data2[PER_GENNAME]."-".$SIGN_NAME.$T_PIC_SEQ.".jpg".($tmp_SERVER_NAME ? " [".$tmp_SERVER_NAME."]" : "");
			//$TMP_SHOW_PIC = ($tmp_http_server ? $tmp_http_server."/" : "").$data[PER_PICPATH].$PER_CARDNO."-".$SIGN_NAME.$T_PIC_SEQ.".jpg";
		}
		if(file_exists("../".$TMP_PIC_NAME)){
			$PIC_SIGN = "../".$TMP_PIC_NAME;		//	if($PER_CARDNO && $TMP_PIC_NAME)		$PIC_SIGN = "../../attachments/".$PER_CARDNO."/PER_SIGN/".$TMP_PIC_NAME;
		}
		} //end count	
	return $PIC_SIGN;
	}
	
	if ($PER_ID) {
		$cond_per_id = "and a.PER_ID = $PER_ID";
	} else {
		$cond_per_id = "";
	}
	
	$cmd = " select		a.PN_CODE, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, $position_no_name as POS_NO_NAME, $position_no as POS_NO, $line_code as PL_CODE, 
										b.ORG_ID, SAH_SALARY_MIDPOINT, SAH_PERCENT_UP, SAH_SALARY_UP, SAH_SALARY, SAH_EFFECTIVEDATE,
										SAH_SALARY_EXTRA, SAH_DOCNO, SAH_DOCDATE, SAH_REMARK, c.MOV_CODE, a.DEPARTMENT_ID $mgt_code, SAH_OLD_SALARY, SAH_POSITION
					  from			PER_PERSONAL a, $position_table b, PER_SALARYHIS c
					  where		$position_join and a.PER_ID=c.PER_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $cond_per_id and 
										SAH_KF_YEAR = '$search_budget_year' and SAH_KF_CYCLE = $KF_CYCLE
					  $search_condition
					  order by a.PER_NAME, a.PER_SURNAME ";	
	$count_data = $db_dpis->send_cmd($cmd);
 	//  $db_dpis->show_error();
   
   //echo $cmd;
	if($count_data){
		$data_count = 0;
		while ($data = $db_dpis->get_array()) {
			$MOV_CODE = trim($data[MOV_CODE]);
			$cmd = " select MOV_SUB_TYPE from PER_MOVMENT where trim(MOV_CODE)='$MOV_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$MOV_SUB_TYPE = trim($data2[MOV_SUB_TYPE]);

			if (substr($MOV_SUB_TYPE,0,1)=="4") {
				$data_count++;

				$PN_CODE = trim($data[PN_CODE]);
				$LEVEL_NO = trim($data[LEVEL_NO]);
				$POS_NO = trim($data[POS_NO_NAME]).' '.trim($data[POS_NO]);
				$PL_CODE = trim($data[PL_CODE]);
				$PM_CODE = trim($data[PM_CODE]);
				$ORG_ID = $data[ORG_ID];
				$SAH_SALARY_MIDPOINT = $data[SAH_SALARY_MIDPOINT]?number_format($data[SAH_SALARY_MIDPOINT]):" ";
				$SAH_PERCENT_UP = $data[SAH_PERCENT_UP]?number_format($data[SAH_PERCENT_UP],4):" ";
				$SAH_SALARY_UP = $data[SAH_SALARY_UP]?number_format($data[SAH_SALARY_UP]):" ";
				$SAH_SALARY = $data[SAH_SALARY]?number_format($data[SAH_SALARY]):" ";
				$SAH_SALARY_EXTRA = $data[SAH_SALARY_EXTRA]?number_format($data[SAH_SALARY_EXTRA],2):" ";
				$PER_SALARY = $data[SAH_SALARY] - $data[SAH_SALARY_UP];
				$PER_SALARY = number_format($PER_SALARY);
				$SAH_DOCNO = trim($data[SAH_DOCNO]);
				$SAH_DOCDATE = show_date_format(trim($data[SAH_DOCDATE]),$DATE_DISPLAY);
				if ($search_sah_docdate) $SAH_DOCDATE = show_date_format(save_date($search_sah_docdate),$DATE_DISPLAY);
				$SAH_REMARK = trim($data[SAH_REMARK]);
				$SIGN_DATE = trim($data[SAH_DOCDATE]);
				$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];
				$SAH_EFFECTIVEDATE = $data[SAH_EFFECTIVEDATE];
				$arr_temp = explode("-", $SAH_EFFECTIVEDATE);
				if ($arr_temp[1]=="04") $KF_CYCLE = 1;
				elseif ($arr_temp[1]=="10") $KF_CYCLE = 2;
				if($KF_CYCLE == 1){
					$KF_START_DATE = show_date_format(($arr_temp[0] - 1)."-10-01",$DATE_DISPLAY);
					$KF_END_DATE = show_date_format($arr_temp[0]."-03-31",$DATE_DISPLAY);
				}elseif($KF_CYCLE == 2){
					$KF_START_DATE = show_date_format($arr_temp[0]."-04-01",$DATE_DISPLAY);
					$KF_END_DATE = show_date_format($arr_temp[0]."-09-30",$DATE_DISPLAY);
				} // end if
				$SAH_OLD_SALARY = $data[SAH_OLD_SALARY]+0;
				$SAH_POSITION = trim($data[SAH_POSITION]);

				$cmd = "select PN_NAME from PER_PRENAME where PN_CODE = '$PN_CODE'";
				$db_dpis2->send_cmd($cmd);
				$data2  = $db_dpis2->get_array();
				$PN_NAME = $data2[PN_NAME];
				$PER_NAME = "$PN_NAME$data[PER_NAME] $data[PER_SURNAME]";
		
				$cmd = "select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where LEVEL_NO = '$LEVEL_NO'";
				$db_dpis2->send_cmd($cmd);
				$data2  = $db_dpis2->get_array();
				$LEVEL_NAME = $data2[LEVEL_NAME];
				$POSITION_LEVEL = $data2[POSITION_LEVEL];
				if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
		
				$cmd = "	select PL_NAME from PER_LINE where	PL_CODE='$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$PL_NAME = trim($data2[PL_NAME]). $POSITION_LEVEL;

				$cmd = "	select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
				if($select_org_structure==1)	 $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$ORG_NAME = trim($data2[ORG_NAME]);
		
				$cmd = " select ORG_ID_REF, ORG_NAME  from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
				if($select_org_structure==1)	 $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$DEPARTMENT_NAME = $data2[ORG_NAME];
		
				$MINISTRY_ID = $data2[ORG_ID_REF];
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
				if($select_org_structure==1)	 $cmd = str_replace("PER_ORG", "PER_ORG_ASS", $cmd);
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$MINISTRY_NAME = $data2[ORG_NAME];

				if ($PM_CODE) {
					$cmd = " 	select PM_NAME from PER_MGT	where PM_CODE='$PM_CODE'  ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PM_NAME = trim($data2[PM_NAME]);
					if ($PM_NAME=="ผู้ว่าราชการจังหวัด" || $PM_NAME=="รองผู้ว่าราชการจังหวัด" || $PM_NAME=="ปลัดจังหวัด" || 
						$PM_NAME=="ผู้อำนวยการ" || $PM_NAME=="ผู้อำนวยการกอง" || $PM_NAME=="ผู้อำนวยการศูนย์" || 
						$PM_NAME=="ผู้อำนวยการสำนัก" || $PM_NAME=="ผู้อำนวยการสถาบัน" || $PM_NAME=="หัวหน้าสำนักงานจังหวัด") {
						$PM_NAME .= $ORG_NAME;
						$PM_NAME = str_replace("จังหวัดจังหวัด", "จังหวัด", $PM_NAME); 
						$PM_NAME = str_replace("กองกอง", "กอง", $PM_NAME); 
						$PM_NAME = str_replace("ศูนย์ศูนย์", "ศูนย์", $PM_NAME); 
						$PM_NAME = str_replace("สำนักสำนัก", "สำนัก", $PM_NAME); 
						$PM_NAME = str_replace("สถาบันสถาบัน", "สถาบัน", $PM_NAME); 
						$PM_NAME = str_replace("สำนักงานจังหวัดสำนักงานจังหวัด", "สำนักงานจังหวัด", $PM_NAME); 
					} elseif ($PM_NAME=="นายอำเภอ") {
						$PM_NAME .= $ORG_NAME_1;
						$PM_NAME = str_replace("อำเภอที่ทำการปกครองอำเภอ", "อำเภอ", $PM_NAME); 
					}
					if($PM_NAME) $PL_NAME = trim($PM_NAME);
				}
				if ($SAH_POSITION) $PL_NAME = $SAH_POSITION;

				$RTF->set_font($font, 18);
				$RTF->color("0");	// 0='BLACK'
	//			$pdf->SetFont($font,'',18);
	//			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

	//			$this_y = $pdf->y;
	//			$this_x = $pdf->x;
	//			if($img_file){
	//				$image_x = $pdf->x;		$image_y = $pdf->y;		$image_w = 30;			$image_h = 35;
	//				$pdf->Image($img_file, $image_x, $image_y, $image_w, $image_h);
	//				$RTF->cellImage(($img_file ? $img_file : ""), "$new_ratio", "35", "center", "8", "LTR");
	//			} // end if

	//			$pdf->x = $this_x+35;
	//			$pdf->y = $this_y;
	//			$pdf->SetFont($font,'',18);
	//			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	//			$pdf->Cell(35 ,28," ",0,1,"L");
				$RTF->open_line();
				$RTF->cell("", "100", "left", "8", "");
				$RTF->close_line();

				$RTF->open_line();
				$RTF->set_font($font,14);
				$RTF->color("0");	// 0=DARKGRAY
				$RTF->cell("ชื่อ-นามสกุล :  ", "20", "left", "8", "");
				$RTF->cell($PER_NAME, "80", "left", "8", "");
				$RTF->close_line();
	///			$pdf->x = $this_x+35;
	//			$pdf->Cell(35,7,"ชื่อ-นามสกุล :  ",0,0,"L");
	//			$pdf->Cell(35,7,$PER_NAME,0,1,"L");

				$RTF->open_line();
				$RTF->set_font($font,14);
				$RTF->color("0");	// 0=DARKGRAY
				$RTF->cell("ตำแหน่ง :  ", "20", "left", "8", "");
				$RTF->cell($PL_NAME, "80", "left", "8", "");
				$RTF->close_line();
	//			$pdf->x = $this_x+35;
	//			$pdf->Cell(35, 7, "ตำแหน่ง :  ", 0, 0, "L");
	//			$pdf->Cell(35, 7, $PL_NAME, 0, 1, "L");

				$RTF->open_line();
				$RTF->set_font($font,14);
				$RTF->color("0");	// 0=DARKGRAY
				$RTF->cell("สังกัด :  ", "20", "left", "8", "");
				$RTF->cell($ORG_NAME, "80", "left", "8", "");
				$RTF->close_line();
	//			$pdf->x = $this_x+35;
	//			$pdf->Cell(35 , 7, "สังกัด :  ",0,0,"L");
	//			$pdf->Cell(35 , 7, $ORG_NAME,0,1,"L");
				
				$RTF->open_line();
				$RTF->set_font($font,14);
				$RTF->color("0");	// 0=DARKGRAY
				$RTF->cell("  ", "20", "left", "8", "");
				$RTF->cell($DEPARTMENT_NAME, "80", "left", "8", "");
				$RTF->close_line();
	//			$pdf->x = $this_x+35;
	//			$pdf->Cell(35 , 7," ",0,0,"L");
	//			$pdf->Cell(35 , 7,$DEPARTMENT_NAME,0,1,"L");

				$RTF->open_line();
				$RTF->cell("", "100", "left", "8", "");
				$RTF->close_line();

				$RTF->open_line();
				$RTF->cell("", "100", "left", "8", "");
				$RTF->close_line();

				$RTF->open_line();
				$RTF->set_font($font,14);
				$RTF->color("0");	// 0=DARKGRAY
	//			$RTF->cell("  ", "10", "left", "8", "");
				$RTF->cell("--------------------------------------------------------------------------------------------------------------------", "100", "left", "8", "");
	//			$RTF->cell("  ", "10", "left", "8", "");
				$RTF->close_line();
	//			$pdf->x = $this_x+35;
	//			$pdf->Cell(35 , 14," ",0,1,"L");

	//			$pdf->x = $this_x+35;
	//			$pdf->Cell(35 ,7,"--------------------------------------------------------------------------------------------------------------------",0,1,"L");

				$RTF->open_line();
				$RTF->set_font($font,22);
				$RTF->color("1");	// 0=DARKGRAY
				$RTF->cell("หนังสือแจ้งผลการเลื่อนเงินเดือน", "100", "center", "8", "");
				$RTF->close_line();
	//			$pdf->x = $this_x+35;
	//			$pdf->SetFont($font,'',22);
	//			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	//			$pdf->Cell(140 ,7,"หนังสือแจ้งผลการเลื่อนเงินเดือน",0,1,"C");

	//			$pdf->SetFont($font,'',18);
	//			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
				
				$RTF->open_line();
				$RTF->set_font($font,18);
				$RTF->color("1");	// 0=DARKGRAY
				$RTF->cell("รอบการประเมิน :  ", "20", "left", "8", "");
				$RTF->cell("รอบที่  ".($NUMBER_DISPLAY==2 ? convert2thaidigit($KF_CYCLE) : $KF_CYCLE), "20", "left", "8", "");
				$RTF->cell(($NUMBER_DISPLAY==2 ? convert2thaidigit("$KF_START_DATE"."  ถึง :  $KF_END_DATE") : "$KF_START_DATE"."  ถึง :  $KF_END_DATE"), "60", "left", "8", "");
				$RTF->close_line();
	//			$pdf->x = $this_x+35;
	//			$pdf->Cell(35 ,7,"รอบการประเมิน :  ",0,0,"L");
	//			$pdf->Cell(35 ,7,"รอบที่  ".($NUMBER_DISPLAY==2 ? convert2thaidigit($KF_CYCLE) : $KF_CYCLE),0,0,"L");
	//			$pdf->Cell(40 ,7,($NUMBER_DISPLAY==2 ? convert2thaidigit("$KF_START_DATE"."  ถึง :  $KF_END_DATE") : "$KF_START_DATE"."  ถึง :  $KF_END_DATE"),0,1,"L");

				$RTF->open_line();
				$RTF->set_font($font,18);
				$RTF->color("1");	// 0=DARKGRAY
				$RTF->cell("คำสั่งเลขที่ :  ", "20", "left", "8", "");
				$RTF->cell(($NUMBER_DISPLAY==2 ? convert2thaidigit($SAH_DOCNO) : $SAH_DOCNO), "20", "left", "8", "");
				$RTF->cell("ลงวันที่  ".($NUMBER_DISPLAY==2 ? convert2thaidigit($SAH_DOCDATE) : $SAH_DOCDATE), "60", "left", "8", "");
				$RTF->close_line();
	//			$pdf->x = $this_x+35;
	//			$pdf->Cell(35 ,7,"คำสั่งเลขที่ :  ",0,0,"L");
	//			$pdf->Cell(35 ,7,($NUMBER_DISPLAY==2 ? convert2thaidigit($SAH_DOCNO) : $SAH_DOCNO),0,0,"L");
	//			$pdf->Cell(40 ,7,"ลงวันที่  ".($NUMBER_DISPLAY==2 ? convert2thaidigit($SAH_DOCDATE) : $SAH_DOCDATE),0,1,"L");

				$RTF->open_line();
				$RTF->set_font($font,18);
				$RTF->color("1");	// 0=DARKGRAY
				$RTF->cell("ชื่อ-นามสกุล :  ", "20", "left", "8", "");
				$RTF->cell(($NUMBER_DISPLAY==2  ? convert2thaidigit($PER_NAME) : $PER_NAME), "80", "left", "8", "");
				$RTF->close_line();
	//			$pdf->x = $this_x+35;
	//			$pdf->Cell(35,7,"ชื่อ-นามสกุล :  ",0,0,"L");
	//			$pdf->Cell(35,7,($NUMBER_DISPLAY==2  ? convert2thaidigit($PER_NAME) : $PER_NAME),0,1,"L");

				$RTF->open_line();
				$RTF->set_font($font,18);
				$RTF->color("1");	// 0=DARKGRAY
				$RTF->cell("ตำแหน่ง :  ", "20", "left", "8", "");
				$RTF->cell(($NUMBER_DISPLAY==2 ? convert2thaidigit($PL_NAME) : $PL_NAME), "80", "left", "8", "");
				$RTF->close_line();
	//			$pdf->x = $this_x+35;
	//			$pdf->Cell(35, 7, "ตำแหน่ง :  ", 0, 0, "L");
	//			$pdf->Cell(35, 7, ($NUMBER_DISPLAY==2 ? convert2thaidigit($PL_NAME) : $PL_NAME), 0, 1, "L");

				$RTF->open_line();
				$RTF->set_font($font,18);
				$RTF->color("1");	// 0=DARKGRAY
				$RTF->cell("เลขที่ตำแหน่ง :  ", "20", "left", "8", "");
				$RTF->cell(($NUMBER_DISPLAY==2 ? convert2thaidigit($POS_NO) : $POS_NO), "80", "left", "8", "");
				$RTF->close_line();
	//			$pdf->x = $this_x+35;
	//			$pdf->Cell(35 ,7,"เลขที่ตำแหน่ง :  ",0,0,"L");
	//			$pdf->Cell(35 ,7,($NUMBER_DISPLAY==2 ? convert2thaidigit($POS_NO) : $POS_NO),0,1,"L");

				$RTF->open_line();
				$RTF->set_font($font,18);
				$RTF->color("1");	// 0=DARKGRAY
				$RTF->cell("สังกัด :  ", "20", "left", "8", "");
				$RTF->cell(($NUMBER_DISPLAY==2 ? convert2thaidigit($ORG_NAME) : $ORG_NAME), "80", "left", "8", "");
				$RTF->close_line();
	//			$pdf->x = $this_x+35;
	//			$pdf->Cell(35 , 7, "สังกัด :  ",0,0,"L");
	//			$pdf->Cell(35 , 7, ($NUMBER_DISPLAY==2 ? convert2thaidigit($ORG_NAME) : $ORG_NAME),0,1,"L");
				
				$RTF->open_line();
				$RTF->set_font($font,18);
				$RTF->color("1");	// 0=DARKGRAY
				$RTF->cell("  ", "20", "left", "8", "");
				$RTF->cell(($NUMBER_DISPLAY==2 ? convert2thaidigit($DEPARTMENT_NAME) : $DEPARTMENT_NAME), "80", "left", "8", "");
				$RTF->close_line();
	//			$pdf->x = $this_x+35;
	//			$pdf->Cell(35 , 7," ",0,0,"L");
	//			$pdf->Cell(35 , 7,($NUMBER_DISPLAY==2 ? convert2thaidigit($DEPARTMENT_NAME) : $DEPARTMENT_NAME),0,1,"L");

				$RTF->open_line();
				$RTF->set_font($font,18);
				$RTF->color("1");	// 0=DARKGRAY
				$RTF->cell("เงินเดือนเดิม (บาท) :  ", "20", "left", "8", "");
				$RTF->cell(($NUMBER_DISPLAY==2 ? convert2thaidigit($PER_SALARY) : $PER_SALARY), "80", "left", "8", "");
				$RTF->close_line();
	//			$pdf->x = $this_x+35;
	//			$pdf->Cell(35 ,7,"เงินเดือนเดิม (บาท) :  ",0,0,"L");
	//			$pdf->Cell(35 ,7,($NUMBER_DISPLAY==2 ? convert2thaidigit($PER_SALARY) : $PER_SALARY),0,1,"L");

				$RTF->open_line();
				$RTF->set_font($font,18);
				$RTF->color("0");	// 0=DARKGRAY
				if ($SAH_SALARY_UP > 0 || $SAH_SALARY_EXTRA > 0 || $data[SAH_SALARY] > $data[SAH_OLD_SALARY])
					$RTF->cellImage("../images/checkbox_check.jpg", "$new_ratio", "10", "center", "8", "");
	//				$pdf->Image("../images/checkbox_check.jpg", 41, ($pdf->y - 6), 4, 4,"jpg");
				else
					$RTF->cellImage("../images/checkbox_blank.jpg", "$new_ratio", "10", "center", "8", "");
	//				$pdf->Image("../images/checkbox_blank.jpg", 41, ($pdf->y - 6), 4, 4,"jpg");
				$RTF->cell("ได้รับการเลื่อนเงินเดือน", "90", "left", "8", "");
				$RTF->close_line();
	//			$pdf->x = $this_x+35;
	//			$pdf->Cell(35 ,7,"        ได้รับการเลื่อนเงินเดือน",0,1,"L");
	//			if ($MOV_CODE != "21375")
	//				$pdf->Image("../images/checkbox_check.jpg", 41, ($pdf->y - 6), 4, 4,"jpg");
	//			else
	//				$pdf->Image("../images/checkbox_blank.jpg", 41, ($pdf->y - 6), 4, 4,"jpg");
				
				$RTF->open_line();
				$RTF->cell("  ", "20", "left", "8", "");
				$RTF->close_line();
	//			$pdf->x = $this_x+35;
	//			$pdf->Cell(35 ,7," ",0,1,"L");

				$RTF->open_line();
				$RTF->set_font($font,18);
				$RTF->color("0");	// 0=DARKGRAY
				$RTF->cell("ฐานในการคำนวณ", "20", "center", "16", "LTR");
				$RTF->cell("ร้อยละ", "20", "center", "16", "LTR");
				$RTF->cell("จำนวนเงินที่ได้เพิ่ม", "40", "center", "16", "LTRB");
				$RTF->cell("เงินเดือนที่ได้รับ", "20", "center", "16", "LTR");
				$RTF->close_line();
	//			$pdf->SetTextColor(hexdec("00"),hexdec("66"),hexdec("CC"));
	//			$pdf->SetFillColor(hexdec("EE"),hexdec("EE"),hexdec("FF"));
	//			$pdf->x = $this_x+35;
	//			$pdf->Cell(35 ,7,"ฐานในการคำนวณ",'LTR',0,'C',1);
	//			$pdf->Cell(25 ,7,"ร้อยละ",'LTR',0,'C',1);
	//			$pdf->Cell(60 ,7,"จำนวนเงินที่ได้เพิ่ม",'LTRB',0,'C',1);
	//			$pdf->Cell(30 ,7,"เงินเดือนที่ได้รับ",'LTR',1,'C',1);
		
				$RTF->open_line();
				$RTF->set_font($font,18);
				$RTF->color("1");	// 0=DARKGRAY
				$RTF->cell("(บาท)", "20", "center", "16", "LBR");
				$RTF->cell("ที่ได้เลื่อน", "20", "center", "16", "LBR");
				$RTF->cell("เงินเดือน\\parที่ได้เลื่อน (บาท)", "20", "center", "16", "LRB");
				$RTF->cell("เงินค่าตอบแทน\\parพิเศษ (บาท)", "20", "center", "16", "LRB");
				$RTF->cell("(บาท)", "20", "center", "16", "LBR");
				$RTF->close_line();
	//			$pdf->x = $this_x+35;
	//			$pdf->Cell(35 ,7,"(บาท)",'LBR',0,'C',1);
	//			$pdf->Cell(25 ,7,"ที่ได้เลื่อน",'LBR',0,'C',1);
	//			$pdf->Cell(30 ,7,"เงินเดือน",'LTBR',0,'C',1);
	//			$pdf->Cell(30 ,7,"เงินค่าตอบแทน",'LTBR',0,'C',1);
	//			$pdf->Cell(30 ,7,"(บาท)",'LBR',1,'C',1);

	//			$pdf->x = $this_x+35;
	//			$pdf->Cell(35 ,7,"",'LBR',0,'C',1);
	//			$pdf->Cell(25 ,7,"",'LBR',0,'C',1);
	//			$pdf->Cell(30 ,7,"ที่ได้เลื่อน (บาท)",'LBR',0,'C',1);
	//			$pdf->Cell(30 ,7,"พิเศษ (บาท)",'LBR',0,'C',1);
	//			$pdf->Cell(30 ,7,"",'LBR',1,'C',1);

				$RTF->open_line();
				$RTF->set_font($font,18);
				$RTF->color("1");	// 0=DARKGRAY
				$RTF->cell(($NUMBER_DISPLAY==2 ? convert2thaidigit($SAH_SALARY_MIDPOINT) : $SAH_SALARY_MIDPOINT), "20", "center", "8", "LBR");
				$RTF->cell(($NUMBER_DISPLAY==2 ? convert2thaidigit($SAH_PERCENT_UP) : $SAH_PERCENT_UP), "20", "center", "8", "LBR");
				$RTF->cell(($NUMBER_DISPLAY==2 ? convert2thaidigit($SAH_SALARY_UP) : $SAH_SALARY_UP), "20", "center", "8", "LTRB");
				$RTF->cell(($NUMBER_DISPLAY==2 ? convert2thaidigit($SAH_SALARY_EXTRA) : $SAH_SALARY_EXTRA), "20", "center", "8", "LTRB");
				$RTF->cell(($NUMBER_DISPLAY==2 ? convert2thaidigit($SAH_SALARY) : $SAH_SALARY), "20", "center", "8", "LBR");
				$RTF->close_line();
	//			$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
	//			$pdf->x = $this_x+35;
	//			$pdf->Cell(35 ,7,($NUMBER_DISPLAY==2 ? convert2thaidigit($SAH_SALARY_MIDPOINT) : $SAH_SALARY_MIDPOINT),'LBR',0,'C');
	//			$pdf->Cell(25 ,7,($NUMBER_DISPLAY==2 ? convert2thaidigit($SAH_PERCENT_UP) : $SAH_PERCENT_UP),'LBR',0,'C');
	//			$pdf->Cell(30 ,7,($NUMBER_DISPLAY==2 ? convert2thaidigit($SAH_SALARY_UP) : $SAH_SALARY_UP),'LBR',0,'C');
	//			$pdf->Cell(30 ,7,($NUMBER_DISPLAY==2 ? convert2thaidigit($SAH_SALARY_EXTRA) : $SAH_SALARY_EXTRA),'LBR',0,'C');
	//			$pdf->Cell(30 ,7,($NUMBER_DISPLAY==2 ? convert2thaidigit($SAH_SALARY) : $SAH_SALARY),'LBR',1,'C');

				$RTF->open_line();
				$RTF->set_font($font,18);
				$RTF->color("1");	// 0=DARKGRAY
				$RTF->cell(" ", "20", "center", "8", "");
				$RTF->close_line();
	//			$pdf->x = $this_x+35;
	//			$pdf->Cell(35 ,7," ",0,1,"L");

				$RTF->open_line();
				$RTF->set_font($font,18);
				$RTF->color("0");	// 0=DARKGRAY
				if ($SAH_SALARY_UP > 0 || $SAH_SALARY_EXTRA > 0 || $data[SAH_SALARY] > $data[SAH_OLD_SALARY])
					$RTF->cellImage("../images/checkbox_blank.jpg", "$new_ratio", "10", "center", "8", "");
	//				$pdf->Image("../images/checkbox_blank.jpg", 41, ($pdf->y - 6), 4, 4,"jpg");
				else
					$RTF->cellImage("../images/checkbox_check.jpg", "$new_ratio", "10", "center", "8", "");
	//				$pdf->Image("../images/checkbox_check.jpg", 41, ($pdf->y - 6), 4, 4,"jpg");
				$RTF->cell("กรณีที่ไม่ได้รับการเลื่อนเงินเดือน เนื่องจาก (เหตุผล)......................................................", "90", "left", "8", "");
				$RTF->close_line();
	//			$pdf->x = $this_x+35;
	//			$pdf->Cell(35 ,7,"        กรณีที่ไม่ได้รับการเลื่อนเงินเดือน เนื่องจาก (เหตุผล)......................................................",0,1,"L");
	//			if ($MOV_CODE == "21375")
	//				$pdf->Image("../images/checkbox_check.jpg", 41, ($pdf->y - 6), 4, 4,"jpg");
	//			else
	//				$pdf->Image("../images/checkbox_blank.jpg", 41, ($pdf->y - 6), 4, 4,"jpg");
				
				$RTF->open_line();
				$RTF->set_font($font,18);
				$RTF->color("1");	// 0=DARKGRAY
				if ($SAH_REMARK)
					$RTF->cell(($NUMBER_DISPLAY==2 ? convert2thaidigit($SAH_REMARK) : $SAH_REMARK), "100", "center", "8", "");
				else
					$RTF->cell("   ...................................................................................................................................................", "100", "leftr", "8", "");
				$RTF->close_line();
	//			$pdf->x = $this_x+35;
	//			if ($SAH_REMARK)
	//				$pdf->Cell(35 , 7, ($NUMBER_DISPLAY==2 ? convert2thaidigit($SAH_REMARK) : $SAH_REMARK),0,1,"L");
	//			else
	//				$pdf->Cell(35 ,7,".................................................................................................................................................",0,1,"L");

				$RTF->open_line();
				$RTF->set_font($font,18);
				$RTF->color("0");	// 0=DARKGRAY
				$RTF->cell("   ...................................................................................................................................................", "100", "left", "8", "");
				$RTF->close_line();
	//			$pdf->x = $this_x+35;
	//			$pdf->Cell(35 ,7,".................................................................................................................................................",0,1,"L");

	/*
				$pdf->x = $this_x+35;
				$pdf->Cell(35 ,12,"",0,1,"L");
				$pdf->Image("../images/sign_up_salary$RPT_N.jpg", 110, ($pdf->y - 6), 85, 38,"jpg");
	*/	
				
			// หาผู้มีหน้าที่จ่ายเงิน
			//print("<pre>"); print_r($SESS_E_SIGN); print("</pre>");		// 1-> แบบประเมินผลการปฏิบัติราชการ   2->ใบลา   3->สลิปเงินเดือน   4->หนังสือแจ้งผลการเลื่อนเงินเดือน  5->หนังสือรับรอง 		
			$PIC_SIGN=$SIGN_NAME=$SIGN_POSITION=$PIC_SIGN_PER=""; 
			$SIGN_TYPE = 2;	 // type หนังสือแจ้งผลการเลื่อนเงินเดือน
			//หาว่าใครเป็นคน ผู้มีหน้าที่จ่ายเงิน  NVL
			$cmd = " select PER_ID, SIGN_NAME, SIGN_POSITION from PER_SIGN 
							where DEPARTMENT_ID = $TMP_DEPARTMENT_ID and SIGN_TYPE = '$SIGN_TYPE' and SIGN_PER_TYPE = $search_per_type and ((SIGN_ENDDATE IS NOT NULL and ('$SIGN_DATE' between SIGN_STARTDATE and SIGN_ENDDATE or '$SIGN_DATE' between SIGN_STARTDATE and SIGN_ENDDATE)) or (SIGN_ENDDATE IS NULL and '$SIGN_DATE' >= SIGN_STARTDATE))
							order by SIGN_STARTDATE desc, SIGN_ENDDATE desc ";	
			$count_exist=$db_dpis2->send_cmd($cmd);
			//echo "$count_exist -> $cmd";
			if($count_exist>0){
				$data2 = $db_dpis2->get_array();
				$SIGN_PER_ID = $data2[PER_ID];
				$SIGN_NAME  = trim($data2[SIGN_NAME]);
				$SIGN_POSITION  = trim($data2[SIGN_POSITION]);
				if($SIGN_PER_ID && $SESS_E_SIGN[4]==1){ // ใช้รูปแบบของลายเซ็นอิเล็คทรอนิกส์
					// หา PER_CARDNO
					$cmd = " select 	PER_CARDNO from PER_PERSONAL where PER_ID=$SIGN_PER_ID ";
					$db_dpis2->send_cmd($cmd);	
					$data2 = $db_dpis2->get_array();
					$SIGN_PER_CARDNO = trim($data2[PER_CARDNO]);

					//echo "$cmd ->$SIGN_FULL_NAME ".$SESS_E_SIGN[4];
					$PIC_SIGN_PER = getPIC_SIGN($SIGN_PER_ID,$SIGN_PER_CARDNO);
				}
			} 

			$RTF->open_line();
			$RTF->set_font($font,18);
			$RTF->color("0");	// 0=DARKGRAY
			$RTF->cell("", "20", "left", "8", "");
			$RTF->cell("", "80", "center", "8", "");
			$RTF->close_line();
			if($PIC_SIGN_PER){  // มีรูปลายเซ็น
				$RTF->open_line();
				$RTF->set_font($font,18);
				$RTF->color("0");	// 0=DARKGRAY
				$RTF->cell("", "60", "left", "8", "");
				$RTF->cell("ลงชื่อ", "8", "left", "8", "");
//				$RTF->cellImage($PIC_SIGN_PER, "$new_ratio", "25", "center", "8", "");
				$RTF->cellImagexy($PIC_SIGN_PER, 35, 8, 30, "center", 0);
				$RTF->close_line();
	//			$pdf->Cell(100, 7, "", 0, 0, 'L', 0);		//space
	//			$pdf->Cell(30, 15, "ลงชื่อ", 0, 0, 'L', 0);
	//			$pdf->Image($PIC_SIGN_PER,($pdf->x-15), ($pdf->y+1), 40, 15,"jpg");	// Original size = wxh (60x15)
				//$save_x = $pdf->x;		$save_y = $pdf->y;
				//$pdf->x += 5;			$pdf->y -= 15;
	//			$pdf->Cell(10, 15, "", 0, 1, 'L', 0);
			}else{
				$RTF->open_line();
				$RTF->set_font($font,18);
				$RTF->color("0");	// 0=DARKGRAY
				$RTF->cell("", "60", "left", "8", "");
				$RTF->cell("ลงชื่อ.......................................................", "40", "center", "8", "");
				$RTF->close_line();
	//			$pdf->Cell(100, 7, "", 0, 0, 'L', 0);		//space
	//			$pdf->Cell(30, 15, "ลงชื่อ.......................................................", 0, 0, 'L', 0);
	//			$pdf->Cell(50, 15, "", 0, 1, 'L', 0);
			}
			if ($SIGN_NAME){	
				$RTF->open_line();
				$RTF->set_font($font,18);
				$RTF->color("0");	// 0=DARKGRAY
				$RTF->cell("", "60", "left", "8", "");
				$RTF->cell("($SIGN_NAME)", "40", "center", "8", "");
				$RTF->close_line();
	//			$pdf->Cell(115,15, "", 0, 0, 'L', 0);
	//			$pdf->Cell(40,12,"($SIGN_NAME)",0,0,"C");
	//			$pdf->Cell(10, 7, "", 0, 1, 'L', 0);
			}else{
				$RTF->open_line();
				$RTF->set_font($font,18);
				$RTF->color("0");	// 0=DARKGRAY
				$RTF->cell("", "60", "left", "8", "");
				$RTF->cell("(.......................................................)", "40", "center", "8", "");
				$RTF->close_line();
	//			$pdf->Cell(115,15, "", 0, 0, 'L', 0);
	//			$pdf->Cell(40,12,"(.......................................................)",0,0,"C");
	//			$pdf->Cell(10, 7, "", 0, 1, 'L', 0);
			}
			if ($SIGN_POSITION){	
				$RTF->open_line();
				$RTF->set_font($font,18);
				$RTF->color("0");	// 0=DARKGRAY
				$RTF->cell("", "60", "left", "8", "");
				$RTF->cell("$SIGN_POSITION", "40", "center", "8", "");
				$RTF->close_line();
		//		$pdf->Cell(115,15, "", 0, 0, 'L', 0);
		//		$pdf->Cell(40,12,"$SIGN_POSITION",0,0,"C");
		//		$pdf->Cell(10,7, "", 0, 1, 'L', 0);
			}else{
				$RTF->open_line();
				$RTF->set_font($font,18);
				$RTF->color("0");	// 0=DARKGRAY
				$RTF->cell("", "60", "left", "8", "");
				$RTF->cell("ตำแหน่ง.....................................................", "40", "center", "8", "");
				$RTF->close_line();
	//			$pdf->Cell(100, 7, "", 0, 0, 'L', 0);		//space
	//			$pdf->Cell(30, 15, "ลงชื่อ.......................................................", 0, 0, 'L', 0);
	//			$pdf->Cell(50, 15, "", 0, 1, 'L', 0);
			}
			
			$RTF->open_line();
			$RTF->set_font($font,18);
			$RTF->color("0");	// 0=DARKGRAY
			$RTF->cell("", "60", "left", "8", "");
			$RTF->cell("ลงวันที่  ".($NUMBER_DISPLAY==2 ? convert2thaidigit($SAH_DOCDATE) : $SAH_DOCDATE), "40", "center", "8", "");
			$RTF->close_line();
	//		$pdf->Cell(115,15, "", 0, 0, 'L', 0);
	//		$pdf->Cell(40 ,12,"ลงวันที่  ".($NUMBER_DISPLAY==2 ? convert2thaidigit($SAH_DOCDATE) : $SAH_DOCDATE),0,0,"C");
	//		$pdf->Cell(10,7, "", 0, 1, 'L', 0);
				
				/***********
				$pdf->x = $this_x+35;
				$pdf->Cell(150 ,7,"ลงชื่อ .......................................................",0,1,"R");

				$pdf->x = $this_x+35;
				$pdf->Cell(150 ,7,"(ผู้บังคับบัญชาผู้มีอำนาจสั่งเลื่อนเงินเดือนหรือผู้ได้รับมอบหมาย)",0,1,"R");

				$pdf->x = $this_x+35;
				$pdf->Cell(150 ,7,"วันที่ .......................................................",0,1,"R");
				************/
	  
	//			if ($data_count < $count_data) 	$pdf->AddPage();
				$RTF->new_page();
				$RTF->paragraph();
			}
		} // end loop while $data
	}else{
		$RTF->set_table_font($font, 16);
		$RTF->color("0");	// 0=BLACK
		
		$RTF->open_line();
		$RTF->cell($RTF->bold(1)."********** ไม่มีข้อมูล **********".$RTF->bold(0), "$page_ch", "center", "8", "");
		$RTF->close_line();
//		$pdf->SetFont($font,'b',16);
//		$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));
//		$pdf->Cell(200,10,"********** ไม่มีข้อมูล **********",0,1,'C');
	} // end if

	$RTF->display($fname);

	ini_set("max_execution_time", 30);
?>