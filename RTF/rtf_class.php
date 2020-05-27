<?

/*
 * **************************
 * *** RTF Class Code PHP ***
 * --------------------------
 * ** Based on a design found in phpclasses.org - By Michele Brodoloni (michele@xtnet.it)
 * ** RTF Generation Class (http://www.phpclasses.org/browse/package/3560.html)
 *
 * Fixes and implementations:
 * ---------------------------
 * 1. Small fixes
 * 2. Implementation of tables
 * 3. Setting the margins
 *
 * ===============================================================================================================
 * IMPORTANT NOTICE:
 * -----------------
 *
 * I am not responsible for any problems caused by class "RTF Class Code PHP"
 *
 * This php class is distributed as-is.
 * So do not bother for broken functions, nor any non-working thing.
 *
 * Hints and suggestions are welcome.
 *
 * ===============================================================================================================
 *
 * ******************************************************************
 * *** Author: Maury Miranda Marques - maurymmarques@gmail.com.br ***
 * ******************************************************************
 */

### START OF COLOR TABLE
define('BLACK', 	0);
define('DARKGRAY',	1);
define('LIGHTBLUE',	2);
define('CYAN',		3);
define('LIGHTGREEN',4);
define('PURPLE',	5);
define('RED', 		6);
define('YELLOW', 	7);
define('WHITE',		8);
define('BLUE', 		9);
define('DARKCYAN',  10);
define('DARKGREEN', 11);
define('DARKPURPLE',12);
define('BROWN',	    13);
define('DARKYELLOW',14);
define('GRAY',		15);
define('LIGHTGRAY', 16);
define('DARKGRAY1',	21);
define('LIGHTBLUE1',22);
define('CYAN1',		23);
define('LIGHTGREEN1',24);
define('PURPLE1',	25);
define('RED1', 		26);
define('YELLOW1', 	27);
define('WHITE1',	28);
define('BLUE1', 	29);
define('DARKCYAN1',  30);
define('DARKGREEN1', 31);
define('DARKPURPLE1',32);
define('BROWN1',	 33);
define('DARKYELLOW1',34);
define('GRAY1',		35);
define('LIGHTGRAY1', 36);
### END OF COLOR TABLE

class RTF{

	var $MyRTF;
	var $dfl_FontID;
	var $dfl_FontSize = 20;
	var $FontID;
	var $TextDecoration;
    var $previousResult = 0;
	var $paper_w;
	var $paper_h;
	var $margl;
	var $margr;
	var $margt;
	var $margb;
	var $page_w;
	var $page_h;
	var $lang_code="TH";
	
	var $map_papersize = array("letter"=>"\\paperw12240\\paperh15840\n","letter_l"=>"\\paperw15840\\paperh12240\n","ledger"=>"\\paperw15840\\paperh24480\n","ledger_l"=>"\\paperw24480\\paperh15840\n","legal"=>"\\paperw12240\\paperh20160\n","legal_l"=>"\\paperw20160\\paperh12240\n","a3"=>"\\paperw16838\\paperh23811\n","a3_l"=>"\\paperw23811\\paperh16838\n","a4"=>"\\paperw11905\\paperh16838\n","a4_l"=>"\\paperw16838\\paperh11905\n");

	var $f_table;            //onTable loop = 1
	var $f_no_head;            //onTable not print head
	var $arr_tab_head;			// array tab head
	var $arr_head_width;			// array tab head width
	var $head_fill_color;		//  table head fill color  format RRGGBB
	var $head_font_name;			//  table head font name ex  "AngsanaUPC" "cordia"
	var $head_font_size;		//  table head font size
	var $head_font_style;		//  table head font style "B" bold "I" italic "U" underline
	var $head_font_color;		//  table head font color  format RRGGBB
	var $head_border;			//  table head border
	var $arr_head_align;			//  table head align
	var $head_line_height;			//  table head line height
	var $tabdata_fill_color;		//  table data fill color  format RRGGBB
	var $tabdata_font_name;			//  table data font name ex  "AngsanaUPC" "cordia"
	var $tabdata_font_size;		//  table data font size
	var $tabdata_font_style;		//  table data font style "B" bold "I" italic "U" underline
	var $tabdata_font_color;		//  table data font color  format RRGGBB
	var $tabdata_border;		//  table column border
	var $arr_tabdata_font_name;		// array font name for tabdata
	var $arr_tabdata_font_style;		// array font style for tabdata
	var $arr_tabdata_font_size;		// array font size for tabdata
	var $arr_tabdata_font_color;		// array font color for tabdata
	var $arr_tabdata_border;			// array border for tabdata
	var $arr_tabdata_fill_color;			// array fill color for tabdata
	var $merge_rows;		//  string array table data merge row dilimeter = ,
	var $rows_image;		//  string array table image data merge row dilimeter = ,
	var $merge_rows_h;		// array height of row merge
	var $tbalign;			// table align value = left, center, rignt, N width form left (in,cm,mm,twip) and full(100% page_w)
	var $table_w;			// table width in twip
	var $tab_align;			// table align value = left, center, rignt, N width form left (in,cm,mm,twip) and full(100% page_w)
	var $report_title;			// report_title
	var $report_code;			// report_code
	var $company_name;	// company_name
	var $tab_head_title;		//	table head title  แสดง title บน head ของ table
	
	/**
	 * Creates the RTF file on RAM and writed the header
     * including the font table and the color table
	 * see also: load_color_table() e load_font_table()
	 *    $u_color_tab agument format is hex rrggbb^rrggbb^rrggbb^.....
     * @return void
	 */
	function RTF($papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient = "p", $u_color_tab=""){
		
		if (strpos($margl,"cm")!==false) $margl = $this->conv2twip("cm",$margl); 
		else if (strpos($margl,"mm")!==false) $margl = $this->conv2twip("mm",$margl);
		else if (strpos($margl,"in")!==false) $margl = $this->conv2twip("in",$margl);
		if (strpos($margr,"cm")!==false) $margr = $this->conv2twip("cm",$margr); 
		else if (strpos($margr,"mm")!==false) $margr = $this->conv2twip("mm",$margr);
		else if (strpos($margr,"in")!==false) $margr = $this->conv2twip("in",$margr);
		if (strpos($margt,"cm")!==false) $margt = $this->conv2twip("cm",$margt); 
		else if (strpos($margt,"mm")!==false) $margt = $this->conv2twip("mm",$margt);
		else if (strpos($margt,"in")!==false) $margt = $this->conv2twip("in",$margt);
		if (strpos($margb,"cm")!==false) $margb = $this->conv2twip("cm",$margb); 
		else if (strpos($margb,"mm")!==false) $margb = $this->conv2twip("mm",$margb);
		else if (strpos($margb,"in")!==false) $margb = $this->conv2twip("in",$margb);

		$this->MyRTF="{\\rtf1\\ansi\\ansicpg874\n";
        $this->load_color_table($u_color_tab);
		$this->load_font_table();
        $this->config_margin($papersize, $margl, $margr, $margt, $margb, $orient);
        $this->MyRTF .= "\n{\n\n";
		
		$this->page_w = $this->paper_w - $this->margl - $this->margr;
		$this->page_h = $this->paper_h - $this->margt - $this->margb;
	}
	
	/**
	 * Loads the color table (RGB)
	 *    $u_color_tab agument format is hex rrggbb^rrggbb^rrggbb^.....
	 *
     * @return void
	 */
	function load_color_table($u_color_tab){

		if ($u_color_tab) {
			$buff = explode("^",$u_color_tab);
			$arr_col_tab = (array) null;
			for($i = 0; $i < count($buff); $i++) {
				$arr_col_tab[] = "\\red".hexdec(substr($buff[$i],0,2))."\\green".hexdec(substr($buff[$i],2,2))."\\blue".hexdec(substr($buff[$i],4,2));
			}
			$this->MyRTF.="{\\colortbl;".implode(";",$arr_col_tab)."}";
		} else {
			$this->MyRTF.="{\\colortbl;\n".
						  "\\red70\\green70\\blue70;\\red0\\green0\\blue255;\\red0\\green255\\blue255;\n".
						  "\\red0\\green255\\blue0;\\red255\\green0\\blue255;\\red255\green0\\blue0;\n".
						  "\\red255\\green255\\blue0;\\red255\\green255\\blue255;\\red0\green0\\blue128;\n".
						  "\\red0\\green128\\blue128;\\red0\\green128\\blue0;\\red128\\green0\\blue128;\n".
						  "\\red128\\green0\\blue0;\\red128\\green128\\blue0;\\red128\\green128\\blue128;\n".
						  "\\red192\\green192\\blue192;\n".
						  "\\red80\\green80\\blue80;\\red0\\green0\\blue240;\\red0\\green200\\blue200;\n".
						  "\\red0\\green200\\blue0;\\red200\\green0\\blue200;\\red200\green0\\blue0;\n".
						  "\\red200\\green200\\blue0;\\red200\\green200\\blue200;\\red0\green0\\blue200;\n".
						  "\\red0\\green90\\blue90;\\red0\\green90\\blue0;\\red90\\green0\\blue90;\n".
						  "\\red90\\green0\\blue0;\\red90\\green90\\blue0;\\red90\\green90\\blue90;\n".
						  "\\red150\\green150\\blue150;\\red220\\green220\\blue220;\n".
						  "}\n";
		}
	}

	/**
	 * Loads the fonts table
     * @return void
	 */
	function load_font_table(){

		$this->MyRTF.="{\\fonttbl\n".
										  "{\\f0\\froman\\fcharset0\\fprq2{\\*\\panose 02020603050405020304} Times New Roman;}\n".
										  "{\\f1\\fswiss\\fcharset0\\fprq2{\\*\\panose 020b0304020202020204} Arial;}\n".
										  "{\\f2\\fswiss\\fcharset0\\fprq2{\\*\\panose 020b0304020202020204} Arial Black;}\n".
										  "{\\f3\\fswiss\\fcharset0\\fprq2{\\*\\panose 020b0304020202020204} Verdana;}\n".
										  "{\\f4\\fswiss\\fcharset0\\fprq2{\\*\\panose 020b0304020202020204} Tahoma;}\n".
										  "{\\f5\\fmodern\\fcharset0\\fprq2{\\*\\panose 02020603050405020304} Courier New;}\n".
										  "{\\f6\\fmodern\\fcharset222\\fprq2{\\*\\panose 02020603050405020304} AngsanaUPC;}\n".
										  "{\\f7\\fmodern\\fcharset222\\fprq2{\\*\\panose 02020603050405020304} TH SarabunPSK;}\n".
										  "{\\f8\\fmodern\\fcharset222\\fprq2{\\*\\panose 02020603050405020304}Cordia New;}\n".
										  "{\\f9\\fmodern\\fcharset222\\fprq2{\\*\\panose 02020603050405020304}Browallia New;}\n".
										  "{\\f22\\fbidi \\froman\\fcharset0\\fprq2{\\*\\panose 02020603050405020304} Angsana New;}\n".
										  "{\\f23\\fmodern\\fcharset222\\fprq2{\\*\\panose 02020603050405020304} TH SarabunPSK;}\n".
									"}";
	}

	/**
	 * These two function will insert into the document the *CURRENT* time
     * and/or the *CURRENT* date. So, it's not the date of the last modify as these
     * values will change upon the opening of the generated document.
     * @return string
     */
	function cur_date() { return "\\chdate "; }
	function cur_time() { return "\\chtime "; }
	function cur_page() { return "\\chpgn "; }
	function space() 	{ return "\\'20 "; }
//	function symbol_ascii($asc) 	{ return "{\\rtlch\\fcs1 \\af0 \\ltrch\\fcs0\\'$asc }{\\rtlch\\fcs0 \\af0 \\ltrch\\fcs1}"; }
	function symbol_ascii($asc) 	{ return "{\\rtlch\\fcs1 \\af22 \\ltrch\\fcs0\\f22\\fs30\\lang1054\\insrsid2049607\\'$asc}"; }

	// setting report title
	function set_report_title($report_title) { $this->report_title = $report_title; }
	function set_report_code($report_code) { $this->report_code = $report_code; }
	function set_company_name($company_name) { $this->company_name = $company_name; }
	
	/**
	 * Creates a list taking values from an array using bullets.
     * @arg1		array
	 * @arg2		keyword  (left|center|right|justify)
	 * @return		void|NULL on failure
     */
	function add_list($array, $align = 'left', $font = 'AngsanaUPC', $fontsize = 16){

		if (!is_array($array)) return NULL;

		foreach ($array as $k => $v)
		{
			$this->set_font($font, $fontsize);
            $this->align($align);
            $this->MyRTF .= "{ ";
			$this->bullet($v);
			$this->MyRTF .= "} ";
			$this->paragraph();
		}
	}

	/**
	 * Creates a list field using bullets.
     * @arg1	string
     * @arg2	keyword	(left|center|right|justify)
	 * @return	void
     */
	function bullet($text)
	{
        $this->TextDecoration .= "\\bullet  "; // 2 spaces are needed at the end for spacing the word from the bullet
        $this->add_text($text);
	}

	/* Open Section
     * @arg1	Start Page
	 * @return	void
     */
	function open_section($restart_page=1)
	{
		global $NUMBER_DISPLAY;
		
//		echo "NUMBER_DISPLAY=$NUMBER_DISPLAY";
		$f_numthai_char = (($NUMBER_DISPLAY==2)?"\\pgnthaib":"");
//		echo "restart_page::$restart_page<br>";
		$this->MyRTF .= "\\sect\\sectd$f_numthai_char";	// \\linex0\\endnhere
//		$this->MyRTF .= "\\sectd$f_numthai_char";	// \\linex0\\endnhere
		if ($restart_page) $this->MyRTF .= "\\pgnstarts$restart_page\\pgnrestart";
	}

	/* Close Section
	 * @return	void
     */
	function close_section()
	{
//		$this->MyRTF .= "\\sbkpage\\sect\\pard";
//		$this->MyRTF .= "\\sect\\pard\\plain";
		$this->MyRTF .= "\\sect";
	}

	function clear_header()
	{
		$this->MyRTF .= "{\\headerr \\ltrpar \\pard\\plain \\ltrpar\\s15\\ql \\li0\\ri0\\widctlpar
\\tqc\\tx4513\\tqr\\tx9026\\wrapdefault\\aspalpha\\aspnum\\faauto\\adjustright\\rin0\\lin0\\itap0 \\rtlch\\fcs1 \\af31507\\afs28\\alang1054 \\ltrch\\fcs0 \\f31506\\fs22\\lang1033\\langfe1033\\cgrid\\langnp1033\\langfenp1033 {\\rtlch\\fcs1 \\af31507 \\ltrch\\fcs0 \\insrsid226333 
\\par }}\\pard\\plain";
	}
	
	function clear_footer()
	{
		$this->MyRTF .= "{\\footerr \\ltrpar \\pard\\plain \\ltrpar\\s17\\ql \\li0\\ri0\\widctlpar\\tqc\\tx4513\\tqr\\tx9026\\wrapdefault\\aspalpha\\aspnum\\faauto\\adjustright\\rin0\\lin0\\itap0\\pararsid2957617 \\rtlch\\fcs1 \\af31507\\afs28\\alang1054 \\ltrch\\fcs0 
\\f31506\\fs22\\lang1033\\langfe1033\\cgrid\\langnp1033\\langfenp1033 {\\rtlch\\fcs1 \\af31507 \\ltrch\\fcs0 \\insrsid226333 
\\par }}\\pard\\plain";
	}
	
	/**
	 * Creates Report Title
     * @arg1	text = text1^font^size^style^color^fill^align^border||text2^font^size^style^color^fill^align^border||text3^font^size^style^color^fill^align^border
     * @arg2	f_newpage = true is newpage
	 * @return	void
     */
	function add_title($text, $f_newpage=true)
	{
		global $NUMBER_DISPLAY;
		
		if ($text) {
//			if ($f_newpage) $this->MyRTF .= "\\page";

			$tline = explode("||",$text);
			for($i_line = 0; $i_line < count($tline); $i_line++) {
				$tbuff = explode("&&",$tline[$i_line]);
				if ($tbuff[1] && $tbuff[2]) { $typ = 3; $cellw = 33; }
				else if ($tbuff[1]) { $typ = 2; $cellw = 50; }
				else { $typ = 1; $cellw = 100;}
	
				$tbalign = "full";
				$this->open_line($tbalign, false, false);
				for($i = 0; $i < count($tbuff); $i++){
					if ($tbuff[$i]) {
						$subtbuff = explode("^",$tbuff[$i]);
						if ($subtbuff[1]) $fontid = $this->get_font_id($subtbuff[1]); else $fontid = $this->_font($this->dfl_FontID);
						if ($subtbuff[2]) $fontsz = $this->_font_size($subtbuff[2]); else $fontsz = $this->_font_size($this->dfl_FontSize);
						$this->MyRTF .= $fontid.$fontsz;	// set font
	//					echo "|--fontid-fontsz=$fontid-$fontsz--|";
						if (strpos(strtolower($subtbuff[3]),"b")!==false) { $bold1 = $this->bold(1); $bold0 = $this->bold(0); }
						else { $bold1 = ""; $bold0 = ""; }
						if (strpos(strtolower($subtbuff[3]),"i")!==false) { $italic1 = $this->italic(1); $italic0 = $this->italic(0); }
						else { $italic1 = ""; $italic0 = ""; }
						if (strpos(strtolower($subtbuff[3]),"u")!==false) { $underline1 = $this->underline(1); $underline0 = $this->underline(0); }
						else { $underline1 = ""; $underline0 = ""; }
						if ($subtbuff[4]) $this->MyRTF .= $this->color($subtbuff[4]);
						if ($subtbuff[5]) $fill = $subtbuff[5]; else $fill = "0";
						$buffalign = strtolower(trim($subtbuff[6]));
						if ($buffalign) 
							$align = ($buffalign=="l" ? "left" : ($buffalign=="r" ? "right" : ($buffalign=="c" ? "center" : $buffalign))); 
						else {
							if ($typ==1 && $i==0) $align = "center"; 
							else if ($typ==2 && $i==0) $align = "left"; 
							else if ($typ==2 && $i==1) $align = "right";
							else if ($typ==3 && $i==0) $align = "left"; 
							else if ($typ==3 && $i==1) $align = "center"; 
							else if ($typ==3 && $i==2) $align = "right"; 
						}
						if ($subtbuff[7]) $border = trim($subtbuff[7]); else $border = "";
//						echo ">>>>>data=".$subtbuff[0]."align=$align($buffalign)..(typ=$typ, i=$i)..";
//						if ($NUMBER_DISPLAY==2) $subtbuff[0] = convert2thaidigit($subtbuff[0]);
						$sym_ch = "";
						$csym = strpos($subtbuff[0],"#symbol_");
						if ($csym!==false) { // replace#symbol# with page number
							$csym1 = strpos($subtbuff[0],"#",$csym+1);
							$sym_ch = substr($subtbuff[0],$csym+8,$csym1-$csym-8);
//							$subtbuff[0] = substr($subtbuff[0],0,$csym).$this->symbol_ascii($sym_ch).substr($subtbuff[0],$csym1+1);
//							echo "--->".$subtbuff[0]." ($sym_ch)<br>";
						}
						if ($NUMBER_DISPLAY==2) $subtbuff[0] = convert2thaidigit($subtbuff[0]);
						if ($sym_ch) $subtbuff[0] = substr($subtbuff[0],0,$csym).$this->symbol_ascii($sym_ch).substr($subtbuff[0],$csym1+1);
						if ($cellw==33 && $i==1) $cellw=34; else if ($cellw==34) $cellw=33; 
						$this->cell($bold1.$italic1.$underline1.$subtbuff[0].$underline0.$italic0.$bold0, $cellw, $align, $fill, $border);
						// restore font size color to default
						if ($subtbuff[4]) $this->color("0");
						$this->MyRTF .= $this->_font($this->dfl_FontID).$this->_font_size($this->dfl_FontSize);
						// end restore font size color to default
					} // if ($tbuff[$i])
				} // end for loop $i
				$this->close_line();
			} // end for loop $i_line
		} // end if ($text)
	}
	
	/**
	 * Creates header
     * @arg1	text = text1^font^size^style^color^fill^align^border||text2^font^size^style^color^fill^align^border||text3^font^size^style^color^fill^align^border
     * @arg2	topDown = Header is N twips from the top of the page = N ' ex .5in = Hale Inchs, 15mm = 15 minlimeter, 720 = 720 twips (default)
     * @arg3	brdSurround = page border surround header = true/false
	 * @return	void
     */
	function add_header($text, $topDown=720, $brdSurround=false)
	{
		global $NUMBER_DISPLAY;

		// set_table_font ($this->dfl_FontID, $this->dfl_FontSize);
		$this->MyRTF .= "\\f".$this->dfl_FontID."\\fs".$this->dfl_FontSize."\n";

		$MONTH_TH[] = "เดือน";
		$MONTH_TH[] = "ม.ค.";		$MONTH_TH[] = "ก.พ.";
		$MONTH_TH[] = "มี.ค.";		$MONTH_TH[] = "เม.ย.";
		$MONTH_TH[] = "พ.ค.";		$MONTH_TH[] = "มิ.ย.";
		$MONTH_TH[] = "ก.ค.";		$MONTH_TH[] = "ส.ค.";
		$MONTH_TH[] = "ก.ย.";		$MONTH_TH[] = "ต.ค.";
		$MONTH_TH[] = "พ.ย.";		$MONTH_TH[] = "ธ.ค.";

		$MONTH_EN[] = "Month";
		$MONTH_EN[] = "Jan";		$MONTH_EN[] = "Feb";
		$MONTH_EN[] = "Mar";		$MONTH_EN[] = "Apr";
		$MONTH_EN[] = "May";		$MONTH_EN[] = "Jun";
		$MONTH_EN[] = "Jul";		$MONTH_EN[] = "Aug";
		$MONTH_EN[] = "Sep";		$MONTH_EN[] = "Oct";
		$MONTH_EN[] = "Nov";		$MONTH_EN[] = "Dec";

		$today = getdate(); 
		$year = $today['year'];
		if  ($this->lang_code == "TH")
				{		$year = $year + 543; 
						$month = $MONTH_TH[$today['mon']]; 
				}
		else
				{		$month = $MONTH_EN[$today['mon']]; 
				}
		$mday = $today['mday'];
		$time = date('H:i:s');
//		$mday = (($NUMBER_DISPLAY==2)?convert2thaidigit($mday):$mday);
//		$year = (($NUMBER_DISPLAY==2)?convert2thaidigit($year):$year);
//		$time = (($NUMBER_DISPLAY==2)?convert2thaidigit($time):$time);
		$st_today = $mday . " " . $month . " " . $year ;
		$st_time = $time ;

		if (!$text) {
			$text = $this->report_title."^^34^BU^0^^c^||".$this->company_name."^^24^B^0^^l^";	// default ของ header
		}

		if ($text) {
			$this->MyRTF .= "{\\header";
			
//			echo ">>agu::topDown=$topDown>>";
			if (strpos($topDown,"cm")!==false) $topDown = $this->conv2twip("cm",$topDown); 
			else if (strpos($topDown,"mm")!==false) $topDown = $this->conv2twip("mm",$topDown);
			else if (strpos($topDown,"in")!==false) $topDown = $this->conv2twip("in",$topDown);
			if (!$topDown) $topDown=720;
			$this->MyRTF .= "\\headery$topDown";
//			echo "|***topDown=$topDown***|";

			if ($brdSurround) $this->MyRTF .= "\\pgbrdrhead";

			$tline = explode("||",$text);
			for($i_line = 0; $i_line < count($tline); $i_line++) {
//				echo "$i_line-".$tline[$i_line]."<br>";
				$tbuff = explode("&&",$tline[$i_line]);
				if ($tbuff[1] && $tbuff[2]) { $typ = 3; $cellw = 33; }
				else if ($tbuff[1]) { $typ = 2; $cellw = 50; }
				else { $typ = 1; $cellw = 100;}
	
				$tbalign = "full";
				$this->open_line($tbalign, false, false);
				for($i = 0; $i < count($tbuff); $i++){
//					echo "$i-".$tbuff[$i]."<br>";
					if ($tbuff[$i]) {
						$subtbuff = explode("^",$tbuff[$i]);
						if ($subtbuff[1]) $fontid = $this->get_font_id($subtbuff[1]); else $fontid = $this->_font($this->dfl_FontID);
						if ($subtbuff[2]) $fontsz = $this->_font_size($subtbuff[2]); else $fontsz = $this->_font_size($this->dfl_FontSize);
						$this->MyRTF .= $fontid.$fontsz;	// set font
	//					echo "|--fontid-fontsz=$fontid-$fontsz--|";
						if (strpos(strtolower($subtbuff[3]),"b")!==false) { $bold1 = $this->bold(1); $bold0 = $this->bold(0); }
						else { $bold1 = ""; $bold0 = ""; }
						if (strpos(strtolower($subtbuff[3]),"i")!==false) { $italic1 = $this->italic(1); $italic0 = $this->italic(0); }
						else { $italic1 = ""; $italic0 = ""; }
						if (strpos(strtolower($subtbuff[3]),"u")!==false) { $underline1 = $this->underline(1); $underline0 = $this->underline(0); }
						else { $underline1 = ""; $underline0 = ""; }
						if ($subtbuff[4]) $this->MyRTF .= $this->color($subtbuff[4]);
						if ($subtbuff[5]) $fill = $subtbuff[5]; else $fill = "0";
						$buffalign = strtolower(trim($subtbuff[6]));
						if ($buffalign) 
							$align = ($buffalign=="l" ? "left" : ($buffalign=="r" ? "right" : ($buffalign=="c" ? "center" : $buffalign))); 
						else {
							if ($typ==1 && $i==0) $align = "center"; 
							else if ($typ==2 && $i==0) $align = "left"; 
							else if ($typ==2 && $i==1) $align = "right";
							else if ($typ==3 && $i==0) $align = "left"; 
							else if ($typ==3 && $i==1) $align = "center"; 
							else if ($typ==3 && $i==2) $align = "right"; 
						}
						if ($subtbuff[7]) $border = trim($subtbuff[7]); else $border = "";
//						echo ">>>>>data=".$subtbuff[0]."align=$align($buffalign)..(typ=$typ, i=$i)..";
						if (strpos($subtbuff[0],"#page_no#")!==false) // replace#page_no# with page number
							$subtbuff[0] = str_replace("#page_no#", "\\chpgn", $subtbuff[0]);
						if (strpos($subtbuff[0],"#page_no_/_total#")!==false) // replace#page_no# with page number
							$subtbuff[0] = str_replace("#page_no_/_total#", "{\\field{\\*\\fldinst PAGE}{\\fldrslt 1}}/{\\field{\\*\\fldinst NUMPAGES}{\\fldrslt 1}}", $subtbuff[0]);
						if (strpos($subtbuff[0],"#today#")!==false)
							$subtbuff[0] = str_replace("#today#", "$st_today", $subtbuff[0]);
						if (strpos($subtbuff[0],"#time#")!==false)
							$subtbuff[0] = str_replace("#time#", "$st_time", $subtbuff[0]);
						$sym_ch = "";
						$csym = strpos($subtbuff[0],"#symbol_");
						if ($csym!==false) { // replace#symbol# with page number
							$csym1 = strpos($subtbuff[0],"#",$csym+1);
							$sym_ch = substr($subtbuff[0],$csym+8,$csym1-$csym-8);
//							$subtbuff[0] = substr($subtbuff[0],0,$csym).$this->symbol_ascii($sym_ch).substr($subtbuff[0],$csym1+1);
//							echo "--->".$subtbuff[0]." ($sym_ch)<br>";
						}
						if ($NUMBER_DISPLAY==2) $subtbuff[0] = convert2thaidigit($subtbuff[0]);
						if ($sym_ch) $subtbuff[0] = substr($subtbuff[0],0,$csym).$this->symbol_ascii($sym_ch).substr($subtbuff[0],$csym1+1);
						if ($cellw==33 && $i==1) $cellw=34; else if ($cellw==34) $cellw=33; 
						$this->cell($bold1.$italic1.$underline1.$subtbuff[0].$underline0.$italic0.$bold0, $cellw, $align, $fill, $border);
						// restore font size color to default
						if ($subtbuff[4]) $this->color("0");
						$this->MyRTF .= $this->_font($this->dfl_FontID).$this->_font_size($this->dfl_FontSize);
						// end restore font size color to default
					} // if ($tbuff[$i])
				} // end for loop $i
				$this->close_line();
			} // end for loop $i_line
			$this->MyRTF .= "} \\pard";
		} // end if ($text)
	}
	/**
	 * Creates footer
     * @arg1	text = text1^font^size^style^color^fill^align^border||text2^font^size^style^color^fill^align^border||text3^font^size^style^color^fill^align^border
	 * @return	void
     */
	function add_footer($text, $bottomUp=720, $brdSurround=false)
	{
		global $NUMBER_DISPLAY;

		// set_table_font ($this->dfl_FontID, $this->dfl_FontSize);
		$this->MyRTF .= "\\f".$this->dfl_FontID."\\fs".$this->dfl_FontSize."\n";

		$MONTH_TH[] = "เดือน";
		$MONTH_TH[] = "ม.ค.";		$MONTH_TH[] = "ก.พ.";
		$MONTH_TH[] = "มี.ค.";		$MONTH_TH[] = "เม.ย.";
		$MONTH_TH[] = "พ.ค.";		$MONTH_TH[] = "มิ.ย.";
		$MONTH_TH[] = "ก.ค.";		$MONTH_TH[] = "ส.ค.";
		$MONTH_TH[] = "ก.ย.";		$MONTH_TH[] = "ต.ค.";
		$MONTH_TH[] = "พ.ย.";		$MONTH_TH[] = "ธ.ค.";

		$MONTH_EN[] = "Month";
		$MONTH_EN[] = "Jan";		$MONTH_EN[] = "Feb";
		$MONTH_EN[] = "Mar";		$MONTH_EN[] = "Apr";
		$MONTH_EN[] = "May";		$MONTH_EN[] = "Jun";
		$MONTH_EN[] = "Jul";		$MONTH_EN[] = "Aug";
		$MONTH_EN[] = "Sep";		$MONTH_EN[] = "Oct";
		$MONTH_EN[] = "Nov";		$MONTH_EN[] = "Dec";

		$today = getdate(); 
		$year = $today['year'];
		if  ($this->lang_code == "TH")
				{		$year = $year + 543; 
						$month = $MONTH_TH[$today['mon']]; 
				}
		else
				{		$month = $MONTH_EN[$today['mon']]; 
				}
		$mday = $today['mday'];
		$time = date('H:i:s');
//		$mday = (($NUMBER_DISPLAY==2)?convert2thaidigit($mday):$mday);
//		$year = (($NUMBER_DISPLAY==2)?convert2thaidigit($year):$year);
//		$time = (($NUMBER_DISPLAY==2)?convert2thaidigit($time):$time);
		$st_today = $mday . " " . $month . " " . $year ;
		$st_time = $time ;

		if (!$text) {
			$text = ($this->report_code ? ("รายงาน : ".(($NUMBER_DISPLAY==2)?convert2thaidigit($this->report_code) : $this->report_code)) : "")."^^20^^0^^left^&&หน้า : [#page_no_/_total#]^^20^^0^^center^&&วันที่พิมพ์ : ".(($NUMBER_DISPLAY==2)?convert2thaidigit($st_today):$st_today)." ".(($NUMBER_DISPLAY==2)?convert2thaidigit($st_time):$st_time)."^^20^^0^^right^";	// default ของ footer
//			$text = "$report_title^^34^BU^$fmtTitle_col_idx^$fmtTitle_bgcol_idx^c^&&$company_name^^24^B^$fmtTitle_col_idx^$fmtTitle_bgcol_idx^l^";	// default ของ header
		}
		
		if ($text) {
			$this->MyRTF .= "{\\footer";
			
//			echo ">>agu::bottomUp=$bottomUp>>";
			if (strpos($bottomUp,"cm")!==false) $bottomUp = $this->conv2twip("cm",$bottomUp); 
			else if (strpos($bottomUp,"mm")!==false) $bottomUp = $this->conv2twip("mm",$bottomUp);
			else if (strpos($bottomUp,"in")!==false) $bottomUp = $this->conv2twip("in",$bottomUp);
			if (!$bottomUp) $bottomUp=720;
			$this->MyRTF .= "\\footery$bottomUp";
//			echo "|***bottomUp=$bottomUp***|";

			if ($brdSurround) $this->MyRTF .= "\\pgbrdrfoot";

			$tline = explode("||",$text);
			for($i_line = 0; $i_line < count($tline); $i_line++) {
//				echo "line-$i_line=".$tline[$i_line]."<br>";
				$tbuff = explode("&&",$tline[$i_line]);
				if ($tbuff[1] && $tbuff[2]) { $typ = 3; $cellw = 33; }
				else if ($tbuff[1]) { $typ = 2; $cellw = 50; }
				else { $typ = 1; $cellw = 100;}
	
				$tbalign = "full";
				$this->open_line($tbalign, false, false);
				for($i = 0; $i < count($tbuff); $i++){
//					echo "$i-$tbuff[$i] (cellw=$cellw)<br>";
					if ($tbuff[$i]) {
						$subtbuff = explode("^",$tbuff[$i]);
						if ($subtbuff[1]) $fontid = $this->get_font_id($subtbuff[1]); else $fontid = $this->_font($this->dfl_FontID);
						if ($subtbuff[2]) $fontsz = $this->_font_size($subtbuff[2]); else $fontsz = $this->_font_size($this->dfl_FontSize);
						$this->MyRTF .= $fontid.$fontsz;	// set font
	//					echo "|--fontid-fontsz=$fontid-$fontsz--|";
						if (strpos(strtolower($subtbuff[3]),"b")!==false) { $bold1 = $this->bold(1); $bold0 = $this->bold(0); }
						else { $bold1 = ""; $bold0 = ""; }
						if (strpos(strtolower($subtbuff[3]),"i")!==false) { $italic1 = $this->italic(1); $italic0 = $this->italic(0); }
						else { $italic1 = ""; $italic0 = ""; }
						if (strpos(strtolower($subtbuff[3]),"u")!==false) { $underline1 = $this->underline(1); $underline0 = $this->underline(0); }
						else { $underline1 = ""; $underline0 = ""; }
						if ($subtbuff[4]) $this->MyRTF .= $this->color($subtbuff[4]);
						if ($subtbuff[5]) $fill = $subtbuff[5]; else $fill = "0";
						$buffalign = strtolower(trim($subtbuff[6]));
						if ($buffalign) 
							$align = ($buffalign=="l" ? "left" : ($buffalign=="r" ? "right" : ($buffalign=="c" ? "center" : $buffalign))); 
						else {
							if ($typ==1 && $i==0) $align = "center"; 
							else if ($typ==2 && $i==0) $align = "left"; 
							else if ($typ==2 && $i==1) $align = "right";
							else if ($typ==3 && $i==0) $align = "left"; 
							else if ($typ==3 && $i==1) $align = "center"; 
							else if ($typ==3 && $i==2) $align = "right"; 
						}
						if ($subtbuff[7]) $border = trim($subtbuff[7]); else $border = "";
	//					echo ">>>>>data=".$subtbuff[0]."align=$align($buffalign)..(typ=$typ, i=$i)..";
						if (strpos($subtbuff[0],"#page_no#")!==false) // replace#page_no# with page number
							$subtbuff[0] = str_replace("#page_no#", "\\chpgn", $subtbuff[0]);
						if (strpos($subtbuff[0],"#page_no_/_total#")!==false) // replace#page_no# with page number
							$subtbuff[0] = str_replace("#page_no_/_total#", "{\\field{\\*\\fldinst PAGE}{\\fldrslt 1}}/{\\field{\\*\\fldinst NUMPAGES}{\\fldrslt 1}}", $subtbuff[0]);
						if (strpos($subtbuff[0],"#today#")!==false)
							$subtbuff[0] = str_replace("#today#", "$st_today", $subtbuff[0]);
						if (strpos($subtbuff[0],"#time#")!==false)
							$subtbuff[0] = str_replace("#time#", "$st_time", $subtbuff[0]);
						if ($NUMBER_DISPLAY==2) $subtbuff[0] = convert2thaidigit($subtbuff[0]);
						$sym_ch = "";
						$csym = strpos($subtbuff[0],"#symbol_");
						if ($csym!==false) { // replace#symbol# with page number
							$csym1 = strpos($subtbuff[0],"#",$csym+1);
							$sym_ch = substr($subtbuff[0],$csym+8,$csym1-$csym-8);
//							$subtbuff[0] = substr($subtbuff[0],0,$csym).$this->symbol_ascii($sym_ch).substr($subtbuff[0],$csym1+1);
//							echo "--->".$subtbuff[0]." ($sym_ch)<br>";
						}
						if ($NUMBER_DISPLAY==2) $subtbuff[0] = convert2thaidigit($subtbuff[0]);
						if ($sym_ch) $subtbuff[0] = substr($subtbuff[0],0,$csym).$this->symbol_ascii($sym_ch).substr($subtbuff[0],$csym1+1);
						if ($cellw==33 && $i==1) $cellw=34; else if ($cellw==34) $cellw=33; 
//						echo "$i-$tbuff[$i] (cellw=$cellw)<br>";
						$this->cell($bold1.$italic1.$underline1.$subtbuff[0].$underline0.$italic0.$bold0, $cellw, $align, $fill, $border);
						// restore font size color to default
						if ($subtbuff[4]) $this->color("0");
						$this->MyRTF .= $this->_font($this->dfl_FontID).$this->_font_size($this->dfl_FontSize);
						// end restore font size color to default
					} // if ($tbuff[$i])
				} // end for loop $i
				$this->close_line();
			} // end for loop $i_line
			$this->MyRTF .= "} \\pard";
		} // end if ($text)
	}
	/**
     * Insert some text in the document
	 * @arg1	string
	 * @arg2	keyword  (left|center|right|justify)
	 * @return	void
	 */
	function add_text($msg, $align = 'left')
	{
		/** FIX RITORNI A CAPO **/
		$msg = str_replace("\r", "", $msg);
		$msg = str_replace("\n", "", $msg);

		/** FIX LETTERE ACCENTATE **/

//		echo "align=$align<br>";
		$this->align($align);
		$this->MyRTF .= "{ ";

		if (empty($this->TextDecoration))
		{
			$this->TextDecoration .= $this->_font($this->dfl_FontID);
			$this->TextDecoration .= $this->_font_size($this->dfl_FontSize);
		}

        $this->MyRTF .= $this->TextDecoration;
		
			$sym_ch = "";
			$csym = strpos($msg,"#symbol_");
			if ($csym!==false) { // replace#symbol# with page number
				$csym1 = strpos($msg,"#",$csym+1);
				$sym_ch = substr($msg,$csym+8,$csym1-$csym-8);
				$sym_ch = convert_thaidigit2arabic($sym_ch);	// ถ้าค่าตัวเลขถูกเปลี่ยนเป็น ตัวเลขไทยที่ขั้นตอนไหนซักแห่ง เปลี่ยนกลับเป็น อาราบิค
//				echo "$msg ($sym_ch)<br>";
			}
			if ($sym_ch) $msg = substr($msg,0,$csym).$this->symbol_ascii($sym_ch).substr($msg,$csym1+1);
			
		$this->MyRTF .= $msg;
		$this->MyRTF .= " } ";

		$this->TextDecoration = '';
	}

	/**
	 * Insert one or ${times} carriage returns in the document
	 * @arg1		int
     * @return 	void
     */
	function new_line($times = 1)
	{
		for ($i=0; $i<$times; $i++)
		{ $this->MyRTF .= "\\line\n";	}
	}

	/**
     * Ends the current paragraph (or thought to do so... duh)
     * @return void
     */
	function paragraph()			{ $this->MyRTF .= "\\par\n";  }

	/**
	 * Text formatting functions
     * bold:			grassetto
     * italic:			corsivo
	 * underline:		sottolineato
	 * caps:			testo in maiuscolo
	 * emboss:			effetto testo in rilievo
     * engrave:		    effetto testo scavato
     * outline:		    effetto testo con contorno
     * shadow:			effetto testo con ombra
	 * sub:				pedice
     * super:			apice
     * @arg1			int	(0|1) 1: default
     * @return			void
	 */
	function bold($s = 1)			{ return ($s == 0) ? " \\b0 " : "\\b "; 			}
	function italic($s = 1)			{ return ($s == 0) ? " \\i0 " : "\\i "; 			}
	function underline($s = 1)		{ return ($s == 0) ? " \\ulnone " : "\\ul "; 		}
	function caps($s = 1)			{ return ($s == 0) ? " \\caps0 " : "\\caps "; 		}
	function emboss($s = 1)			{ return ($s == 0) ? " \\embo0 " : "\\embo "; 		}
	function engrave($s = 1)		{ return ($s == 0) ? " \\impr0 " : "\\impr "; 		}
	function outline($s = 1)		{ return ($s == 0) ? " \\outl0 " : "\\outl "; 		}
	function shadow($s = 1)			{ return ($s == 0) ? " \\shad0 " : "\\shad ";	    }
	function sub($s = 1)		   	{ return ($s == 0) ? " \\nosupersub " : "\\shad ";  }
	function super($s = 1)			{ return ($s == 0) ? " \\nosupersub " : "\\super "; }

   /**
     * Internal function used to set the font type
     * (Not to be used directly. set_font() function as been written for this)
     * @arg1		int
     * @return		string
     */
	function _font($id = 0)			{ return ("\\f$id "); }

	/**
     * Internal function used to set the font size (X pt == X*2 pt)
     * (Not to be used directly. set_font_size() function as been written for this)
	 * @arg1		int
	 * @return		string
     */
	function _font_size($size = 20)		{ return ("\\fs$size "); }

	/**
	 * Sets the default font used in the document ( set_default_font() )
	 * used when the font is not assigned using set_font() function before
     * calling the add_text() function. Same thing for set_default_font_size().
     * @arg1		string
	 * @arg2		int
	 * @return 	void
     */
	function set_default_font($font_name, $font_size = 10 ){

		$this->dfl_FontID = $this->get_font_id($font_name);
		$this->set_default_font_size($font_size);
	}

	function set_default_font_size($font_size = 10)	{

		$this->dfl_FontSize = ($font_size * 2);
	}

	/**
	 * Returns the requested font id (used in RTF syntax)
     * @arg1		string
     * @return		int
     */
	function get_font_id($font_name = NULL)
	{
		switch ( strtolower($font_name) )
      	{	// 'angsa','angsab','cordia','cordiab','thsarabun','thsarabunb'

			case 'times':        return(0); break;
   	        case 'arial':        return(1); break;
			case 'arial black':  return(2); break;
			case 'verdana':      return(3); break;
			case 'tahoma':       return(4); break;
 			case 'courier new': return(5); break;
//			case 'cordia':
//			case 'cordiab': 	 return(5); break;
 			case 'angsanaupc':
 			case 'angsa':
			case 'angsb':		 return(6); break;
 			case 'thsarabun':
			case 'thsarabunb':	 return(7); break;
 			case 'cordia':
			case 'cordiab':	 return(8); break;
 			case 'browallia':
			case 'browalliab':	 return(9); break;
 			default:             return(0); break;
		}
	}

	/**
	 * Sets the font size only
     * @arg1		int
	 * @return		void
     */
	function set_font_size($size)
	{
		$size *= 2;
		$this->TextDecoration .= $this->_font_size($size);
	}

	/**
	 * Sets the text font and its size
     * @arg1		string	(font name)
     * @arg2		int		(font size)
	 * @return		void
	 */
	function set_font($font, $size = 10)
	{
		$this->FontID = $this->get_font_id($font);
//		echo "FontID=".$this->FontID." font=$font size=$size<br>";
		$this->TextDecoration .= $this->_font($this->FontID);
		$this->set_font_size($size);
//		echo "TextDecoration=".$this->TextDecoration."<br>";
	}

	/**
     * Jump to the next page of the document
     * @return		void
     */
	function new_page(){ 
            $this->MyRTF .= "\\pard\\insrsid\\page\\par";//"\\page\n"; 
        }

	/**
     * Suppress extra line spacing like WordPerfect version 5.x. set in paragraph format
     * @return		void
     */
	function line_spacing($n)		{ $this->MyRTF .= "\\pard\\sl$n\\slmult0"; }

	/**
     * Sets the font's color
     * @return		void
     */
	function color($ColorID=0)		{ return "\\cf$ColorID "; }

	/**
     * Align text and images
     * (This is not intended to be used directly)
     * @arg1		keyword  (left|center|right|justify)
     */
	function align($where = 'left')
	{
		switch ( strtolower ($where) )
		{
			case 'left': 		$this->MyRTF .= "\\ql ";	break;
			case 'center':		$this->MyRTF .= "\\qc ";	break;
			case 'right':		$this->MyRTF .= "\\qr ";	break;
			case 'justify':	    $this->MyRTF .= "\\qj ";	break;
			default:			$this->align('left');		break;
		}
	}

	/**
     * Insert an image and manages its alignment on the document
	 * ** TODO ** :: fix bug on image size handling
     * @arg1		string	(image filename)
	 * @arg2		int		(int 1-100)
	 * @arg3		keyword  (left|center|right|justify)
	 * @return		void
     */
	function add_image($image, $ratio, $align = 'left')
	{
		$file = @file_get_contents($image);

		if (empty($file))
			return NULL;

		$this->align($align);
		$this->MyRTF .= "{";
		$this->MyRTF .= "\\pict\\jpegblip\\picscalex". $ratio ."\\picscaley". $ratio ."\\bliptag132000428 ";
		$this->MyRTF .= trim(bin2hex($file));
		$this->MyRTF .= "\n}\n";
//		$this->paragraph();
	}

	/**
     * Insert an image and manages its alignment on the document
	 * ** TODO ** :: fix bug on image size handling
     * @arg1		string	(image filename)
	 * @arg2		int		x (int 1-100)
	 * @arg3		int		y (int 1-100)
	 * @arg4		keyword  (left|center|right|justify)
	 * @return		void
     */
	function add_imagexy($image, $ratiox, $ratioy, $align = 'left')
	{
		$file = @file_get_contents($image);

		if (empty($file))
			return NULL;

		$this->align($align);
		$this->MyRTF .= "{";
		$this->MyRTF .= "\\pict\\jpegblip\\picscalex". $ratiox ."\\picscaley". $ratioy ."\\bliptag132000428 ";
		$this->MyRTF .= trim(bin2hex($file));
		$this->MyRTF .= "\n}\n";
//		$this->paragraph();
	}

	/**
	 * View/Download of the created RTF files
     * NOTE: View feature is for *DEBUG* purposes
     * @arg1		string
	 * @return		void
	 */
	function display($filename = "document.rtf", $download = true)
	{
		$this->MyRTF .= "\n}\n}\n";

		if ($download == true) // Download
		{
//			header("Content-type: application/msword");
//			header("Content-type: text/html; charset=utf-8");
//			header("Content-Lenght: ". sizeof($this->MyRTF));
//	   	    header("Content-Disposition: inline; filename=". $filename);
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
			header("Content-Type: application/x-msword; name=".$filename);
			header("Content-Disposition: inline; filename=".$filename);
		}

		print $this->MyRTF;
	}

    /**
     * Method to set the margin of spacing (called the method "RTF")
     * @return		void
     */
    function config_margin($papersize = "a4", $margl = 720, $margr = 720, $margt = 720, $margb = 720, $orient)
	{
        ### Set the margins of the paper
		if (strpos($margl,"cm")!==false) $margl = $this->conv2twip("cm",$margl); 
		else if (strpos($margl,"mm")!==false) $margl = $this->conv2twip("mm",$margl);
		else if (strpos($margl,"in")!==false) $margl = $this->conv2twip("in",$margl);
		if (strpos($margr,"cm")!==false) $margr = $this->conv2twip("cm",$margr); 
		else if (strpos($margr,"mm")!==false) $margr = $this->conv2twip("mm",$margr);
		else if (strpos($margr,"in")!==false) $margr = $this->conv2twip("in",$margr);
		if (strpos($margt,"cm")!==false) $margt = $this->conv2twip("cm",$margt); 
		else if (strpos($margt,"mm")!==false) $margt = $this->conv2twip("mm",$margt);
		else if (strpos($margt,"in")!==false) $margt = $this->conv2twip("in",$margt);
		if (strpos($margb,"cm")!==false) $margb = $this->conv2twip("cm",$margb); 
		else if (strpos($margb,"mm")!==false) $margb = $this->conv2twip("mm",$margb);
		else if (strpos($margb,"in")!==false) $margb = $this->conv2twip("in",$margb);
//		echo "map_papersize['$papersize']=".$this->map_papersize["$papersize"]."<br>";
		$papersize = strtolower($papersize);
		if (strtolower($orient)!="p") {
			$this->MyRTF .= "\\landscape";	// set page orientation to landscape
			$papersize .= "_l";
		}
        $this->MyRTF .= $this->map_papersize["$papersize"]; // "\\paperw12240\\paperh15840\n";
        $this->MyRTF .= "\\margl".$margl."\\margr".$margr."\\margt".$margt."\\margb".$margb."\n";
		$buff = explode("\\",$this->map_papersize["$papersize"]);
		$this->paper_w = substr($buff[1],6);
		$this->paper_h = substr(str_replace("\n","",$buff[2]),6);
		$this->margl = $margl;
		$this->margr = $margr;
		$this->margt = $margt;
		$this->margb = $margb;
    }
	/**
     * convert cm mm in to twip
     * @return		twip
     */
	function conv2twip($unit="", $val){
		$retval = 0;
		$val = str_replace("$unit","",$val);
		if (strpos($unit,"cm")!==false) $retval = $val * 567;	// 1 cm = 567 Twips
		else if (strpos($unit,"mm")!==false) $retval = floor($val * 56.7);	// 1 mm = 56.7 Twips
		else if (strpos($unit,"in")!==false) $retval = floor($val * 1440.18);	// 1 inch = 1440.18 Twips
		
		return $retval;
	}
	
	/**
     * Method to set the font and font size for tables (called the method "RTF")
     * @return		void
     */
	function set_table_font($font_name, $size){

		$size = $size*2;
		$this->MyRTF .= "\\f".$this->get_font_id($font_name)."\\fs".$size."\n";
	}

    /**
     * Method to start the row of the table
	 * 	agument 1	tbalign = table align => left,center,rignt,1.5in(form left),150mm(form left),375(twip)(form left),full(default table width full page width 100%) 
	 * 	agument 2	keep = keep all line togeter not split by a page break (true/false)
	 * 	agument 3	head = set this line to table head show every page if table (true/false)
     * @return		void
     */
    function open_line($tbalign = "full", $keep=false, $head=false){
		
		$tbalign = strtolower($tbalign);
		if (strpos($tbalign,"cm")!==false) $tbalign = $this->conv2twip("cm",$tbalign);
		else if (strpos($tbalign,"mm")!==false) $tbalign = $this->conv2twip("mm",$tbalign);
		else if (strpos($tbalign,"in")!==false) $tbalign = $this->conv2twip("in",$tbalign);
		
		if (is_numeric($tbalign)) $c_tbalign = "\\trleft$tbalign";
		else if ($tbalign == "right") $c_tbalign = "\\trqr";
		else if ($tbalign == "center") $c_tbalign = "\\trqc";
		else if ($tbalign == "left") $c_tbalign = "\\trql";
		else $c_tbalign = "";
		
		### Mount the table within the document
        $this->MyRTF .= "\\trowd\\trgaph35".$c_tbalign."\\trpaddfr3\\trpaddfl3\\trpaddft3\\trpaddfb3\\trpaddr55\\trpaddl55\\trpaddb15\\trpaddt35";
		if ($head)	$this->MyRTF .= "\\trhdr";
		if ($keep)	$this->MyRTF .= "\\trkeep";
//		echo "head=$head, keep=$keep...";
        $this->MyRTF .= "\n";
    }

    /**
     * Method to close the line of table
     * @return		void
     */
    function close_line(){

        ### Go to the next line
        $this->MyRTF .= "\\pard \\intbl \\row\\pard\n";

        ### Zero for the previousResult a new line of table
        $this->previousResult=0;
    }

	/**
     * Method to skip lines
     * @return		void
     */
    function ln($times = 1){

        for ($i=0; $i<$times; $i++){
            $this->MyRTF .= "\\par\n";
        }
    }

    /**
     * Method for spacing with tab
     * @return		void
     */
    function tab($times = 1){

        for ($i=0; $i<$times; $i++){
            $this->MyRTF .= "\\tab\n";
        }
    }

    /**
     * Method for creating the cells of the table
     * @arg1		text
     * @arg2		size of the cell in % percent
	 * @arg3		keyword  (left|center|right|justify)
	 * @arg4		background colour of the cell
	 * @arg5		border "TLBR"
	 * @arg6		valign ["top"(default),"bottom","center"]
	 * @arg7		textFlow ["lrtb"(default), "tbrl", "btlr", "lrtbv", "tbrlv"]
	 * @arg8		mrgCol [1=first, >1=next false=no merge column]
	 * @arg9		mrgRow [1=first, >1=next false=no merge row]
	 * @return		void
     */
    function cell($msg, $sizeCell, $align="left", $cellBgColorId = NULL, $border = "", $valign="top", $textFlow="lrtb", $mrgCol=false, $mrgRow=false){

        ### Calculate the size of the cell
        $sizeCell = floor($this->page_w*($sizeCell/100)) + $this->previousResult;
        $this->previousResult = $sizeCell;

        ### Border 1
		if (strpos(strtolower($border),"t") !== false)
			$this->MyRTF .= "\\trbrdrt\\brdrs\\brdrw2";
		if (strpos(strtolower($border),"l") !== false)
			$this->MyRTF .= "\\trbrdrl\\brdrs\\brdrw2";
		if (strpos(strtolower($border),"b") !== false)
			$this->MyRTF .= "\\trbrdrb\\brdrs\\brdrw2";
		if (strpos(strtolower($border),"r") !== false)
			$this->MyRTF .= "\\trbrdrr\\brdrs\\brdrw2";

        ### Put the background color of the cell
//		if ($cellBgColorId){$this->MyRTF .= "\\clcfpatraw".$cellBgColorId."\\clshdngraw10000\n";}
		if ($cellBgColorId){$this->MyRTF .= "\\clcbpat".$cellBgColorId."\n";}

        ### Border 2
		if (strpos(strtolower($border),"t") !== false)
			$this->MyRTF .= "\\clbrdrt\\brdrs\\brdrw2";
		if (strpos(strtolower($border),"l") !== false)
			$this->MyRTF .= "\\clbrdrl\\brdrs\\brdrw2";
		if (strpos(strtolower($border),"b") !== false)
			$this->MyRTF .= "\\clbrdrb\\brdrs\\brdrw2";
		if (strpos(strtolower($border),"r") !== false)
			$this->MyRTF .= "\\clbrdrr\\brdrs\\brdrw2";

        ### set Vertical align
        if (strtolower($valign)=="center" || strtolower($valign)=="c"){$this->MyRTF .= "\\clvertalc";}
        else if (strtolower($valign)=="bottom" || strtolower($valign)=="b"){$this->MyRTF .= "\\clvertalb";}
        else if (strtolower($valign)=="top" || strtolower($valign)=="t"){$this->MyRTF .= "\\clvertalt";}

        ### set text flow -> lrtb (default), tbrl, btlr, lrtbv, tbrlv(top bottom right left vertical
		$textFlow = strtolower($textFlow);
        if ($textFlow=="lrtbv" || $textFlow=="tbrlv" || $textFlow=="lrtb" || $textFlow=="tbrl" || $textFlow=="btlr")
			$this->MyRTF .= "\\cltx$textFlow";

        ### set merge column
        if ($mrgCol==1)	$this->MyRTF .= "\\clmgf";
        else if ($mrgCol > 1)	$this->MyRTF .= "\\clmrg";

        ### set merge row
        if ($mrgRow==1)	$this->MyRTF .= "\\clvmgf";
        else if ($mrgRow > 1)	$this->MyRTF .= "\\clvmrg";

        ### Assemble the cells of the table (the sum of the sizes of the cells should not exceed 21,600)
        $this->MyRTF .= "\\cellx".$sizeCell."\n";

        $this->MyRTF .= "\\pard \\intbl ";
        $this->align($align);

			$sym_ch = "";
			$csym = strpos($msg,"#symbol_");
			if ($csym!==false) { // replace#symbol# with page number
				$csym1 = strpos($msg,"#",$csym+1);
				$sym_ch = substr($msg,$csym+8,$csym1-$csym-8);
				$sym_ch = convert_thaidigit2arabic($sym_ch);	// ถ้าค่าตัวเลขถูกเปลี่ยนเป็น ตัวเลขไทยที่ขั้นตอนไหนซักแห่ง เปลี่ยนกลับเป็น อาราบิค
//				echo "$msg ($sym_ch)<br>";
			}
			if ($sym_ch) $msg = substr($msg,0,$csym).$this->symbol_ascii($sym_ch).substr($msg,$csym1+1);

//		echo "cell->msg=$msg<br>";
		$c1 = strpos($msg,"<*img*");
		$c2 = strpos($msg,"*img*>");
		if ($c1 !== false && $c2 !== false) {
			$imgname = substr($msg, $c1+6, $c2-($c1+6));
			$text1 = substr($msg, 0, $c1);
			$text2 = substr($msg, $c2+6);
//			echo ">>>$text1|$imgname|$text2<br>";
		} else {
			$imgname = "";
			$text1 = $msg;
			$text2 = "";
		}

		$tt = "";
        if (trim($text1)) {
//			echo "............text1=$text1<br>"; 
			$tt .= $text1; 
		}
		
		if (trim($imgname)) { 
//			echo "............image=$imgname (align=$align)<br>"; 
//			$tt .= $this->add_image($imgname, $sizeCell, $align);
			$img = explode(",",$imgname);
			if (!$img[1]) $img_ratio = 100;
			else $img_ratio = (int)$img[1];	// img[1] เป็น ratio
//			$tt .= $this->add_image($img[0], (int)$img[1], $align);
			$tt .= $this->add_image($img[0], $img_ratio, $align);
		}

        if (trim($text2)) { 
//			echo "............text2=$text2<br>"; 
			$tt .= $text2; 
		}
		
		$this->MyRTF .= $tt." \\cell\n";
    }

    /**
     * Method for creating the cellsImage of the table
     * @arg1		image file name
     * @arg2		size of the cell
	 * @arg3		keyword  (left|center|right|justify)
	 * @arg4		background colour of the cell
	 * @return		void
     */
    function cellImage($image, $ratio, $sizeCell, $align="left", $cellColorId = NULL, $border = ""){

        ### Calculate the size of the cell
        $sizeCell = floor($this->page_w*($sizeCell/100)) + $this->previousResult;
        $this->previousResult = $sizeCell;

        ### Border 1
		if (strpos(strtolower($border),"t") !== false)
			$this->MyRTF .= "\\trbrdrt\\brdrs\\brdrw2";
		if (strpos(strtolower($border),"l") !== false)
			$this->MyRTF .= "\\trbrdrl\\brdrs\\brdrw2";
		if (strpos(strtolower($border),"b") !== false)
			$this->MyRTF .= "\\trbrdrb\\brdrs\\brdrw2";
		if (strpos(strtolower($border),"r") !== false)
			$this->MyRTF .= "\\trbrdrr\\brdrs\\brdrw2";

        ### Put the background color of the cell
        if ($cellColorId){$this->MyRTF .= "\\clcfpatraw".$cellColorId."\\clshdngraw10000\n";}

        ### Border 2
		if (strpos(strtolower($border),"t") !== false)
			$this->MyRTF .= "\\clbrdrt\\brdrs\\brdrw2";
		if (strpos(strtolower($border),"l") !== false)
			$this->MyRTF .= "\\clbrdrl\\brdrs\\brdrw2";
		if (strpos(strtolower($border),"b") !== false)
			$this->MyRTF .= "\\clbrdrb\\brdrs\\brdrw2";
		if (strpos(strtolower($border),"r") !== false)
			$this->MyRTF .= "\\clbrdrr\\brdrs\\brdrw2";

        ### Assemble the cells of the table (the sum of the sizes of the cells should not exceed 21,600)
        $this->MyRTF .= "\\cellx".$sizeCell."\n";

        $this->MyRTF .= "\\pard \\intbl ";
        $this->align($align);
        $this->MyRTF .= $this->add_image($image, $ratio, $align)." \\cell\n";
    }
	
    /**
     * Method for creating the cellsImage of the table
     * @arg1		image file name
     * @arg2		size x of the cell
     * @arg3		size y of the cell
	 * @arg4		keyword  (left|center|right|justify)
	 * @arg5		background colour of the cell
	 * @return		void
     */
    function cellImagexy($image, $ratiox, $ratioy, $sizeCell, $align="left", $cellColorId = NULL, $border = ""){

        ### Calculate the size of the cell
        $sizeCell = floor($this->page_w*($sizeCell/100)) + $this->previousResult;
        $this->previousResult = $sizeCell;

        ### Border 1
		if (strpos(strtolower($border),"t") !== false)
			$this->MyRTF .= "\\trbrdrt\\brdrs\\brdrw2";
		if (strpos(strtolower($border),"l") !== false)
			$this->MyRTF .= "\\trbrdrl\\brdrs\\brdrw2";
		if (strpos(strtolower($border),"b") !== false)
			$this->MyRTF .= "\\trbrdrb\\brdrs\\brdrw2";
		if (strpos(strtolower($border),"r") !== false)
			$this->MyRTF .= "\\trbrdrr\\brdrs\\brdrw2";

        ### Put the background color of the cell
        if ($cellColorId){$this->MyRTF .= "\\clcfpatraw".$cellColorId."\\clshdngraw10000\n";}

        ### Border 2
		if (strpos(strtolower($border),"t") !== false)
			$this->MyRTF .= "\\clbrdrt\\brdrs\\brdrw2";
		if (strpos(strtolower($border),"l") !== false)
			$this->MyRTF .= "\\clbrdrl\\brdrs\\brdrw2";
		if (strpos(strtolower($border),"b") !== false)
			$this->MyRTF .= "\\clbrdrb\\brdrs\\brdrw2";
		if (strpos(strtolower($border),"r") !== false)
			$this->MyRTF .= "\\clbrdrr\\brdrs\\brdrw2";

        ### Assemble the cells of the table (the sum of the sizes of the cells should not exceed 21,600)
        $this->MyRTF .= "\\cellx".$sizeCell."\n";

        $this->MyRTF .= "\\pard \\intbl ";
        $this->align($align);
        $this->MyRTF .= $this->add_imagexy($image, $ratiox, $ratioy, $align)." \\cell\n";
    }
	
	/*
	*
	*	set of function for table
	*
	*/
	function open_tab($head_text, $head_width, $head_line_height=7, $head_border="TRHBL", $head_align="C", $font, $font_size="14", $font_style="b", $font_color=0, $fill_color=0, $COLUMN_FORMAT, $func_aggreg, $f_nofirsthead=false, $tab_align="full")
	{
		if (!$font) $font=$this->dfl_FontID;
		if (!$font_size) $font_size=$this->dfl_FontSize;
//		echo "font::$font, font_size::$font_size<br>";
		
		$this->f_table = 1;
		$this->arr_func_aggreg = (array) null;		// array tab head text format=text1 line 1|text1 line 2|text1 line 3,text2 line 1|text2 line 2|text2 line 3,.....
		$this->arr_tab_head = (array) null;		// array tab head text format=text1 line 1|text1 line 2|text1 line 3,text2 line 1|text2 line 2|text2 line 3,.....
		$this->arr_head_width = explode(",", $head_width);	// array tab head width
//		echo "$this->arr_head_width::".implode(",",$this->arr_head_width)."<br>";
		$this->head_fill_color = explode(",", $fill_color);			//  table head fill color format RRGGBB
		$this->head_font_name = explode(",", $font);					//  table head font name ex  "AngsanaUPC" "cordia"
		$this->head_font_size = explode(",", $font_size);			//  table head font size
		$this->head_font_style = explode(",", $font_style);			//  table head font style "B" bold "I" italic "U" underline
		$this->head_font_color = explode(",", $font_color);		//  table head font color  format RRGGBB
		$this->head_border = explode(",", $head_border);		//  table head border
		$this->arr_head_align = explode(",", $head_align);		//  table head align
		$this->head_line_height = $head_line_height;		//  table head line height
		$this->tab_align = $tab_align;						//  table align "full" adjust to full page width, "left", "center", "right"
		
		$this->tab_head_title = "";		//  table head title

//		echo "this->head_fill_color=".$fill_color." , this->head_font_name=$font , this->head_font_size=".$font_size." , this->head_font_style=".$font_style."<br>";
//		echo "head_text=".$head_text." , head_width=$head_width , head_align=".$head_align."<br>";
		$this->arr_column_map = (array) null;
		$this->arr_column_sel = (array) null;
		$this->arr_column_width = (array) null;
		$this->arr_column_align = (array) null;
		if (!$COLUMN_FORMAT) {	
			for($i=0; $i < count($this->arr_head_width); $i++) {
				$this->arr_column_map[] = $i;		// link index of head 
				$this->arr_column_sel[] = 1;			// 1=display 0=not display
			}
			$this->arr_column_width = explode(",",$head_width);	// width
			$this->arr_column_align = explode(",",$head_align);		// align data
			$COLUMN_FORMAT = implode(",",$this->arr_column_map)."|".implode(",",$this->arr_column_sel)."|".implode(",",$this->arr_column_width)."|".implode(",",$this->arr_column_align);
		} else {
			$arrbuff = explode("|",$COLUMN_FORMAT);
			$this->arr_column_map = explode(",",$arrbuff[0]);		//
			$this->arr_column_sel = explode(",",$arrbuff[1]);			// 1=select	0=not select
			$this->arr_column_align = explode(",",$arrbuff[3]);		// align
			if ($head_width != $arrbuff[2]) {
				$this->arr_column_width = explode(",",$head_width);		// width
				$COLUMN_FORMAT = implode(",",$this->arr_column_map)."|".implode(",",$this->arr_column_sel)."|".implode(",",$this->arr_column_width)."|".implode(",",$this->arr_column_align);
			} else {
				$this->arr_column_width = explode(",",$arrbuff[2]);		// width
			}
		}

		// if found TNUM at least one then $func_aggreg head set all to TNUM automeric
		if (strpos($func_aggreg, "TNUM") !== false) $this->f_head_tnum = true;
		else $this->f_head_tnum = false;
//		echo "map=".implode(",",$this->arr_column_map)."  sel=".implode(",",$this->arr_column_sel)."  width=".implode(",",$this->arr_column_width)."  align=".implode(",",$this->arr_column_align)." [$COLUMN_FORMAT]<br>";

//		echo "head font=".implode(",",$this->head_font_name).", size=".implode(",",$this->head_font_size)."<br>";

//		$this->SetFont($this->head_font_name,$this->head_font_style,$this->head_font_size);
		
		if (strlen(trim($head_text))==0) {
			$cnt_column_head = 0;
//			echo "1..head text = 0<br>";
		} else {
			$buff_arr_tab_head = explode(",", $head_text);
			$cnt_column_head = count($buff_arr_tab_head);
//			echo "2..head text = $cnt_column_head [$head_text]<br>";
		}	// end if (strlen(trim($head_text))==0)
		$cnt_column_width = count($this->arr_head_width);

//		echo "cnt_column_head=$cnt_column_head ($head_text) , cnt_column_width=$cnt_column_width (".implode(",",$this->arr_head_width).")<br>";
		if ($cnt_column_head==0) {
			$this->f_no_head = true; 
			for($i = 0; $i < $cnt_column_width; $i++) {
				$this->arr_tab_head[]="";
			}
			$cnt_column_head = $cnt_column_width;
//			echo "3..head width = $cnt_column_head<br>";
		} else {	// else if ($cnt_column_head==0)
//			echo "4..head text = $cnt_column_head<br>";
			// end of function aggregate
			if ($func_aggreg) {
//				echo "func_aggreg=$func_aggreg<br>";
				$this->arr_func_aggreg = explode(",", $func_aggreg);
			} // end if ($func_aggreg)
			// end of  function aggregate

			$founded = false;
			for($i = 0; $i < $cnt_column_head; $i++) {
				$this->arr_tab_head[] = str_replace("*Enter*","\\par",trim($buff_arr_tab_head[$i]));	// กรณีใส่ *Enter* หมายถึงตัดตัวอักษรขึ้นบรรทัดใหม่
				if (strlen(trim($buff_arr_tab_head[$i])) > 0) $founded = true;
			} // end for $i loop
			//  head has value at lease one column then print head
			if ($founded)	
				$this->f_no_head = false;
			else
				$this->f_no_head = true;
		}	// end if ($cnt_column_head==0)
//		echo "cnt_column_head ($cnt_column_head) != cnt_column_width ($cnt_column_width) (f_no_head=".$this->f_no_head.")<br>";
		if ($cnt_column_head != $cnt_column_width) {	// if wrong column number then return error
			return false; 
		} else {
//			echo "cnt_column_head ($cnt_column_head) != cnt_column_width ($cnt_column_width) (f_no_head=".$this->f_no_head.")<br>";
			// $f_nofirsthead not print first head
			return ($f_nofirsthead ? true : $this->print_tab_header());
		}
	}
	
	function print_tab_header()
	{
		global $NUMBER_DISPLAY;
		
		$res = true;
//		echo "tab_header->f_table=".$this->f_table.", f_no_head=".$this->f_no_head."<br>";
		if ($this->f_table==1 && (!$this->f_no_head)) {

//			echo "tab_head->".$this->arr_tab_head[0]."<br>";
			$cnt_column_head = count($this->arr_tab_head);
			$cnt_line_head = 0;
			for($ii = 0; $ii < $cnt_column_head; $ii++) {
				if ($cnt_line_head < count(explode("|", $this->arr_tab_head[$ii]))) {
					$cnt_line_head = count(explode("|", $this->arr_tab_head[$ii]));
				}
			}
//			echo "cnt_line_head=$cnt_line_head, cnt_column_head=$cnt_column_head  (f_no_head=".$this->f_no_head.")<br>";
			if ($cnt_column_head > 0) {	// if has column the print
				$cnt_column_show = 0;
				$head_t_w = 0;
				for($jjj = 0; $jjj < $cnt_column_head; $jjj++) {
					$arr_setborder[$jjj] = ""; 	// use for adjust L R Border
//					echo "$jjj-".$this->arr_column_map[$jjj]."=".$this->arr_column_sel[$this->arr_column_map[$jjj]]."<br>";
					if ($this->arr_column_sel[$this->arr_column_map[$jjj]]==1) { 
						$cnt_column_show++; 
						$head_t_w += $this->arr_column_width[$this->arr_column_map[$jjj]]; 
					}
				}

/* 				$this->tab_head_title = 
						text1^font^size^style^color^fill^align^border||text2^font^size^style^color^fill^align^border||text3^font^size^style^color^fill^align^border */

				if ($this->tab_head_title) {	// กรณีที่มีค่าของ table head title ค่า space ให้ถือว่าเป็นบรรทัดว่าง
//					echo "this->tab_head_title=$this->tab_head_title<br>";
					$a_text = explode("||",$this->tab_head_title);
					for($ii = 0; $ii < count($a_text); $ii++) {
						$this->open_line($this->tab_align, false, true);	// table head
						if ($this->head_line_height) $this->MyRTF .= "\\trrh".$this->head_line_height."\n";
//						echo "a_text [$ii]=".$a_text[$ii]."<br>";
						$a_sub_text = explode("^",$a_text[$ii]);
						$font = ($a_sub_text[1] ? $a_sub_text[1] : "");
						$border = ($a_sub_text[7] ? $a_sub_text[7] : "");
						$align = ($a_sub_text[6] ? $a_sub_text[6] : "C");
						$size = ($a_sub_text[2] ? $a_sub_text[2] : "14");
						$style = ($a_sub_text[3] ? $a_sub_text[3] : "");
						$color = ($a_sub_text[4] ? $a_sub_text[4] : 0);
						$fill_co = ($a_sub_text[5] ? $a_sub_text[5] : 0);
						
						if ($font) $this->MyRTF .= $this->_font($font );
						if ($size) $this->MyRTF .= $this->set_font_size($size);
						if ($color) $this->MyRTF .= $this->color($color);	// forground $color_id
						
						$f_bold = false;
						if (strpos(strtolower($style),"b")!==false) {
							$f_bold = true;
						}
						$f_underline = false;
						if (strpos(strtolower($buff),"u")!==false) {
							$f_underline = true;
						}
						$f_italic = false;
						if (strpos(strtolower($buff),"i")!==false) {
							$f_italic = true;
						}
						$f_rotate = false;
						if (strpos(strtolower($buff),"r")!==false) {
							$f_rotate = true;
						}
						$text = ($f_bold?$this->bold(1):"").$a_sub_text[0].($f_bold?$this->bold(0):"");
						$text = ($f_underline?$this->underline(1):"").$text.($f_underline?$this->underline(0):"");
						$text = ($f_italic?$this->italic(1):"").$text.($f_italic?$this->italic(0):"");
//						cell($msg, $sizeCell, $align="left", $cellBgColorId = NULL, $border = "", $valign="top", $textFlow="lrtb", $mrgCol=false, $mrgRow=false)
//						echo "TEXT=$text , W=$head_t_w , align=$align<br>";
						$this->cell($text, $head_t_w, $align, $fill_co, $border, "top", "lrtb", false, false);
						$this->close_line();
					}
				}

				$columngrp="";
				for($cntline = 0; $cntline < $cnt_line_head; $cntline++) {
					
					$this->open_line($this->tab_align, false, true);	// table head
					
					if ($this->head_line_height) $this->MyRTF .= "\\trrh".$this->head_line_height."\n";
					
					$text_merge = "";
					$head_align = "";
					$sum_merge = 0;
					$cnt_sel_i = 0;	// column select count
					for($iii = 0; $iii < $cnt_column_head; $iii++) {
						if ($this->arr_column_sel[$this->arr_column_map[$iii]]==1) {	// 1 = show this column
							$arr_font_name = explode("|", ($this->head_font_name[$this->arr_column_map[$iii]] ? $this->head_font_name[$this->arr_column_map[$iii]] : $this->head_font_name[0]) );
							$arr_font_style = explode("|", ($this->head_font_style[$this->arr_column_map[$iii]] ? $this->head_font_style[$this->arr_column_map[$iii]] : $this->head_font_style[0]) ); 
							// 	not style then none
							$arr_font_size = explode("|",  ($this->head_font_size[$this->arr_column_map[$iii]] ? $this->head_font_size[$this->arr_column_map[$iii]] : $this->head_font_size[0]) );
							$arr_font_color = explode("|",  ($this->head_font_color[$this->arr_column_map[$iii]] ? $this->head_font_color[$this->arr_column_map[$iii]] : $this->head_font_color[0]) );	// default black
							$arr_head_border = explode("|",  ($this->head_border[$this->arr_column_map[$iii]] ? $this->head_border[$this->arr_column_map[$iii]] : $this->head_border[0]) );
							$arr_head_fill_color = explode("|",  ($this->head_fill_color[$this->arr_column_map[$iii]] ? $this->head_fill_color[$this->arr_column_map[$iii]] : $this->head_fill_color[0]) );	// default width
							$t_head_font_name = ($arr_font_name[$cntline] ? $arr_font_name[$cntline] : $arr_font_name[0]);

							$buff = ($arr_font_style[$cntline] ? $arr_font_style[$cntline] : $arr_font_style[0]);
							$t_head_font_style = "";
							$f_bold = false;
							if (strpos(strtolower($buff),"b")!==false) {
								$t_head_font_style .= "b";

								$f_bold = true;
								$buff = str_replace("b","",$buff);
								$buff = str_replace("B","",$buff);
							}
							$f_underline = false;
							if (strpos(strtolower($buff),"u")!==false) {
								$t_head_font_style .= "u";
								$f_underline = true;
								$buff = str_replace("u","",$buff);
								$buff = str_replace("U","",$buff);
							}
							$f_italic = false;
							if (strpos(strtolower($buff),"i")!==false) {
								$t_head_font_style .= "i";
								$f_italic = true;

								$buff = str_replace("i","",$buff);
								$buff = str_replace("I","",$buff);
							}
							$f_rotate = false;
							if (strpos(strtolower($buff),"r")!==false) {
								$buff = str_replace("r","",$buff);
								$buff = str_replace("R","",$buff);
//								$t_head_rotate_angle = ($buff ? $buff : "90");
								$f_rotate = true;
							}
							$t_head_font_size	= ($arr_font_size[$cntline] ? $arr_font_size[$cntline] : $arr_font_size[0]);
							$t_head_font_color	= ($arr_font_color[$cntline] ? $arr_font_color[$cntline] : $arr_font_color[0]);		// default black
							$t_head_border		= ($arr_head_border[$cntline] ? $arr_head_border[$cntline] : $arr_head_border[0]);
							$t_head_fill_color	= ($arr_head_fill_color[$cntline] ? $arr_head_fill_color[$cntline] : $arr_head_fill_color[0]);	// default white

							$border = $t_head_border;
							$head_align = $this->arr_head_align[$this->arr_column_map[$iii]];

							$nline = explode("|", $this->arr_tab_head[$this->arr_column_map[$iii]]);
//							echo "line:$cntline column:$iii text:".$nline[$cntline]." font::$t_head_font_name, size::$t_head_font_size<br>";

							$chk_merge = strpos($nline[$cntline],"<**");
							if ($chk_merge!==false) {	// column merge
								$c = strpos($nline[$cntline],"**>");
								if ($c!==false) {
									$buff_colgrp = substr($nline[$cntline], $chk_merge+3, $c-($chk_merge+3));
									if (strpos($buff_colgrp,"^")!==false) { 
										$f_mergeup = 2;
										$buff_colgrp = substr($buff_colgrp,0,strlen($buff_colgrp)-1);
									} else {
										$f_mergeup = 0;
										if (trim($nline[$cntline+1])) {	// check col in next row ifmerge row then this cell is first cell row
											$chk_merge1 = strpos($nline[$cntline+1],"<**");
											if ($chk_merge1!==false) {	// column merge
												$c1 = strpos($nline[$cntline+1],"**>");
												if ($c1!==false) {
													$buff_colgrp1 = substr($nline[$cntline+1], $chk_merge1+3, $c1-($chk_merge1+3));
													if (strpos($buff_colgrp1,"^")!==false) { 
														$f_mergeup = 1;
													}
												}
											}
										} else {
											$f_mergeup = 1;
										}
									}
									$text_merge = substr($nline[$cntline],0,$chk_merge).substr($nline[$cntline],$c+3);
									if ($columngrp==$buff_colgrp)	// next  cell 
										$f_mergecol = 2;
									else {
										$f_mergecol = 1;
										$columngrp = $buff_colgrp;
									}

									if ($this->f_head_tnum) $text_merge = convert2thaidigit($text_merge);

									$this->MyRTF .= $this->_font($t_head_font_name);
									$this->MyRTF .= $this->set_font_size($t_head_font_size);
									if ($t_head_font_color) $this->MyRTF .= $this->color($t_head_font_color);	// forground $color_id

//									echo "1.>>>".$text_merge."<br>";

									$text_merge = ($f_bold?$this->bold(1):"").$text_merge.($f_bold?$this->bold(0):"");
									$text_merge = ($f_underline?$this->underline(1):"").$text_merge.($f_underline?$this->underline(0):"");
									$text_merge = ($f_italic?$this->italic(1):"").$text_merge.($f_italic?$this->italic(0):"");
									$v = "";
//									echo "2.>>>".$text_merge."<br>";
									if (strpos(strtolower($head_align),"c")!==false) $a = "center";
									else if (strpos(strtolower($head_align),"l")!==false) $a = "left";
									else if (strpos(strtolower($head_align),"r")!==false) $a = "right";
									else if (strpos(strtolower($head_align),"t")!==false) $v = "top";
									else if (strpos(strtolower($head_align),"b")!==false) $v = "bottom";
									if (strpos(strtolower($head_align),"cc")!==false) $v = "center";
									if (!$v) $v = "top";
//									echo "3.>>>".$text_merge." $head_align ($a)($v)<br>";

//	cell($msg, $sizeCell, $align="left", $cellBgColorId = NULL, $border = "", $valign="top", $textFlow="lrtb", $mrgCol=false, $mrgRow=false )
//									echo "head-merge $text_merge, font-".$t_head_font_color.", bg-$t_head_fill_color<br>";
									$col_w = $this->arr_column_width[$this->arr_column_map[$iii]];
//									echo "4.>>>".$text_merge."<br>";
									if ($f_rotate)	// cell merge column not first column
										$this->cell($text_merge, $col_w, $a, $t_head_fill_color, $border, $v, "btlr", $f_mergecol, $f_mergeup);
									else
										$this->cell($text_merge, $col_w, $a, $t_head_fill_color, $border, $v, "", $f_mergecol, $f_mergeup);
								} else {
									$ret = false;
								} // end if ($c!==false)
							} else {	// text นี้ไม่เป็นกลุ่ม
								$f_mergeup = 0;
								if (!trim($nline[$cntline])) {
									if ($cntline == 0) {	// text นี้ ไม่มีค่า
										if (strpos($nline[$cntline+1],"<**")===false) {	// column ล่างไม่เป็นกลุ่ม
//											echo "1-($cntline)-".$nline[$cntline]."<br>";
											$f_mergeup = 1;
										}
									} else {	// text นี้ ไม่มีค่า
										if (strpos($nline[$cntline-1],"<**")===false) {	// column บนไม่เป็นกลุ่ม
//											echo "2-($cntline)-".$nline[$cntline]."<br>";
											$f_mergeup = 2;
										} else {
											$f_mergeup = 1;
										}
									}
								} else {	// text นี้มีค่า
									if ($cntline==0) {
										if (strpos($nline[$cntline+1],"<**")===false) {	// column ล่างไม่เป็นกลุ่ม
											$f_mergeup = 1;
										}
									} else {	// $cntline > 0
										if (strpos($nline[$cntline-1],"<**")===false) {		// column บนไม่เป็นกลุ่ม
//											echo "3-($cntline)-".$nline[$cntline]."<br>";
											$f_mergeup = 2;
										} else if (strpos($nline[$cntline+1],"<**")===false) {	// column ล่างไม่เป็นกลุ่ม
//											echo "4-($cntline)-".$nline[$cntline]."<br>";
											$f_mergeup = 1;
										}
									}
								}
//								if ($f_mergeup) echo "..($cntline)..".$nline[$cntline]."..[".$nline[$cntline-1]."]...f_mergeup=$f_mergeup<br>";
								if ($f_mergeup==1) {
									for($cc = $cntline+1; $cc < $cnt_line_head; $cc++) {
										if (strpos($nline[$cc],"<**")===false) {	// ถ้าตัวถัดไปไม่เป็นกลุ่ม
											$nline[$cntline] .= $nline[$cc];
										} else break;
									}
								}
								if ($this->f_head_tnum) 
									$text_merge = convert2thaidigit($nline[$cntline]); 
								else $text_merge = $nline[$cntline];

								$v = "";
								if (strpos(strtolower($head_align),"c")!==false) $a = "center";
								else if (strpos(strtolower($head_align),"l")!==false) $a = "left";
								else if (strpos(strtolower($head_align),"r")!==false) $a = "right";
								else if (strpos(strtolower($head_align),"t")!==false) $v = "top";
								else if (strpos(strtolower($head_align),"b")!==false) $v = "bottom";
								if (strpos(strtolower($head_align),"cc")!==false) $v = "center";
								if (!$v) $v = "top";

//	cell($msg, $sizeCell, $align="left", $cellBgColorId = NULL, $border = "", $valign="top", $textFlow="lrtb", $mrgCol=false, $mrgRow=false )
								$this->MyRTF .= $this->_font($t_head_font_name);
								$this->MyRTF .= $this->set_font_size($t_head_font_size);
								if ($t_head_font_color) $this->MyRTF .= $this->color($t_head_font_color);	// forground $color_id

								$text_merge = ($f_bold?$this->bold(1):"").$text_merge.($f_bold?$this->bold(0):"");
								$text_merge = ($f_underline?$this->underline(1):"").$text_merge.($f_underline?$this->underline(0):"");
								$text_merge = ($f_italic?$this->italic(1):"").$text_merge.($f_italic?$this->italic(0):"");
								$col_w = $this->arr_column_width[$this->arr_column_map[$iii]];
//								echo "head $text_merge, font-".$t_head_font_color.", bg-$t_head_fill_color<br>";
								if ($f_rotate)
									$this->cell($text_merge, $col_w, $a, $t_head_fill_color, $border, $v, "btlr", false, $f_mergeup);
								else
									$this->cell($text_merge, $col_w, $a, $t_head_fill_color, $border, $v, "", false, $f_mergeup);
								$columngrp="";
							} // end if ($chk_merge!==false)
							$last_iii = $iii;
							$cnt_sel_i++;
						}	// end if ($this->arr_column_sel[$iii]==1)
					} //// end for $iii loop
					$this->close_line();
				}	// end for $cntline loop
			} else {
				$ret = false;
			} // end if ($cnt_column_head > 0)
		} // end if ($this->f_table==1)

		return $res;
	}	// end function print_tab_header

	function set_tab_head_title($title)
	{
		$this->tab_head_title = $title;
	}

	function add_data_tab($arr_data, $line_height=7, $border="TRHBL", $data_align, $font, $font_size="14", $font_style="", $font_color=0, $fill_color=0, $f_thispage=false)
	{
		// เรื่อง merge row --> &&row&& ใน RTF ใช้ &&rowone&& บอกว่าเป็น rowแรก &&row&& เป็น row ต่อ ๆ ไป
		$ret = true;
		if ($this->f_table==1) {

			$this->arr_column_align = $data_align;		// align for data (data_align not map index so in function need to map index)

			if (!$font) $font=$this->dfl_FontID;
			if (!$font_size) $font_size=$this->dfl_FontSize;
//			echo "font=".$font." -- ".implode("|",$arr_data)."<br>";

			if (gettype($fill_color)=="array")	$this->arr_tabdata_fill_color = $fill_color;
			else { $this->arr_tabdata_fill_color = (array) null;	$this->arr_tabdata_fill_color[] = ($fill_color ? $fill_color : 0);	}
			if (gettype($font)=="array")		$this->arr_tabdata_font_name = $font;
			else { $this->arr_tabdata_font_name = (array) null;		$this->arr_tabdata_font_name[] = $font; }
			if (gettype($font_size)=="array")	$this->arr_tabdata_font_size = $font_size;
			else { $this->arr_tabdata_font_size = (array) null;		$this->arr_tabdata_font_size[] = $font_size; }
			if (gettype($font_style)=="array")	$this->arr_tabdata_font_style = $font_style;
			else { $this->arr_tabdata_font_style = (array) null;	$this->arr_tabdata_font_style[] = $font_style; }
			if (gettype($font_color)=="array")	$this->arr_tabdata_font_color = $font_color;	
			else { $this->arr_tabdata_font_color = (array) null;	$this->arr_tabdata_font_color[] = ($font_color ? $font_color : 0); }
			if (gettype($border)=="array")		$this->arr_tabdata_border = $border;
			else { $this->arr_tabdata_border = (array) null;		$this->arr_tabdata_border[] = $border; }

			$d_align = "C";
			$max_line = 0;
			$arr_sub_data = (array) null;
			$arr_merge_grp = (array) null;
			$arr_msum_grp = (array) null;
			
			$cnt_column_head = count($this->arr_tab_head);
			for($iii = 0; $iii < $cnt_column_head; $iii++) 	$arr_msum_grp[]=0;

			$cnt_column = count($arr_data);
			$cnt_column_show = 0;
			for($jjj = 0; $jjj <= count($this->arr_tab_head); $jjj++)  if ($this->arr_column_sel[$this->arr_column_map[$jjj]]==1) $cnt_column_show++;	
			
			if ($this->merge_rows) {
				$arr_merge_rows = explode(",", $this->merge_rows);
				$arr_rows_image = explode(",", $this->rows_image);
			} else {
				$arr_merge_rows = (array) null;
				$arr_rows_image = (array) null;
				for($iii = 0; $iii < $cnt_column_head; $iii++) {
					$arr_merge_rows[]="";
					$arr_rows_image[]="";
				}
			}

			$col_aggreg = $this->do_aggregate($arr_data);
			$arr_column_aggreg = explode("|", $col_aggreg);
//			echo ">>".implode(",",$arr_data).">>$col_aggreg (cnt_column_head=$cnt_column_head)<br>";
//			echo "col_aggreg=$col_aggreg<br>";

			$this->open_line($this->tab_align, $f_thispage, false);	// agument 2 is true full line , false is rest line print on next page
																												// agument 3 is head  = true print all page, false just one time

			if ($line_height) $this->MyRTF .= "\\trrh".$line_height."\n";

			$grpidx = "";
			$cnt=0;
			$img_idx = 0;
			for($iii = 0; $iii < $cnt_column_head; $iii++) {
//				echo "column loop iii=$iii<br>";
				if ($this->arr_column_sel[$this->arr_column_map[$iii]]==1) {	// 1 = show this column
					$nline = $arr_data[$this->arr_column_map[$iii]];
//					echo "aggregate at $iii-(".$this->arr_column_map[$iii].")->$nline==aggreg->".$arr_column_aggreg[$iii]."<br>";
					if (trim($arr_column_aggreg[$iii])) {	// agregate from do_agregate already map 
						$nline = $arr_column_aggreg[$iii];
//						echo "aggregate at $iii-(".$this->arr_column_map[$iii].")->$nline==aggreg->".$arr_column_aggreg[$iii]."<br>";
					}

					$this->tabdata_font_name = ($this->arr_tabdata_font_name[$this->arr_column_map[$iii]]	? $this->arr_tabdata_font_name[$this->arr_column_map[$iii]]	: $this->arr_tabdata_font_name[0]);
//					echo "--->style $iii [".$this->arr_tabdata_font_style[$this->arr_column_map[$iii]]."]=".$this->arr_tabdata_font_style[$this->arr_column_map[$iii]].",".$this->arr_tabdata_font_style[0]."<br>";
					$buff = ($this->arr_tabdata_font_style[$this->arr_column_map[$iii]]	? $this->arr_tabdata_font_style[$this->arr_column_map[$iii]] : $this->arr_tabdata_font_style[0]);
					$this->tabdata_font_style = "";
					$f_bold = false;
					if (strpos(strtolower($buff),"b")!==false) {
						$this->tabdata_font_style .= "b";
						$buff = str_replace("b","",$buff);
						$buff = str_replace("B","",$buff);
						$f_bold = true;
					}
					$f_underline = false;
					if (strpos(strtolower($buff),"u")!==false) {
						$this->tabdata_font_style .= "u";
						$buff = str_replace("u","",$buff);
						$buff = str_replace("U","",$buff);
						$f_underline = true;
					}
					$f_italic = false;
					if (strpos(strtolower($buff),"i")!==false) {
						$this->tabdata_font_style .= "i";
						$buff = str_replace("i","",$buff);
						$buff = str_replace("I","",$buff);
						$f_italic = true;
					}
					$f_rotate = false;
					if (strpos(strtolower($buff),"r")!==false) {
						$buff = str_replace("r","",$buff);
						$buff = str_replace("R","",$buff);
						$f_rotate = true;
					}

					$this->tabdata_font_size	= ($this->arr_tabdata_font_size[$this->arr_column_map[$iii]]		? $this->arr_tabdata_font_size[$this->arr_column_map[$iii]]		: $this->arr_tabdata_font_size[0]);
					$this->tabdata_font_color	= ($this->arr_tabdata_font_color[$this->arr_column_map[$iii]]		? $this->arr_tabdata_font_color[$this->arr_column_map[$iii]]	: $this->arr_tabdata_font_color[0]);
					$this->tabdata_border		= ($this->arr_tabdata_border[$this->arr_column_map[$iii]]			? $this->arr_tabdata_border[$this->arr_column_map[$iii]]			: $this->arr_tabdata_border[0]);
					$this->tabdata_fill_color		= ($this->arr_tabdata_fill_color[$this->arr_column_map[$iii]]		? $this->arr_tabdata_fill_color[$this->arr_column_map[$iii]]		: $this->arr_tabdata_fill_color[0]);

					$border = $this->tabdata_border;

//					echo "$iii-".$nline."|<br>";
					$chk_merge = strpos($nline,"<**");
					if ($chk_merge!==false) {
						$c = strpos($nline,"**>");
						if ($c!==false) {	// if group merge
							$mgrp = substr($nline, $chk_merge+3, $c-($chk_merge+3));
//							echo "[$nline]..group>>".$mgrp."==".$grpidx."<br>"; 
							if ($mgrp != $grpidx) {	// if merge column group change
								$f_mergecol = 1;
								$grpidx = $mgrp;
							} else {	// if same merge column group
								$f_mergecol = 2;
							}
							$ntext = substr($nline, 0, $chk_merge)." ".substr($nline, $c+3);
						} else {	// wrong format
							$ret = false;
						}
					} else {	// if this column not group
						$ntext = $nline;
						$grpidx = "";
						$f_mergecol = 0;
					} // end if ($chk_merge!==false)
					$chk_mrow = strpos($ntext,"<&&row&&>");
					if ($chk_mrow!==false) {
						if ($f_nextfirst == 1) // ถ้าตัวก่อนเป็น &&end&&
							$f_mergerow = 1;
						else
							$f_mergerow = 2;
						$ntext = str_replace("<&&row&&>","",$ntext);
						$f_nextfirst = 0;
					} else {
						$chk_mrow = strpos($ntext,"<&&rowone&&>");
						if ($chk_mrow!==false) {
							$f_mergerow = 1;
							$ntext = str_replace("<&&rowone&&>","",$ntext);
						} else
							$f_mergerow = 0;
						$f_nextfirst = 0;
					}
					
					if ($ret) {

						$head_align = $this->arr_column_align[$this->arr_column_map[$iii]];
	
						$text_merge = $ntext;

						$this->MyRTF .= $this->_font($this->tabdata_font_name);
						$this->MyRTF .= $this->set_font_size($this->tabdata_font_size);
						if ($this->tabdata_font_color) $this->MyRTF .= $this->color($this->tabdata_font_color);	// forground $color_id

						$text_merge = ($f_bold?$this->bold(1):"").$text_merge.($f_bold?$this->bold(0):"");
						$text_merge = ($f_underline?$this->underline(1):"").$text_merge.($f_underline?$this->underline(0):"");
						$text_merge = ($f_italic?$this->italic(1):"").$text_merge.($f_italic?$this->italic(0):"");

						if (strpos(strtolower($head_align),"c")!==false) $a = "center";
						else if (strpos(strtolower($head_align),"l")!==false) $a = "left";
						else if (strpos(strtolower($head_align),"r")!==false) $a = "right";
						else if (strpos(strtolower($head_align),"t")!==false) $v = "top";
						else if (strpos(strtolower($head_align),"b")!==false) $v = "bottom";
						if (strpos(strtolower($head_align),"cc")!==false) $v = "center";
						if (!$v) $v = "top";

//	cell($msg, $sizeCell, $align="left", $cellBgColorId = NULL, $border = "", $valign="top", $textFlow="lrtb", $mrgCol=false, $mrgRow=false )
//						echo "add data-$text_merge, font-".$this->tabdata_font_color." bg-".$this->tabdata_fill_color."<br>";

						$text_merge = str_replace("*Enter*","\\par",$text_merge);	// กรณีใส่ *Enter* หมายถึงตัดตัวอักษรขึ้นบรรทัดใหม่
						
						$col_w = $this->arr_column_width[$this->arr_column_map[$iii]];
						if ($f_rotate) {	// cell merge column not first column
							$this->cell($text_merge, $col_w, $a, $this->tabdata_fill_color, $border, $v, "btlr", $f_mergecol, $f_mergerow);
						} else {
							$this->cell($text_merge, $col_w, $a, $this->tabdata_fill_color, $border, $v, "", $f_mergecol, $f_mergerow);
						}
						$cnt++;
//						echo "just show ($cnt)-$ntext | ".$this->arr_column_width[$iii]." | ".$this->arr_column_align[$this->arr_column_map[$iii]]." | ".$this->arr_column_align[$this->arr_column_map[$iii]]."<br>";
					} // end if ($ret)
				} // if ($this->arr_column_sel[$iii]==1) {	// 1 = show this column
			} // end for $iii (each column)
			$this->close_line();
		} // end if ($this->f_table==1)
		return $ret;
	}	// end function add_data_tab

	function add_text_line($text, $line_height=7, $border="TRHBL", $align, $font, $font_size="14", $font_style="", $font_color=0, $fill_color=0)
	{
		global $NUMBER_DISPLAY;

		$ret = true;
		if ($this->f_table==1) {

			if (!$font) $font=$this->dfl_FontID;
			if (!$font_size) $font_size=$this->dfl_FontSize;
//			echo "font=".$font."<br>";

			if (gettype($fill_color)=="array")	$this->arr_tabdata_fill_color = $fill_color;
			else { $this->arr_tabdata_fill_color = (array) null;	$this->arr_tabdata_fill_color[] = ($fill_color ? $fill_color : 0);	}
			if (gettype($font)=="array")		$this->arr_tabdata_font_name = $font;
			else { $this->arr_tabdata_font_name = (array) null;		$this->arr_tabdata_font_name[] = $font; }
			if (gettype($font_size)=="array")	$this->arr_tabdata_font_size = $font_size;
			else { $this->arr_tabdata_font_size = (array) null;		$this->arr_tabdata_font_size[] = $font_size; }
			if (gettype($font_style)=="array")	$this->arr_tabdata_font_style = $font_style;
			else { $this->arr_tabdata_font_style = (array) null;	$this->arr_tabdata_font_style[] = $font_style; }
			if (gettype($font_color)=="array")	$this->arr_tabdata_font_color = $font_color;	
			else { $this->arr_tabdata_font_color = (array) null;	$this->arr_tabdata_font_color[] = ($font_color ? $font_color : 0); }
			if (gettype($border)=="array")		$this->arr_tabdata_border = $border;
			else { $this->arr_tabdata_border = (array) null;		$this->arr_tabdata_border[] = $border; }
			
			$this->tabdata_fill_color = $fill_color;
			$this->tabdata_font_name = $font;
			$this->tabdata_font_size = $font_size;
			$this->tabdata_font_color = $font_color;

			if ($NUMBER_DISPLAY==2) $text = convert2thaidigit($text);

			$buff = ($font_style ? strtolower($font_style) : "");
//			echo "buff=$buff, font_style=$font_style<br>";
			$this->tabdata_font_style = "";
			$f_bold = false;
			if (strpos($buff,"b")!==false) {
				$this->tabdata_font_style .= "b";
				$buff = str_replace("b","",$buff);
				$buff = str_replace("B","",$buff);
				$f_bold = true;
			}
			$f_underline = false;
			if (strpos($buff,"u")!==false) {
				$this->tabdata_font_style .= "u";
				$buff = str_replace("u","",$buff);
				$buff = str_replace("U","",$buff);
				$f_underline = true;
			}
			$f_italic = false;
			if (strpos($buff,"i")!==false) {
				$this->tabdata_font_style .= "i";
				$buff = str_replace("i","",$buff);
				$buff = str_replace("I","",$buff);
				$f_italic = true;
			}
			$f_rotate = false;
			if (strpos($buff,"r")!==false) {
				$buff = str_replace("r","",$buff);
				$buff = str_replace("R","",$buff);
				$f_rotate = true;
			}

			$this->open_line($this->tab_align, true, false);	// agument 2 = true is show all line in this page, false rest line show on next page

			$this->MyRTF .= $this->_font($this->tabdata_font_name);
			$this->MyRTF .= $this->set_font_size($this->tabdata_font_size);
			if ($this->tabdata_font_color) $this->MyRTF .= $this->color($this->tabdata_font_color);	// forground $color_id

			$cnt_column_head = count($this->arr_tab_head);
			$cnt_column_show = 0;
			$sum_w = 0;
			for($jjj = 0; $jjj < $cnt_column_head; $jjj++) {
				if ($this->arr_column_sel[$this->arr_column_map[$jjj]]==1) { 
					$cnt_column_show++;	 
					$sum_w += $this->arr_column_width[$this->arr_column_map[$jjj]]; 
				}
			}

			$text = ($f_bold?$this->bold(1):"").$text.($f_bold?$this->bold(0):"");
			$text = ($f_underline?$this->underline(1):"").$text.($f_underline?$this->underline(0):"");
			$text = ($f_italic?$this->italic(1):"").$text.($f_italic?$this->italic(0):"");
//			echo "text=$text(".$f_bold.",".$f_underline.",".$f_italic.") font_style=".$this->tabdata_font_style."<br>";

			if (strpos(strtolower($head_align),"c")!==false) $a = "center";
			else if (strpos(strtolower($head_align),"l")!==false) $a = "left";
			else if (strpos(strtolower($head_align),"r")!==false) $a = "right";
			else if (strpos(strtolower($head_align),"t")!==false) $v = "top";
			else if (strpos(strtolower($head_align),"b")!==false) $v = "bottom";
			if (strpos(strtolower($head_align),"cc")!==false) $v = "center";
			if (!$v) $v = "top";

//	cell($msg, $sizeCell, $align="left", $cellBgColorId = NULL, $border = "", $valign="top", $textFlow="lrtb", $mrgCol=false, $mrgRow=false )
			$text = str_replace("*Enter*","\\par",$text);	// กรณีใส่ *Enter* หมายถึงตัดตัวอักษรขึ้นบรรทัดใหม่

			$col_w = $this->arr_column_width[$this->arr_column_map[$iii]];
			if ($f_rotate)	// cell merge column not first column
				$this->cell($text, $sum_w, $a, $this->tabdata_fill_color, $border, $v, "btlr", false, false);
			else
				$this->cell($text, $sum_w, $a, $this->tabdata_fill_color, $border, $v, "", false, false);
				
			$this->close_line();
		} // end if ($this->f_table==1)
		return $ret;
	}	// end function add_text_line

	function close_tab()
	{
		if ($this->f_table==1) {
			// print close table line 
			$this->f_table = 0;
			$this->arr_func_aggreg = (array) null;	
			$this->arr_tab_head = (array) null;
			$this->arr_head_width = (array) null;
			$this->head_fill_color = "";
			$this->head_font_name = "";
			$this->head_font_size = "";
			$this->head_font_style = "";
			$this->head_font_color = "";
			$this->head_border = "";
			$this->arr_head_align = (array) null;
			$this->head_line_height = "";
			$this->tab_align = "";
			$this->tabdata_fill_color = "";
			$this->tabdata_font_name = "";
			$this->tabdata_font_size = "";
			$this->tabdata_font_style = "";
			$this->tabdata_rotate_angle = "";
			$this->tabdata_font_color = "";
			$this->arr_tabdata_font_name = (array) null;
			$this->arr_tabdata_font_style = (array) null;
			$this->arr_tabdata_font_size = (array) null;
			$this->arr_tabdata_font_color = (array) null;
			$this->arr_tabdata_border = (array) null;
			$this->arr_tabdata_fill_color = (array) null;
			$this->merge_rows = "";
			$this->rows_image = "";
			$this->merge_rows_h = (array) null;
			$this->arr_column_map = (array) null;
			$this->arr_column_sel = (array) null;
			$this->arr_column_width = (array) null;
			$this->arr_column_align = (array) null;
			
			$this->MyRTF .= "\\pard";
		} // end if ($this->f_table==1)
	}	// end function create_tab

	function do_aggregate($arr_data) {
			// set agregate function
//			echo "data>".implode(",",$arr_data).", func>".implode(",",$this->arr_func_aggreg).", sel>".implode(",",$this->arr_column_sel).", map>".implode(",",$this->arr_column_map)."<br>";
			$arr_column_aggreg = (array) null;
			
			$ret = $this->do_aggregate_argu($arr_data, $this->arr_func_aggreg, $this->arr_column_sel, $this->arr_column_map);
//			echo "agregate=$ret<br>";
			
			return $ret;
	} // end function

	function do_aggregate_argu($arr_data, $arr_func_aggreg, $arr_column_sel, $arr_column_map) {
			// set agregate function with parameter for calling outsite open_tab


			$arr_column_aggreg = (array) null;
//			echo "1..data>".implode(",",$arr_data)."<br>";

			$arr_grp = (array) null;
			$arr_text = (array) null;
			$arr_ff = (array) null;
			$arr_sum = (array) null;
			
			/**************************************************************************/
			/**** superate group data number, string add to array 						****/
			/**************************************************************************/
			for($i=0; $i < count($arr_data); $i++) {
				$arr_ff[] = "";
				$arr_sum[] = 0;
				$chk_merge = strpos($arr_data[$i],"<**");
				if ($chk_merge!==false) {
					$c = strpos($arr_data[$i],"**>");
					if ($c!==false) {
						$mgrp = substr($arr_data[$i], $chk_merge, $c-$chk_merge+3);
						$buff = str_replace($mgrp,"",$arr_data[$i]);
//						if (is_numeric($buff))	{ $arr_data[$i] = $buff; $arr_grp[] = $mgrp; }
//						else { $arr_data[$i] = "$buff"; $arr_grp[] = $mgrp; }
//						if (is_numeric($buff))	{ $arr_data[$i] = $buff; $arr_grp[] = $mgrp; }
//						else { $arr_data[$i] = ""; $arr_grp[] = $mgrp.$buff; }
						if (is_numeric($buff))	{ $arr_data[$i] = $buff; $arr_grp[] = $mgrp; $arr_text[] = ""; }
						else { $arr_data[$i] = ""; $arr_grp[] = $mgrp;  $arr_text[] = $buff; }
					} else {
						$arr_grp[] = ""; $arr_text[] = "";
					}
				} else {	// no group
					$arr_grp[] = ""; $arr_text[] = "";
				}
			}
			/**************************************************************************/
			/**** superate group data number, string add to array 						****/
			/***************************** End Block *********************************/
//			echo "0..data>".implode(",",$arr_data)." | ".implode(",",$arr_grp)." | ".implode(",",$arr_text)."<br>";
			$arr_column_aggreg = (array) null;
//			echo ">map>".implode(",",$arr_column_map)."<br>";

			if (count($arr_func_aggreg) > 0) {
				/**************************************************************************/
				/****** compute argregate function $arr_data is numeric only *********/
				/**************************************************************************/
				for($kk=0; $kk < count($arr_func_aggreg); $kk++) {
					if ($arr_column_sel[$arr_column_map[$kk]]==1) {	// 1 = show this column
//						echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-".$arr_func_aggreg[$arr_column_map[$kk]]."<br>";
						$a_sub_func = explode("|",$arr_func_aggreg[$arr_column_map[$kk]]);
						for($ii = 0; $ii < count($a_sub_func); $ii++) {
//							echo "a_sub_func [$ii]=".$a_sub_func[$ii]."<br>";
							$c = strpos($a_sub_func[$ii],"SUM-");
							if ($c !== false) {
								$arr_ff[$kk] = "S";
								$buff = substr($a_sub_func[$ii],4);
								$a = explode("-",$buff);
								$sum = 0;
								$havedata = false;
								$pgrp = "";
								for($kkk=0; $kkk < count($a); $kkk++) {
									if ($arr_column_sel[$a[$kkk]]==1) {	// 1 = show this column
										$ndata = trim($arr_data[$a[$kkk]]);
										if ($ndata!="") $havedata = true;
//										echo "sum kk=$kk--kkk=$kkk--a[$kkk]=".$a[$kkk]."--map=".$arr_column_map[$a[$kkk]]."-->ndata=$ndata, havedata=$havedata<br>";
										if (!$arr_grp[$a[$kkk]] || $pgrp != $arr_grp[$a[$kkk]]) {
											$sum += (float)$ndata;
											$pgrp = $arr_grp[$a[$kkk]];
										}
									}
								}
								if ($havedata) $lastval = (string)$sum; else $lastval = chr(96);	 // chr(96) replace space
//								if ($havedata) echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-sum-".$sum."-lastval=$lastval<br>";
							} else {
								$c = strpos($a_sub_func[$ii],"PERC");
								if ($c !== false) {
									$arr_ff[$kk] = "P";
									$c1=strpos($a_sub_func[$ii],"-");
									if ($c1==4) $forcol=-1;
									else $forcol=substr($a_sub_func[$ii],4,$c1-4);
									$buff = substr($a_sub_func[$ii],$c1+1);
									$a = explode("-",$buff);
									$thisdata = -1;
									$sum = 0;
									$cnt = 0;
									$havedata = false;
									$pgrp = "";
									for($kkk=0; $kkk < count($a); $kkk++) {
										if ($arr_column_sel[$a[$kkk]]==1) {	// 1 = show this column 
											$ndata = trim($arr_data[$a[$kkk]]);
											if ($ndata!="") $havedata = true;
//											echo "perc kk=$kk--kkk=$kkk--(".$arr_column_map[$kkk].")--a[$kkk]=".$a[$kkk]."--map=".$arr_column_map[$a[$kkk]]."-->ndata=$ndata, forcol=$forcol, havedata=$havedata<br>";
											if ($forcol==$a[$kkk]) $thisdata = (float)$ndata;
											if (!$arr_grp[$a[$kkk]] || $pgrp != $arr_grp[$a[$kkk]]) {
												$sum += (float)$ndata;
												$pgrp = $arr_grp[$a[$kkk]];
											}
											$cnt++;
										}
									}
									if ($havedata)	
										if ($sum > 0) {
											if ($thisdata == -1) $thisdata = $sum;	// summary
											$lastval = (string)($thisdata / $sum * 100);	// in percent
//											echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-perc=$thisdata / $sum * 100<br>";
										} else $lastval="0";
									else $lastval = chr(96);	 // chr(96) replace space
								} else {
									$c = strpos($a_sub_func[$ii],"AVG-");
									if ($c !== false) {
										$buff = substr($a_sub_func[$ii],4);
										$arr_ff[$kk] = "A";
										$a = explode("-",$buff);
										$thisdata = 0;
										$sum = 0;
										$cnt = 0;
										$havedata = false;
										$pgrp = "";
										for($kkk=0; $kkk < count($a); $kkk++) {
											if ($arr_column_sel[$a[$kkk]]==1) {
												$ndata = trim($arr_data[$a[$kkk]]);
												if ($ndata!="") $havedata = true;
												if (!$arr_grp[$a[$kkk]] || $pgrp != $arr_grp[$a[$kkk]]) {
													$sum += (float)$ndata;
													$pgrp = $arr_grp[$a[$kkk]];
												}
												$cnt++;
											}
										}
										if ($havedata)
											$lastval = (string)($sum / $cnt);	 // do average
										else 	$lastval = chr(96);	 // chr(96) repalce space
//										echo "function aggregate avg-".($sum / $cnt)."<br>";
									} else {
										$c = strpos($a_sub_func[$ii],"FORM");
										if ($c !== false) {
											$buff = substr($a_sub_func[$ii],4);
											$arr_ff[$kk] = "F";
//											echo "buff=$buff, len=".strlen($buff)."<br>";
											preg_match_all("/([+|-|*|\/|\(|\)])/", $buff, $matches, PREG_OFFSET_CAPTURE);
//											echo "data=".implode(",",$arr_data)."<br>";
											$para = (array) null;
											foreach($matches[0] as $key1 => $val1) {
//												echo "......key1=$key1 => ".$matches[0][$key1][0]."";
												if ($key1==0) $str = substr($buff, 0, $matches[0][$key1][1]);
												else if ((int)$matches[0][$key1][1] > 0) 
													$str = substr($buff, $matches[0][$key1-1][1]+1, $matches[0][$key1][1]-$matches[0][$key1-1][1]-1);
//												echo ">>".$matches[0][$key1][1]."=>$str<br>";
												if (strpos($str,"@")!==false) { 
//													if ($ii > 0) $ndata = trim($arr_column_aggreg[$arr_column_map[(int)substr($str,1)]]);
//													else $ndata = (string)trim($arr_data[$arr_column_map[(int)substr($str,1)]]);
													if ($ii > 0) $ndata = trim($arr_column_aggreg[(int)substr($str,1)]);
													else $ndata = (string)trim($arr_data[(int)substr($str,1)]);
//													echo "1..parameter [$str]=$ndata map:".$arr_column_map[(int)substr($str,1)]."<br>";
													$para[str_replace("@","",$str)] = $ndata;
												}
												if (((int)$matches[0][$key1+1][1] == 0)) {
													$str = substr($buff, $matches[0][$key1][1]+1);
//													echo "...last key =>".$matches[0][$key1][0].">>".$matches[0][$key1][1]."=>$str<br>";
													if (strpos($str,"@")!==false) { 
//														if ($ii > 0) $ndata = trim($arr_column_aggreg[$arr_column_map[(int)substr($str,1)]]);
//														else $ndata = (string)trim($arr_data[$arr_column_map[(int)substr($str,1)]]);
														if ($ii > 0) $ndata = trim($arr_column_aggreg[(int)substr($str,1)]);
														else $ndata = (string)trim($arr_data[(int)substr($str,1)]);
//														echo "2..parameter [$str]=$ndata map:".$arr_column_map[(int)substr($str,1)]."<br>";
														$para[str_replace("@","",$str)] = $ndata;
													}
												}
											}
//											print_r($para);
											$val = compute_formula($buff, $para);
//											echo "<br>compute=$val<br>";
											$lastval = (string)$val;	// average
//											echo "function aggregate formula-".($sum / $cnt)."<br>";
										} else {	// if nothing
											if ($ii > 0) $ndata = $arr_column_aggreg[$arr_column_map[$kk]];
											else $ndata = (string)$arr_data[$arr_column_map[$kk]];
											$lastval = $ndata;
//											echo "no function-".$lastval.", ndata=$ndata<br>";
										} // end if FORMULA
									} // end if AVG
								} // end if PERC
							} // end if SUM
//							$arr_column_aggreg[$arr_column_map[$kk]] = $lastval;
							$arr_column_aggreg[$arr_column_map[$kk]] = $lastval;
						} // end for loop $ii
					} else { // else if ($arr_column_sel[$iii]==1)
						$arr_column_aggreg[$arr_column_map[$kk]] = "";
					} // end if ($arr_column_sel[$iii]==1)
				} // end loop for $kk
				/**************************************************************************/
				/****** compute argregate function $arr_data is numeric only *********/
				/****************************** End Block ********************************/

//				echo "1..aggreg=".implode(",",$arr_column_aggreg)." | func=".implode(",",$arr_func_aggreg)." | grp=".implode(",",$arr_grp)." | text=".implode(",",$arr_text)." | ff=".implode(",",$arr_ff)."<br>";
				
				/**************************************************************************/
				/****** compute argregate function for merge Number              *********/
				/**************************************************************************/
				if (count($arr_column_aggreg) > 0) {
					$mergesum = 0; $grp = "";
					for($i=0; $i < count($arr_column_aggreg); $i++) {
//						echo "grp=".$arr_grp[$i]." && ff=".$arr_ff[$i]." && val=".$arr_column_aggreg[$i]."<br>";
						if ($arr_grp[$i] && $arr_ff[$i] && is_numeric($arr_column_aggreg[$i])) {
							if ($arr_grp[$i] != $grp) {
//								echo "1..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
								if ($mergesum)
									$arr_sum[$last_i] = $mergesum;
								$grp = $arr_grp[$i];
								$mergesum = $arr_column_aggreg[$i];
								$last_i = $i;
							}  else {
								$mergesum += $arr_column_aggreg[$i];
//								echo "2..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
							}
						} else {
//							echo "3..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
							if ($mergesum) {
								$arr_sum[$last_i] = $mergesum;
								$last_i = -1;
							}
							$mergesum = 0;
							$grp = "";
						}
					} // end loop for
//					echo "4..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
					if ($mergesum)
						$arr_sum[$last_i] = $mergesum;
						
					$grp = ""; $ss = 0;
					for($i=0; $i < count($arr_column_aggreg); $i++) {
//						echo "$i .. arr_grp ..".$arr_grp[$i].",  grp=$grp<br>";
						if ($arr_sum[$i])
							if ($arr_grp[$i] && $arr_grp[$i] != $grp) {
								$ss = $arr_sum[$i];
								$arr_column_aggreg[$i] = $ss;
								$grp = $arr_grp[$i];	
//								echo "$i .1. set ss=$ss<br>";
							} else {
								if ($arr_grp[$i]) {
									$arr_column_aggreg[$i] = $ss;
//									echo "$i .2. loop ss=$ss<br>";										
								}
							}
//						echo "5..$i..".$arr_column_aggreg[$i].".. arr_text..".$arr_text[$i]."<br>";
						if ($arr_column_aggreg[$i]=="" || ($arr_column_aggreg[$i]==chr(96) && $arr_text[$i])) $arr_column_aggreg[$i] = $arr_text[$i];
//						echo "6..$i..".$arr_column_aggreg[$i].".. arr_text..".$arr_text[$i]."<br>";
					} // end loop for
				}
				/**************************************************************************/
				/****** compute argregate function for merge Number value  *********/
				/****************************** End Block ********************************/

//				echo "2..aggreg=".implode(",",$arr_column_aggreg)." , func=".implode(",",$arr_func_aggreg)." , sum=".implode(",",$arr_sum)."<br>";

				/***************************************************************************/
				/******************* second loop format  TNUM , ENUM *****************/
				/***************************************************************************/
				for($kk=0; $kk < count($arr_func_aggreg); $kk++) {
					if ($arr_column_sel[$arr_column_map[$kk]]==1) {	// 1 = show this column
//						echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-".$arr_func_aggreg[$arr_column_map[$kk]]."<br>";
						$a_sub_func = explode("|",$arr_func_aggreg[$arr_column_map[$kk]]);
						for($ii = 0; $ii < count($a_sub_func); $ii++) {
//							echo "a_sub_func [$ii]=".$a_sub_func[$ii]."<br>";
							$c = strpos($a_sub_func[$ii],"TNUM");
							if ($c !== false) {
								$imgpart = "";
								$ndata = $arr_column_aggreg[$arr_column_map[$kk]];
//								echo "TNUM - $ii - ".$a_sub_func[$ii]." , [$ndata]<br>";
								$c1 = strpos($ndata,"<*img*");
								$c2 = strpos($ndata,"*img*>");
								if ($c1!==false && $c2!==false) {
									$imgpart = substr($ndata,$c1,$c2-$c1+6);
//									echo "imgpart=$imgpart<br>";
									$ndata = str_replace($imgpart,chr(126),$ndata);
								}
								if ($ndata==chr(96)) {	 // chr(96)replace space
									$lastval = " ";
//									echo "TNUM space ($ndata) (".chr(96).") logic(".($ndata==chr(96)).")<br>";
								} else {
//									echo "TNUM $ndata<br>";
									$zero = (string)substr($a_sub_func[$ii], 4, 1);
//									echo "zero=$zero, TNUM $ndata<br>";
									if ($zero=="0")  $c1=$c+6;
									else {
//										if (!$ndata)
//											echo "null [".trim($ndata)."] (is_numeric:".is_numeric($ndata).") (float:".((float)$ndata==0).") (".($ndata=="").")<br>";
										if ((is_numeric($ndata) && (float)$ndata==0)) $ndata="-";
										$c1=$c+5;
									}
									$nocomma = (strtolower(substr($a_sub_func[$ii], $c1,1))=="x"?true:false);
//									echo "function-".$a_sub_func[$ii]." nocomma=$nocomma<br>";
									if ($nocomma) 
										$dig = substr($a_sub_func[$ii], $c1+1);
									else
										$dig = substr($a_sub_func[$ii], $c1);
									if (!is_numeric($ndata))  {
										if (!$ndata && $zero=="0") $ndata = 0;
//										echo "1..ndata=$ndata<br>";
										$lastval = convert2thaidigit($ndata);
									} else if ((int)$dig > 0) {
//										echo "2..ndata=$ndata<br>";
										if ($nocomma)
											$lastval = convert2thaidigit($ndata,$dig);
										else
											$lastval = convert2thaidigit(number_format($ndata,$dig));
									} else {
//										echo "3..ndata=$ndata<br>";
										if ($dig == "z") {
											$numd = explode(".",$ndata);
											$digstr = $numd[1];
											$ndig = 0;
											for($i=strlen($digstr)-1; $i >= 0; $i--) {
												if (substr($digstr,$i,1)!="0") { $ndig = $i+1; break; }
											}
//											echo "first part:".$numd[0]." second part:".$numd[1]." ndig:$ndig<br>";
											if ($ndig == 0) 
												if ($nocomma)
													$lastval = convert2thaidigit($ndata);
												else
													$lastval = convert2thaidigit(number_format($ndata));
											else
												if ($nocomma)
													$lastval = convert2thaidigit($ndata,$ndig);
												else
													$lastval = convert2thaidigit(number_format($ndata,$ndig));
										} else 
											if ($nocomma)
												$lastval = convert2thaidigit($ndata);
											else
												$lastval = convert2thaidigit(number_format($ndata));
									}
								}
								if ($imgpart) $lastval = str_replace(chr(126),$imgpart,$lastval);
								$arr_column_aggreg[$arr_column_map[$kk]] = $lastval;
//								echo "function TNUM $kk-(map=".$arr_column_map[$kk].")-tnum-lastval=".$lastval.", ndata=$ndata, dig=$dig<br>";
							} else {
								$c = strpos($a_sub_func[$ii],"ENUM");
								if ($c !== false) {
									$ndata = $arr_column_aggreg[$arr_column_map[$kk]];
//									echo "ENUM - $ii - ".$a_sub_func[$ii]." , $ndata<br>";
									if ($ndata==chr(96)) {  // chr(96) replace space
										$lastval = " ";
									} else {
										$zero = (string)substr($a_sub_func[$ii], 4, 1);
										if ($zero=="0")  $c1=$c+6;
										else {
//											if ((is_numeric($ndata) && (float)$ndata==0) || (!$ndata)) $ndata="-";
											if ((is_numeric($ndata) && (float)$ndata==0)) $ndata="-";
											$c1=$c+5;
										}
										$nocomma = (strtolower(substr($a_sub_func[$ii], $c1,1))=="x"?true:false);
//										echo "function-".$a_sub_func[$ii]." nocomma=$nocomma<br>";
										if ($nocomma) 
											$dig = substr($a_sub_func[$ii], $c1+1);
										else
											$dig = substr($a_sub_func[$ii], $c1);
										if (!is_numeric($ndata))  {
											if (!$ndata && $zero=="0") $ndata = 0;
											$lastval = $ndata;
										} else if ((int)$dig > 0) {
											if ($nocomma)
												$lastval = $ndata;
											else
												$lastval = number_format($ndata,$dig);
										} else {
											if ($dig == "z") {
												$numd = explode(".",$ndata);
												$digstr = $numd[1];
												$ndig = 0;
												for($i=strlen($digstr)-1; $i >= 0; $i--) {
													if (substr($digstr,$i,1)!="0") { $ndig = $i+1; break; }
												}
//												echo "first part:".$numd[0]." second part:".$numd[1]." ndig:$ndig<br>";
												if ($ndig == 0) 
													if ($nocomma)
														$lastval = $ndata;
													else
														$lastval = number_format($ndata);
												else
													if ($nocomma)
														$lastval = $ndata;
													else
														$lastval = number_format($ndata,$ndig);
											} else 
												if ($nocomma)
													$lastval = $ndata;
												else
													$lastval = number_format($ndata);
										}
									}
									$arr_column_aggreg[$arr_column_map[$kk]] = $lastval;
//									echo "function enum-".$lastval.", ndata=$ndata, dig=$dig<br>";
//									echo "function ENUM $kk-(map=".$arr_column_map[$kk].")-tnum-lastval=".$lastval.", ndata=$ndata, dig=$dig<br>";
								} // end if ENUM
							} // end if TNUM
						} // end for loop $ii
					} // end if ($this->arr_column_sel[$iii]==1)
				} // end loop for $kk
				/***************************************************************************/
				/******************* second loop format  TNUM , ENUM *****************/
				/******************************* End Block ********************************/

//						echo "3..aggreg=".implode(",",$arr_column_aggreg)."<br>";
			
			} // end if if (count($arr_func_aggreg) > 0)

			/***************************************************************************/
			/************  do Merge column and Write print WorkSheet    ************/
			/***************************************************************************/
			$cntcolumn = count($arr_column_aggreg);
			
			if ($cntcolumn > 0) {
				$cntshow = 0;
				for($i=0; $i < $cntcolumn; $i++) {
					if ($arr_column_aggreg[$i])
						$arr_column_aggreg[$i] = $arr_grp[$i].$arr_column_aggreg[$i];
					else $arr_column_aggreg[$i] = $arr_grp[$i];
					if ($arr_column_sel[$arr_column_map[$i]]==1) $cntshow++;
				}
			}
			// end setting agregate function
			
			return (count($arr_column_aggreg) > 0 ? implode("|", $arr_column_aggreg) : "");
	} // end function

}
?>