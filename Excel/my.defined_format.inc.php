<?php
	//  Usage 	: 	set_format(fmtName, fontStyle, alignment, border, isMerge) 
	// 	Remark 	:	fmtName : 
	//							choose one from : "xlsFmtTitle", "xlsFmtSubTitle", "xlsFmtTableHeader", "xlsFmtTableDetail"
	//						fontStyle : 
	//							sequence of character , choose from : "" = no style, "B" = bold, "I" = italic, "U" = underline
	//							example : "BUI" = bold , underline, italic
	//						alignment : 
	//							choose one from : "L" = left , "R" = right , "C" = center
	//						border : 
	//							sequence of character , choose from : "T" = top, "L"= left, "R" = right, "B" = bottom
	//							example : "TLR" = top , left , right
	// 						isMerge : Merge Cell -> 1 or 0
	//  Problem :	if set isMerge = 1, alignment will not work properly, it will set alignment to center 
	//						because it share same variable to set merge and alignment [ class.writeexcel_format.inc.php -> $_text_h_align ]

	$arr_alignment = array("center", "left", "right", "justify");
	$arr_font_style = array("none", "B", "I", "U", "BI", "BU", "IU", "BIU");
	$arr_cell_border = array("none", "T", "L", "R", "B", "TL", "TR", "TB", "LR", "LB", "RB", "TLR", "TLB", "TRB", "LRB", "TLRB");
	$arr_valignment = array("top", "bottom", "vcenter");
	
//	echo "111..font=$font<br>";
//	echo "1..PRINT_FONT=$PRINT_FONT<br>";
	if ($PRINT_FONT==1) {
		$xlsfont = "Angsana New";
		$xlstitlesize = 16;
		$xlssubtitlesize = 16;
		$xlstableheadsize = 14;
		$xlstabledetailsize = 14;
	} else if ($PRINT_FONT==2) {
		$xlsfont = "Cordia New";
		$xlstitlesize = 16;
		$xlssubtitlesize = 16;
		$xlstableheadsize = 14;
		$xlstabledetailsize = 14;
	} else if ($PRINT_FONT==3) {
		$xlsfont = "TH SarabunPSK";
		$xlstitlesize = 16;
		$xlssubtitlesize = 16;
		$xlstableheadsize = 14;
		$xlstabledetailsize = 14;
	} else if ($PRINT_FONT==4) {
		$xlsfont = "Browallia New";
		$xlstitlesize = 16;
		$xlssubtitlesize = 16;
		$xlstableheadsize = 14;
		$xlstabledetailsize = 14;
	} else if ($PRINT_FONT==99) {
		$xlsfont = getToFont($CH_PRINT_FONT);
		$xlstitlesize = $CH_PRINT_SIZE;
		$xlssubtitlesize = $CH_PRINT_SIZE;
		$xlstableheadsize = $CH_PRINT_SIZE;
		$xlstabledetailsize = $CH_PRINT_SIZE;
	} else {
		if ($font)
			$xlsfont = $font;
		else
			$xlsfont = "TH SarabunPSK";	// ถ้าไม่ได้กำหนดค่า font ให้ default=TH SarabunPSK
		$xlstitlesize = 16;
		$xlssubtitlesize = 16;
		$xlstableheadsize = 14;
		$xlstabledetailsize = 14;
	}
//	echo "1..xlsfont=$xlsfont<br>";

	function set_format($fmtName, $fontFormat="", $alignment="C", $border="", $isMerge=0, $wrapText=0, $rotate=0, $color="", $bgcolor=""){
		global ${$fmtName}, ${$fmtName."Merge"};

		$FormatName = ${$fmtName.(($isMerge)?"Merge":"")};

//		echo "in set_format-->wrap=".$wrapText.", rotate=".$rotate.", align=$alignment<br>";

		$alignment = strtoupper(trim($alignment));
		if (strpos($alignment,"T") !== false) {
			$valign = "top";
			$alignment = str_replace("T","",$alignment);
		} 
		if (strpos($alignment,"V") !== false) {
			$valign = "vcenter";
			$alignment = str_replace("T","",$alignment);
		}
		if (strpos($alignment,"B") !== false) {
			$valign = "bottom";
			$alignment = str_replace("B","",$alignment);
		}
		if (strpos($alignment,"VJ") !== false) {
			$valign = "vjustify";
			$alignment = str_replace("V","",$alignment);
		}
		// set text alignment
		switch($alignment){
			case "L" 				:
			case "LEFT" 		:
				$alignment = "left";
				break;
			case "R" 				:
			case "RIGHT" 		:
				$alignment = "right";
				break;
			case "C" 				:
			case "CENTER" 	:
				$alignment = "center";
				break;
			case "J" 					:
			case "JUSTIFY" 	:
				$alignment = "justify";
				break;
			default 	: 
				$alignment = "center";
		} // end switch case

		// set font style		
		if(trim($fontFormat)==""){ 		
			$fontStyle = "none";
		}else{
			$fontStyle = "";
			if(strpos(strtoupper($fontFormat), "B") !== false) $fontStyle .= "B";
			if(strpos(strtoupper($fontFormat), "I") !== false) $fontStyle .= "I";
			if(strpos(strtoupper($fontFormat), "U") !== false) $fontStyle .= "U";
		} // end if

		// set cell border
		if(trim($border)==""){ 
			$cellBorder = "none";
		}else{
			$cellBorder = "";
			if(strpos(strtoupper($border), "T") !== false) $cellBorder .= "T";
			if(strpos(strtoupper($border), "L") !== false) $cellBorder .= "L";
			if(strpos(strtoupper($border), "R") !== false) $cellBorder .= "R";
			if(strpos(strtoupper($border), "B") !== false) $cellBorder .= "B";
		} // end if

//		echo "$alignment :: $fontStyle :: $cellBorder<br>";

		$FormatName[$alignment][$fontStyle][$cellBorder]->set_align("top");
		if ((int)$rotate > 0) {
			$FormatName[$alignment][$fontStyle][$cellBorder]->set_rotation((int)$rotate); // 1.แนวตั้ง ตัวอักษรปกติ 2. แนวตั้งตัวอักษรนอนซ้าย
		}
		if ($wrapText==1) {
			$FormatName[$alignment][$fontStyle][$cellBorder]->set_text_wrap();
		}
		
		if ($valign)	$FormatName[$alignment][$fontStyle][$cellBorder]->set_align($valign);
		else $FormatName[$alignment][$fontStyle][$cellBorder]->set_align("top");

//		echo "color=$color, bgcolor=$bgcolor<br>";
		if ($color)
			$FormatName[$alignment][$fontStyle][$cellBorder]->set_color("$color");
		if ($bgcolor)
			$FormatName[$alignment][$fontStyle][$cellBorder]->set_bg_color("$bgcolor");

		return	$FormatName[$alignment][$fontStyle][$cellBorder];
	} // function

	//  Usage 	: 	set_format_new(fmtName, function_parameter) 
	// 	Remark 	:	fmtName : 
	//							choose one from : "xlsFmtTitle", "xlsFmtSubTitle", "xlsFmtTableHeader", "xlsFmtTableDetail"
	//						function_parameter :
	//							combination of parameter seperate by & (same way as you pass parameter to html file)
	//							list of combination , see function set_format 
	//						example :
	//							set_format_new("xlsFmtTableDetail", "alignment=C&border=LR&bgColor=black");
	//							this will return format that defined in xlsFmtTableDetail 
	//							with cell alignment = center, cell border = left & right, cell background color = black

	function set_format_new( $fmtName, $function_parameter ) {
		if(trim($function_parameter)) parse_str($function_parameter);		
		$returnFormat = set_format($fmtName, $fontFormat, $alignment, $border, $isMerge, $fgColor, $bgColor, $setRotation, $valignment, $fontSize, $fontName, $wrapText);
		return $returnFormat;
	} // function
	
	if (!$xlsFmtTitle_color) $xlsFmtTitle_color_idx = 32; else { $buff = explode("^",$xlsFmtTitle_color); $xlsFmtTitle_color_idx = $buff[0]; }
	if (!$xlsFmtTitle_bgcolor) $xlsFmtTitle_bgcolor_idx = 'white'; else { $buff = explode("^",$xlsFmtTitle_bgcolor); $xlsFmtTitle_bgcolor_idx = $buff[0]; }
	foreach($arr_alignment as $alignment){
		foreach($arr_font_style as $fontStyle){
			foreach($arr_cell_border as $cellBorder){
				$xlsFmtTitle1[$alignment][$fontStyle][$cellBorder][font]=$xlsfont;
				$xlsFmtTitle1[$alignment][$fontStyle][$cellBorder][size]=$xlstitlesize;
				$xlsFmtTitle1[$alignment][$fontStyle][$cellBorder][fcolor_idx]=$xlsFmtTitle_color_idx;
				$xlsFmtTitle1[$alignment][$fontStyle][$cellBorder][bcolor_idx]=$xlsFmtTitle_bgcolor_idx;
				
				$xlsFmtTitle[$alignment][$fontStyle][$cellBorder] = & $workbook->addformat();
				$xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_font($xlsfont);
				$xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_size($xlstitlesize);
				$xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_color($xlsFmtTitle_color_idx);
				if(strpos($fontStyle, "B") !== false) $xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_bold(1);
				if(strpos($fontStyle, "I") !== false) $xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_italic(1);
				if(strpos($fontStyle, "U") !== false) $xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_underline(1);
				$xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_bg_color($xlsFmtTitle_bgcolor_idx);
				if(strpos($cellBorder, "T") !== false) $xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_top(1);
				if(strpos($cellBorder, "L") !== false) $xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_left(1);
				if(strpos($cellBorder, "R") !== false) $xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_right(1);
				if(strpos($cellBorder, "B") !== false) $xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_bottom(1);
				$xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_align("$alignment");
					
				$xlsFmtTitleMerge[$alignment][$fontStyle][$cellBorder] = & $workbook->addformat();
				$xlsFmtTitleMerge[$alignment][$fontStyle][$cellBorder]->set_font($xlsfont);
				$xlsFmtTitleMerge[$alignment][$fontStyle][$cellBorder]->set_size($xlstitlesize);
				$xlsFmtTitleMerge[$alignment][$fontStyle][$cellBorder]->set_color($xlsFmtTitle_color_idx);
				if(strpos($fontStyle, "B") !== false) $xlsFmtTitleMerge[$alignment][$fontStyle][$cellBorder]->set_bold(1);
				if(strpos($fontStyle, "I") !== false) $xlsFmtTitleMerge[$alignment][$fontStyle][$cellBorder]->set_italic(1);
				if(strpos($fontStyle, "U") !== false) $xlsFmtTitleMerge[$alignment][$fontStyle][$cellBorder]->set_underline(1);
				$xlsFmtTitleMerge[$alignment][$fontStyle][$cellBorder]->set_bg_color($xlsFmtTitle_bgcolor_idx);
				if(strpos($cellBorder, "T") !== false) $xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_top(1);
				if(strpos($cellBorder, "L") !== false) $xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_left(1);
				if(strpos($cellBorder, "R") !== false) $xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_right(1);
				if(strpos($cellBorder, "B") !== false) $xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_bottom(1);
//				$xlsFmtTitleMerge[$alignment][$fontStyle][$cellBorder]->set_align("$alignment");
				$xlsFmtTitleMerge[$alignment][$fontStyle][$cellBorder]->set_merge();
			} // end foreach
		} // end foreach
	} // end foreach
	
//	echo "<pre>"; print_r($xlsFmtTitle); echo "</pre>";
//	echo "<pre>"; print_r($xlsFmtTitleMerge); echo "</pre>";

	if (!$xlsFmtSubTitle_color) $xlsFmtSubTitle_color_idx = 32; else { $buff = explode("^",$xlsFmtSubTitle_color); $xlsFmtSubTitle_color_idx = $buff[0]; }
	if (!$xlsFmtSubTitle_bgcolor) $xlsFmtSubTitle_bgcolor_idx = 'white'; else { $buff = explode("^",$xlsFmtSubTitle_bgcolor); $xlsFmtSubTitle_bgcolor_idx = $buff[0]; }
	foreach($arr_alignment as $alignment){
		foreach($arr_font_style as $fontStyle){			
			foreach($arr_cell_border as $cellBorder){
				$xlsFmtSubTitle1[$alignment][$fontStyle][$cellBorder][font]=$xlsfont;
				$xlsFmtSubTitle1[$alignment][$fontStyle][$cellBorder][size]=$xlssubtitlesize;
				$xlsFmtSubTitle1[$alignment][$fontStyle][$cellBorder][fcolor_idx]=$xlsFmtSubTitle_color_idx;
				$xlsFmtSubTitle1[$alignment][$fontStyle][$cellBorder][bcolor_idx]=$xlsFmtSubTitle_bgcolor_idx;
				
				$xlsFmtSubTitle[$alignment][$fontStyle][$cellBorder] = & $workbook->addformat();
				$xlsFmtSubTitle[$alignment][$fontStyle][$cellBorder]->set_font($xlsfont);
				$xlsFmtSubTitle[$alignment][$fontStyle][$cellBorder]->set_size($xlssubtitlesize);
				$xlsFmtSubTitle[$alignment][$fontStyle][$cellBorder]->set_color($xlsFmtSubTitle_color_idx);
				if(strpos($fontStyle, "B") !== false) $xlsFmtSubTitle[$alignment][$fontStyle][$cellBorder]->set_bold(1);
				if(strpos($fontStyle, "I") !== false) $xlsFmtSubTitle[$alignment][$fontStyle][$cellBorder]->set_italic(1);
				if(strpos($fontStyle, "U") !== false) $xlsFmtSubTitle[$alignment][$fontStyle][$cellBorder]->set_underline(1);
				$xlsFmtSubTitle[$alignment][$fontStyle][$cellBorder]->set_bg_color($xlsFmtSubTitle_bgcolor_idx);
				if(strpos($cellBorder, "T") !== false) $xlsFmtSubTitle[$alignment][$fontStyle][$cellBorder]->set_top(1);
				if(strpos($cellBorder, "L") !== false) $xlsFmtSubTitle[$alignment][$fontStyle][$cellBorder]->set_left(1);
				if(strpos($cellBorder, "R") !== false) $xlsFmtSubTitle[$alignment][$fontStyle][$cellBorder]->set_right(1);
				if(strpos($cellBorder, "B") !== false) $xlsFmtSubTitle[$alignment][$fontStyle][$cellBorder]->set_bottom(1);
				$xlsFmtSubTitle[$alignment][$fontStyle][$cellBorder]->set_align("$alignment");
					
				$xlsFmtSubTitleMerge[$alignment][$fontStyle][$cellBorder] = & $workbook->addformat();
				$xlsFmtSubTitleMerge[$alignment][$fontStyle][$cellBorder]->set_font($xlsfont);
				$xlsFmtSubTitleMerge[$alignment][$fontStyle][$cellBorder]->set_size($xlssubtitlesize);
				$xlsFmtSubTitleMerge[$alignment][$fontStyle][$cellBorder]->set_color($xlsFmtSubTitle_color_idx);
				if(strpos($fontStyle, "B") !== false) $xlsFmtSubTitleMerge[$alignment][$fontStyle][$cellBorder]->set_bold(1);
				if(strpos($fontStyle, "I") !== false) $xlsFmtSubTitleMerge[$alignment][$fontStyle][$cellBorder]->set_italic(1);
				if(strpos($fontStyle, "U") !== false) $xlsFmtSubTitleMerge[$alignment][$fontStyle][$cellBorder]->set_underline(1);
				$xlsFmtSubTitleMerge[$alignment][$fontStyle][$cellBorder]->set_bg_color($xlsFmtSubTitle_bgcolor_idx);
				if(strpos($cellBorder, "T") !== false) $xlsFmtSubTitleMerge[$alignment][$fontStyle][$cellBorder]->set_top(1);
				if(strpos($cellBorder, "L") !== false) $xlsFmtSubTitleMerge[$alignment][$fontStyle][$cellBorder]->set_left(1);
				if(strpos($cellBorder, "R") !== false) $xlsFmtSubTitleMerge[$alignment][$fontStyle][$cellBorder]->set_right(1);
				if(strpos($cellBorder, "B") !== false) $xlsFmtSubTitleMerge[$alignment][$fontStyle][$cellBorder]->set_bottom(1);
//				$xlsFmtSubTitleMerge[$alignment][$fontStyle][$cellBorder]->set_align("$alignment");
				$xlsFmtSubTitleMerge[$alignment][$fontStyle][$cellBorder]->set_merge();
			} // end foreach
		} // end foreach
	} // end foreach

//	echo "<pre>"; print_r($xlsFmtSubTitle); echo "</pre>";
//	echo "<pre>"; print_r($xlsFmtSubTitleMerge); echo "</pre>";

	if (!$xlsFmtTableHeader_color) $xlsFmtTableHeader_color_idx = 48; else { $buff = explode("^",$xlsFmtTableHeader_color); $xlsFmtTableHeader_color_idx = $buff[0]; }
	if (!$xlsFmtTableHeader_bgcolor) $xlsFmtTableHeader_bgcolor_idx = 31; else { $buff = explode("^",$xlsFmtTableHeader_bgcolor); $xlsFmtTableHeader_bgcolor_idx = $buff[0]; }
	foreach($arr_alignment as $alignment){
		foreach($arr_font_style as $fontStyle){			
			foreach($arr_cell_border as $cellBorder){
				$xlsFmtTableHeader1[$alignment][$fontStyle][$cellBorder][font]=$xlsfont;
				$xlsFmtTableHeader1[$alignment][$fontStyle][$cellBorder][size]=$xlstableheadsize;
				$xlsFmtTableHeader1[$alignment][$fontStyle][$cellBorder][fcolor_idx]=$xlsFmtTableHeader_color_idx;
				$xlsFmtTableHeader1[$alignment][$fontStyle][$cellBorder][bcolor_idx]=$xlsFmtTableHeader_bgcolor_idx;
				
				$xlsFmtTableHeader[$alignment][$fontStyle][$cellBorder] = & $workbook->addformat();
				$xlsFmtTableHeader[$alignment][$fontStyle][$cellBorder]->set_font($xlsfont);
				$xlsFmtTableHeader[$alignment][$fontStyle][$cellBorder]->set_size($xlstableheadsize);
				$xlsFmtTableHeader[$alignment][$fontStyle][$cellBorder]->set_color($xlsFmtTableHeader_color_idx);
				if(strpos($fontStyle, "B") !== false) $xlsFmtTableHeader[$alignment][$fontStyle][$cellBorder]->set_bold(1);
				if(strpos($fontStyle, "I") !== false) $xlsFmtTableHeader[$alignment][$fontStyle][$cellBorder]->set_italic(1);
				if(strpos($fontStyle, "U") !== false) $xlsFmtTableHeader[$alignment][$fontStyle][$cellBorder]->set_underline(1);
				$xlsFmtTableHeader[$alignment][$fontStyle][$cellBorder]->set_bg_color($xlsFmtTableHeader_bgcolor_idx);
				if(strpos($cellBorder, "T") !== false) $xlsFmtTableHeader[$alignment][$fontStyle][$cellBorder]->set_top(1);
				if(strpos($cellBorder, "L") !== false) $xlsFmtTableHeader[$alignment][$fontStyle][$cellBorder]->set_left(1);
				if(strpos($cellBorder, "R") !== false) $xlsFmtTableHeader[$alignment][$fontStyle][$cellBorder]->set_right(1);
				if(strpos($cellBorder, "B") !== false) $xlsFmtTableHeader[$alignment][$fontStyle][$cellBorder]->set_bottom(1);
				$xlsFmtTableHeader[$alignment][$fontStyle][$cellBorder]->set_align("$alignment");
					
				$xlsFmtTableHeaderMerge[$alignment][$fontStyle][$cellBorder] = & $workbook->addformat();
				$xlsFmtTableHeaderMerge[$alignment][$fontStyle][$cellBorder]->set_font($xlsfont);
				$xlsFmtTableHeaderMerge[$alignment][$fontStyle][$cellBorder]->set_size($xlstableheadsize);
				$xlsFmtTableHeaderMerge[$alignment][$fontStyle][$cellBorder]->set_color($xlsFmtTableHeader_color_idx);
				if(strpos($fontStyle, "B") !== false) $xlsFmtTableHeaderMerge[$alignment][$fontStyle][$cellBorder]->set_bold(1);
				if(strpos($fontStyle, "I") !== false) $xlsFmtTableHeaderMerge[$alignment][$fontStyle][$cellBorder]->set_italic(1);
				if(strpos($fontStyle, "U") !== false) $xlsFmtTableHeaderMerge[$alignment][$fontStyle][$cellBorder]->set_underline(1);
				$xlsFmtTableHeaderMerge[$alignment][$fontStyle][$cellBorder]->set_bg_color($xlsFmtTableHeader_bgcolor_idx);
				if(strpos($cellBorder, "T") !== false) $xlsFmtTableHeaderMerge[$alignment][$fontStyle][$cellBorder]->set_top(1);
				if(strpos($cellBorder, "L") !== false) $xlsFmtTableHeaderMerge[$alignment][$fontStyle][$cellBorder]->set_left(1);
				if(strpos($cellBorder, "R") !== false) $xlsFmtTableHeaderMerge[$alignment][$fontStyle][$cellBorder]->set_right(1);
				if(strpos($cellBorder, "B") !== false) $xlsFmtTableHeaderMerge[$alignment][$fontStyle][$cellBorder]->set_bottom(1);
//				$xlsFmtTableHeaderMerge[$alignment][$fontStyle][$cellBorder]->set_align("$alignment");
				$xlsFmtTableHeaderMerge[$alignment][$fontStyle][$cellBorder]->set_merge();
			} // end foreach
		} // end foreach
	} // end foreach

//	echo "<pre>"; print_r($xlsFmtTableHeader); echo "</pre>";
//	echo "<pre>"; print_r($xlsFmtTableHeaderMerge); echo "</pre>";

	if (!$xlsFmtTableDetail_color) $xlsFmtTableDetail_color_idx = 'black'; else { $buff = explode("^",$xlsFmtTableDetail_color); $xlsFmtTableDetail_color_idx = $buff[0]; }
	if (!$xlsFmtTableDetail_bgcolor) $xlsFmtTableDetail_bgcolor_idx = 'white'; else { $buff = explode("^",$xlsFmtTableDetail_bgcolor); $xlsFmtTableDetail_bgcolor_idx = $buff[0]; }
	foreach($arr_alignment as $alignment){
		foreach($arr_font_style as $fontStyle){			
			foreach($arr_cell_border as $cellBorder){
				$xlsFmtTableDetail1[$alignment][$fontStyle][$cellBorder][font]=$xlsfont;
				$xlsFmtTableDetail1[$alignment][$fontStyle][$cellBorder][size]=$xlstabledetailsize;
				$xlsFmtTableDetail1[$alignment][$fontStyle][$cellBorder][fcolor_idx]=$xlsFmtTableDetail_color_idx;
				$xlsFmtTableDetail1[$alignment][$fontStyle][$cellBorder][bcolor_idx]=$xlsFmtTableDetail_bgcolor_idx;

				$xlsFmtTableDetail[$alignment][$fontStyle][$cellBorder] = & $workbook->addformat();
				$xlsFmtTableDetail[$alignment][$fontStyle][$cellBorder]->set_font($xlsfont);
				$xlsFmtTableDetail[$alignment][$fontStyle][$cellBorder]->set_size($xlstabledetailsize);
				$xlsFmtTableDetail[$alignment][$fontStyle][$cellBorder]->set_color($xlsFmtTableDetail_color_idx);
				if(strpos($fontStyle, "B") !== false) $xlsFmtTableDetail[$alignment][$fontStyle][$cellBorder]->set_bold(1);
				if(strpos($fontStyle, "I") !== false) $xlsFmtTableDetail[$alignment][$fontStyle][$cellBorder]->set_italic(1);
				if(strpos($fontStyle, "U") !== false) $xlsFmtTableDetail[$alignment][$fontStyle][$cellBorder]->set_underline(1);
				$xlsFmtTableDetail[$alignment][$fontStyle][$cellBorder]->set_bg_color($xlsFmtTableDetail_bgcolor_idx);
				if(strpos($cellBorder, "T") !== false) $xlsFmtTableDetail[$alignment][$fontStyle][$cellBorder]->set_top(1);
				if(strpos($cellBorder, "L") !== false) $xlsFmtTableDetail[$alignment][$fontStyle][$cellBorder]->set_left(1);
				if(strpos($cellBorder, "R") !== false) $xlsFmtTableDetail[$alignment][$fontStyle][$cellBorder]->set_right(1);
				if(strpos($cellBorder, "B") !== false) $xlsFmtTableDetail[$alignment][$fontStyle][$cellBorder]->set_bottom(1);
				$xlsFmtTableDetail[$alignment][$fontStyle][$cellBorder]->set_align("$alignment");
					
				$xlsFmtTableDetailMerge[$alignment][$fontStyle][$cellBorder] = & $workbook->addformat();
				$xlsFmtTableDetailMerge[$alignment][$fontStyle][$cellBorder]->set_font($xlsfont);
				$xlsFmtTableDetailMerge[$alignment][$fontStyle][$cellBorder]->set_size($xlstabledetailsize);
				$xlsFmtTableDetailMerge[$alignment][$fontStyle][$cellBorder]->set_color($xlsFmtTableDetail_color_idx);
				if(strpos($fontStyle, "B") !== false) $xlsFmtTableDetailMerge[$alignment][$fontStyle][$cellBorder]->set_bold(1);
				if(strpos($fontStyle, "I") !== false) $xlsFmtTableDetailMerge[$alignment][$fontStyle][$cellBorder]->set_italic(1);
				if(strpos($fontStyle, "U") !== false) $xlsFmtTableDetailMerge[$alignment][$fontStyle][$cellBorder]->set_underline(1);
				$xlsFmtTableDetailMerge[$alignment][$fontStyle][$cellBorder]->set_bg_color($xlsFmtTableDetail_bgcolor_idx);
				if(strpos($cellBorder, "T") !== false) $xlsFmtTableDetailMerge[$alignment][$fontStyle][$cellBorder]->set_top(1);
				if(strpos($cellBorder, "L") !== false) $xlsFmtTableDetailMerge[$alignment][$fontStyle][$cellBorder]->set_left(1);
				if(strpos($cellBorder, "R") !== false) $xlsFmtTableDetailMerge[$alignment][$fontStyle][$cellBorder]->set_right(1);
				if(strpos($cellBorder, "B") !== false) $xlsFmtTableDetailMerge[$alignment][$fontStyle][$cellBorder]->set_bottom(1);
//				$xlsFmtTableDetailMerge[$alignment][$fontStyle][$cellBorder]->set_align("$alignment");
				$xlsFmtTableDetailMerge[$alignment][$fontStyle][$cellBorder]->set_merge();
			} // end foreach
		} // end foreach
	} // end foreach
//	echo "<pre>"; print_r($xlsFmtTableDetail); echo "</pre>";
//	echo "<pre>"; print_r($xlsFmtTableDetailMerge); echo "</pre>";

	//  Usage 	: 	set_free_format_new($workbook, function_parameter) 
	//						example :
	//							set_free_format("alignment=C&border=LR&bgColor=black");
	//							this will return format that defined in xlsFmtTableDetail 
	//							with cell alignment = center, cell border = left & right, cell background color = black

	function set_free_format_new( $workbook, $function_parameter ) {
//		echo "function_parameter=$function_parameter<br>";
		if(trim($function_parameter)) parse_str($function_parameter);		
		$returnFormat = set_free_format($workbook, $fontFormat, $alignment, $border, $isMerge, $fgColor, $bgColor, $setRotation, $valignment, $fontSize, $fontStyle, $fontName, $wrapText);
		return $returnFormat;
	} // function

	function set_free_format($workbook, $fontFormat="", $alignment="C", $border="", $isMerge=0, $fgColor="", $bgColor="", $setRotation=0, $valignment="", $fontSize=14, $fontStyle="", $fontName="", $wrapText=0){
		global $font, $xlsfont, $xlstabledetailsize;

		if (!$fontName) $fontName=$xlsfont;
		if (!$fontName) $fontName=$font;
		if (!$fontSize) 	$fontSize=$xlstabledetailsize;

//		echo "fontFormat=$fontFormat, alignment=$alignment, border=$border, isMerge=$isMerge, fgColor=$fgColor, bgColor=$bgColor, setRotation=$setRotation, valignment=$valignment, fontSize=$fontSize, fontStyle=$fontStyle, fontName=$fontName, wrapText=$wrapText<br>";
		
		$freeFormat = & $workbook->addformat();
		$freeFormat->set_font($fontName);
		$freeFormat->set_size($fontSize);
		if (strpos($fontStyle, "B") !== false)	$freeFormat->set_bold(1);
		if (strpos($fontStyle, "I") !== false)		$freeFormat->set_italic(1);
		if (strpos($fontStyle, "U") !== false)	$freeFormat->set_underline(1);
		if (!trim($fgColor))							$fgColor = "black";
		if (strlen(trim($fgColor))==6)  {
			$workbook->set_custom_color(40, hexdec(substr($fgColor,0,2)), hexdec(substr($fgColor,2,2)), hexdec(substr($fgColor,4,2))); 
			$freeFormat->set_color(40);
//			echo "1..set color=$fgColor<br>";
		} else { 
			$freeFormat->set_color(trim($fgColor));
//			echo "2..set color=$fgColor<br>";
		}
		if (strlen(trim($bgColor))==6) {
			$workbook->set_custom_color(41, hexdec(substr($bgColor,0,2)), hexdec(substr($bgColor,2,2)), hexdec(substr($bgColor,4,2))); 
			$freeFormat->set_bg_color(41);
//			echo "1..set bg color=$bgColor<br>";
		} else {
			if (trim($bgColor))	$freeFormat->set_bg_color(trim($bgColor));
//			echo "2..set bg color=$bgColor<br>";
		}
		if (strpos($border, "T") !== false)		$freeFormat->set_top(1);
		if (strpos($border, "L") !== false)		$freeFormat->set_left(1);
		if (strpos($border, "R") !== false)		$freeFormat->set_right(1);
		if (strpos($border, "B") !== false)		$freeFormat->set_bottom(1);
		if (strtolower($alignment)=="c")			$freeFormat->set_align("center");
		else if (strtolower($alignment)=="l")			$freeFormat->set_align("left");
		else if (strtolower($alignment)=="r")			$freeFormat->set_align("right");
		else if (strtolower($alignment)=="f")			$freeFormat->set_align("fill");
		else if (strtolower($alignment)=="j")			$freeFormat->set_align("justify");
		else if (strtolower($alignment)=="a")		$freeFormat->set_align("center_across");
		else 																$freeFormat->set_align("$alignment");
		if (strtolower($valignment)=="t")				$freeFormat->set_align("top");
		else if (strtolower($valignment)=="c")		$freeFormat->set_align("vcenter");
		else if (strtolower($valignment)=="b")		$freeFormat->set_align("bottom");
		else if (strtolower($valignment)=="j")		$freeFormat->set_align("vjustify");
		else																	$freeFormat->set_align("$valignment");
		if ($isMerge) 			$freeFormat->set_merge();
		if ($setRotation)	$freeFormat->set_rotation($setRotation);
		if ($wrapText)		$freeFormat->set_text_wrap();

		return	$freeFormat;
	} // function

	function get_format_font($fmtName, $fontFormat="", $alignment="C", $border=""){
		global ${$fmtName."1"};

		$FormatName = ${$fmtName."1"};

//		echo "....$fmtName+1||".implode(",",$FormatName)."....<br>";
//		echo "in set_format-->wrap=".$wrapText.", rotate=".$rotate.", align=$alignment<br>";

		$alignment = strtoupper(trim($alignment));
		if (strpos($alignment,"T") !== false) {
			$valign = "top";
			$alignment = str_replace("T","",$alignment);
		} 
		if (strpos($alignment,"V") !== false) {
			$valign = "vcenter";
			$alignment = str_replace("T","",$alignment);
		}
		if (strpos($alignment,"B") !== false) {
			$valign = "bottom";
			$alignment = str_replace("B","",$alignment);
		}
		if (strpos($alignment,"VJ") !== false) {
			$valign = "vjustify";
			$alignment = str_replace("V","",$alignment);
		}
		// set text alignment
		switch($alignment){
			case "L" 				:
			case "LEFT" 		:
				$alignment = "left";
				break;
			case "R" 				:
			case "RIGHT" 		:
				$alignment = "right";
				break;
			case "C" 				:
			case "CENTER" 	:
				$alignment = "center";
				break;
			case "J" 					:
			case "JUSTIFY" 	:
				$alignment = "justify";
				break;
			default 	: 
				$alignment = "center";
		} // end switch case

		// set font style		
		if(trim($fontFormat)==""){ 		
			$fontStyle = "none";
		}else{
			$fontStyle = "";
			if(strpos(strtoupper($fontFormat), "B") !== false) $fontStyle .= "B";
			if(strpos(strtoupper($fontFormat), "I") !== false) $fontStyle .= "I";
			if(strpos(strtoupper($fontFormat), "U") !== false) $fontStyle .= "U";
		} // end if

		// set cell border
		if(trim($border)==""){ 
			$cellBorder = "none";
		}else{
			$cellBorder = "";
			if(strpos(strtoupper($border), "T") !== false) $cellBorder .= "T";
			if(strpos(strtoupper($border), "L") !== false) $cellBorder .= "L";
			if(strpos(strtoupper($border), "R") !== false) $cellBorder .= "R";
			if(strpos(strtoupper($border), "B") !== false) $cellBorder .= "B";
		} // end if

		$buff = $FormatName[$alignment][$fontStyle][$cellBorder][font]."^".$FormatName[$alignment][$fontStyle][$cellBorder][size]."^".$FormatName[$alignment][$fontStyle][$cellBorder][fcolor_idx]."^".$FormatName[$alignment][$fontStyle][$cellBorder][bcolor_idx];
//		echo "$fmtName+1 >> $alignment :: $fontStyle :: $cellBorder ==>[$buff]<br>";

		return	$buff;
	} // function

?>
