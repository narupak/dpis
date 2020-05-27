<?
//	echo "1...org_parent=$org_parent, sel_org_id=$sel_org_id , sel_per_id=$sel_per_id , org_parent_ol_code=$org_parent_ol_code<br>";
	include("../../php_scripts/connect_database.php");
	if (!$FLAG_RTF) include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	if ($FLAG_RTF) {
		include ("rtf_setvar.php");	// กำหนดตัวแปรค่าสี set_of_colors
		require("../../RTF/rtf_class.php");
	} else	 {
		define('FPDF_FONTPATH','../../PDF/font/');
		include ("../../PDF/fpdf.php");
		include ("../../PDF/pdf_extends_DPIS.php"); 
	}

	ini_set("max_execution_time", $max_execution_time);

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

	$PIC_SHOW = '1';

//	echo "2...org_parent=$org_parent, sel_org_id=$sel_org_id , sel_per_id=$sel_per_id , org_parent_ol_code=$org_parent_ol_code<br>";

	$company_name = "";
	$report_title = "";
	$report_code = "Structure_by_Assign";

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	if ($FLAG_RTF) {

		$today = date("Y-m-d H:i:s");
		$dt = explode(" ",$today);
		$print_today =  show_date_format($dt[0],1);
		$print_time = $dt[1];
	//	echo "[$today] $print_today $print_time<br>";

		$fname= "rpt_structure_assignchart.rtf";
		
	//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
		$paper_size="a4";
		$orientation='P';
		$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

		$RTF->set_default_font($font, 14);
	//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

		$RTF->set_report_code($report_code);
		$RTF->set_report_title($report_title);
		$RTF->set_company_name($company_name);

	} else {
		$unit="mm";
		$paper_size="A4";
		$lang_code="TH";
		$orientation='P';

		$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align);
		
		$pdf->Open();
		$pdf->SetFont($font,'',14);
		$pdf->SetMargins(5,5,5);
		$pdf->AliasNbPages();
		$pdf->AddPage();
		$pdf->SetTextColor(0, 0, 0);
		
		$page_start_x = $pdf->x;			$page_start_y = $pdf->y;
	}

	//(ORG_ID ระดับเหนือขึ้นไปต้องไม่เป็น NULL แต่ ORG_ID ระดับต่ำลงมาต้องไม่มี (NULL)
	$where = " and a.PER_STATUS = 1 ";
	if ($BKK_FLAG==1) $where .= " and a.PER_TYPE = 1";
	$cmd_per = " select a.DEPARTMENT_ID,PER_ID , PER_NAME, PER_SURNAME,a.PN_CODE,a.POS_ID, a.POEM_ID, a.POEMS_ID, a.LEVEL_NO, f.POSITION_LEVEL, a.PER_TYPE, a.PER_STATUS, a.PER_CARDNO
								from 	((((	PER_PERSONAL a
									left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
								) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
								) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
								) left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
								) left  join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
						where a.PER_ID=". $sel_per_id." $where 
						order by a.PER_TYPE, f.LEVEL_SEQ_NO DESC, a.PER_SEQ_NO, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID ";	
	$count_data = $db_dpis->send_cmd($cmd_per);
//		echo "$cmd_per<br>";
//		$db_dpis->show_error();
	if($count_data){
		$i=0;
		while($data = $db_dpis->get_array()){
			$cntnext_per++;
			$i++;
			$PER_ID = trim($data[PER_ID]);
			if (!in_array($PER_ID, $per_list)) { // ถ้าไม่มี PER_ID นี้ ใน list ที่แสดงไปแล้วในโครงสร้างบุคคล ถึงแสดงได้
				$per_list[] = $PER_ID;
				$LEVEL_NO = trim($data[LEVEL_NO]);
				$LEVEL_NAME = trim($data[POSITION_LEVEL]);
				$PER_NAME = $data[PER_NAME];
				$PER_SURNAME = $data[PER_SURNAME];
				$FULLNAME = "$PER_NAME $PER_SURNAME";
				$PER_CARDNO = $data[PER_CARDNO];
				//-------------หารูปภาพที่ใช้แสดง
	//			$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID AND PIC_SHOW = '1' ";
				$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID order by PER_PICSAVEDATE asc ";
	//echo "IMG:$cmd<br>";
				$piccnt = $db_dpis2->send_cmd($cmd);
				if ($piccnt > 0) { 
					while ($data2 = $db_dpis2->get_array()) {
						$TMP_PER_CARDNO = trim($data2[PER_CARDNO]);
						$PER_GENNAME = trim($data2[PER_GENNAME]);
						$PIC_PATH = trim($data2[PER_PICPATH]);
						$PIC_SEQ = trim($data2[PER_PICSEQ]);
						$T_PIC_SEQ = substr("000",0,3-strlen("$PIC_SEQ"))."$PIC_SEQ";
						$PIC_SERVER_ID = trim($data2[PIC_SERVER_ID]);
						$PIC_SHOW = trim($data2[PIC_SHOW]);

						if ($PIC_SHOW == '1') {
							$img_file = $PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
							$arr_img[] = "../".$PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
							$arr_imgshow[] = 1;
							$PER_PICSAVEDATE = trim($data2[PER_PICSAVEDATE]);
						} else {
							$arr_img[] = "../".$PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
							$arr_imgsavedate[] = trim($data2[PER_PICSAVEDATE]);
							$arr_imgshow[] = 0;
						}
					} // end while loop
				} else {
					//$img_file="";
					$img_file=$IMG_PATH."shadow.jpg";
				}

				if ($PIC_SERVER_ID && $PIC_SERVER_ID > 0) {
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
					} else{
						$img_file = file_exists("../".$img_file)?("../".$img_file):$IMG_PATH."shadow.jpg";
					}
				} else{ 
					//$img_file = file_exists("../".$img_file)?("../".$img_file):$IMG_PATH."shadow.jpg";
					$img_file = file_exists($img_file)?($img_file):$IMG_PATH."shadow.jpg";
	//				echo "../".$img_file." @@@@ ".file_exists("../".$img_file);
				}
	//echo "img_file=$img_file // $PIC_SERVER_ID<br>";

				$PN_CODE = trim($data[PN_CODE]);
				if ($PN_CODE) {
					$cmd = "	select PN_NAME, PN_SHORTNAME from PER_PRENAME where PN_CODE='$PN_CODE'";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PN_NAME = $data2[PN_NAME];
					$PN_SHORTNAME = $data2[PN_SHORTNAME];
				}
				$PRENAME = ($PN_SHORTNAME)?$PN_SHORTNAME:$PN_NAME;
				$FULLNAME = $PRENAME.$FULLNAME;

				$PER_TYPE = $data[PER_TYPE];
				$PER_STATUS = $data[PER_STATUS];
				$POSEM_NO = "";
				$TMP_PL_NAME = "";
				$ORG_NAME = "";
				$ORG_NAME_REF = "";
				if ($PER_TYPE == 1) { // ข้าราชการ
					$POS_ID = $data[POS_ID];
					if ($POS_ID) {
						$cmd = " 	select		POS_NO_NAME, POS_NO, pl.PL_NAME, po.ORG_NAME, pp.PT_CODE, pt.PT_NAME, po.ORG_ID, po.ORG_ID_REF , pp.PM_CODE
										from		PER_POSITION pp, PER_LINE pl, PER_ORG po, PER_TYPE pt 
										where		pp.POS_ID=$POS_ID and pp.ORG_ID=po.ORG_ID and pp.PL_CODE=pl.PL_CODE and pp.PT_CODE=pt.PT_CODE ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$POSEM_NO = $data2[POS_NO_NAME].$data2[POS_NO];
						$PL_NAME = $data2[PL_NAME];
						$ORG_NAME = $data2[ORG_NAME];
						$ORG_ID = trim($data2[ORG_ID]);
						$ORG_ID_REF = trim($data2[ORG_ID_REF]);
						$PT_CODE = trim($data2[PT_CODE]);
						$PT_NAME = trim($data2[PT_NAME]);
						$PM_CODE = trim($data2[PM_CODE]);

						$cmd = " 	select PM_NAME from PER_MGT	where PM_CODE='$PM_CODE'  ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$PM_NAME = trim($data2[PM_NAME]);
						if ($RPT_N)
							$TMP_PL_NAME = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)? "$PL_NAME$LEVEL_NAME" : "") . (trim($PM_NAME) ?")":"");
						else
							$TMP_PL_NAME = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO) . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"") . (trim($PM_NAME) ?")":"");
					} // end if ($POS_ID) 
				} elseif ($PER_TYPE == 2) { // 
					$POEM_ID = $data[POEM_ID];
					if ($POEM_ID) {
						$cmd = " 	select		POEM_NO_NAME, POEM_NO, pl.PN_NAME, po.ORG_NAME, po.ORG_ID, po.ORG_ID_REF   
										from			PER_POS_EMP pp, PER_POS_NAME pl, PER_ORG po 
										where		pp.POEM_ID=$POEM_ID and pp.ORG_ID=po.ORG_ID and pp.PN_CODE=pl.PN_CODE  ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$POSEM_NO = trim($data2[POEM_NO_NAME]).trim($data2[POEM_NO]);
						$PL_NAME = trim($data2[PN_NAME]);
						$ORG_NAME = trim($data2[ORG_NAME]);
						$ORG_ID = trim($data2[ORG_ID]);
						$ORG_ID_REF = trim($data2[ORG_ID_REF]);
						$TMP_PL_NAME = (trim($PL_NAME))? "$PL_NAME$LEVEL_NAME" : "";	
					}
				} elseif ($PER_TYPE == 3) {
					$POEMS_ID = $data[POEMS_ID];
					if ($POEMS_ID) {
						$cmd = " 	select		POEMS_NO_NAME, POEMS_NO, pl.EP_NAME, po.ORG_NAME, po.ORG_ID, po.ORG_ID_REF   
										from			PER_POS_EMPSER pp, PER_EMPSER_POS_NAME pl, PER_ORG po 
										where		pp.POEMS_ID=$POEMS_ID and pp.ORG_ID=po.ORG_ID and pp.EP_CODE=pl.EP_CODE  ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$POSEM_NO = trim($data2[POEMS_NO_NAME]).trim($data2[POEMS_NO]);
						$PL_NAME = trim($data2[EP_NAME]);
						$ORG_NAME = trim($data2[ORG_NAME]);
						$ORG_ID = trim($data2[ORG_ID]);
						$ORG_ID_REF = trim($data2[ORG_ID_REF]);
						$TMP_PL_NAME = (trim($PL_NAME))? "$PL_NAME$LEVEL_NAME" : "";	
					}
				} elseif ($PER_TYPE == 4) { // 
					$POT_ID = $data[POT_ID];
					if ($POT_ID) {
						$cmd = " 	select		POT_NO_NAME, POT_NO, pl.TP_NAME, po.ORG_NAME, po.ORG_ID, po.ORG_ID_REF   
										from			PER_POS_TEMP pp, PER_TEMP_POS_NAME pl, PER_ORG po 
										where		pp.POT_ID=$POT_ID and pp.ORG_ID=po.ORG_ID and pp.TP_CODE=pl.TP_CODE  ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$POSEM_NO = trim($data2[POT_NO_NAME]).trim($data2[POT_NO]);
						$PL_NAME = trim($data2[TP_NAME]);
						$ORG_NAME = trim($data2[ORG_NAME]);
						$ORG_ID = trim($data2[ORG_ID]);
						$ORG_ID_REF = trim($data2[ORG_ID_REF]);
						$TMP_PL_NAME = (trim($PL_NAME))? "$PL_NAME$LEVEL_NAME" : "";	
					}
				} // end if PER_TYPE

				// หาต้นสังกัด ORG_ID_REF
				$cmd_org_ref = " select * from PER_ORG where ORG_ID = $ORG_ID";
				$db_dpis2->send_cmd($cmd_org_ref);
				if ($data2 = $db_dpis2->get_array()){
//					$ORG_ID_REF_NAME = $data2[ORG_NAME];
					while ($data2[ORG_ID_REF]!=$data2[ORG_ID]) {
						$cmd_org_ref = " select * from PER_ORG where ORG_ID = ".$data2[ORG_ID_REF]."";
						$db_dpis2->send_cmd($cmd_org_ref);
						if ($data2 = $db_dpis2->get_array()){
							if ($data2[ORG_ID_REF]!=$data2[ORG_ID]) {
//								$a_ORG_ID_REF_NAME[] = $data2[ORG_NAME];
								$ORG_ID_REF_NAME = $data2[ORG_NAME]." ".$ORG_ID_REF_NAME;
							}
						}
					}
				}
//				echo "	ต้นสังกัด :: ".implode(",",$a_ORG_ID_REF_NAME)."<br>";
//				echo "	ต้นสังกัด :: ".$ORG_ID_REF_NAME."<br>";
				$arr_content["L"][] = $ORG_ID_REF_NAME;
				$arr_content["R"][] = "";
				$arr_content[ldept][] = 0;
				$arr_content[rdept][] = -1;
				
//				$deptstr="$deptstr"."<img src=\"$img_file\" width=\"14\" height=\"24\" align=\"middle\" border=\"0\"></img>";
//				echo ">>".$ORG_NAME. "-".$FULLNAME."-".$PER_TYPE."-".$TMP_PL_NAME." [$deptstr] (img_file=$img_file)<br>";
				$arr_content["L"][] = $ORG_NAME. "^".$FULLNAME."^".$PER_TYPE."^".$TMP_PL_NAME."^".$img_file;
				$arr_content["R"][] = "";
				$arr_content[ldept][] = 1;
				$arr_content[rdept][] = -1;

				$lr_pos = "L";

					// ถ้ามีผู้ใต้บังตับบัญชา
					$cmd_sub = " select  a.DEPARTMENT_ID, PER_ID , PER_NAME, PER_SURNAME, a.PN_CODE, a.POS_ID, a.POEM_ID, a.POEMS_ID, 
												  a.LEVEL_NO, f.POSITION_LEVEL, a.PER_TYPE, a.PER_STATUS, a.PER_CARDNO
											from   ((((PER_PERSONAL a
															left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
															) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
															) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
															) left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
															) left  join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
										where a.PER_ID_ASS_REF=". $PER_ID." $where
										order by a.PER_TYPE, f.LEVEL_SEQ_NO DESC, a.PER_SEQ_NO, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID ";	
					$count_data2 = $db_dpis2->send_cmd($cmd_sub);
					if ($count_data2) {
						list_tree_per($PER_ID, 2);
					}
//					}	//end if($PER_ID != $TOP_PER_ID)
			} // end if (!in_array($PER_ID, $per_list))
		}//end while
		$SELECTED_PER_ID = implode(",",$per_list);
	} //end if($count_data)
//	echo "content :: ".implode(",",$arr_content["L"])."<br>";
//	echo ".................".implode(",",$arr_content["R"])."<br>";
	for($i=0; $i < count($arr_content["L"]); $i++) {
		if ($i==0) {
			$dept1 = 0; $dept2 = 0;
			if ($FLAG_RTF) {
				$RTF->new_line();
//				$RTF->add_text($RTF->bold(1) . (($NUMBER_DISPLAY==2)?convert2thaidigit($arr_content["L"][$i]):$arr_content["L"][$i]) . $RTF->bold(0) , "center");
				$RTF->set_font($font, 16);
				$RTF->set_table_font($font, 16);
	
				$RTF->open_line();	
				$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit($arr_content["L"][$i]):$arr_content["L"][$i]), "100", "center", "0");
				$RTF->close_line();
			} else {
				$border = "";
				$pdf->SetFont($fontb,'',14);
				$pdf->SetTextColor(hexdec("00"),hexdec("00"),hexdec("00"));

				$start_x = $pdf->lMargin;			$start_y = $pdf->tMargin;
				$page_w = $pdf->w;
				$center_x = $pdf->lMargin + $page_w/2;
				$pdf->x = $center_x - 25;  // (50 = 25/2)

				$line_start_y = $pdf->y+=10;				$line_start_x = $pdf->x -17;			$pdf->x-=5;
				$pdf->MultiCellThaiCut(50, 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($arr_content["L"][$i]):$arr_content["L"][$i]), $border, "C");
				$line_h = $pdf->y - $line_start_y;		$line_w = 75;	//$pdf->x - $line_start_x;
				$pdf->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ
				$pdf->Rect($line_start_x, $line_start_y, $line_w, $line_h);
//				echo "$i--pdf->Rect($line_start_x, $line_start_y, $line_w, $line_h)<br>";
				$point_x = $line_start_x + ($line_w / 2);  $point_y = $line_start_y + $line_h;
				$pdf->x = $center_x - 25;  // (50 = 25/2)
				$pdf->y += 14;
			}
		} else if ($i==1) {
			$ss = explode("^",$arr_content["L"][$i]);
			//	$ORG_NAME. "^".$FULLNAME."^".$PER_TYPE."^".$TMP_PL_NAME."^".$img_file;
			$oo = $ss[0]. "*enter*".$ss[1]."*enter*".$ss[3];
			$img = $ss[4];
			if ($FLAG_RTF) {
//				$RTF->new_line();
//				$RTF->new_line();
				$RTF->set_font($font, 16);
//				$RTF->add_image($ss[4], 10, "center");
//				$RTF->add_text($RTF->bold(1) . (($NUMBER_DISPLAY==2)?convert2thaidigit($oo):$oo) . $RTF->bold(0) , "center");
				$RTF->set_table_font($font, 16);

				$RTF->open_line();			
				$RTF->cellImage($img, 20, 15, "center", 0, "TLBR");
				$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit($oo):$oo), "30", "center", "0", "TLBR", "center");
				$RTF->close_line();
			} else {
				$max_y = 0;
				$w = 75;
				if (file_exists($img))  { 
					$pdf->x -= 17; // ครึ่งของ 25
					$line_start_y = $pdf->y;		$line_start_x = $pdf->x;
					$w = 50;
					$pdf->Image($img,$pdf->x,$pdf->y,20,25,'','');
					$pdf->x += 25;
					$max_y = $pdf->y;
				} else  {
					$pdf->x -= 17; // ครึ่งของ 25
					$line_start_y = $pdf->y;		$line_start_x = $pdf->x;
				}
				$pdf->MultiCellThaiCut($w, 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($oo):$oo), $border, "C");
				if ($pdf->y > $max_y) $max_y = $pdf->y;
				$line_h = $max_y - $line_start_y + 4;		$line_w = 75;	//$pdf->x - $line_start_x;
				$pdf->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ
				$pdf->Rect($line_start_x, $line_start_y, $line_w, $line_h);
				$point_x1 = $line_start_x + (75 / 2);  $point_y1 = $line_start_y;
				$pdf->SetDrawColor(hexdec("FF"),hexdec("00"),hexdec("00")); //สีแดง
				$pdf->Line($point_x, $point_y, $point_x1, $point_y1);
//				echo "$i--pdf->Rect($line_start_x, $line_start_y, $line_w, $line_h)<br>";
				$point_x = $point_x1;  $point_y = $line_start_y + $line_h;
				$pdf->x = $start_x;
				$pdf->y += 14;
			}
		} else {	// $i >= 2...
			if ($pdf->y + 42 > $pdf->h) {
				if ($arr_content[ldept][$i]==2) {
					$pdf->Line($point_x, $point_y, $point_x, $pdf->h);
				}
				$pdf->AddPage(); 
				if ($arr_content[ldept][$i]==2) {
					$pdf->Line($point_x , $pdf->tMargin, $point_x, $pdf->tMargin+5);
				}
				$pdf->y+=$start_y+5; 
			}
			$ss = explode("^",$arr_content["L"][$i]);
			//	$ORG_NAME. "^".$FULLNAME."^".$PER_TYPE."^".$TMP_PL_NAME."^".$img_file;
			$oo = $ss[0]. "*enter*".$ss[1]."*enter*".$ss[3];
			$img = $ss[4];
			$spc1 = ($arr_content[ldept][$i]-2) * 5;
			if ($FLAG_RTF) {
//				$RTF->new_line();
//				$RTF->new_line();
				$RTF->set_font($font, 14);
//				$RTF->add_image($ss[4], 10, "center");
//				$RTF->add_text((($NUMBER_DISPLAY==2)?convert2thaidigit($oo):$oo) , "center");
				$RTF->set_table_font($font, 16);

				$RTF->open_line();			
				$RTF->cellImage($img, 20, 15, "center", 0, "TLBR");
				$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit($oo):$oo), "30", "center", "0", "TLBR", "center");
				$RTF->close_line();
			} else {
//				echo "bf...x=".$pdf->x.", y=".$pdf->y."<br>";
				$l_start_y = $pdf->y;		$l_start_x = $pdf->x;
				$pdf->x +=10+$spc1;
				$line_start_y = $pdf->y;		$line_start_x = $pdf->x;
				$w = 75;
				$max_y = 0;
				if (file_exists($img))  { 
					$pdf->Image($img,$pdf->x,$pdf->y,20,25,'','');
					$pdf->x += 25;
					$max_y = $pdf->y;
					$w = 50;					
				}
				$pdf->MultiCellThaiCut($w, 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($oo):$oo), $border, "C");
				if ($pdf->y > $max_y) $max_y = $pdf->y;
//				echo "af...x=".$pdf->x.", y=".$pdf->y." , max_y=$max_y<br>";
				$line_h = $max_y - $line_start_y + 4;		$line_w = 75;	// $pdf->x - $line_start_x;
				$pdf->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ
				$pdf->Rect($line_start_x, $line_start_y, $line_w, $line_h);
//				echo "$i--pdf->Rect($line_start_x, $line_start_y, $line_w, $line_h)<br>";
				if ($arr_content[ldept][$i]==2) { // ตั้งแต่ $i==2 เป็นต้นไป
					$point_x1 = $line_start_x + (75 / 2);  $point_y1 = $line_start_y;
					$pdf->SetDrawColor(hexdec("FF"),hexdec("00"),hexdec("00")); //สีแดง
					$pdf->Line($point_x, $point_y, $point_x, $point_y1 - 5);
					$pdf->Line($point_x, $point_y1 - 5, $point_x1, $point_y1 - 5);
					$pdf->Line($point_x1, $point_y1 - 5, $point_x1, $point_y1);
					$point_x11 = $line_start_x + (5 / 2);  $point_y11 = $line_start_y + $line_h;
//					$dept1 = 2;
				} else {
					$point_x12 = $line_start_x;  $point_y12 = $line_start_y+$line_h/2;
					$pdf->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("FF")); //สีน้ำเงิน
					$pdf->Line($point_x11, $point_y11, $point_x11, $point_y12);
					$pdf->Line($point_x11, $point_y12, $point_x12, $point_y12);
//					if ($arr_content[ldept][$i] != $dept1) {
//						$point_x11 = $line_start_x + (5 / 2);  $point_y11 = $line_start_y + $line_h;
//						$dept1 = $arr_content[ldept][$i];
//					}
				}
				$pdf->x = $start_x;
			}
			if ($arr_content["R"][$i]) {	// ข้อมูลทางขวา...
//				echo "$i--point_x=$point_x , point_y=$point_y<br>";
				$ss = explode("^",$arr_content["R"][$i]);
				//	$ORG_NAME. "^".$FULLNAME."^".$PER_TYPE."^".$TMP_PL_NAME."^".$img_file;
				$oo = $ss[0]. "*enter*".$ss[1]."*enter*".$ss[3];
				$img = $ss[4];
				$spc2 = ($arr_content[rdept][$i]-2) * 5;
				if ($FLAG_RTF) {
//					$RTF->new_line();
//					$RTF->new_line();
					$RTF->set_font($font, 14);
//					$RTF->add_image($ss[4], 10, "center");
//					$RTF->add_text((($NUMBER_DISPLAY==2)?convert2thaidigit($oo):$oo) , "center");
					$RTF->set_table_font($font, 16);
	
					$RTF->open_line();			
					$RTF->cellImage($img, 20, 15, "center", 0, "TLBR");
					$RTF->cell((($NUMBER_DISPLAY==2)?convert2thaidigit($oo):$oo), "30", "center", "0", "TLBR", "center");
					$RTF->close_line();
				} else {
					$max_y = 0;
					$pdf->x = $l_start_x + $spc2 + 105;
					$pdf->y = $l_start_y;
	//				echo "bf...x=".$pdf->x.", y=".$pdf->y."<br>";
					$line_start_y = $pdf->y;		$line_start_x = $pdf->x;
					$w = 75;					
					if (file_exists($img))  { 
						$pdf->Image($img,$pdf->x,$pdf->y,20,25,'','');
						$pdf->x += 25;
						$max_y = $pdf->y;
						$w = 50;					
					}
					$pdf->MultiCellThaiCut($w, 7, (($NUMBER_DISPLAY==2)?convert2thaidigit($oo):$oo), $border, "C");
					if ($pdf->y > $max_y) $max_y = $pdf->y;
//					echo "af...x=".$pdf->x.", y=".$pdf->y." , max_y=$max_y<br>";
					$line_h = $max_y - $line_start_y + 4;		$line_w = 75;	//$pdf->x - $line_start_x;
					$pdf->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00")); //สีดำ
					$pdf->Rect($line_start_x, $line_start_y, $line_w, $line_h);
					if ($arr_content[rdept][$i]==2) {
						$point_x1 = $line_start_x + (75 / 2);  $point_y1 = $line_start_y;
						$pdf->SetDrawColor(hexdec("FF"),hexdec("00"),hexdec("00")); //สีแดง
						$pdf->Line($point_x, $point_y, $point_x, $point_y1 - 5);
						$pdf->Line($point_x, $point_y1 - 5, $point_x1, $point_y1 - 5);
						$pdf->Line($point_x1, $point_y1 - 5, $point_x1, $point_y1);
						$point_x21 = $line_start_x + (5 / 2);  $point_y21 = $line_start_y + $line_h;
//						$dept2 = 2;
					} else {
						$point_x22 = $line_start_x;  $point_y22 = $line_start_y+$line_h/2;
						$pdf->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("FF")); //สีน้ำเงิน
						$pdf->Line($point_x21, $point_y21, $point_x21, $point_y22);
						$pdf->Line($point_x21, $point_y22, $point_x22, $point_y22);
//						if ($arr_content[rdept][$i+1] != $dept2) {
//							$point_x21 = $line_start_x + (5 / 2);  $point_y21 = $line_start_y + $line_h;
//							$dept2 = $arr_content[rdept][$i+1];
//						}
					}
					$pdf->x = $start_x;
				}
			}
			$pdf->y += 14;
		}
	}

	if ($FLAG_RTF) {
//		$RTF->close_section(); 
	
		$RTF->display($fname);
	} else {
		$pdf->close();
		$pdf->Output();
	}
		
	//แสดงรายชื่อตามสายบังคับบัญชา
	function list_tree_per ($parent_per_id, $dept) {	
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $per_list, $cntnext_per, $arr_struct_pic,$RPT_N,$BKK_FLAG;
		global $pdf, $arr_content, $lr_pos;
		global $start_x, $start_y, $center_x;

		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

		//หา PER_ID ตาม PER_ID_REF = $parent_per_id
		$where = " and a.PER_STATUS = 1 ";
		if ($BKK_FLAG==1) $where .= " and a.PER_TYPE = 1";
		$cmd_per = " select  a.DEPARTMENT_ID, PER_ID , PER_NAME, PER_SURNAME, a.PN_CODE, a.POS_ID, a.POEM_ID, a.POEMS_ID, 
										  a.LEVEL_NO, f.POSITION_LEVEL, a.PER_TYPE, a.PER_STATUS, a.PER_CARDNO
								from   ((((PER_PERSONAL a
												left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
												) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
												) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
												) left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
												) left  join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
							where a.PER_ID_ASS_REF=". $parent_per_id." $where 
							order by a.PER_TYPE, f.LEVEL_SEQ_NO DESC, a.PER_SEQ_NO, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID ";	
		$count_data = $db_dpis->send_cmd($cmd_per);
//		echo "$cmd_per<br>";
//		$db_dpis->show_error();
		if($count_data){
			$i=0;
			while($data = $db_dpis->get_array()){
				$cntnext_per++;
				$i++;
				$PER_ID = trim($data[PER_ID]);
				$per_list[] = $PER_ID;
				$LEVEL_NO = trim($data[LEVEL_NO]);
				$LEVEL_NAME = trim($data[POSITION_LEVEL]);
				$PER_NAME = $data[PER_NAME];
				$PER_SURNAME = $data[PER_SURNAME];
				$FULLNAME = "$PER_NAME $PER_SURNAME";
				$PER_CARDNO = $data[PER_CARDNO];
					//-------------หารูปภาพที่ใช้แสดง
					$cmd = "	SELECT * FROM PER_PERSONALPIC
									WHERE	(PER_ID=$PER_ID and PIC_SHOW=1)
									ORDER BY	 PER_PICSEQ ";
					$db_dpis2->send_cmd($cmd);
					$data1 = $db_dpis2->get_array();
					$TMP_PIC_SEQ = $data1[PER_PICSEQ];
					$current_list .= ((trim($current_list))?",":"") . $TMP_PIC_SEQ;
					$T_PIC_SEQ = substr("000",0,3-strlen("$TMP_PIC_SEQ"))."$TMP_PIC_SEQ";
					if (trim($data1[PER_CARDNO]) && trim($data1[PER_CARDNO]) != "NULL")
						$TMP_PIC_NAME = "../".$data1[PER_PICPATH].$data1[PER_CARDNO]."-".$T_PIC_SEQ.".jpg";
					else
						$TMP_PIC_NAME = "../".$data1[PER_PICPATH].$data1[PER_GENNAME]."-".$T_PIC_SEQ.".jpg";
					//----------------------------------------------------------
					$img_file = "";
					$IMG_PATH = "../attachment/pic_personal/";
					if($PER_CARDNO && file_exists($TMP_PIC_NAME)) $img_file = $TMP_PIC_NAME;
//					if($img_file=="")	$img_file="images/my_preview.jpg";
				
				$PN_CODE = trim($data[PN_CODE]);
				if ($PN_CODE) {
					$cmd = "	select PN_NAME, PN_SHORTNAME from PER_PRENAME where PN_CODE='$PN_CODE'";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$PN_NAME = $data2[PN_NAME];
					$PN_SHORTNAME = $data2[PN_SHORTNAME];
				}
				$PRENAME = ($PN_SHORTNAME)?$PN_SHORTNAME:$PN_NAME;
				$FULLNAME = $PRENAME.$FULLNAME;

				$PER_TYPE = $data[PER_TYPE];
				$PER_STATUS = $data[PER_STATUS];
				$POSEM_NO = "";
				$TMP_PL_NAME = "";
				$ORG_NAME = "";
				$ORG_NAME_REF = "";
				if ($PER_TYPE == 1) { // ข้าราชการ
					$POS_ID = $data[POS_ID];
					if ($POS_ID) {
						$cmd = " 	select		POS_NO_NAME, POS_NO, pl.PL_NAME, po.ORG_NAME, pp.PT_CODE, pt.PT_NAME, po.ORG_ID, pp.ORG_ID_1, pp.ORG_ID_2, po.ORG_ID_REF , pp.PM_CODE
										from		PER_POSITION pp, PER_LINE pl, PER_ORG po, PER_TYPE pt 
										where		pp.POS_ID=$POS_ID and pp.ORG_ID=po.ORG_ID and pp.PL_CODE=pl.PL_CODE and pp.PT_CODE=pt.PT_CODE ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$POSEM_NO = $data2[POS_NO_NAME].$data2[POS_NO];
						$PL_NAME = $data2[PL_NAME];
						$ORG_NAME = $data2[ORG_NAME];
						$ORG_ID = trim($data2[ORG_ID]);
						$ORG_ID_1 = trim($data2[ORG_ID_1]);
						$ORG_ID_2 = trim($data2[ORG_ID_2]);
						$ORG_ID_REF = trim($data2[ORG_ID_REF]);
						$PT_CODE = trim($data2[PT_CODE]);
						$PT_NAME = trim($data2[PT_NAME]);
						$PM_CODE = trim($data2[PM_CODE]);

						$cmd = " 	select PM_NAME from PER_MGT	where PM_CODE='$PM_CODE'  ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$PM_NAME = trim($data2[PM_NAME]);
						if ($RPT_N)
						    $TMP_PL_NAME = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)? "$PL_NAME$LEVEL_NAME" : "") . (trim($PM_NAME) ?")":"");
						else
						    $TMP_PL_NAME = (trim($PM_NAME) ?"$PM_NAME (":"") . (trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO) . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"") . (trim($PM_NAME) ?")":"");
					} // end if ($POS_ID) 
				} elseif ($PER_TYPE == 2) { // 
					$POEM_ID = $data[POEM_ID];
					if ($POEM_ID) {
						$cmd = " 	select		POEM_NO_NAME, POEM_NO, pl.PN_NAME, po.ORG_NAME, po.ORG_ID, pp.ORG_ID_1, pp.ORG_ID_2, po.ORG_ID_REF   
										from			PER_POS_EMP pp, PER_POS_NAME pl, PER_ORG po 
										where		pp.POEM_ID=$POEM_ID and pp.ORG_ID=po.ORG_ID and pp.PN_CODE=pl.PN_CODE  ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$POSEM_NO = trim($data2[POEM_NO_NAME]).trim($data2[POEM_NO]);
						$PL_NAME = trim($data2[PN_NAME]);
						$ORG_NAME = trim($data2[ORG_NAME]);
						$ORG_ID = trim($data2[ORG_ID]);
						$ORG_ID_1 = trim($data2[ORG_ID_1]);
						$ORG_ID_2 = trim($data2[ORG_ID_2]);
						$ORG_ID_REF = trim($data2[ORG_ID_REF]);
						$TMP_PL_NAME = (trim($PL_NAME))? "$PL_NAME$LEVEL_NAME" : "";	
					}
				} elseif ($PER_TYPE == 3) {
					$POEMS_ID = $data[POEMS_ID];
					if ($POEMS_ID) {
						$cmd = " 	select		POEMS_NO_NAME, POEMS_NO, pl.EP_NAME, po.ORG_NAME, po.ORG_ID, pp.ORG_ID_1, pp.ORG_ID_2, po.ORG_ID_REF   
										from			PER_POS_EMPSER pp, PER_EMPSER_POS_NAME pl, PER_ORG po 
										where		pp.POEMS_ID=$POEMS_ID and pp.ORG_ID=po.ORG_ID and pp.EP_CODE=pl.EP_CODE  ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$POSEM_NO = trim($data2[POEMS_NO_NAME]).trim($data2[POEMS_NO]);
						$PL_NAME = trim($data2[EP_NAME]);
						$ORG_NAME = trim($data2[ORG_NAME]);
						$ORG_ID = trim($data2[ORG_ID]);
						$ORG_ID_1 = trim($data2[ORG_ID_1]);
						$ORG_ID_2 = trim($data2[ORG_ID_2]);
						$ORG_ID_REF = trim($data2[ORG_ID_REF]);
						$TMP_PL_NAME = (trim($PL_NAME))? "$PL_NAME$LEVEL_NAME" : "";	
					}
				} elseif ($PER_TYPE == 4) { // 
					$POT_ID = $data[POT_ID];
					if ($POT_ID) {
						$cmd = " 	select		POT_NO_NAME, POT_NO, pl.TP_NAME, po.ORG_NAME, po.ORG_ID, pp.ORG_ID_1, pp.ORG_ID_2, po.ORG_ID_REF   
										from			PER_POS_TEMP pp, PER_TEMP_POS_NAME pl, PER_ORG po 
										where		pp.POT_ID=$POT_ID and pp.ORG_ID=po.ORG_ID and pp.TP_CODE=pl.TP_CODE  ";
						$db_dpis2->send_cmd($cmd);
						$data2 = $db_dpis2->get_array();
						$POSEM_NO = trim($data2[POT_NO_NAME]).trim($data2[POT_NO]);
						$PL_NAME = trim($data2[TP_NAME]);
						$ORG_NAME = trim($data2[ORG_NAME]);
						$ORG_ID = trim($data2[ORG_ID]);
						$ORG_ID_1 = trim($data2[ORG_ID_1]);
						$ORG_ID_2 = trim($data2[ORG_ID_2]);
						$ORG_ID_REF = trim($data2[ORG_ID_REF]);
						$TMP_PL_NAME = (trim($PL_NAME))? "$PL_NAME$LEVEL_NAME" : "";	
					}
				} // end if PER_TYPE
				if ($ORG_ID_2) {
					$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$ORG_NAME = $data2[ORG_NAME];
				}
				if ($ORG_ID_1) {
					$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
					$db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$ORG_NAME = $data2[ORG_NAME];
				}

				$deptstr="";
				for($j = 2; $j < $dept; $j++) {
					$deptstr="$deptstr<img src=\"".$arr_struct_pic[$j]."\" width=\"14\" height=\"24\" align=\"middle\" border=\"0\"></img>";
				}
				$deptstr="$deptstr"."<img src=\"$struct_pic\" width=\"14\" height=\"24\" align=\"middle\" border=\"0\"></img>";
				$img="<img src=\"$img_file\" width=\"14\" height=\"24\" align=\"middle\" border=\"0\"></img>";
//				echo "..........$deptstr".$ORG_NAME. "-".$FULLNAME."-".$PER_TYPE."-".$TMP_PL_NAME." [$img] (img_file=$img_file)<br>";
				if ($dept==2)
					if ($i % 2 == 0)
						$lr_pos = "R";
					else
						$lr_pos = "L";
//				echo ".($lr_pos).........$deptstr".$ORG_NAME. "-".$FULLNAME."-".$PER_TYPE."-".$TMP_PL_NAME." [$img] (img_file=$img_file)<br>";
				$arr_content[$lr_pos][] = $ORG_NAME. "^".$FULLNAME."^".$PER_TYPE."^".$TMP_PL_NAME."^".$img_file;
				if ($lr_pos == "R") {
					$arr_content[rdept][] = $dept;
				} else {
					$arr_content[ldept][] = $dept;
				}

				$cmd_sub = " select  a.DEPARTMENT_ID, PER_ID , PER_NAME, PER_SURNAME, a.PN_CODE, a.POS_ID, a.POEM_ID, a.POEMS_ID, 
												  a.LEVEL_NO, f.POSITION_LEVEL, a.PER_TYPE, a.PER_STATUS 
										from   ((((PER_PERSONAL a
														left join PER_POSITION c on (a.POS_ID=c.POS_ID) 
														) left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
														) left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID)
														) left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
														) left  join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
									where a.PER_ID_ASS_REF=". $PER_ID." $where
									order by a.PER_TYPE, f.LEVEL_SEQ_NO DESC, a.PER_SEQ_NO, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID ";	
				$count_data2 = $db_dpis2->send_cmd($cmd_sub);
				if ($count_data2) {
					if ($dept < 3) {	// ให้ 3 ระดับ
						list_tree_per($PER_ID, $dept+1);
					}
				}
			} // end loop while
		} // if($count_data)
	} // end function
?>