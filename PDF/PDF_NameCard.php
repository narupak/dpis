<?php
////////////////////////////////////////////////////////////////////////////////////////////////
// PDF_namecard
// modify from PDF_Label class
////////////////////////////////////////////////////////////////////////////////////////////////

class PDF_NameCard extends PDF {

	// Private properties
	var $_Margin_Left;			// Left margin of labels
	var $_Margin_Top;			// Top margin of labels
	var $_X_Number;				// Number of labels horizontally
	var $_Y_Number;				// Number of labels vertically
	var $_Width;				// Width of label
	var $_Height;				// Height of label
	var $_Line_Height;			// Line height
	var $_Metric_Doc;			// Type of metric for the document
	var $_COUNTX;				// Current x position
	var $_COUNTY;				// Current y position

	var $_Avery_NC = array(
		'5160' => array('paper-size'=>'letter', 'metric'=>'mm', 'marginLeft'=>1.762, 'marginTop'=>10.7, 'width'=>66.675, 'height'=>25.4),
		'5161' => array('paper-size'=>'letter', 'metric'=>'mm', 'marginLeft'=>0.967, 'marginTop'=>10.7, 'width'=>101.6, 'height'=>25.4),
		'5162' => array('paper-size'=>'letter', 'metric'=>'mm', 'marginLeft'=>0.97, 'marginTop'=>20.224, 'width'=>100.807, 'height'=>35.72),
		'5163' => array('paper-size'=>'letter', 'metric'=>'mm', 'marginLeft'=>1.762, 'marginTop'=>10.7, 'width'=>101.6, 'height'=>50.8),
		'5164' => array('paper-size'=>'letter', 'metric'=>'in', 'marginLeft'=>0.148, 'marginTop'=>0.5, 'width'=>4.0, 'height'=>3.33),
		'8600' => array('paper-size'=>'letter', 'metric'=>'mm', 'marginLeft'=>7.1, 'marginTop'=>19, 'width'=>66.6, 'height'=>25.4),
		'L7163'=> array('paper-size'=>'A4', 'metric'=>'mm', 'marginLeft'=>5, 'marginTop'=>15, 'width'=>99.1, 'height'=>38.1),
		'3422' => array('paper-size'=>'A4', 'metric'=>'mm', 'marginLeft'=>6, 'marginTop'=>6, 'width'=>89, 'height'=>55),
		'9007' => array('paper-size'=>'wh(100,65)', 'metric'=>'mm', 'marginLeft'=>6, 'marginTop'=>6, 'width'=>89, 'height'=>55)
	);

	// Constructor
	function PDF_NameCard($format, $posX=1, $posY=1) {
		if (is_array($format)) {
			// Custom format
			$Tformat = $format;
		} else {
			// Built-in format
			if (!isset($this->_Avery_NC[$format]))
				$this->Error('Unknown label format: '.$format);
			$Tformat = $this->_Avery_NC[$format];
		}
		
		$unit = $Tformat['metric'];

		if (strpos($Tformat['paper-size'], "wh") !== false) {
			$c1 = strpos($Tformat['paper-size'], "(");
			$c2 = strpos($Tformat['paper-size'], ")");
			$wh = substr($Tformat['paper-size'], $c1+1, $c2-$c1-1);
			$aa = explode(",", $wh);
			parent::PDF('P', $unit, array((int)$aa[0],(int)$aa[1]),"","","","","",0,0,0,"",1);			
		} else {
			parent::PDF('P', $unit, $Tformat['paper-size'],"","","","","",0,0,0,"",1);
		}
		$this->_Metric_Doc = $unit;
		$this->_Set_Format($Tformat);
		$this->SetFont('Arial');
		$this->SetMargins(0,0); 
		$this->SetAutoPageBreak(false); 
		$this->_COUNTX = $posX-1;
		$this->_COUNTY = $posY-1;
	}

	function _Set_Format($format) {
		$this->_Margin_Left	= $this->_Convert_Metric($format['marginLeft'], $format['metric']);
		$this->_Margin_Top	= $this->_Convert_Metric($format['marginTop'], $format['metric']);
//		$this->_X_Number 	= $format['NX'];
//		$this->_Y_Number 	= $format['NY'];
		$this->_Width 		= $this->_Convert_Metric($format['width'], $format['metric']);
		$this->_Height	 	= $this->_Convert_Metric($format['height'], $format['metric']);
		$f_end=false; $k=0;
		while(!$f_end) {
			$k++;
			$px = $this->_Margin_Left + $k*($this->_Width);
//			echo "x = $k, $px > $this->w<br>";
			if ($px > $this->w) {	// margin left + ความกว้างของบัตรทุกใบ > ความกว้างของกระดาษ ก็จะหยุด loop
				$this->_X_Number = $k-1;
				$f_end = true;
			}
		}
		$f_end=false; $k=0;
		while(!$f_end) {
			$k++;
			$py = $this->_Margin_Left + $k*($this->_Height);
//			echo "y = $k, $py > $this->h<br>";
			if ($py > $this->h) {	// margin top + ความสูงของบัตรทุกใบ > ความสูงของกระดาษ ก็จะหยุด loop
				$this->_Y_Number 	= $k-1;
				$f_end = true;
			}
		}
		if ($this->_X_Number == 0) $this->_X_Number = 1;
		if ($this->_Y_Number == 0) $this->_Y_Number = 1;
//		$this->Set_Font_Size($format['font-size']);
	}

	// convert units (in to mm, mm to in)
	// $src must be 'in' or 'mm'
	function _Convert_Metric($value, $src) {
		$dest = $this->_Metric_Doc;
		if ($src != $dest) {
			$a['in'] = 39.37008;
			$a['mm'] = 1000;
			return $value * $a[$dest] / $a[$src];
		} else {
			return $value;
		}
	}

	// Give the line height for a given font size
	function _Get_Height_Chars($pt) {
//		$a = array(6=>2, 7=>2.5, 8=>3, 9=>4, 10=>5, 11=>6, 12=>7, 13=>8, 14=>9, 15=>10, 16=>11, 18=>12, 20=>14, 24=>16);
//		if (!isset($a[$pt]))
//			$this->Error('Invalid font size: '.$pt);
//		return $this->_Convert_Metric($a[$pt], 'mm');

		$lh = $pt*0.38;
		return $this->_Convert_Metric($lh, 'mm');
	}

	// Set the character size
	// This changes the line height too
	function Set_Font_Size($pt) {
		$this->_Line_Height = $this->_Get_Height_Chars($pt);
		$this->SetFontSize($pt);
	}

	// Print a NameCard
	function Print_NameCard($text, $page=1, $ruler=false) {
//		echo "Add paper w=".$this->w.",h=".$this->h.",cntX=".$this->_COUNTX.",cntY=".$this->_COUNTY.",limX=".$this->_X_Number.",limY=".$this->_Y_Number."<br>";
//		echo "text=$text<bt>";

		$cntX = 0; $cntY = 0;

		$this->AddPage();

		for($cnt_page = 0; $cnt_page < $page; $cnt_page++) {
			$f_endpage = false;
			while (!$f_endpage) {
				$_PosX = $this->_Margin_Left + $cntX*($this->_Width);
				$_PosY = $this->_Margin_Top + $cntY*($this->_Height);
//				echo "1. $_PosX, $_PosY, cnt=$cntX, ".$this->_Width.", ".$this->_Height."<br>";
				$this->SetXY($_PosX, $_PosY);
				$this->SetDrawColor(hexdec("DD"),hexdec("DD"),hexdec("DD"));	// สีเทาอ่อน
				if ($cntX==0 && $cntY==0) { // จุด mark มุมบนซ้าย
					$this->Line($_PosX-5, $_PosY, $_PosX, $_PosY);
					$this->Line($_PosX, $_PosY-5, $_PosX, $_PosY);
				} else if ($cntY==0) { // จุด mark กลางบนสุด
					$this->Line($_PosX-1, $_PosY, $_PosX+1, $_PosY);
					$this->Line($_PosX, $_PosY-5, $_PosX, $_PosY);
				} else if ($cntX==0) { // จุด mark ด้านซ้ายปกติ
					$this->Line($_PosX-5, $_PosY, $_PosX, $_PosY);
					$this->Line($_PosX, $_PosY-1, $_PosX, $_PosY+1);
				} else { // จุด mark ตรงกลางปกติ
					$this->Line($_PosX-1, $_PosY, $_PosX+1, $_PosY);
					$this->Line($_PosX, $_PosY-1, $_PosX, $_PosY+1);
				}
				$arr_obj = explode("|", $text);
				sort($arr_obj);
				for($i = 0; $i < count($arr_obj); $i++) {
					$arr_part = explode(",", $arr_obj[$i]);
					if ($arr_part[0]=="text") {
						$inX=(int)$arr_part[3]; $inY=(int)$arr_part[4];
						$inW=(int)$arr_part[5];
						$this->SetXY($_PosX+$inX, $_PosY+$inY);
//						$this->SetFont($arr_part[6],$arr_part[7],$arr_part[8]);
						$this->SetFont($arr_part[6],"",$arr_part[8]);
						$this->Set_Font_Size($arr_part[8]);
						$this->SetTextColor(hexdec($arr_part[10]),hexdec($arr_part[11]),hexdec($arr_part[12]));
						if ($arr_part[13]=="lines")
//							$this->MultiCellThaiCut($inW,$this->_Line_Height,$arr_part[1],0,$arr_part[9]);
							$this->MultiCellThaiCut($inW,$this->_Line_Height,$arr_part[1],0,$arr_part[9]);
						else
							$this->Cell($inW,$this->_Line_Height,$arr_part[1],0,1,$arr_part[9]);
					} else if ($arr_part[0]=="variable") {
						if ($arr_part[1]!="New Variable") {
							$inX=(int)$arr_part[2]; $inY=(int)$arr_part[3];
							$inW=(int)$arr_part[4];
							$this->SetXY($_PosX+$inX, $_PosY+$inY);
//							$this->SetFont($arr_part[5],$arr_part[6],$arr_part[7]);
							$this->SetFont($arr_part[5],"",$arr_part[7]);
							$this->Set_Font_Size($arr_part[7]);
							$this->SetTextColor(hexdec($arr_part[9]),hexdec($arr_part[10]),hexdec($arr_part[11]));
							if ($arr_part[12]=="lines")
// 								$this->MultiCell($inW,$this->_Line_Height,$arr_part[1],0,$arr_part[8]);
								$this->MultiCellThaiCut($inW,$this->_Line_Height,$arr_part[1],0,$arr_part[8]);
							else
								$this->Cell($inW,$this->_Line_Height,$arr_part[1],0,1,$arr_part[8]);
						} // if ($arr_part[1]!="new Variable") 
					} elseif ($arr_part[0]=="background") {
						$inX=(int)$arr_part[2]; $inY=(int)$arr_part[3];
						$inW=(int)$arr_part[4]; $inH=(int)$arr_part[5];
						$this->Image($arr_part[1], $_PosX+$inX, $_PosY+$inY, $inW, $inH);
					} elseif ($arr_part[0]=="image") {
						$inX=(int)$arr_part[2]; $inY=(int)$arr_part[3];
						$inW=(int)$arr_part[4]; $inH=(int)$arr_part[5];
						$this->Image($arr_part[1], $_PosX+$inX, $_PosY+$inY, $inW, $inH);
					} elseif ($arr_part[0]=="line") {
						$inX1=(int)$arr_part[2]; $inY1=(int)$arr_part[3];
						$inX2=(int)$arr_part[4]; $inY2=(int)$arr_part[5];
						$this->SetDrawColor(hexdec($arr_part[6]),hexdec($arr_part[7]),hexdec($arr_part[8]));
						$lnW=(int)$arr_part[9];
						$this->SetLineWidth($lnW);
						$this->Line($_PosX+$inX1, $_PosY+$inY1, $_PosX+$inX2, $_PosY+$inY2);
						$this->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00"));	// สีดำ
						$this->SetLineWidth(0);
					} elseif ($arr_part[0]=="rect") {
						$inX1=(int)$arr_part[2]; $inY1=(int)$arr_part[3];
						$inX2=(int)$arr_part[4]; $inY2=(int)$arr_part[5];
						$this->SetDrawColor(hexdec($arr_part[6]),hexdec($arr_part[7]),hexdec($arr_part[8]));
						$lnW=(int)$arr_part[9];
						$this->SetLineWidth($lnW);
						$this->Rect($_PosX+$inX1, $_PosY+$inY1, $inX2-$inX1, $inY2-$inY1, $arr_part[10]);
						$this->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00"));	// สีดำ
						$this->SetLineWidth(0);
					} // end if check type
				} // end for loop $i

				if ($ruler)  $this->DrawRuler($_PosX, $_PosY, $this->_Width, $this->_Height, false);

				$cntX++;
				if ($cntX == $this->_X_Number) {	// จุด mark ขวาสุด
					$_PosX = $this->_Margin_Left + $cntX*($this->_Width);
					$_PosY = $this->_Margin_Top + $cntY*($this->_Height);
//					echo "2. $_PosX, $_PosY, cnt=$cntX, ".$this->_Width.", ".$this->_Height."<br>";
					$this->SetXY($_PosX, $_PosY);
					$this->SetDrawColor(hexdec("DD"),hexdec("DD"),hexdec("DD"));	// สีเทาอ่อน
					if ($cntY == 0) {		// จุดบนขวา
						$this->Line($_PosX, $_PosY, $_PosX+5, $_PosY);
						$this->Line($_PosX, $_PosY-5, $_PosX, $_PosY);
					} else {	// จุดกลางขวา
						$this->Line($_PosX, $_PosY, $_PosX+5, $_PosY);
						$this->Line($_PosX, $_PosY-1, $_PosX, $_PosY+1);
					}
					$cntY++;
					// Row full, we start a new one
					if ($cntY == $this->_Y_Number) {	// จุดล่างสุด
						for($runX=0; $runX <= $this->_X_Number; $runX++) {
							$_PosX = $this->_Margin_Left + $runX*($this->_Width);
							$_PosY = $this->_Margin_Top + $cntY*($this->_Height);
//							echo "3. $_PosX, $_PosY, cnt=$cntX, ".$this->_Width.", ".$this->_Height."<br>";
							$this->SetXY($_PosX, $_PosY);
							$this->SetDrawColor(hexdec("DD"),hexdec("DD"),hexdec("DD"));	// สีเทาอ่อน
							if ($runX==0) {	// จุดล่างซ้ายสุด
								$this->Line($_PosX-5, $_PosY, $_PosX, $_PosY);
								$this->Line($_PosX, $_PosY, $_PosX, $_PosY+5);
							} else if ($runX==$this->_X_Number) {	// จุดล่างขวาสุด
								$this->Line($_PosX, $_PosY, $_PosX+5, $_PosY);
								$this->Line($_PosX, $_PosY, $_PosX, $_PosY+5);
							} else {	// จุดล่างกลาง
								$this->Line($_PosX-1, $_PosY, $_PosX+1, $_PosY);
								$this->Line($_PosX, $_PosY, $_PosX, $_PosY+5);
							}
						}
						$cntY=0;
						if ($cnt_page+1 < $page)
							$this->AddPage();
						$f_endpage = true;
					}
					$cntX=0;
				} // end if ($cntX == $this->_X_Number)
			} // end while (!$f_endpage);
		} // end for loop $cnt_page
	} // end function

	// Print a Personal Card
	function Print_PERCard($text, $page=1, $ruler=false) {
//		echo "Add paper w=".$this->w.",h=".$this->h.",cntX=".$this->_COUNTX.",cntY=".$this->_COUNTY.",limX=".$this->_X_Number.",limY=".$this->_Y_Number."<br>";
//		echo "text=$text<bt>";

		$cntX = 0; $cntY = 0;

		$this->AddPage();

		for($cnt_page = 0; $cnt_page < $page; $cnt_page++) {
			$f_endpage = false;
			while (!$f_endpage) {
				$_PosX = $this->_Margin_Left + $cntX*($this->_Width);
				$_PosY = $this->_Margin_Top + $cntY*($this->_Height);
//				echo "1. $_PosX, $_PosY, cnt=$cntX, ".$this->_Width.", ".$this->_Height."<br>";
				$this->SetXY($_PosX, $_PosY);
				$this->SetDrawColor(hexdec("DD"),hexdec("DD"),hexdec("DD"));	// สีเทาอ่อน
				if ($cntX==0 && $cntY==0) { // จุด mark มุมบนซ้าย
					$this->Line($_PosX-5, $_PosY, $_PosX, $_PosY);
					$this->Line($_PosX, $_PosY-5, $_PosX, $_PosY);
				} else if ($cntY==0) { // จุด mark กลางบนสุด
					$this->Line($_PosX-1, $_PosY, $_PosX+1, $_PosY);
					$this->Line($_PosX, $_PosY-5, $_PosX, $_PosY);
				} else if ($cntX==0) { // จุด mark ด้านซ้ายปกติ
					$this->Line($_PosX-5, $_PosY, $_PosX, $_PosY);
					$this->Line($_PosX, $_PosY-1, $_PosX, $_PosY+1);
				} else { // จุด mark ตรงกลางปกติ
					$this->Line($_PosX-1, $_PosY, $_PosX+1, $_PosY);
					$this->Line($_PosX, $_PosY-1, $_PosX, $_PosY+1);
				}
				// เส้นแบ่งกลาง
				$mid_posX = $_PosX + ($this->_Width / 2);
				$end_posY = $_PosY + $this->_Height;
				$this->Line($mid_posX, $_PosY, $mid_posX, $end_posY);
				// เส้นแบ่งกลาง
				$arr_obj = explode("|", $text);
				sort($arr_obj);
				for($i = 0; $i < count($arr_obj); $i++) {
					$arr_part = explode(",", $arr_obj[$i]);
					if ($arr_part[0]=="text") {
						$inX=(int)$arr_part[3]; $inY=(int)$arr_part[4];
						$inW=(int)$arr_part[5];
						$this->SetXY($_PosX+$inX, $_PosY+$inY);
//						$this->SetFont($arr_part[6],$arr_part[7],$arr_part[8]);
						$this->SetFont($arr_part[6],"",$arr_part[8]);
						$this->Set_Font_Size($arr_part[8]);
						$this->SetTextColor(hexdec($arr_part[10]),hexdec($arr_part[11]),hexdec($arr_part[12]));
						if ($arr_part[13]=="lines")
//							$this->MultiCellThaiCut($inW,$this->_Line_Height,$arr_part[1],0,$arr_part[9]);
							$this->MultiCellThaiCut($inW,$this->_Line_Height,$arr_part[1],0,$arr_part[9]);
						else
							$this->Cell($inW,$this->_Line_Height,$arr_part[1],0,1,$arr_part[9]);
					} else if ($arr_part[0]=="variable") {
						if ($arr_part[1]!="New Variable") {
							$inX=(int)$arr_part[2]; $inY=(int)$arr_part[3];
							$inW=(int)$arr_part[4];
							$this->SetXY($_PosX+$inX, $_PosY+$inY);
//							$this->SetFont($arr_part[5],$arr_part[6],$arr_part[7]);
							$this->SetFont($arr_part[5],"",$arr_part[7]);
							$this->Set_Font_Size($arr_part[7]);
							$this->SetTextColor(hexdec($arr_part[9]),hexdec($arr_part[10]),hexdec($arr_part[11]));
							if ($arr_part[12]=="lines")
//								$this->MultiCell($inW,$this->_Line_Height,$arr_part[1],0,$arr_part[8]);
								$this->MultiCellThaiCut($inW,$this->_Line_Height,$arr_part[1],0,$arr_part[8]);
							else
								$this->Cell($inW,$this->_Line_Height,$arr_part[1],0,1,$arr_part[8]);
						} // if ($arr_part[1]!="new Variable") 
					} elseif ($arr_part[0]=="background") {
						if (file_exists($arr_part[1])) {
							$inX=(int)$arr_part[2]; $inY=(int)$arr_part[3];
							$inW=(int)$arr_part[4]; $inH=(int)$arr_part[5];
							$this->Image($arr_part[1], $_PosX+$inX, $_PosY+$inY, $inW, $inH);
						}
					} elseif ($arr_part[0]=="image") {
						if (file_exists($arr_part[1])) {
							$inX=(int)$arr_part[2]; $inY=(int)$arr_part[3];
							$inW=(int)$arr_part[4]; $inH=(int)$arr_part[5];
							$this->Image($arr_part[1], $_PosX+$inX, $_PosY+$inY, $inW, $inH);
						}
					} elseif ($arr_part[0]=="per_pic") {
						if (file_exists($arr_part[1])) {
							$inX=(int)$arr_part[2]; $inY=(int)$arr_part[3];
							$inW=(int)$arr_part[4]; $inH=(int)$arr_part[5];
							$this->Image($arr_part[1], $_PosX+$inX, $_PosY+$inY, $inW, $inH);
						}
					} elseif ($arr_part[0]=="line") {
						$inX1=(int)$arr_part[2]; $inY1=(int)$arr_part[3];
						$inX2=(int)$arr_part[4]; $inY2=(int)$arr_part[5];
						$this->SetDrawColor(hexdec($arr_part[6]),hexdec($arr_part[7]),hexdec($arr_part[8]));
						$lnW=(int)$arr_part[9];
						$this->SetLineWidth($lnW);
						$this->Line($_PosX+$inX1, $_PosY+$inY1, $_PosX+$inX2, $_PosY+$inY2);
						$this->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00"));	// สีดำ
						$this->SetLineWidth(0);
					} elseif ($arr_part[0]=="rect") {
						$inX1=(int)$arr_part[2]; $inY1=(int)$arr_part[3];
						$inX2=(int)$arr_part[4]; $inY2=(int)$arr_part[5];
						$this->SetDrawColor(hexdec($arr_part[6]),hexdec($arr_part[7]),hexdec($arr_part[8]));
						$lnW=(int)$arr_part[9];
						$this->SetLineWidth($lnW);
						$this->Rect($_PosX+$inX1, $_PosY+$inY1, $inX2-$inX1, $inY2-$inY1, $arr_part[10]);
						$this->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("00"));	// สีดำ
						$this->SetLineWidth(0);
					} // end if check type
				} // end for loop $i

				if ($ruler)  $this->DrawRuler($_PosX, $_PosY, $this->_Width, $this->_Height, true);

				$cntX++;
				if ($cntX == $this->_X_Number) {	// จุด mark ขวาสุด
					$_PosX = $this->_Margin_Left + $cntX*($this->_Width);
					$_PosY = $this->_Margin_Top + $cntY*($this->_Height);
//					echo "2. $_PosX, $_PosY, cnt=$cntX, ".$this->_Width.", ".$this->_Height."<br>";
					$this->SetXY($_PosX, $_PosY);
					$this->SetDrawColor(hexdec("DD"),hexdec("DD"),hexdec("DD"));	// สีเทาอ่อน
					if ($cntY == 0) {		// จุดบนขวา
						$this->Line($_PosX, $_PosY, $_PosX+5, $_PosY);
						$this->Line($_PosX, $_PosY-5, $_PosX, $_PosY);
					} else {	// จุดกลางขวา
						$this->Line($_PosX, $_PosY, $_PosX+5, $_PosY);
						$this->Line($_PosX, $_PosY-1, $_PosX, $_PosY+1);
					}
					$cntY++;
					// Row full, we start a new one
					if ($cntY == $this->_Y_Number) {	// จุดล่างสุด
						for($runX=0; $runX <= $this->_X_Number; $runX++) {
							$_PosX = $this->_Margin_Left + $runX*($this->_Width);
							$_PosY = $this->_Margin_Top + $cntY*($this->_Height);
//							echo "3. $_PosX, $_PosY, cnt=$cntX, ".$this->_Width.", ".$this->_Height."<br>";
							$this->SetXY($_PosX, $_PosY);
							$this->SetDrawColor(hexdec("DD"),hexdec("DD"),hexdec("DD"));	// สีเทาอ่อน
							if ($runX==0) {	// จุดล่างซ้ายสุด
								$this->Line($_PosX-5, $_PosY, $_PosX, $_PosY);
								$this->Line($_PosX, $_PosY, $_PosX, $_PosY+5);
							} else if ($runX==$this->_X_Number) {	// จุดล่างขวาสุด
								$this->Line($_PosX, $_PosY, $_PosX+5, $_PosY);
								$this->Line($_PosX, $_PosY, $_PosX, $_PosY+5);
							} else {	// จุดล่างกลาง
								$this->Line($_PosX-1, $_PosY, $_PosX+1, $_PosY);
								$this->Line($_PosX, $_PosY, $_PosX, $_PosY+5);
							}
						}
						$cntY=0;
						if ($cnt_page+1 < $page)
							$this->AddPage();
						$f_endpage = true;
					}
					$cntX=0;
				} // end if ($cntX == $this->_X_Number)
			} // end while (!$f_endpage);
		} // end for loop $cnt_page
	} // end function

	function DrawRuler($x, $y, $w, $h, $half) {
			$this->Set_Font_Size(8);
			$this->SetTextColor(hexdec("AA"),hexdec("99"),hexdec("99"));
			if ($half) {
				$new_w = $w / 2;
				$loop = 2;
			} else {
				$loop = 1;
				$new_w = $w;
			}
			for($n = 0; $n < $loop; $n++) {
				$st_x = $x + ($n * $new_w);
				$runX = 0;
				$runY = $y;
				$this->SetDrawColor(hexdec("FF"),hexdec("00"),hexdec("00"));	// สีแดง
				$this->Line($runX, $runY, $st_x+$new_w, $runY);	// แนวนอน แนวเส้น
				for($i = 0; $i <= $new_w; $i++) {	// แนวนอน
					$ix = $i + ($n * $new_w);
					$runX = $st_x+$i;
					$this->SetXY($runX-2, $runY-6);
					if ($i==0) {	// หลักแรกสุด
						$this->SetDrawColor(hexdec("FF"),hexdec("00"),hexdec("00"));	// สีแดง
						$this->Cell(3,5,"$ix",0,1,"C");
						$this->Line($runX, $runY-4, $runX, $y+$h+1);		// หลักบอกหน่วยหลักแรก = 0
					} else if ($i==$new_w) {	// หลักสุดท้าย
						$this->SetDrawColor(hexdec("FF"),hexdec("DD"),hexdec("DD"));	// สีเทาอ่อน
						$this->Cell(3,5,"$ix",0,1,"C");
						$this->Line($runX, $runY-4, $runX, $y+$h+1);		// หลักบอกหน่วย
					} else if (($ix % 10)==0) {	// หลัก 10 หน่วย
						$this->SetDrawColor(hexdec("FF"),hexdec("DD"),hexdec("DD"));	// สีเทาอ่อน
						$this->Cell(3,5,"$ix",0,1,"C");
						$this->Line($runX, $runY-4, $runX, $y+$h+1);		// หลักบอกหน่วย
					} else if (($ix % 5)==0) {	// หลัก 5 หน่วย
						$this->SetDrawColor(hexdec("DD"),hexdec("DD"),hexdec("FF"));	// สีเทาอ่อน
						$this->Cell(3,5,"$ix",0,1,"C");
						$this->Line($runX, $runY-2, $runX, $y+$h+1);		// หลักบอกหน่วย
				} else {	// จุดล่างกลาง
//						$this->SetDrawColor(hexdec("EE"),hexdec("EE"),hexdec("EE"));	// สีเทาอ่อน
						$this->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("EE"));	// สีน้ำเงินเข้ม
						$this->Line($runX, $runY-1, $runX, $runY);		// หลักบอกหน่วย
					}		
				} // end for loop $i
			} // end for loop $n จำนวน รอบ $half
			$runX = $x;
			$runY = 0;
			$this->Line($runX, $runY, $runX, $y+$h);	// แนวตั้ง แนวเส้น
			for($j = 0; $j <= $h; $j++) {	// แนวตั้ง
				$runY = $y+$j;
				$this->SetXY($runX-5, $runY-3);
				if ($j==0) {	// หลักแรกสุด
//					$this->SetDrawColor(hexdec("FF"),hexdec("00"),hexdec("00"));	// สีแดง
					$this->SetDrawColor(hexdec("FF"),hexdec("DD"),hexdec("DD"));	// สีเทาอ่อน
					$this->Cell(3,5,"$j",0,1,"C");
					$this->Line($runX-4, $runY, $x+$w+1, $runY);		// หลักบอกหน่วย
				} else if ($j==$h) {	// หลักสุดท้าย
					$this->SetDrawColor(hexdec("FF"),hexdec("DD"),hexdec("DD"));	// สีเทาอ่อน
					$this->Cell(3,5,"$j",0,1,"C");
					$this->Line($runX-4, $runY, $x+$w+1, $runY);		// หลักบอกหน่วย
				} else if (($j % 10)==0) {	// หลัก 10 หน่วย
					$this->SetDrawColor(hexdec("FF"),hexdec("DD"),hexdec("DD"));	// สีเทาอ่อน
					$this->Cell(3,5,"$j",0,1,"C");
					$this->Line($runX-4, $runY, $x+$w+1, $runY);		// หลักบอกหน่วย
				} else if (($j % 5)==0) {	// หลัก 5 หน่วย
					$this->SetDrawColor(hexdec("DD"),hexdec("DD"),hexdec("FF"));	// สีเทาอ่อน
					$this->Cell(3,5,"$j",0,1,"C");
					$this->Line($runX-2, $runY, $x+$w+1, $runY);		// หลักบอกหน่วย
				} else {	// จุดล่างกลาง
//					$this->SetDrawColor(hexdec("EE"),hexdec("EE"),hexdec("EE"));	// สีเทาอ่อน
					$this->SetDrawColor(hexdec("00"),hexdec("00"),hexdec("EE"));	// สีน้ำเงินเข้ม
					$this->Line($runX-1, $runY, $runX, $runY);		// หลักบอกหน่วย
				}		
			} // end for loop $j
	}
	
	function _putcatalog()
	{
		parent::_putcatalog();
		// Disable the page scaling option in the printing dialog
		$this->_out('/ViewerPreferences <</PrintScaling /None>>');
	}

}
?>
