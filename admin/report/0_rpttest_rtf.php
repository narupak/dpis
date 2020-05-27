<?	
	include("../../php_scripts/connect_database.php");
	include("../../php_scripts/calendar_data.php");
	include ("../php_scripts/function_share.php");

	require("../../RTF/rtf_class.php");

//	session_cache_limiter("nocache");
	session_cache_limiter("private");
	session_start();

	$a_set_of_colors = (array) null;
	$color_buff = explode("^",$xlsFmtTitle_color);
	$a_set_of_colors[] = $color_buff[1];
	$fmtTitle_col_idx = 1;
	$color_buff = explode("^",$xlsFmtTitle_bgcolor);
	$a_set_of_colors[] = $color_buff[1];
	$fmtTitle_bgcol_idx = 2;
	$color_buff = explode("^",$xlsFmtSubTitle_color);
	$a_set_of_colors[] = $color_buff[1];
	$fmtSubTitle_col_idx = 3;
	$color_buff = explode("^",$xlsFmtSubTitle_bgcolor);
	$a_set_of_colors[] = $color_buff[1];
	$fmtSubTitle_bgcol_idx = 4;
	$color_buff = explode("^",$xlsFmtTableHeader_color);
	$a_set_of_colors[] = $color_buff[1];
	$fmtTableHeader_col_idx = 5;
	$color_buff = explode("^",$xlsFmtTableHeader_bgcolor);
	$a_set_of_colors[] = $color_buff[1];
	$fmtTableHeader_bgcol_idx = 6;
	$color_buff = explode("^",$xlsFmtTableDetail_color);
	$a_set_of_colors[] = $color_buff[1];
	$fmtTableDetail_col_idx = 7;
	$color_buff = explode("^",$xlsFmtTableDetail_bgcolor);
	$a_set_of_colors[] = $color_buff[1];
	$fmtTableDetail_bgcol_idx = 8;

//	echo "xlsFmtTitle_color=$xlsFmtTitle_color, xlsFmtTitle_bgcolor=$xlsFmtTitle_bgcolor, xlsFmtSubTitle_color=$xlsFmtSubTitle_color, xlsFmtSubTitle_bgcolor=$xlsFmtSubTitle_bgcolor, xlsFmtTableHeader_color=$xlsFmtTableHeader_color, xlsFmtTableHeader_bgcolor=$xlsFmtTableHeader_bgcolor, xlsFmtTableDetail_color=$xlsFmtTableDetail_color, xlsFmtTableDetail_bgcolor=$xlsFmtTableDetail_bgcolor<br>";

	$set_of_colors = implode("^",$a_set_of_colors);

	if (!$font) $font = "AngsanaUPC";

	$IMG_PATH = "../../attachment/pic_personal/";
		
	if($_GET[NUMBER_DISPLAY])	$NUMBER_DISPLAY = $_GET[NUMBER_DISPLAY];

	include ("0_rpttest_rtf_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น
	
	$today = date("Y-m-d H:i:s");
	$dt = explode(" ",$today);
	$print_today =  show_date_format($dt[0],1);
	$print_time = $dt[1];
//	echo "[$today] $print_today $print_time<br>";

	$fname= "0_rpttest_rtf.rtf";

//	RTF $papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab="";
	$paper_size="a4";
	$orientation='L';
	$RTF = new RTF($paper_size, ".7in", ".3in", ".5in", ".1in", $orientation, $set_of_colors);

	$RTF->set_default_font($font, 14);
//	echo "default font_id::".$RTF->dfl_FontID."<br>";	

	$company_name = "บ.แสงดำ จำกัด";
	$report_title = "รายงานการทดสอบ class RTF เพื่อเตรียมการปรับปรุงการ เขียนรายงานใหม่";
	$report_code = "RPTTEST1";

	print_header();
	print_footer();

	function print_header() {
		global $RTF, $fmtTitle_col_idx, $fmtTitle_bgcol_idx, $fmtSubTitle_col_idx, $fmtSubTitle_bgcol_idx;
		global $company_name, $report_title, $report_code, $print_today, $print_time;
		
		// text1^font^size^style^color^fill^align^border
		$prt_text_header = "$company_name^^20^B^$fmtTitle_col_idx^$fmtTitle_bgcol_idx^l^||$report_title^^28^BU^$fmtTitle_col_idx^$fmtTitle_bgcol_idx^c^||$report_code^^20^B^$fmtTitle_col_idx^$fmtTitle_bgcol_idx^r^";
		//	$RTF->add_footer($text, $bottomUp=720, $brdSurround=false)
		$RTF->add_header($prt_text_header, "1", false);
	} // function footer
	
	function print_footer() {
		global $RTF, $fmtTitle_col_idx, $fmtTitle_bgcol_idx, $fmtSubTitle_col_idx, $fmtSubTitle_bgcol_idx;
		global $company_name, $report_title, $report_code, $print_today, $print_time;
		
		// text1^font^size^style^color^fill^align^border
		$prt_text_footer = "ผู้ออกรายงาน พงษ์ศักดิ์ กุลประฑีปัญญา  สถาบันการศึกษาวิชาการคอมพิวเตอร์เพื่ออนาคต^^16^BI^$fmtTitle_col_idx^$fmtTitle_bgcol_idx^l^||พิมพ์เมื่อ $print_today $print_time หน้า [#page_no#]^^20^B^$fmtTitle_col_idx^$fmtTitle_bgcol_idx^r^";
		//	$RTF->add_footer($text, $bottomUp=720, $brdSurround=false)
		$RTF->add_footer($prt_text_footer, "1", false);
	} // function footer

	$RTF->set_font($font, 20);
	$RTF->color("0");	// 0=BLACK
	$RTF->add_text($RTF->bold(1) . $report_title . $RTF->bold(0) , "center");
	$RTF->new_line();
	$RTF->paragraph();

	$head_text1 = implode(",", $heading_text);
	$head_width1 = implode(",", $heading_width);
	$head_align1 = implode(",", $heading_align);
	$col_function = implode(",", $column_function);
//	echo "head_text:$head_text1<br>";
	$tab_align = "center";
	$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, false, $tab_align);
	if (!$result) echo "****** error ****** on open table for $table<br>";

	$count_data = 4;
	$arr_data1[] = array("บรรทัดที่ 1",230,310,240,210,130.25,255);
	$arr_data1[] = array("บรรทัดที่ 2",110,150,320,280,400.50,165);
	$arr_data1[] = array("บรรทัดที่ 3",540,250,420,280,200.75,265);
	$arr_data1[] = array("บรรทัดที่ 4",260,350,120,380,100.33,465);

	if($count_data){
		$arr_t = array("รวม",0,0,0,0,0,0);
		$gs = 0;
		for($loop = 0; $loop < 10; $loop++) {
			for($ii = 0; $ii < $count_data; $ii++) {
				$s = 0;
				$arr_data = (array) null;
				for($jj = 0; $jj < count($arr_data1[$ii]); $jj++) {
					$arr_data[] = $arr_data1[$ii][$jj];
					if ($jj > 0) {
						$s += $arr_data1[$ii][$jj];
						$arr_t[$jj] += $arr_data1[$ii][$jj];
					}
				}
				$arr_data[] = $s;
	//			echo "$ii--sum=$s<br>";
	
				$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
				if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
				$gs += $s;
			}
		}
		
		$arr_t[] = $gs;

		$arr_data = $arr_t;

		$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

//		$arr_data = array("สรุป","<**1**>243","<**1**>243","<**2**>567","<**2**>567","<**3**>192","<**3**>192","777");
		$arr_data = array("สรุป","<**1**>243","<**1**>243","<**1**>243","<**2**>567","<**2**>567","<**2**>567","777");

		$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		
		$arr_data = array("สรุป 2","<**1**>1235","<**1**>1235","<**2**>7231","<**2**>7231","<**3**>7822","<**3**>7822","563823");
		$arr_align = array("L","R","R","R","R","R","R","R");

		$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $arr_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";

		$arr_data = array("สรุป 3","<**1**>3333","<**1**>3333","<**2**>4444","<**2**>4444","<**3**>5555","<**3**>5555","123446");
		$arr_align = array("R","L","L","R","R","C","C","R");

		$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $arr_align, "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		
	}else{
		$result = $RTF->add_text_line("********** ไม่มีข้อมูล **********", 7, "", "L", "", "14", "b", $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
		if (!$result) echo "****** error ****** add text line to table at record count = $data_count <br>";
	} // end if
	
	$RTF->close_tab(); 

	include ("0_rpttest_rtf2_format.php");	// ใช้เฉพาะในส่วนการแปลง COLUMN_FORMAT เท่านั้น

	$RTF->new_line();
	$RTF->set_font($font, 20);
	$RTF->color("0");	// 0=BLACK
	$RTF->add_text($RTF->bold(1) . "ตารางสอง ทดสอบ เรื่องการ merge row รูปภาพ และ vertical text" . $RTF->bold(0) , "center");
	$RTF->new_line();
	$RTF->paragraph();

	$head_text1 = implode(",", $heading_text);
	$head_width1 = implode(",", $heading_width);
	$head_align1 = implode(",", $heading_align);
	$col_function = implode(",", $column_function);
//	echo "head_text:$head_text1<br>";
	$tab_align = "center";
	$result = $RTF->open_tab($head_text1, $head_width1, 7, "TRHBL", $head_align1, "", "14", "b", $fmtTableHeader_col_idx, $fmtTableHeader_bgcol_idx, $COLUMN_FORMAT, $col_function, true, $tab_align);
	if (!$result) echo "****** error ****** on open table for $table<br>";

	$imgfile = "../images/imgtest.bmp";
	$count_data = 4;
	$arr_data1 = (array) null;
	$arr_data1[] = array("บรรทัดที่ 1","<&&rowone&&>ข้อความตั้งเป็นแนวสูง 123450","<&&rowone&&>ทดสอบ\n<*img*".$imgfile."*img*>\nรูปสมมุติ");
	$arr_data1[] = array("บรรทัดที่ 2","<&&row&&>","<&&row&&>");
	$arr_data1[] = array("บรรทัดที่ 3","<&&row&&>","<&&row&&>");
	$arr_data1[] = array("บรรทัดที่ 4","ไม่มีไร","<&&row&&>");

	if($count_data){
		for($ii = 0; $ii < $count_data; $ii++) {
			$arr_data = (array) null;
			$arr_style = (array) null;
			for($jj = 0; $jj < count($arr_data1[$ii]); $jj++) {
				$arr_data[] = $arr_data1[$ii][$jj];
				if ($ii == 0 && $jj == 1) $arr_style[] = "br"; else $arr_style[] = "b";	// b = bold,  r = rotate
			}

			$result = $RTF->add_data_tab($arr_data, 7, "TRHBL", $data_align, "", "14", $arr_style, $fmtTableDetail_col_idx, $fmtTableDetail_bgcol_idx);
			if (!$result) echo "****** error ****** add data to table at record count = $data_count <br>";
		}
	}

	$RTF->close_tab(); 
	
	$RTF->display($fname);
?>