<?
	function myEncode ($clearString) {
		$output = '';
		$x = 0;
//		$clearString = $clearString.toString();
		$regex = "/(^[a-zA-Z0-9_.]*)/";
		while ($x < strlen($clearString)) {
			preg_match($regex, substr($clearString, $x), $match);
			if ($match && strlen($match) > 1 && $match[1] != '') {
//				echo "match 0=".$match[0]." 1=".$match[1]."<br>";
				$output .= $match[1];
				$x += strlen($match[1]);
			} else {
				if (substr($clearString, $x, 1) == " ")
					$output .= chr(126);
				else {
					$charCode = ord(substr($clearString, $x, 1));
					$hexVal = dechex($charCode);
//					echo "$charCode - $hexVal<br>";
					$output .= '%'.( strlen($hexVal) < 2 ? '0' : '' ).strtoupper($hexVal);
				}
				$x++;
			}
		}
		return $output;
	}

	function myDecode ($encodedString) {
		$ascii_start="161"; // decimal = 161(A1) ¶Ö§ 255(FF)
		$thai_ascii_map = "¡¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶·¸¹º»¼½¾¿ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõö÷øùúû";
	
		$output = str_replace(chr(126), " ", $encodedString);
		$binVal = "";
		$thisString = "";
		$myregexp = "/(%[^%]{2})/";
		preg_match($myregexp, $output, $match);
		while ($match && strlen($match) > 1 && $match[1] != '') {
			$binVal = hexdec(substr($match[1],1));
			$thai_asc = $binVal - $ascii_start;			
//			echo "match 0=".$match[0]." 1=".$match[1]." chr=".$binVal." map=".$thai_asc."<br>";
			if ($thai_asc >= 0)
				$thisString = $thai_ascii_map[$thai_asc];
			else
				$thisString = chr($binVal);
			$output = str_replace($match[1], $thisString, $output);
			preg_match($myregexp, $output, $match);
		}
		return $output;
	}
?>