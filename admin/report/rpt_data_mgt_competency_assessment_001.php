<?
	include("../../php_scripts/connect_database.php");
	include("../php_scripts/pdf_wordarray_thaicut.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");
	include("../php_scripts/load_per_control.php");
	
	define('FPDF_FONTPATH','../../PDF/font/');
	include ("../../PDF/fpdf.php");
	include ("../../PDF/pdf_extends_DPIS.php");

	ini_set("max_execution_time", $max_execution_time);

	include ("rpt_data_mgt_competency_assessment_001_format.php");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
//	echo "$search_test_date_from-$search_test_date_to<br>";
	$arr_monstr = array("มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฏาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");
	if ($search_test_date_from) {
		$s_date_from = explode("/",$search_test_date_from);
		$s_date_from_str = "ตั้งแต่ ".$arr_monstr[(int)$s_date_from[1]-1]." พ.ศ.".$s_date_from[2];
	} else $s_date_from_str = "";
	if ($search_test_date_to) {
		$s_date_to = explode("/",$search_test_date_to);
		$s_date_to_str = "ถึง ".$arr_monstr[(int)$s_date_to[1]-1]." พ.ศ.".$s_date_to[2];
	} else $s_date_to_str = "";

	$unit="mm";
	$paper_size="A4";
	$lang_code="TH";
	$company_name = "";
	$report_title = "ภาพรวมการประเมินซ้ำคะแนน".($dup_way==1 ? "สูงขึ้น" : "ลดลง").($s_date_from_str ? " " : "").$s_date_from_str.($s_date_from_str && $s_date_to_str ? " " : "").$s_date_to_str;
	$report_code = "";
	$orientation='P';

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
	$pdf->SetAutoPageBreak(true,10);

	$search_condition = substr($search_condition,4);
	$search_condition = str_replace("a.","",$search_condition);
	$temp_start =  save_date($search_test_date_from);
	$temp_end =  save_date($search_test_date_to);
	if ($temp_start) $arr_condi[] = "CA_TEST_DATE>='$temp_start'";
	if ($temp_end) $arr_condi[] = "CA_TEST_DATE<='$temp_end'";
	if (count($arr_condi) > 0)  $where = "where ".implode(" and ", $arr_condi); else $where = "";
	
	$cmd = " select 	PN_CODE, CA_NAME, CA_SURNAME, CA_TEST_DATE, ORG_CODE, CA_MEAN
					 from 		PER_MGT_COMPETENCY_ASSESSMENT
					 $where
					 order by ORG_CODE, CA_NAME, CA_SURNAME, CA_TEST_DATE";
	$cmd = stripslashes($cmd);
	$count_data = $db_dpis->send_cmd($cmd);
//	$db_dpis->show_error();
//	echo "cmd=$cmd ($count_data)<br>";		//	exit;
	$data_count = $data_row = 0;
	$arr_cnt = (array) null;
	$cnt = 0;
	$tot_target = (array) null;
	$tot_all = (array) null;
	$LAST_PERSON_KEY = "";
     while ($data = $db_dpis->get_array()) {
            $data_row++;

            $PN_CODE = trim($data[PN_CODE]);
            $CA_NAME = trim($data[CA_NAME]);		
            $CA_SURNAME = trim($data[CA_SURNAME]);
			$CA_TEST_DATE = trim($data[CA_TEST_DATE]);
            $ORG_CODE = trim($data[ORG_CODE]);
            $TMP_CA_MEAN = trim($data[CA_MEAN]);
			
			$cnt++;
			$PERSON_KEY = $PN_CODE.$CA_NAME." ".$CA_SURNAME;
			if ($LAST_PERSON_KEY && $LAST_PERSON_KEY!=$PERSON_KEY) {
				// 	ประมวลผลจำนวนคน
				if ($cnt >= 2 && $cnt <= 5) {
					if ($dup_way==1) {	// หาจำนวนที่สูงขึ้น
						if ($TMP_CA_MEAN > $LAST_CA_MEAN)  { // กรณึ คะแนนสูงขึ้น
							$arr_cnt[$LAST_ORG_CODE][$cnt]++;
							$tot_target[$LAST_ORG_CODE]++;
							if ($cnt==2) $tot_col2++;
							else if ($cnt==3) $tot_col3++;
							else if ($cnt==4) $tot_col4++;
							else $tot_col5++;
						} else {
							$arr_cnt[$LAST_ORG_CODE][0]++;
							$tot_col0++;
						}
						$tot_all[$LAST_ORG_CODE]++;
						$tot_colall++;
					} else { // กรณี คะแนนลดลง
						if ($TMP_CA_MEAN < $LAST_CA_MEAN)  { // กรณึ คะแนนลดลง
							$arr_cnt[$LAST_ORG_CODE][$cnt]++;
							$tot_target[$LAST_ORG_CODE]++;
							if ($cnt==2) $tot_col2++;
							else if ($cnt==3) $tot_col3++;
							else if ($cnt==4) $tot_col4++;
							else $tot_col5++;
						} else {
							$arr_cnt[$LAST_ORG_CODE][0]++;
							$tot_col0++;
						}
						$tot_all[$LAST_ORG_CODE]++;
						$tot_colall++;
					}
				} else if ($cnt > 5) {
					if ($dup_way==1) {	// หาจำนวนที่สูงขึ้น
						if ($TMP_CA_MEAN > $LAST_CA_MEAN) {   // กรณึ คะแนนสูงขึ้น
							$arr_cnt[$LAST_ORG_CODE][0]++;
							$tot_col0++;
						}
						$tot_all[$LAST_ORG_CODE]++;
						$tot_colall++;
					} else {
						if ($TMP_CA_MEAN < $LAST_CA_MEAN) {   // กรณึ คะแนนลดลง
							$arr_cnt[$LAST_ORG_CODE][0]++;
							$tot_col0++;
						}
						$tot_all[$LAST_ORG_CODE]++;
						$tot_colall++;
					}
				} else {	// $cnt <= 1 คือไม่อยู่ในเงื่อนไขที่ต้องการแต่มีค่าเพราะต้องการแสดงค่าในตาราง
					$arr_cnt[$LAST_ORG_CODE][0]++;
				}
				//  reset ค่า สำหรับคนต่อไป
				$LAST_PERSON_KEY = $PERSON_KEY;
				$LAST_CA_MEAN = $TMP_CA_MEAN;
				$LAST_ORG_CODE = $ORG_CODE;
				$cnt = 0;
			} else if (!$LAST_PERSON_KEY) {
				//  reset ค่า สำหรับคนแรก
				$LAST_PERSON_KEY = $PERSON_KEY;
				$LAST_CA_MEAN = $TMP_CA_MEAN;
				$LAST_ORG_CODE = $ORG_CODE;
			} else {	// ชื่อเป็นคนเดิม
				if ($LAST_ORG_CODE!= $ORG_CODE) { // ถ้า รหัสส่วนราชการเปลี่่ยน
					// 	ประมวลผลจำนวนคน
					if ($cnt >= 2 && $cnt <= 5) {
						if ($dup_way==1) {	// หาจำนวนที่สูงขึ้น
							if ($TMP_CA_MEAN > $LAST_CA_MEAN) {   // กรณึ คะแนนสูงขึ้น
								$arr_cnt[$LAST_ORG_CODE][$cnt]++;
								$tot_col[$cnt]++;
								$tot_target[$LAST_ORG_CODE]++;
								if ($cnt==2) $tot_col2++;
								else if ($cnt==3) $tot_col3++;
								else if ($cnt==4) $tot_col4++;
								else $tot_col5++;
							} else {
								$arr_cnt[$LAST_ORG_CODE][0]++;
								$tot_col[0]++;
							}
							$tot_all[$LAST_ORG_CODE]++;
						} else {	// หาจำนวนที่ลดลง
							if ($TMP_CA_MEAN < $LAST_CA_MEAN) {   // กรณึ คะแนนลดลง
								$arr_cnt[$LAST_ORG_CODE][$cnt]++;
								$tot_col[$cnt]++;
								$tot_target[$LAST_ORG_CODE]++;
								if ($cnt==2) $tot_col2++;
								else if ($cnt==3) $tot_col3++;
								else if ($cnt==4) $tot_col4++;
								else $tot_col5++;
							} else {
								$arr_cnt[$LAST_ORG_CODE][0]++;
								$tot_col[0]++;
							}
							$tot_all[$LAST_ORG_CODE]++;
						}
					} else if ($cnt > 5) {
						if ($dup_way==1) {	// หาจำนวนที่สูงขึ้น
							if ($TMP_CA_MEAN > $LAST_CA_MEAN) {   // กรณึ คะแนนสูงขึ้น
								$arr_cnt[$LAST_ORG_CODE][0]++;
								$tot_col0++;
							}
							$tot_all[$LAST_ORG_CODE]++;
							$tot_colall++;
						} else {
							if ($TMP_CA_MEAN < $LAST_CA_MEAN) {   // กรณึ คะแนนลดลง
								$arr_cnt[$LAST_ORG_CODE][0]++;
								$tot_col0++;
							}
							$tot_all[$LAST_ORG_CODE]++;
							$tot_colall++;
						}
					} else {	// $cnt <= 1 คือไม่อยู่ในเงื่อนไขที่ต้องการแต่มีค่าเพราะต้องการแสดงค่าในตาราง
						$arr_cnt[$LAST_ORG_CODE][0]++;
					}
					$LAST_CA_MEAN = $TMP_CA_MEAN;
					$LAST_ORG_CODE = $ORG_CODE;
					$cnt = 0;
				}
			}
			$data_count++;
	} // end while
	
	$msort_result = array_multisort($arr_cnt, SORT_ASC, SORT_STRING, $tot_target, SORT_ASC, SORT_STRING, $tot_all, SORT_ASC, SORT_STRING);
//	echo "<pre>"; print_r($arr_content); echo "</pre>";
	
	if($count_data){
		$pdf->AutoPageBreak = false;
		$head_text1 = implode(",", $heading_text);
		$head_width1 = implode(",", $heading_width);
		$head_align1 = implode(",", $heading_align);
		$head_font_style = implode(",", $heading_font_style);
		$head_font_size = implode(",", $heading_font_size);
		$head_fill_color = implode(",", $heading_fill_color);
		$head_font_color = implode(",", $heading_font_color);
		$head_border = implode(",", $heading_border);
		$col_function = implode(",", $column_function);
//		echo "$head_text1, $head_width1, $head_align1, $col_function<br>";
		$result = $pdf->open_tab($head_text1, $head_width1, 7, $head_border, $head_align1, "", $head_font_size, $head_font_style, $head_font_color, $head_fill_color, $COLUMN_FORMAT, $col_function);
		if (!$result) echo "****** error ****** on open table for $table<br>";

		$tot_font_size1 = $tot_font_size;
		$arr_data = array("",0,0,0,0,0,0,0,0,0,0,0);
		$arr_data[0] = "ทุกส่วนราชการ";	$tot_font_size1[0] = 12;
		$arr_data[1] = $tot_col2;
		$arr_data[2] = $tot_col2 / $tot_colall * 100;
		$arr_data[3] = $tot_col3;
		$arr_data[4] = $tot_col3 / $tot_colall * 100;
		$arr_data[5] = $tot_col4;
		$arr_data[6] = $tot_col4 / $tot_colall * 100;
		$arr_data[7] = $tot_col5;
		$arr_data[8] = $tot_col5 / $tot_colall * 100;
		$arr_data[9] = ($tot_col2+$tot_col3+$tot_col4+$tot_col5);
		$arr_data[10] = ($tot_col2+$tot_col3+$tot_col4+$tot_col5) / $tot_colall * 100;
		$arr_data[11] = $tot_colall;
		
		$result = $pdf->add_data_tab($arr_data, 7, $data_border, $data_align, "", $tot_font_size1, $tot_font_style, $tot_font_color, $tot_fill_color);
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

		$key_index = array(-1,-1,1,3,5,7);		// map key ตามตาราง
		$arr_tot_col = array("รวม",0,0,0,0,0,0,0,0,0,0,0);
		foreach($arr_cnt as $key => $arr_cnt2) {
			$arr_data = array("",0,0,0,0,0,0,0,0,0,0,0);
			$arr_data[0] = "$key";
			$tot_target1= $tot_target[$key];
			$tot_all1 = $tot_all[$key];
//			echo "key=$key tot_target=$tot_target1 tot_all=$tot_all1";
			foreach($arr_cnt2 as $key1 => $val) {
				if ($key1 >= 2) {
					$arr_data[$key_index[$key1]] = $val;
					$arr_data[$key_index[$key1]+1] = $val / $tot_all1 * 100;
					$arr_tot_col[$key_index[$key1]] += $val;
//					echo " (1) key1=$key1 val=$val";
//				} else {
//					echo " (2) key1=$key1 val=$val";
				}
			}
//			echo "<br>";
			$arr_data[9] = $tot_target1;
			$arr_tot_col[9] += $tot_target1;
			$arr_data[10] = $tot_target1 / $tot_all1 * 100;
			$arr_data[11] = $tot_all1;
			$arr_tot_col[11] += $tot_all1;

			$result = $pdf->add_data_tab($arr_data, 7, $data_border, $data_align, "", $data_font_size, $data_font_style, $data_font_color, $data_fill_color);
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

//			echo "$data_count $ORDER $PER_NAME $POSITION<br>";
		} // end for

		$arr_data = array("",0,0,0,0,0,0,0,0,0,0,0);
		$arr_data[0] = $arr_tot_col[0];
		$arr_data[1] = $arr_tot_col[1];
		$arr_data[2] = $arr_tot_col[1] / $arr_tot_col[11] * 100;
		$arr_data[3] = $arr_tot_col[3];
		$arr_data[4] = $arr_tot_col[3] / $arr_tot_col[11] * 100;
		$arr_data[5] = $arr_tot_col[5];
		$arr_data[6] = $arr_tot_col[5] / $arr_tot_col[11] * 100;
		$arr_data[7] = $arr_tot_col[7];
		$arr_data[8] = $arr_tot_col[7] / $arr_tot_col[11] * 100;
		$arr_data[9] = $arr_tot_col[9];
		$arr_data[10] = $arr_tot_col[9] / $arr_tot_col[11] * 100;
		$arr_data[11] = $arr_tot_col[11];

		$result = $pdf->add_data_tab($arr_data, 7, $data_border, $data_align, "", $tot_font_size, $tot_font_style, $tot_font_color, $tot_fill_color);
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

		$pdf->close_tab(""); 
	}else{
		$result = $pdf->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "L", "", "14", "b", 0, 0);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if

	$pdf->close();
	$pdf->Output();

	ini_set("max_execution_time", 30);
?>