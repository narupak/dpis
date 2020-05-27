<?	
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/session_start.php");
	include("../php_scripts/function_share.php");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
 	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	if(!$NUMBER_DISPLAY)	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	if(!$NUMBER_DISPLAY)	$NUMBER_DISPLAY = $_POST[NUMBER_DISPLAY];
	if(!$KPI_YEAR)		$KPI_YEAR=trim($_GET[KPI_YEAR]);
	if(!$KPI_YEAR)		$KPI_YEAR=trim($_POST[KPI_YEAR]);
	if(!$DEPARTMENT_ID)		$DEPARTMENT_ID=trim($_GET[DEPARTMENT_ID]);
	if(!$DEPARTMENT_ID)		$DEPARTMENT_ID=trim($_POST[DEPARTMENT_ID]);

	include ("rpt_kpi_distribute4_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น
	
	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "ขั้นตอนที่ 4  การกำหนดตัวชี้วัดระดับบุคคลตามหน้าที่ความรับผิดชอบหลัก";
	$report_title = (($NUMBER_DISPLAY==2)?convert2thaidigit($report_title):$report_title);
	$report_code = "xls_KPI_DISTRIBUTE4";
	$report_footer = "line^ทดสอบพิมพ์หางรายงาน เพื่อการปรับปรุง class PDF|||14||||7|&standard&standard^7";
	$orientation='P';
	$heading_border="TRL";

	session_cache_limiter("private");
	session_start();

	$pdf = new PDF($orientation,$unit,$paper_size,$lang_code,$company_name,$report_title,$report_code,$heading,$heading_width,$heading_align, $heading_border,$report_footer,$NUMBER_DISPLAY);
	
 	$pdf->Open();
	$pdf->SetMargins(5,5,5);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetTextColor(0, 0, 0);
	$pdf->SetFont($font,'',14);
	
	$pdf->AutoPageBreak = false;
	
	$cmd = " select 	KD_ID, KPI_ID, ORG_ID, ORG_ID_1, ORG_ID_2, KD_PER_ID, KD_FLAG, KD_TYPE, KD_FLAG, KD_REMARK
					from 		PER_KPI_DISTRIBUTE
					where 	KPI_YEAR = $KPI_YEAR  and DEPARTMENT_ID = $DEPARTMENT_ID and KD_TYPE=4
					order by 	KD_PER_ID ";
	$count_kpi_dist = $db_dpis->send_cmd($cmd);
//	echo "kpi .(type=$t_kd_type). cmd=$cmd ($count_kpi_dist)<br>";
//	$db_dpis->show_error();
	if ($count_kpi_dist) {
		while($data = $db_dpis->get_array()){
			if ($data[KD_PER_ID]) {
				if ($TMP_PER_ID != $data[KD_PER_ID]) {
					$cmd2 = " select * from PER_KPI where KPI_ID = ".$data[KPI_ID]." ";
					$db_dpis2->send_cmd($cmd2);
					if ($data2 = $db_dpis2->get_array()) {
						$KPI_NAME = $data2[KPI_NAME];
					} else {
						$KPI_NAME = "** ไม่มีชื่อ KPI รหัส ".$data[KPI_ID]." **";
					} 
					if($DPISDB=="odbc"){
						$cmd2 = " select 	a.PER_ID, a.PN_CODE, PER_NAME, PER_SURNAME, a.POS_ID, 
														a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO, 
														a.PER_TYPE, a.PER_STATUS, ES_CODE, PL_NAME_WORK, ORG_NAME_WORK, 
														PAY_ID, a.PER_CARDNO, a.DEPARTMENT_ID, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, PER_SEQ_NO, PER_FILE_NO                                     
										 from	(
														(
															(
																(
																	(	
																		PER_PERSONAL a
																		left join PER_POSITION c on ($search_con) 
																	) 	left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
																)	left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID) 
															) 	left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
														) 	left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
													) 	left join PER_ORG i on (c.ORG_ID=i.ORG_ID)
										where a.PER_ID = ".$data[KD_PER_ID]." ";
					}elseif($DPISDB=="oci8"){
						$cmd2 = "select 		a.PER_ID, a.PN_CODE, PER_NAME, PER_SURNAME, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO, a.PER_TYPE, a.PER_STATUS, ES_CODE, PL_NAME_WORK, ORG_NAME_WORK, PAY_ID, a.PER_CARDNO, a.DEPARTMENT_ID, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, PER_SEQ_NO, PER_FILE_NO
												  from 			PER_PERSONAL a, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, PER_LEVEL f, PER_POS_TEMP g, PER_ORG i
												  where 		a.PER_ID = ".$data[KD_PER_ID]." and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+) and 
																	a.LEVEL_NO=f.LEVEL_NO(+) and a.POT_ID=g.POT_ID(+) and c.ORG_ID=i.ORG_ID(+) ";
					}elseif($DPISDB=="mysql"){
						$cmd2 = " select 	a.PER_ID, a.PN_CODE, PER_NAME, PER_SURNAME, a.POS_ID, a.POEM_ID, a.POEMS_ID, a.POT_ID, a.LEVEL_NO, a.PER_TYPE, a.PER_STATUS, ES_CODE, PL_NAME_WORK, ORG_NAME_WORK, PAY_ID, a.PER_CARDNO, a.DEPARTMENT_ID, a.ORG_ID, a.ORG_ID_1, a.ORG_ID_2, PER_SEQ_NO, PER_FILE_NO
											from (
														(
															(
																(	
																	(
																		PER_PERSONAL a
																		left join PER_POSITION c on ($search_con) 
																	) 	left join PER_POS_EMP d on (a.POEM_ID=d.POEM_ID)
																)	left join PER_POS_EMPSER e on (a.POEMS_ID=e.POEMS_ID) 
															) 	left join PER_LEVEL f on (a.LEVEL_NO=f.LEVEL_NO)
														) 	left join PER_POS_TEMP g on (a.POT_ID=g.POT_ID)
													) 	left join PER_ORG i on (c.ORG_ID=i.ORG_ID)
										 where a.PER_ID = ".$data[KD_PER_ID]." ";
					} // end if
					
					$db_dpis2->send_cmd($cmd2);
//					echo "PER info =$cmd2<br>";
					if ($data2 = $db_dpis2->get_array()) {
						$TMP_PER_TYPE =  trim($data2[PER_TYPE]);
						$TMP_LEVEL_NO = trim($data2[LEVEL_NO]);
						$PER_NAME_SHOW = trim($data2[PER_NAME]);
						$PER_SURNAME_SHOW = trim($data2[PER_SURNAME]);
	//					$NH_NAME = trim($data2[NH_NAME]);
	//					$NH_SURNAME = trim($data2[NH_SURNAME]);
	//					$old_name = "($NH_NAME $NH_SURNAME)";
	//					$FULLNAME = "$PER_NAME_SHOW $PER_SURNAME_SHOW".$old_name;
						$TMP_ES_CODE = trim($data2[ES_CODE]);
						$TMP_PL_NAME_WORK = trim($data2[PL_NAME_WORK]);
						$TMP_ORG_NAME_WORK = trim($data2[ORG_NAME_WORK]);
				
						$TMP_DEPARTMENT_ID = trim($data2[DEPARTMENT_ID]);
						$TMP_ORG_ASS_ID = trim($data2[ORG_ID]);
						$TMP_ORG_ASS_ID_1 = trim($data2[ORG_ID_1]);
						$TMP_ORG_ASS_ID_2 = trim($data2[ORG_ID_2]);
				
						$TMP_PN_NAME = $TMP_PN_SHORTNAME = "";
						$TMP_PN_CODE = trim($data2[PN_CODE]);
						if ($TMP_PN_CODE) {
							$cmd3 = "	select PN_NAME, PN_SHORTNAME from PER_PRENAME where PN_CODE='$TMP_PN_CODE'";
							$db_dpis3->send_cmd($cmd3);
							$data3 = $db_dpis3->get_array();
							$TMP_PN_NAME = $data3[PN_NAME];
							$TMP_PN_SHORTNAME = $data3[PN_SHORTNAME];
						}
	
						$FULL_NAME = ($TMP_PN_SHORTNAME?$TMP_PN_SHORTNAME:$TMP_PN_NAME).$PER_NAME_SHOW ." ". $PER_SURNAME_SHOW;
	
						$cmd3 = " select POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$TMP_LEVEL_NO' ";
						$db_dpis3->send_cmd($cmd3);
						$data3 = $db_dpis3->get_array();
						$TMP_LEVEL_NAME = $data3[POSITION_LEVEL];
						
						$TMP_PER_TYPE = $data2[PER_TYPE];
						$TMP_PER_STATUS = $data2[PER_STATUS];
						$TMP_POSEM_NO = $TMP_ORG_NAME = $TMP_PL_NAME = $TMP_PM_NAME = $TMP_PAY_NO = "";
						if ($TMP_PER_TYPE == 1 || $TMP_PER_TYPE == 5) {
							if ($command == "SEARCHPAY") {
								$TMP_POS_ID = $data2[PAY_ID];
								if ($TMP_POS_ID) {
									$cmd3 = " 	select		POS_NO_NAME, POS_NO, pl.PL_NAME, po.ORG_NAME, pp.PT_CODE, po.ORG_ID, pp.PM_CODE, pp.ORG_ID_1, pp.ORG_ID_2
														from		PER_POSITION pp, PER_LINE pl, PER_ORG po
														where		pp.POS_ID=$TMP_POS_ID and pp.ORG_ID=po.ORG_ID and pp.PL_CODE=pl.PL_CODE ";
									$db_dpis3->send_cmd($cmd3);
									$data3 = $db_dpis3->get_array();
									$TMP_POSEM_NO = $data3[POS_NO_NAME].' '.$data3[POS_NO];
									$TMP_PL_NAME = $data3[PL_NAME];
									$TMP_PT_CODE = trim($data3[PT_CODE]);
									$TMP_PT_NAME = trim($data3[PT_NAME]);
									$TMP_PM_CODE = trim($data3[PM_CODE]);
									$TMP_ORG_NAME = trim($data3[ORG_NAME]);
									$TMP_ORG_ID_1 = trim($data3[ORG_ID_1]);
									$TMP_ORG_ID_2 = trim($data3[ORG_ID_2]);
				
									$cmd3 = " 	select PM_NAME from PER_MGT	where PM_CODE='$TMP_PM_CODE'  ";
									$db_dpis3->send_cmd($cmd3);
									$data3 = $db_dpis3->get_array();
									$TMP_PM_NAME = trim($data3[PM_NAME]);
									if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง" && !$TMP_PM_NAME) $TMP_PM_NAME = $TMP_PL_NAME;
								}
							
								$TMP_PAY_ID = $data2[POS_ID];
								if ($TMP_PAY_ID) {
									$cmd3 = " 	select		POS_NO_NAME, POS_NO, po.ORG_NAME
														from		PER_POSITION pp, PER_ORG po
														where		pp.POS_ID=$TMP_PAY_ID and pp.ORG_ID=po.ORG_ID ";
									if($SESS_ORG_STRUCTURE==1){	$cmd3 = str_replace("PER_ORG","PER_ORG_ASS",$cmd3); 	}
									$db_dpis3->send_cmd($cmd3);
									$data3 = $db_dpis3->get_array();
									$TMP_PAY_NO = $data3[POS_NO_NAME] . $data3[POS_NO] . " " . $data3[ORG_NAME];
								}
							} else {
								$TMP_POS_ID = $data2[POS_ID];
								if ($TMP_POS_ID) {
									$cmd3 = " 	select		POS_NO_NAME, POS_NO, pl.PL_NAME, po.ORG_NAME, pp.PT_CODE, po.ORG_ID, pp.PM_CODE, pp.ORG_ID_1, pp.ORG_ID_2
														from		PER_POSITION pp, PER_LINE pl, PER_ORG po
														where		pp.POS_ID=$TMP_POS_ID and pp.ORG_ID=po.ORG_ID and pp.PL_CODE=pl.PL_CODE ";
									$db_dpis3->send_cmd($cmd3);
									$data3 = $db_dpis3->get_array();
									$TMP_POSEM_NO = $data3[POS_NO_NAME].' '.$data3[POS_NO];
									$TMP_PL_NAME = $data3[PL_NAME];
									$TMP_PT_CODE = trim($data3[PT_CODE]);
									$TMP_PT_NAME = trim($data3[PT_NAME]);
									$TMP_PM_CODE = trim($data3[PM_CODE]);
									$TMP_ORG_NAME = trim($data3[ORG_NAME]);
									$TMP_ORG_ID_1 = trim($data3[ORG_ID_1]);
									$TMP_ORG_ID_2 = trim($data3[ORG_ID_2]);
				
									$cmd3 = " 	select PM_NAME from PER_MGT	where PM_CODE='$TMP_PM_CODE'  ";
									$db_dpis3->send_cmd($cmd3);
									$data3 = $db_dpis3->get_array();
									$TMP_PM_NAME = trim($data3[PM_NAME]);
									if ($SESS_DEPARTMENT_NAME=="กรมการปกครอง" && !$TMP_PM_NAME) $TMP_PM_NAME = $TMP_PL_NAME;
								}
							
								$TMP_PAY_ID = $data2[PAY_ID];
								if ($TMP_PAY_ID) {
									$cmd3 = " 	select		POS_NO_NAME, POS_NO, po.ORG_NAME
														from		PER_POSITION pp, PER_ORG po
														where		pp.POS_ID=$TMP_PAY_ID and pp.ORG_ID=po.ORG_ID ";
									if($SESS_ORG_STRUCTURE==1){	$cmd3 = str_replace("PER_ORG","PER_ORG_ASS",$cmd3); 	}
									$db_dpis3->send_cmd($cmd3);
									$data3 = $db_dpis3->get_array();
									$TMP_PAY_NO = $data3[POS_NO_NAME] . $data3[POS_NO] . " " . $data3[ORG_NAME];
								}
							}
						} elseif ($TMP_PER_TYPE == 2) {
							$TMP_POEM_ID = $data2[POEM_ID];
							if ($TMP_POEM_ID) {
								$cmd3 = " 	select	 POEM_NO_NAME, POEM_NO, pl.PN_NAME, po.ORG_NAME, po.ORG_ID, pp.ORG_ID_1, pp.ORG_ID_2, pl.PG_CODE   
													from		PER_POS_EMP pp, PER_POS_NAME pl, PER_ORG po 
													where	 pp.POEM_ID=$TMP_POEM_ID and pp.ORG_ID=po.ORG_ID and pp.PN_CODE=pl.PN_CODE  ";
								$db_dpis3->send_cmd($cmd3);
								$data3 = $db_dpis3->get_array();
								$TMP_POSEM_NO = trim($data3[POEM_NO_NAME]).trim($data3[POEM_NO]);
								$TMP_PL_NAME = trim($data3[PN_NAME]);
								$TMP_PG_CODE = trim($data3[PG_CODE]);
								$TMP_ORG_NAME = trim($data3[ORG_NAME]);
								$TMP_ORG_ID_1 = trim($data3[ORG_ID_1]);
								$TMP_ORG_ID_2 = trim($data3[ORG_ID_2]);
				
								$cmd3 = " select PG_NAME from PER_POS_GROUP where trim(PG_CODE)='$TMP_PG_CODE'  ";
								$db_dpis3->send_cmd($cmd3);
								$data3 = $db_dpis3->get_array();
								$TMP_PM_NAME = trim($data3[PG_NAME]);
							}
						} elseif ($TMP_PER_TYPE == 3) {
							$TMP_POEMS_ID = $data2[POEMS_ID];
							if ($TMP_POEMS_ID) {
								$cmd3 = " 	select		POEMS_NO, pl.EP_NAME, po.ORG_NAME, po.ORG_ID, pp.ORG_ID_1, pp.ORG_ID_2, le.LEVEL_NAME  
													from			PER_POS_EMPSER pp, PER_EMPSER_POS_NAME pl, PER_ORG po, PER_LEVEL le
													where		pp.POEMS_ID=$TMP_POEMS_ID and pp.ORG_ID=po.ORG_ID and pp.EP_CODE=pl.EP_CODE  and pl.LEVEL_NO = le.LEVEL_NO";
								$db_dpis3->send_cmd($cmd3);
								$data3 = $db_dpis3->get_array();
								$TMP_POSEM_NO = trim($data3[POEMS_NO]);
								$TMP_PL_NAME = trim($data3[EP_NAME]);
								$TMP_PM_NAME = trim($data3[LEVEL_NAME]);
								$TMP_ORG_NAME = trim($data3[ORG_NAME]);
								$TMP_ORG_ID_1 = trim($data3[ORG_ID_1]);
								$TMP_ORG_ID_2 = trim($data3[ORG_ID_2]);
							}
						} elseif ($TMP_PER_TYPE == 4) {
							$TMP_POT_ID = $data2[POT_ID];
							if ($TMP_POT_ID) {
								$cmd3 = " 	select		POT_NO, pl.TP_NAME, po.ORG_NAME, po.ORG_ID, pp.ORG_ID_1, pp.ORG_ID_2   
													from			PER_POS_TEMP pp, PER_TEMP_POS_NAME pl, PER_ORG po 
													where		pp.POT_ID=$TMP_POT_ID and pp.ORG_ID=po.ORG_ID and pp.TP_CODE=pl.TP_CODE  ";
								$db_dpis3->send_cmd($cmd3);
								$data3 = $db_dpis3->get_array();
								$TMP_POSEM_NO = trim($data3[POT_NO]);
								$TMP_PL_NAME = trim($data3[TP_NAME]);
								$TMP_ORG_NAME = trim($data3[ORG_NAME]);
								$TMP_ORG_ID_1 = trim($data3[ORG_ID_1]);
								$TMP_ORG_ID_2 = trim($data3[ORG_ID_2]);
							}
						}
						if ($TMP_ORG_NAME=="-") $TMP_ORG_NAME = "";
					} // end if ($data2 = $db_dpis2->get_array()
					
					if ($in_loop) {	$pdf->close_tab(""); $pdf->AddPage(); }

					$page_start_x = $pdf->x;	  $page_start_y = $pdf->y;

					$spc1 = str_repeat(" ", (140-strlen($FULL_NAME)-1));
					$arr_text = array("ชื่อ"," ".$FULL_NAME.$spc1);
					$arr_align = array("L","L");
					$arr_border = array("","");
					$arr_font = array("$font|b|14|","$font|U|14|");
					$column_function_s = array(($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
					$arr_width = array("15","185");
					$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $column_function_s);
					
					$spc2 = str_repeat(" ", (80-strlen($TMP_PL_NAME)-1));
					$spc3 = str_repeat(" ", (41-strlen($TMP_LEVEL_NAME)-1));
					$arr_text = array("ตำแหน่ง"," ".$TMP_PL_NAME.$spc2,"ระดับ"," ".$TMP_LEVEL_NAME.$spc3);
					$arr_align = array("L","L","L","L");
					$arr_border = array("","","","");
					$arr_font = array("$font|b|14|","$font|U|14|","$font|b|14|","$font|U|14|");
					$column_function_s = array(($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM":"ENUM",($NUMBER_DISPLAY==2)?"TNUM":"ENUM");
					$arr_width = array("15","100","10","75");
					$a = $pdf->add_lineFreeTab($line_h, $arr_text, $arr_align, $arr_border, $arr_font, $arr_width, $column_function_s);
					
//					$result = $pdf->add_text_line("1..".$FULL_NAME, 7, "", "L", "", "14", "b", 0, 0);
//					if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
//					$result = $pdf->add_text_line("2..".$TMP_PL_NAME." ".$TMP_ORG_NAME, 7, "", "L", "", "14", "b", 0, 0);
//					if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
				
					$head_text1 = implode(",", $heading_text);
					$head_width1 = implode(",", $heading_width);
					$head_align1 = implode(",", $heading_align);
					$col_function = implode(",", $column_function);
					$result = $pdf->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", "0066CC", "EEEEFF", $COLUMN_FORMAT, $col_function);
					if (!$result) echo "****** error ****** on open table for $table<br>";
	
					$arr_data = (array) null;
					$arr_data[] = "";
					$arr_data[] = $KPI_NAME;
					$arr_data[] = "";
									
					$result = $pdf->add_data_tab($arr_data, 7, "RHBL", $data_align, "", "14", "", "000000", "");		//TRHBL
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
					
					$TMP_PER_ID = $data[KD_PER_ID];
					$in_loop = true;
				} else { // else if ($TMP_PER_ID != $data[KD_PER_ID])
//					echo "PER_ID=".$data[KD_PER_ID]," , flag=".$data[KD_FLAG]." , remark=".$data[KD_REMARK]."<br>";
					$arr_data = (array) null;
					$arr_data[] = "";
					$arr_data[] = $KPI_NAME;
					$arr_data[] = "";
									
					$result = $pdf->add_data_tab($arr_data, 7, "RHL", $data_align, "", "14", "", "000000", "");		//TRHBL
					if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				} // end if ($TMP_PER_ID != $data[KD_PER_ID])
//				echo "---per_id-->".$data[KD_PER_ID]."<br>";
			} // end if ($data[KD_PER_ID])
		} // end while	data
	} else {	// else if($count_kpi_dist)
		$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "C", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if($count_kpi_dist)

	$pdf->close_tab(""); 

	$pdf->close();
	$pdf->Output();	
?>