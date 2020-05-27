<?php
//	echo "2..font=$font<br>";
//	echo "2..PRINT_FONT=$PRINT_FONT<br>";
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
	} else {
		$xlsfont = "TH SarabunIT˘";	// ∂È“‰¡Ë‰¥È°”Àπ¥§Ë“ font „ÀÈ default=TH SarabunIT˘
		$xlstitlesize = 16;
		$xlssubtitlesize = 16;
		$xlstableheadsize = 14;
		$xlstabledetailsize = 14;
	}
//	echo "2..xlsfont=$xlsfont<br>";

	foreach($arr_alignment as $alignment){
		foreach($arr_font_style as $fontStyle){
			foreach($arr_cell_border as $cellBorder){
				$xlsFmtTitle[$alignment][$fontStyle][$cellBorder] = & $workbook->addformat();
				$xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_font($xlsfont);
				$xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_size($xlstitlesize);
				$xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_color(32);
				if(strpos($fontStyle, "B") !== false) $xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_bold(1);
				if(strpos($fontStyle, "I") !== false) $xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_italic(1);
				if(strpos($fontStyle, "U") !== false) $xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_underline(1);
				$xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_bg_color('white');
				if(strpos($cellBorder, "T") !== false) $xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_top(1);
				if(strpos($cellBorder, "L") !== false) $xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_left(1);
				if(strpos($cellBorder, "R") !== false) $xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_right(1);
				if(strpos($cellBorder, "B") !== false) $xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_bottom(1);
				$xlsFmtTitle[$alignment][$fontStyle][$cellBorder]->set_align("$alignment");
					
				$xlsFmtTitleMerge[$alignment][$fontStyle][$cellBorder] = & $workbook->addformat();
				$xlsFmtTitleMerge[$alignment][$fontStyle][$cellBorder]->set_font($xlsfont);
				$xlsFmtTitleMerge[$alignment][$fontStyle][$cellBorder]->set_size($xlstitlesize);
				$xlsFmtTitleMerge[$alignment][$fontStyle][$cellBorder]->set_color(32);
				if(strpos($fontStyle, "B") !== false) $xlsFmtTitleMerge[$alignment][$fontStyle][$cellBorder]->set_bold(1);
				if(strpos($fontStyle, "I") !== false) $xlsFmtTitleMerge[$alignment][$fontStyle][$cellBorder]->set_italic(1);
				if(strpos($fontStyle, "U") !== false) $xlsFmtTitleMerge[$alignment][$fontStyle][$cellBorder]->set_underline(1);
				$xlsFmtTitleMerge[$alignment][$fontStyle][$cellBorder]->set_bg_color('white');
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

	foreach($arr_alignment as $alignment){
		foreach($arr_font_style as $fontStyle){			
			foreach($arr_cell_border as $cellBorder){
				$xlsFmtSubTitle[$alignment][$fontStyle][$cellBorder] = & $workbook->addformat();
				$xlsFmtSubTitle[$alignment][$fontStyle][$cellBorder]->set_font($xlsfont);
				$xlsFmtSubTitle[$alignment][$fontStyle][$cellBorder]->set_size($xlssubtitlesize);
				$xlsFmtSubTitle[$alignment][$fontStyle][$cellBorder]->set_color(32);
				if(strpos($fontStyle, "B") !== false) $xlsFmtSubTitle[$alignment][$fontStyle][$cellBorder]->set_bold(1);
				if(strpos($fontStyle, "I") !== false) $xlsFmtSubTitle[$alignment][$fontStyle][$cellBorder]->set_italic(1);
				if(strpos($fontStyle, "U") !== false) $xlsFmtSubTitle[$alignment][$fontStyle][$cellBorder]->set_underline(1);
				$xlsFmtSubTitle[$alignment][$fontStyle][$cellBorder]->set_bg_color('white');
				if(strpos($cellBorder, "T") !== false) $xlsFmtSubTitle[$alignment][$fontStyle][$cellBorder]->set_top(1);
				if(strpos($cellBorder, "L") !== false) $xlsFmtSubTitle[$alignment][$fontStyle][$cellBorder]->set_left(1);
				if(strpos($cellBorder, "R") !== false) $xlsFmtSubTitle[$alignment][$fontStyle][$cellBorder]->set_right(1);
				if(strpos($cellBorder, "B") !== false) $xlsFmtSubTitle[$alignment][$fontStyle][$cellBorder]->set_bottom(1);
				$xlsFmtSubTitle[$alignment][$fontStyle][$cellBorder]->set_align("$alignment");
					
				$xlsFmtSubTitleMerge[$alignment][$fontStyle][$cellBorder] = & $workbook->addformat();
				$xlsFmtSubTitleMerge[$alignment][$fontStyle][$cellBorder]->set_font($xlsfont);
				$xlsFmtSubTitleMerge[$alignment][$fontStyle][$cellBorder]->set_size($xlssubtitlesize);
				$xlsFmtSubTitleMerge[$alignment][$fontStyle][$cellBorder]->set_color(32);
				if(strpos($fontStyle, "B") !== false) $xlsFmtSubTitleMerge[$alignment][$fontStyle][$cellBorder]->set_bold(1);
				if(strpos($fontStyle, "I") !== false) $xlsFmtSubTitleMerge[$alignment][$fontStyle][$cellBorder]->set_italic(1);
				if(strpos($fontStyle, "U") !== false) $xlsFmtSubTitleMerge[$alignment][$fontStyle][$cellBorder]->set_underline(1);
				$xlsFmtSubTitleMerge[$alignment][$fontStyle][$cellBorder]->set_bg_color('white');
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

	foreach($arr_alignment as $alignment){
		foreach($arr_font_style as $fontStyle){			
			foreach($arr_cell_border as $cellBorder){
				$xlsFmtTableHeader[$alignment][$fontStyle][$cellBorder] = & $workbook->addformat();
				$xlsFmtTableHeader[$alignment][$fontStyle][$cellBorder]->set_font($xlsfont);
				$xlsFmtTableHeader[$alignment][$fontStyle][$cellBorder]->set_size($xlstableheadsize);
				$xlsFmtTableHeader[$alignment][$fontStyle][$cellBorder]->set_color(48);
				if(strpos($fontStyle, "B") !== false) $xlsFmtTableHeader[$alignment][$fontStyle][$cellBorder]->set_bold(1);
				if(strpos($fontStyle, "I") !== false) $xlsFmtTableHeader[$alignment][$fontStyle][$cellBorder]->set_italic(1);
				if(strpos($fontStyle, "U") !== false) $xlsFmtTableHeader[$alignment][$fontStyle][$cellBorder]->set_underline(1);
				$xlsFmtTableHeader[$alignment][$fontStyle][$cellBorder]->set_bg_color(31);
				if(strpos($cellBorder, "T") !== false) $xlsFmtTableHeader[$alignment][$fontStyle][$cellBorder]->set_top(1);
				if(strpos($cellBorder, "L") !== false) $xlsFmtTableHeader[$alignment][$fontStyle][$cellBorder]->set_left(1);
				if(strpos($cellBorder, "R") !== false) $xlsFmtTableHeader[$alignment][$fontStyle][$cellBorder]->set_right(1);
				if(strpos($cellBorder, "B") !== false) $xlsFmtTableHeader[$alignment][$fontStyle][$cellBorder]->set_bottom(1);
				$xlsFmtTableHeader[$alignment][$fontStyle][$cellBorder]->set_align("$alignment");
					
				$xlsFmtTableHeaderMerge[$alignment][$fontStyle][$cellBorder] = & $workbook->addformat();
				$xlsFmtTableHeaderMerge[$alignment][$fontStyle][$cellBorder]->set_font($xlsfont);
				$xlsFmtTableHeaderMerge[$alignment][$fontStyle][$cellBorder]->set_size($xlstableheadsize);
				$xlsFmtTableHeaderMerge[$alignment][$fontStyle][$cellBorder]->set_color(48);
				if(strpos($fontStyle, "B") !== false) $xlsFmtTableHeaderMerge[$alignment][$fontStyle][$cellBorder]->set_bold(1);
				if(strpos($fontStyle, "I") !== false) $xlsFmtTableHeaderMerge[$alignment][$fontStyle][$cellBorder]->set_italic(1);
				if(strpos($fontStyle, "U") !== false) $xlsFmtTableHeaderMerge[$alignment][$fontStyle][$cellBorder]->set_underline(1);
				$xlsFmtTableHeaderMerge[$alignment][$fontStyle][$cellBorder]->set_bg_color(31);
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

	foreach($arr_alignment as $alignment){
		foreach($arr_font_style as $fontStyle){			
			foreach($arr_cell_border as $cellBorder){
				$xlsFmtTableDetail[$alignment][$fontStyle][$cellBorder] = & $workbook->addformat();
				$xlsFmtTableDetail[$alignment][$fontStyle][$cellBorder]->set_font($xlsfont);
				$xlsFmtTableDetail[$alignment][$fontStyle][$cellBorder]->set_size($xlstabledetailsize);
				$xlsFmtTableDetail[$alignment][$fontStyle][$cellBorder]->set_color('black');
				if(strpos($fontStyle, "B") !== false) $xlsFmtTableDetail[$alignment][$fontStyle][$cellBorder]->set_bold(1);
				if(strpos($fontStyle, "I") !== false) $xlsFmtTableDetail[$alignment][$fontStyle][$cellBorder]->set_italic(1);
				if(strpos($fontStyle, "U") !== false) $xlsFmtTableDetail[$alignment][$fontStyle][$cellBorder]->set_underline(1);
				$xlsFmtTableDetail[$alignment][$fontStyle][$cellBorder]->set_bg_color('white');
				if(strpos($cellBorder, "T") !== false) $xlsFmtTableDetail[$alignment][$fontStyle][$cellBorder]->set_top(1);
				if(strpos($cellBorder, "L") !== false) $xlsFmtTableDetail[$alignment][$fontStyle][$cellBorder]->set_left(1);
				if(strpos($cellBorder, "R") !== false) $xlsFmtTableDetail[$alignment][$fontStyle][$cellBorder]->set_right(1);
				if(strpos($cellBorder, "B") !== false) $xlsFmtTableDetail[$alignment][$fontStyle][$cellBorder]->set_bottom(1);
				$xlsFmtTableDetail[$alignment][$fontStyle][$cellBorder]->set_align("$alignment");
					
				$xlsFmtTableDetailMerge[$alignment][$fontStyle][$cellBorder] = & $workbook->addformat();
				$xlsFmtTableDetailMerge[$alignment][$fontStyle][$cellBorder]->set_font($xlsfont);
				$xlsFmtTableDetailMerge[$alignment][$fontStyle][$cellBorder]->set_size($xlstabledetailsize);
				$xlsFmtTableDetailMerge[$alignment][$fontStyle][$cellBorder]->set_color('black');
				if(strpos($fontStyle, "B") !== false) $xlsFmtTableDetailMerge[$alignment][$fontStyle][$cellBorder]->set_bold(1);
				if(strpos($fontStyle, "I") !== false) $xlsFmtTableDetailMerge[$alignment][$fontStyle][$cellBorder]->set_italic(1);
				if(strpos($fontStyle, "U") !== false) $xlsFmtTableDetailMerge[$alignment][$fontStyle][$cellBorder]->set_underline(1);
				$xlsFmtTableDetailMerge[$alignment][$fontStyle][$cellBorder]->set_bg_color('white');
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
?>
