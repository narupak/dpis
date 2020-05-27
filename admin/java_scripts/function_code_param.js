// JavaScript Document
	function myEncode (clearString) {
		var output = '';
		var x = 0;
		clearString = clearString.toString();
		var regex = /(^[a-zA-Z0-9_.]*)/;
		while (x < clearString.length) {
			var match = regex.exec(clearString.substr(x));
			if (match != null && match.length > 1 && match[1] != '') {
				output += match[1];
				x += match[1].length;
			} else {
				if (clearString[x] == ' ')
					output += String.fromCharCode(126);
				else {
					var charCode = clearString.charCodeAt(x);
					charCode = (charCode>=3585?charCode-3585+161:charCode); // unicode ¢Í§ '¡'
					var hexVal = charCode.toString(16);
//					alert(clearString[x]+":"+charCode+":"+hexVal.toUpperCase());
					output += '%' + ( hexVal.length < 2 ? '0' : '' ) + hexVal.toUpperCase();
				}
				x++;
			}
		}
		return output;
	}

	function myDecode (encodedString) {
		var ascii_start=161; // decimal = 161(A1) ¶Ö§ 255(FF)
		var thai_ascii_map = "¡¢£¤¥¦§¨©ª«¬­®¯°±²³´µ¶·¸¹º»¼½¾¿ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõö÷øùúû";

		var output = ReplaceAll(encodedString,String.fromCharCode(126)," ");
		var binVal, thisString;
		var myregexp = /(%[^%]{2})/;
		while ((match = myregexp.exec(output)) != null
             && match.length > 1
             && match[1] != '') {
			binVal = parseInt(match[1].substr(1),16);
			var thai_asc = binVal - ascii_start;
//			alert("match 0="+match[0]+" 1="+match[1]+" chr="+binVal+" map="+thai_asc);
			if (thai_asc >= 0)
				thisString = thai_ascii_map.substr(thai_asc, 1);
			else
				thisString = String.fromCharCode(binVal);
//			alert("binVal="+binVal+", thisString="+thisString);
			output = output.replace(match[1], thisString);
		}
		return output;
	}
	
	function ReplaceAll(Source,stringToFind,stringToReplace){
		var temp = Source;
		var index = temp.indexOf(stringToFind);
		while(index != -1){
			temp = temp.replace(stringToFind,stringToReplace);
			index = temp.indexOf(stringToFind);
		}
		return temp;
	}

