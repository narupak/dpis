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
	
	$arr_alignment = array("center", "left", "right");
	$arr_font_style = array("none", "B", "I", "U", "BI", "BU", "IU", "BIU");
	$arr_cell_border = array("none", "T", "L", "R", "B", "TL", "TR", "TB", "LR", "LB", "RB", "TLR", "TLB", "TRB", "LRB", "TLRB");

	function set_format($fmtName, $fontFormat="", $alignment="C", $border="", $isMerge=0){
		global ${$fmtName}, ${$fmtName."Merge"};
				
		$FormatName = ${$fmtName.(($isMerge)?"Merge":"")};

		// set text alignment
		switch(strtoupper(trim($alignment))){
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
		return	$FormatName[$alignment][$fontStyle][$cellBorder];
	} // function
?>
