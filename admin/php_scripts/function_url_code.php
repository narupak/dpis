<?
// JavaScript Document
	function URL_Encode ($clearString) {
		$output = '';
		$x = 0;
//		$clearString = $clearString.toString();
		$regex = "/(^[a-zA-Z0-9_.]*)/";
		while ($x < strlen($clearString)) {
			preg_match($regex, substr($clearString, $x), $match);
			if ($match && strlen($match) > 1 && $match[1] != '') {
				$output .= $match[1];
				$x += strlen($match[1]);
			} else {
				if (substr($clearString, $x, 1) == ' ')
					$output .= '+';
				else {
					$charCode = ord(substr($clearString, $x, 1));
					$hexVal = dechex($charCode);
					$output .= '%'.( strlen($hexVal) < 2 ? '0' : '' ).strtoupper($hexVal);
				}
				$x++;
			}
		}
		return $output;
	}

	function URL_Decode ($encodedString) {
		$output = $encodedString;
		$binVal = "";
		$thisString = "";
		$myregexp = "/(%[^%]{2})/";
		preg_match($myregexp, $output, $match);
		while ($match && strlen($match) > 1 && $match[1] != '') {
			$binVal = hexdec(substr($match[1],1,16));
			$thisString = chr($binVal);
			$output = str_replace($match[1], $thisString, $output);
			preg_match($myregexp, $output, $match);
		}
		return $output;
	}
?>