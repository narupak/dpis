<?php
function thaiWrap($dataIn){
	$specword = array("ศาสตร์","ตำแหน่ง","อยู่","อำเภอ","จังหวัด","ปกครอง","ปฏิบัติ","สัญญา","เจ้าหน้าที่", "กระบี่", "กาญจนบุรี", "กาฬสินธุ์", "กำแพงเพชร", "ขอนแก่น", "จันทบุรี", "ฉะเชิงเทรา", "ชลบุรี", "ชัยนาท", "ชัยภูมิ", "ชุมพร", "เชียงราย", "เชียงใหม่", "ตรัง", "ตราด", "ตาก", "นครนายก", "นครปฐม", "นครพนม", "นครราชสีมา", "นครศรีธรรมราช", "นครสวรรค์", "นนทบุรี", "นราธิวาส", "น่าน", "บุรีรัมย์", "ปทุมธานี", "ประจวบคีรีขันธ์", "ปราจีนบุรี", "ปัตตานี", "พระนครศรีอยุธยา", "พะเยา", "พังงา", "พัทลุง", "พิจิตร", "พิษณุโลก", "เพชรบุรี", "เพชรบูรณ์", "แพร่", "ภูเก็ต", "มหาสารคาม", "มุกดาหาร", "แม่ฮ่องสอน", "ยโสธร", "ยะลา", "ร้อยเอ็ด", "ระนอง", "ระยอง", "ราชบุรี", "ลพบุรี", "ลำปาง", "ลำพูน", "เลย", "ศรีสะเกษ", "สกลนคร", "สงขลา", "สตูล", "สมุทรปราการ", "สมุทรสงคราม", "สมุทรสาคร", "สระแก้ว", "สระบุรี", "สิงห์บุรี", "สุโขทัย", "สุพรรณบุรี", "สุราษฎร์ธานี", "สุรินทร์", "หนองคาย", "หนองบัวลำภู", "อ่างทอง", "อำนาจเจริญ", "อุดรธานี", "อุตรดิตถ์", "อุทัยธานี", "อุบลราชธานี", "และ", "เกียรติยศ"); // เพิ่มคำเฉพาะได้ที่นี่
	$mypatt = implode("|", $specword);
	$min_pos_i = strlen($dataIn);
	$pos_i = 0;
	$i_ord = 0;
	$ret_len = 0;
	
	if (preg_match("/($mypatt)/",$dataIn,$match)) {
		$pos_i = strpos($dataIn, $match[0]);
		if ($pos_i < $min_pos_i) {
			$min_pos_i = $pos_i;
			$ret_len = strlen($match[0]);
		}
	} 
	if (preg_match("/(เ([ก-ฮ])(ร|ม)?าะ(ห์)?)/",$dataIn,$match)) { // // เคราะห์ เหมาะ เจาะ เกาะ เอาะ เลาะห์
		$pos_i = strpos($dataIn, $match[0]);
		if ($pos_i < $min_pos_i) {
			$min_pos_i = $pos_i;
			$ret_len = strlen($match[0]);
		}
	} 
	if (preg_replace("/(มา(?:ก|ย|ตรการ|ตรา|ตุภูมิ|ชิก))/","$1<wbr />",$dataIn,$match)) { // 
		$pos_i = strpos($dataIn, $match[0]);
		if ($pos_i < $min_pos_i) {
			$min_pos_i = $pos_i;
			$ret_len = strlen($match[0]);
		}
	}
	if (preg_match("/(ม(?:ี|ัน|ือ))/",$dataIn,$match)) { // มือ มัน มือ
		$pos_i = strpos($dataIn, $match[0]);
		if ($pos_i < $min_pos_i) {
			$min_pos_i = $pos_i;
			$ret_len = strlen($match[0]);
		}
	} 
	if (preg_match("/((เ|แ)([ก-ฮ])[งลรว]([่|้|๊|๋|็|ิ|ี|ึ|ื])?([ง|ว|อ])?)/",$dataIn,$match)) { // เกลือ แกง แฮง แรง แกล้ง แผลง แดง แวง แตง แหว่ง แกร่ง แฝง แสลง แกร๊ง แกล้ว
		$pos_i = strpos($dataIn, $match[0]);
		if ($pos_i < $min_pos_i) {
			$min_pos_i = $pos_i;
			$ret_len = strlen($match[0]);
		}
	} 
	if (preg_match("/(([กขคงจชซดตถทนบปผพมยรลวสหอเแโใไ])(?:ท(?:ำ(?:ให้)?|ี่|(?:า|ั้)ง|่าน)|เ(?:ร(?:ื่อง|า)|ข้?า|พ(?:ราะ|ื่อ)|(?:ป็|ห็|ช่)น|มื่อ|กิด|ด็ก|วลา)|ก(?:ัน|ับ|าร|็|ลุ่ม|ว่า)|ใ(?:ห(?:้|ม่)|น|ช้)|ข(?:อง|ึ้น)|จ(?:ะ|น|าก|(?:ึ|ริ)ง)|แ(?:ล(?:ะ|้ว)|ต่|ห่ง|บบ)|ไ(?:ด้|ว้|ม่|ป|ทย)|ว(?:่าง|่า|ัน)|ค(?:วาม|น|ือ)|น(?:ี้|ั้น|าย)|ห(?:รือ|นึ่ง|ลาย)|อ(?:ย(?:ู่|่าง)|[ีอ]ก|าจ|(?:ื่|ั)น|ะไร)|ต(?:่(?:าง|อ)|้อง|าม|ัว)|ด(?:้(?:วย|าน)|ี)|ถ(?:ึง|้า|ูก)|ผ(?:ู้|ม|ล)|ป(?:ระเทศ|ี|ัญหา)|ส(?:ังคม|ามารถ|่วน|ิ่ง|ำคัญ)|(?:ซึ่|ยั|บา|ล)ง|โดย|รับ|ชีวิต|งาน|พรรค))/","$1<wbr />",$dataIn,$match)) { 
		$pos_i = strpos($dataIn, $match[0]);
		if ($pos_i < $min_pos_i) {
			$min_pos_i = $pos_i;
			$ret_len = strlen($match[0]);
		}
	}
	if (preg_match("/(เ([ก-ฮ])ี([่|้|๊|๋])?ยง)/",$dataIn,$match)) { // เกียง เอี้ยง เมี่ยง เสี่ยง 
		$pos_i = strpos($dataIn, $match[0]);
		if ($pos_i < $min_pos_i) {
			$min_pos_i = $pos_i;
			$ret_len = strlen($match[0]);
		}
	}

	$space_ch_i = strpos($dataIn, " ");
	if ($space_ch_i > -1) {
		if ($space_ch_i < $min_pos_i) {
			$min_pos=0;
			$ret_len = $space_ch_i;
		} else if ($space_ch_i < $min_pos_i + $ret_len) {
			$ret_len = $space_ch_i - $min_pos_i;
		}
	}

	$ch = substr($dataIn,$min_pos_i+$ret_len,1);
	if (strpos("ะาิีึืุูำ่้๊๋ั็",$ch) > -1) {
		$ret_len--;
	} else {
		$ch = substr($dataIn,$min_pos_i+$ret_len+1,1);
		if (strpos(" เแโใไ",$ch) > -1) {
			$ret_len++;
		}		
	}

	return $min_pos_i+$ret_len;
}
function thaiCutLine($dataIn, $n, $delim){
	$vowels = array("<br>", "<BR>", "<Br>", "<br />", "<BR />", "<Br />", "<br/>", "<BR/>", "<Br/>", "\n");
	$tempdataIn = str_replace($vowels, "", $dataIn);
	$midch_cnt=0;
	$i = 0;
	$ctext = "";
	$out = "";
	while (strlen($tempdataIn) > 0) {
		$ch = substr($tempdataIn,$i,1);
		if ($ch == "\n") { // เป็น ตัด line
			$out = "$out".trim($ctext)."$delim";
//			echo "******* $out(".strlen($out).")<br>";
			$ctext=substr($tempdataIn,0,$mylen);
			$tempdataIn = substr($tempdataIn,$mylen);
			$midch_cnt=$mylen1;
		} else if (strpos( "ิีึืุูํ่้๊๋็ํ์ั", $ch) === false) { // ถ้าเป็นอักษรแถวกราง (ไม่เป็นอักษรยก   ิ  ี  ื  ึ  ุ  ู  ํ  ่  ้  ๊  ๋  ั  ็)
			$mylen = thaiWrap($tempdataIn);
			$text1 = substr($tempdataIn,0,$mylen);
			$cnt_up=0;
			for($ii=0; $ii < strlen($text1); $ii++) {
				$ch1=substr($text1,$ii,1);
				if (strpos( "ิีึืุูํ่้๊๋ั็์ั", $ch1) > -1) { // เป็นอักษรยก   ิ  ี  ื  ึ  ุ  ู  ํ  ่  ้  ๊  ๋  ั  ็
					$cnt_up++;
				}
			}
			$mylen1 = $mylen - $cnt_up;
			echo "midch_cnt = $midch_cnt + $mylen($out)<br>";
			if ($midch_cnt + $mylen1 > $n) {
				$out = "$out".trim($ctext)."$delim";
				$ctext=substr($tempdataIn,0,$mylen);
				echo "new-".$ctext."(".strlen($ctext).")<br>";
				$tempdataIn = substr($tempdataIn,$mylen);
				$midch_cnt=$mylen1;
			} else { // <= $n
				$ctext = "$ctext".substr($tempdataIn,0,$mylen);
				echo "old-".$ctext."(".strlen($ctext).")<br>";
				$tempdataIn = substr($tempdataIn,$mylen);
				$midch_cnt = $midch_cnt + $mylen1;
			}
			$i=-1;
		} // end if ch
		$i++;
	} // end loop while
	$out = "$out".trim($ctext);
	return $out; 
}
?>