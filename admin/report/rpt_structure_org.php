<?
	if ($BYASS=="Y") $ORGTAB = "PER_ORG_ASS"; else $ORGTAB = "PER_ORG";
	
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];
	
	$dept_text = call_find_dept($ORG_ID1);
	$arr_dept = explode(",",$dept_text);
	$dept = count($arr_dept);

	$ORG_ID = $ORG_ID1;
//	echo "ORG_ID1=$ORG_ID1 , ORGTAB=$ORGTAB , dept1=$dept1 , [$dept_text] , ($dept)<br>";

	function call_find_dept($ORG_ID) {
		global $db_dpis2, $DPISDB, $ORGTAB;

		$cmd = "	select ORG_ID , ORG_NAME, ORG_ID_REF, ORG_ACTIVE
						 from $ORGTAB where ORG_ID = $ORG_ID  ";
		$count_data = $db_dpis2->send_cmd($cmd);
//		echo "cmd=$cmd<br>";
		$data2 = $db_dpis2->get_array();
		if ($data2[ORG_ID_REF]==$ORG_ID) {
			$dept = $ORG_ID;
//			echo "1..$dept<br>";
		} else if ($data2[ORG_ID_REF]) {
			$dept = $ORG_ID.",".call_find_dept($data2[ORG_ID_REF]);
//			echo "2..$dept<br>";
		} else {
			$dept = "";
//			echo "3..$dept<br>";
		}

		return $dept;
	}

	function call_find_dept_name($ORG_ID) {
		global $db_dpis2, $DPISDB, $ORGTAB;
		
		$cmd = "	select ORG_ID , ORG_NAME, ORG_ID_REF, ORG_ACTIVE
						 from $ORGTAB where ORG_ID = $ORG_ID  ";
		$count_data = $db_dpis2->send_cmd($cmd);
//		echo "cmd=$cmd<br>";
		$data2 = $db_dpis2->get_array();
		if ($data2[ORG_ID_REF]==$ORG_ID) {
			$dept = $data2[ORG_NAME];
//			echo "1..$dept<br>";
		} else if ($data2[ORG_ID_REF]) {
			$dept = $data2[ORG_NAME].",".call_find_dept_name($data2[ORG_ID_REF]);
//			echo "2..$dept<br>";
		} else {
			$dept = "";
//			echo "3..$dept<br>";
		}

		return $dept;
	}

	function print_box($ORG_NAME, $NUM_PER, $border) {
		global $NUMBER_DISPLAY, $pdf;

		$start_y = $pdf->y;
		$keep_x = $pdf->x; $check_h = $pdf->y;
//		echo "print_box....................1......check_h=".$check_h."<br>";
		$pdf->MultiCellThaiCut(50,7,($NUMBER_DISPLAY==2 ? convert2thaidigit($ORG_NAME) : $ORG_NAME),$border,'L',0,1);
//		echo "print_box....................2......check_h=".$check_h."<br>";
		$pdf->x = $keep_x; $check_h = $pdf->y - $check_h;
//		echo "0.1..x:".$pdf->x." ,  y:".$pdf->y." , start_x:".$start_x." , start_y:".$start_y." , (check_h=$check_h)<br>";
		$pdf->y = $start_y; $pdf->x += 50;
//		$keep_x = $pdf->x;
		$pdf->MultiCellThaiCut(15,$check_h,($NUMBER_DISPLAY==2 ? convert2thaidigit($NUM_PER) : $NUM_PER),$border,'R',0,1);
		$pdf->x = $keep_x;
		$pdf->y = $start_y;		
		
		return $check_h;
	}

	$search_condition = "";

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
//	$dept = ($MINISTRY==$DEPARTMENT_NAME ? $DEPARTMENT_NAME : $MINISTRY." ".$DEPARTMENT_NAME);
	$deptorg = call_find_dept_name($ORG_ID1);
	$report_title = "แผนภูมิแสดงโครงสร้างและอัตรากำลัง||$deptorg";
	$report_code = "STRUCTURE_".($BYASS=="Y"?"BY_ASS":"BY_LAW");
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
	
	$start_x = $pdf->x;			$start_y = $pdf->y;
	$pdf->x = $pdf->lMargin+10;

	if ($BKK_FLAG!=1) $dept -= 1;
	if ($dept==1) $search_condition = "and e.ORG_ID_REF = $ORG_ID";
	else if ($dept==2) $search_condition = "and a.DEPARTMENT_ID = $ORG_ID";
	else if ($dept==3) $search_condition = "and b.ORG_ID = $ORG_ID";
	else if ($dept==4) $search_condition = "and b.ORG_ID_1 = $ORG_ID";
	else if ($dept==5) $search_condition = "and b.ORG_ID_2 = $ORG_ID";
	else $search_condition = "";
	
	$a_buff = explode(",",$deptorg);
	$first_bar = $a_buff[0];
	if ($dept==1) 
		$cmd = " select 	e.ORG_ID_REF as MINISTRY_ID, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, 
										f.PL_SEQ_NO, b.PL_CODE as PL_CODE, f.PL_NAME as PL_NAME, count(*) as CNT
						 from	PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_ORG e, PER_LINE f 
						 where	a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=e.ORG_ID(+) and b.PL_CODE=f.PL_CODE(+) 
										and (a.PER_TYPE = 1) and (a.PER_STATUS in (1)) $search_condition
						group by	e.ORG_ID_REF, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, f.PL_SEQ_NO, b.PL_CODE, f.PL_NAME
						order by	e.ORG_ID_REF, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, f.PL_SEQ_NO, b.PL_CODE ";
	else 
		$cmd = " select 	e.ORG_ID_REF as MINISTRY_ID, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, 
										 c.ORG_CODE, b.ORG_ID, f.PL_SEQ_NO, b.PL_CODE as PL_CODE, f.PL_NAME as PL_NAME, count(*) as CNT
						 from	PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_ORG e, PER_LINE f 
						 where	a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=e.ORG_ID(+) and b.PL_CODE=f.PL_CODE(+) 
										and (a.PER_TYPE = 1) and (a.PER_STATUS in (1)) $search_condition
						group by	e.ORG_ID_REF, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, 
											f.PL_SEQ_NO, b.PL_CODE, f.PL_NAME
						order by	e.ORG_ID_REF, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, 
										f.PL_SEQ_NO, b.PL_CODE ";
	//echo "$cmd<br>";
	$count_sub_tree = $db_dpis2->send_cmd($cmd);
	$count_head_total = 0;
	while($data2 = $db_dpis2->get_array()) {
		$count_head_total += $data2[CNT];
	}

	$pdf->x=116;
	$h = print_box($first_bar, $count_head_total, 1);
	$point_main_x=148; $point_main_y=$pdf->y+$h;	// ตำแหน่งหลักในการลากเส้นเชื่อมโยง

	$pdf->x = $pdf->lMargin+15;
	$pdf->y += 15;
	$start_x = $pdf->x;			$start_y = $pdf->y;
	$start_first_y = $start_y; 	$start_first_x = $start_x;
	$max_h = 0;
	
	$cmd = "	select ORG_ID , ORG_NAME, ORG_ID_REF, ORG_ACTIVE
					 from $ORGTAB where ORG_ID_REF = $ORG_ID 
					order by ORG_ACTIVE DESC, ORG_SEQ_NO, ORG_CODE	";

	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo "cmd=$cmd ($count_data)<br>";
//	exit("me terminate");
	$arr_content = (array) null;
	if($count_data){
		$line = 0; $keep_line = 0; $col = 0; $max_line=0;
		$a_line[0] = 0; $a_line[1] = 0; $a_line[2] = 0; $a_line[3] = 0;
		while($data = $db_dpis->get_array()) {
				// เป็นข้อมูล จำนวนบุคคลากรสำหรับตัวลูก จึง check ลดลง 1 ขั้น
				if ($dept==1) $search_condition = "and a.DEPARTMENT_ID = ".$data[ORG_ID]."";
				else if ($dept==2) $search_condition = "and b.ORG_ID = ".$data[ORG_ID]."";
				else if ($dept==3) $search_condition = "and b.ORG_ID_1 = ".$data[ORG_ID]."";
				else if ($dept==4) $search_condition = "and b.ORG_ID_2 = ".$data[ORG_ID]."";
				else $search_condition = "";
				if ($dept==1) 
					$cmd1 = " select 	e.ORG_ID_REF as MINISTRY_ID, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, 
														f.PL_SEQ_NO, b.PL_CODE as PL_CODE, f.PL_NAME as PL_NAME, count(*) as CNT
									 from	PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_ORG e, PER_LINE f 
									 where	a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=e.ORG_ID(+) and b.PL_CODE=f.PL_CODE(+) 
													and (a.PER_TYPE = 1) and (a.PER_STATUS in (1)) $search_condition
									group by	e.ORG_ID_REF, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, f.PL_SEQ_NO, b.PL_CODE, f.PL_NAME
									order by	e.ORG_ID_REF, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, f.PL_SEQ_NO, b.PL_CODE ";
				else
					$cmd1 = " select 	e.ORG_ID_REF as MINISTRY_ID, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, 
													 c.ORG_CODE, b.ORG_ID, f.PL_SEQ_NO, b.PL_CODE as PL_CODE, f.PL_NAME as PL_NAME, count(*) as CNT
									 from	PER_PERSONAL a, PER_POSITION b, PER_ORG c, PER_ORG e, PER_LINE f 
									 where	a.POS_ID=b.POS_ID(+) and b.ORG_ID=c.ORG_ID(+) and a.DEPARTMENT_ID=e.ORG_ID(+) and b.PL_CODE=f.PL_CODE(+) 
													and (a.PER_TYPE = 1) and (a.PER_STATUS in (1)) $search_condition
									group by	e.ORG_ID_REF, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, 
														f.PL_SEQ_NO, b.PL_CODE, f.PL_NAME
									order by	e.ORG_ID_REF, e.ORG_SEQ_NO, e.ORG_CODE, a.DEPARTMENT_ID, c.ORG_SEQ_NO, c.ORG_CODE, b.ORG_ID, 
													f.PL_SEQ_NO, b.PL_CODE ";
				$count_sub_tree = $db_dpis2->send_cmd($cmd1);
				$count_total = 0;
				while($data2 = $db_dpis2->get_array()) {
					$count_total += $data2[CNT];
				}

				if ($count_total > 0) {
					// find min line
					$minline = 9999;
					for($cc = 0; $cc < 4; $cc++) {
//						echo "$cc--minline=$minline , a_line=".$a_line[$cc]."<br>";
						if ($a_line[$cc] < $minline) { 
							$minline = $a_line[$cc];
							$col = $cc;
//							echo "$cc--minline=$minline , col=$col<br>";
						}
					}
//					echo "minline=$minline , col=$col<br>";
					// find min line
					$line = $a_line[$col];
					$arr_content[$line][$col][type] = "head";
					$arr_content[$line][$col][name] = $data[ORG_NAME];
					$arr_content[$line][$col][count] = $count_total;
//					echo "[$line][$col] head ".$data[ORG_NAME]." ".$count_total."<br>";
					$line++;

					$cnt = $db_dpis2->send_cmd($cmd1);
					if ($cnt) {
						while($data2 = $db_dpis2->get_array()) {
							$arr_content[$line][$col][type] = "detail";
							$arr_content[$line][$col][name] = $data2[PL_NAME];
							$arr_content[$line][$col][count] = $data2[CNT];
//							echo "[$line][$col] detail ".$data2[PL_NAME]." ".$data2[CNT]."<br>";
							$line++;
						}
						if ($line > $max_line) $max_line = $line;
						$a_line[$col] = $line;
//						echo "$col--max_line=$max_line , a_line=".$a_line[$col]."<br>";
					} // end if ($cnt)
					$col++;
					if ($col > 3) {
						$col = 0;
						$line = $max_line;		// ครบ 4 col ขึ้นบรรทัดใหม่
						$keep_line = $line;
					} else {
						$line = $keep_line;		// ใน 4 col บรรทัดให้เริ่มเท่ากัน
					}
				} // end if ($count_total > 0)
		} // end while
		$a_lastline_high[0] = 0; $a_lastline_high[1] = 0; $a_lastline_high[2] = 0; $a_lastline_high[3] = 0;
		$next_y[0] = $start_first_y; $next_y[1] = $start_first_y; $next_y[2] = $start_first_y; $next_y[3] = $start_first_y;
		foreach($arr_content as $line => $arr_line) {
//			echo "x:".$pdf->x.", y:".$pdf->y."<br>";
			$max_h = 0;
			if ($pdf->y+40 > $pdf->h-$pdf->bMargin) {
//				echo "pdf->y=".$pdf->y." , max_h=$max_h , pdf->h=".$pdf->h." , pdf->bMargin=".$pdf->bMargin."<br>";
//				echo "3..x:".$pdf->x." ,  y:".$pdf->y." , start_x:".$start_x." , start_y:".$start_y." , ($start_first_x, $start_first_y)<br>";
				$pdf->AddPage();
				$pdf->y = $start_first_y - 18;
				$pdf->x=116;
				$h = print_box($first_bar, $count_head_total, 1);
				$point_main_x=148; $point_main_y=$pdf->y+$h;	// ตำแหน่งหลักในการลากเส้นเชื่อมโยง
//				echo "3...1....".$pdf->x." , ".$pdf->y."<br>";
				$pdf->x = $pdf->lMargin+5;
				$pdf->y += $h;
//				echo "3...2....".$pdf->x." , ".$pdf->y."<br>";
				$start_x = $pdf->x;			$start_y = $pdf->y;
//				$start_first_y = $start_y; 	$start_first_x = $start_x;
				$max_h = 0;
				$next_y[0] = $start_y; $next_y[1] = $start_y; $next_y[2] = $start_y; $next_y[3] = $start_y;
//				echo "3...3....".$pdf->x." , ".$pdf->y."<br>";
//			} else {
//				$pdf->y += $high_box;	//$max_h;
//				$next_y = $pdf->y + $high_box;
			}
			foreach($arr_line as $col => $arr_val) {
//				echo "[$line][$col]".$arr_val[type]." ".$arr_val[name]." ".$arr_val[count]."<br>";
				$pdf->y = $next_y[$col] + $a_lastline_high[$col];
				$pdf->x = $pdf->lMargin + 5 + ($col * 70);
				if ($arr_val[type] == "head") {
//					if ($last_type=="detail") {
//						echo "3..x:".$pdf->x." ,  y:".$pdf->y." , start_x:".$start_x." , start_y:".$start_y." , ($start_first_x, $start_first_y)<br>";
/*						$pdf->AddPage();
						$pdf->y = $pdf->tMargin+14;
						$pdf->x=116;
						$h = print_box($first_bar, $count_head_total, 1);
						$point_main_x=148; $point_main_y=$pdf->y+$h;	// ตำแหน่งหลักในการลากเส้นเชื่อมโยง
//						echo "3...1....".$pdf->x." , ".$pdf->y."<br>";
						$pdf->x = $pdf->lMargin;
						$pdf->y += 15;
//						echo "3...2....".$pdf->x." , ".$pdf->y."<br>";
						$start_x = $pdf->x;			$start_y = $pdf->y;
//						$start_first_y = $start_y; 	$start_first_x = $start_x;
						$max_h = 0;*/
//					}
					if ($line) $pdf->y += 10;
					$high_box = print_box($arr_val[name], $arr_val[count], 1);
					$a_lastline_high[$col] = $high_box;
					$next_y[$col] = $pdf->y;
//					echo "head..[$col]..".$arr_val[name]." , ".$arr_val[count]." , ".$a_lastline_high[$col]."<br>";

					$point_end_x = $pdf->x+30; $point_end_y = $pdf->y; 
					$point_break2_x = $point_end_x; $point_break2_y = $point_end_y - 4;
					$point_break1_x = $point_main_x; $point_break1_y = $point_break2_y;	

//	ลากเส้น
					$pdf->line($point_main_x,$point_main_y,$point_break1_x,$point_break1_y);
					$pdf->line($point_break1_x,$point_break1_y,$point_break2_x,$point_break2_y);
					$pdf->line($point_break2_x,$point_break2_y,$point_end_x,$point_end_y);
//	จบลากเส้น
				} else {	// detail
					$high_box = print_box($arr_val[name], $arr_val[count], 0);
					$a_lastline_high[$col] = $high_box;
					$next_y[$col] = $pdf->y;
//					echo "detail..[$col]..".$arr_val[name]." , ".$arr_val[count]." , ".$a_lastline_high[$col]."<br>";
				}
				if ($high_box > $max_h) $max_h = $high_box;
//				if (!($last_type=="head" && $arr_val[type]=="detail")) $max_h = 7;
				$last_type = $arr_val[type];
			} // end foreach col
/*			if ($pdf->y+30 > $pdf->h-$pdf->bMargin) {
//				echo "pdf->y=".$pdf->y." , max_h=$max_h , pdf->h=".$pdf->h." , pdf->bMargin=".$pdf->bMargin."<br>";
//				echo "3..x:".$pdf->x." ,  y:".$pdf->y." , start_x:".$start_x." , start_y:".$start_y." , ($start_first_x, $start_first_y)<br>";
				$pdf->AddPage();
				$pdf->y = $start_first_y - 18;
				$pdf->x=116;
				$h = print_box($first_bar, $count_head_total, 1);
				$point_main_x=148; $point_main_y=$pdf->y+$h;	// ตำแหน่งหลักในการลากเส้นเชื่อมโยง
//				echo "3...1....".$pdf->x." , ".$pdf->y."<br>";
				$pdf->x = $pdf->lMargin+5;
				$pdf->y += 15;
//				echo "3...2....".$pdf->x." , ".$pdf->y."<br>";
				$start_x = $pdf->x;			$start_y = $pdf->y;
//				$start_first_y = $start_y; 	$start_first_x = $start_x;
				$max_h = 0;
				$next_y[0] = $start_y; $next_y[1] = $start_y; $next_y[2] = $start_y; $next_y[3] = $start_y;
//				echo "3...3....".$pdf->x." , ".$pdf->y."<br>";
//			} else {
//				$pdf->y += $high_box;	//$max_h;
//				$next_y = $pdf->y + $high_box;
			}*/
		} // end foreach line
	}else{
		$result = $pdf->MultiCellThaiCut(200,7,"********** ไม่มีข้อมูล **********",0,'C',0,0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if

	$pdf->close();
	$pdf->Output();	
?>