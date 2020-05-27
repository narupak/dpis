<?
    function set_element($format) {
//    	echo "format:$format<br>";
	    $retstr = "";
        $retnameself = "";
        $arr_detail = explode("|",$format);
        if ($arr_detail[0]=="label" && trim($arr_detail[2])) { // format ==> label|*=require column|Label text
			if (substr($arr_detail[2],0,5)=="func,") {
				$enameret = str_replace(",","|",$arr_detail[2]);	// ส่งทั้งรูปแบบ function ไปคิดที่เกิดเหตุ
                $evalue = "@fvalue@";
			} else {
//				$evalue = ($arr_detail[2]=="@self@" ? $$arr_detail[1] : $arr_detail[2]);
				$evalue = $arr_detail[2];
                $enameret = "";
			}
			$retstr = (($arr_detail[1]=="*") ? "<span class='label_alert'>*</span>" : "")."&nbsp;".$evalue."&nbsp;:&nbsp;";
            $retnameself = $enameret;
//	        echo "f..label--".substr($retstr,1)."<br>";
        } else if ($arr_detail[0]=="text") {
            $ename = ($arr_detail[1] ? "name='$arr_detail[1]'" : "");
			if (substr($arr_detail[2],0,5)=="func,") {
				$enameret = str_replace(",","|",$arr_detail[2]);	// ส่งทั้งรูปแบบ function ไปคิดที่เกิดเหตุ
                $evalue = "@fvalue@";
			} else {
//				$evalue = ($arr_detail[2]=="@self@" ? $$arr_detail[1] : $arr_detail[2]);
				$evalue = $arr_detail[2];
                $enameret = $arr_detail[1];
			}
            $estyle = ($arr_detail[3] ? "style='$arr_detail[3]'" : "");
            $esize = ($arr_detail[4] ? "size='$arr_detail[4]'" : "");
			$retstr = "<input type='text' $ename value='$evalue' $estyle class='textbox' $esize ".$arr_detail[5].">";
            $retnameself = $enameret;
        } else if ($arr_detail[0]=="textarea") {
            $ename = ($arr_detail[1] ? "name='$arr_detail[1]'" : "");
//			$evalue = ($arr_detail[2]=="@self@" ? $$arr_detail[1] : $arr_detail[2]);
            $evalue = $arr_detail[2];
            $erows = ($arr_detail[3] ? "rows='$arr_detail[3]'" : "");
            $estyle = ($arr_detail[4] ? "style='$arr_detail[4]'" : "");
            $retstr = "<textarea $ename $erows class='selectbox' $estyle>$evalue</textarea>";
            $retnameself = $arr_detail[1];
        } else if ($arr_detail[0]=="radio") {
            $ename = ($arr_detail[1] ? "name='$arr_detail[1]'" : "");
			$retstr = "<input type='radio' $ename value='".$arr_detail[2]."' ".$arr_detail[3].">".$arr_detail[4]."";
//			echo substr($retstr,1)."<br>";
            $retnameself = $arr_detail[1];
        } else if ($arr_detail[0]=="checkbox") {
            $ename = ($arr_detail[1] ? "name='$arr_detail[1]'" : "");
			$retstr = "<input type='checkbox' $ename value='".$arr_detail[2]."' ".$arr_detail[3].">".$arr_detail[4]."";
            $retnameself = $arr_detail[1];
        } else if ($arr_detail[0]=="button") {
            $ename = ($arr_detail[1] ? "name='$arr_detail[1]'" : "");
            $eonclick = ($arr_detail[3] ? "onClick='".str_replace(chr(126),"|",$arr_detail[3])."'" : "");	// แปลง chr(126) กลับเป็น |
//			echo "button ename=$ename, eonclick=$eonclick<br>";
			$retstr = "<input type='button' $ename value='".$arr_detail[2]."' ".$eonclick.">";
//			echo "".substr($retstr,1)."<br>";
            $retnameself = $arr_detail[1];
        } else if ($arr_detail[0]=="imgbutton") {
            $ename = ($arr_detail[1] ? "name='$arr_detail[1]'" : "");
			$alt = ($arr_detail[2] ? "alt='$arr_detail[2]'" : "");
            $imgname = ($arr_detail[3] ? "src='".$arr_detail[3]."'" : "");
            $eonclick = ($arr_detail[4] ? "onClick='".$arr_detail[4]."'" : "");
			$retstr = "<input type='image' $ename $src $eonclick  $src $alt>";	
//			echo "retstr=".substr($retstr,1)."<br>";
            $retnameself = "";
        } else if ($arr_detail[0]=="imgclear") {
            $aname = explode(",",$arr_detail[1]);
			$anformclear = (array) null;
			for($i = 0; $i < count($aname); $i++) {
				$anformclear[] = "form1.".$aname[$i].".value=\"\"";
			}
			$retstr = "<input type='image' src='images/icon_clear.gif' width='22' height='22' onClick='".implode("; ",$anformclear)."; return false;' align='center' alt='ล้างค่า'>";	
//			echo "retstr=".substr($retstr,1)."<br>";
            $retnameself = "";
        } else if ($arr_detail[0]=="hidden") {
            $ename = ($arr_detail[1] ? "name='$arr_detail[1]'" : "");
			$retstr = "<input type='hidden' $ename value='".$arr_detail[2]."'>";
            $retnameself = $arr_detail[1];
        } else if ($arr_detail[0]=="func") {
            $funcname = $arr_detail[1];
//			$fargument = explode(",",$arr_detail[2]);	// กรณีใช้ , คั่น argument
			$fargument = (array) null;
            for($j=2; $j < count($arr_detail); $j++) {
				$fargument[] = $arr_detail[$j];
            }
			$retstr = "func_".$funcname."(".implode("|",$fargument).")";
            $arr_gname = (array) null;
			for($k=0; $k < count($fargument); $k++) {
            	if (substr($fargument[$k],0,2)=="'@" && substr($fargument[$k],strlen($fargument[$k])-2)=="@'") {
//                	echo "1..fargument [$k] = ".$fargument[$k]."<br>";
					$arr_gname[] = substr($fargument[$k],1,strlen($fargument[$k])-2);
				} else if (substr($fargument[$k],0,1)=="@" && substr($fargument[$k],strlen($fargument[$k])-1)=="@") {
//                	echo "2..fargument [$k] = ".$fargument[$k]."<br>";
					$arr_gname[] = $fargument[$k];
				}
			}
            $retnameself = implode(",",$arr_gname);
//			echo "retstr=$retstr, retnameself=$retnameself<br>";
        } // end if check element type
        
//		echo "f,,".substr($retstr,1)."||".$retnameself;
		return $retstr."^".$retnameself;
    }
?>
