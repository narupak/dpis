<?
	$time1 = time();
	ini_set("max_execution_time", $max_execution_time);

	function fill_arr_string($st) {
		$arr = explode(",",$st);
		for($i=0; $i < count($arr); $i++) {
			$bb = "'".trim($arr[$i])."'";
			$arr[$i] = $bb;
		}
		return implode(",",$arr);
	}
	
	function call_header($data_count, $head) {
		global $worksheet, $xlsRow;
		global $header_width, $header_text;
		global $count_data, $data_limit, $file_limit;
//		echo "$data_count, $count_data, $file_limit<br>";

		// ************************************************* header *****************************************************
		$xlsRow=0;
		$worksheet->write($xlsRow,0,"ข้อมูล $head ของ บุคลากร", set_format("xlsFmtTitle", "B", "L", "", 0));
		$xlsRow++;
		$worksheet->write($xlsRow,0,"", set_format("xlsFmtTitle", "B", "L", "", 0));
		$xlsRow++;
		$count_to = $data_count + ($count_data - $data_count > $data_limit ? $data_limit : $count_data - $data_count);
		$show_page = "รายการที่ ".($data_count+1)." ถึง ".$count_to;
		$worksheet->write($xlsRow,0,"$show_page", set_format("xlsFmtTitle", "B", "L", "", 0));
		for($ii = 0; $ii < count($header_width); $ii++) {
			$worksheet->set_column($ii, $ii, $header_width[$ii]);
		}
		$xlsRow++;
		for($ii = 0; $ii < count($header_text); $ii++) {
			$worksheet->write($xlsRow,$ii,$header_text[$ii], set_format("xlsFmtTableHeader", "B", "C", "TLBR", 0));
		}
		// end header
	} // end function header

	function add_date_t($givendate,$day=0,$mth=0,$yr=0) {
		$cd = strtotime($givendate);
		$newdate = date('Y-m-d', mktime(0, 0, 0, date('m',$cd)+$mth, date('d',$cd)+$day, date('Y',$cd)+$yr));
		return $newdate;
	}

	$arr_buff = explode(",",$COND_LIST);
	for($i = 0; $i < count($arr_buff); $i++) {
		$arr_sub = explode(":",$arr_buff[$i]);
		if ($arr_sub[1]=="=") $st_cond = $arr_sub[0]." = '".trim(${$arr_sub[0]})."'";
		elseif ($arr_sub[1]=="s%") $st_cond = $arr_sub[0]." like '".trim(${$arr_sub[0]})."%'";
		elseif ($arr_sub[1]=="%%") $st_cond = $arr_sub[0]." like '%".trim(${$arr_sub[0]})."%'";
		elseif ($arr_sub[1]=="s=") $st_cond = $arr_sub[0]." = '".trim(${$arr_sub[0]})."'";
		elseif ($arr_sub[1]=="s<=") $st_cond = $arr_sub[0]." <= '".trim(${$arr_sub[0]})."'";
		elseif ($arr_sub[1]=="s>=") $st_cond = $arr_sub[0]." >= '".trim(${$arr_sub[0]})."'";
		elseif ($arr_sub[1]=="==") $st_cond = $arr_sub[0]." = ".trim(${$arr_sub[0]});
		elseif ($arr_sub[1]=="<=") $st_cond = $arr_sub[0]." <= ".trim(${$arr_sub[0]});
		elseif ($arr_sub[1]==">=") $st_cond = $arr_sub[0]." >= ".trim(${$arr_sub[0]});
		elseif ($arr_sub[1]=="+1W") {
					$arr_temp = explode("/", trim(${$arr_sub[0]}));
					$sdate = ($arr_temp[2]-543)  ."-". str_pad($arr_temp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($arr_temp[0], 2, "0", STR_PAD_LEFT);
					$tdate = add_date_t($sdate,7,0,0);
					$st_cond = "(".$arr_sub[0]." >= '".$sdate."' and ".$arr_sub[0]." <= '".$tdate."')";
		} elseif ($arr_sub[1]=="+2W") {
					$arr_temp = explode("/", trim(${$arr_sub[0]}));
					$sdate = ($arr_temp[2]-543)  ."-". str_pad($arr_temp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($arr_temp[0], 2, "0", STR_PAD_LEFT);
					$tdate = add_date_t($sdate,14,0,0);
					$st_cond = "(".$arr_sub[0]." >= '".$sdate."' and ".$arr_sub[0]." <= '".$tdate."')";
		} elseif ($arr_sub[1]=="+1M") {
					$arr_temp = explode("/", trim(${$arr_sub[0]}));
					$sdate = ($arr_temp[2]-543)  ."-". str_pad($arr_temp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($arr_temp[0], 2, "0", STR_PAD_LEFT);
					$tdate = add_date_t($sdate,0,1,0);
					$st_cond = "(".$arr_sub[0]." >= '".$sdate."' and ".$arr_sub[0]." <= '".$tdate."')";
		} elseif ($arr_sub[1]=="+3M") {
					$arr_temp = explode("/", trim(${$arr_sub[0]}));
					$sdate = ($arr_temp[2]-543)  ."-". str_pad($arr_temp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($arr_temp[0], 2, "0", STR_PAD_LEFT);
					$tdate = add_date_t($sdate,0,3,0);
					$st_cond = "(".$arr_sub[0]." >= '".$sdate."' and ".$arr_sub[0]." <= '".$tdate."')";
		} elseif ($arr_sub[1]=="+6M") {
					$arr_temp = explode("/", trim(${$arr_sub[0]}));
					$sdate = ($arr_temp[2]-543)  ."-". str_pad($arr_temp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($arr_temp[0], 2, "0", STR_PAD_LEFT);
					$tdate = add_date_t($sdate,0,6,0);
					$st_cond = "(".$arr_sub[0]." >= '".$sdate."' and ".$arr_sub[0]." <= '".$tdate."')";
		} elseif ($arr_sub[1]=="+1Y") {
					$arr_temp = explode("/", trim(${$arr_sub[0]}));
					$sdate = ($arr_temp[2]-543)  ."-". str_pad($arr_temp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($arr_temp[0], 2, "0", STR_PAD_LEFT);
					$tdate = add_date_t($sdate,0,0,1);
					$st_cond = "(".$arr_sub[0]." >= '".$sdate."' and ".$arr_sub[0]." <= '".$tdate."')";
		} else {
			if (substr($arr_sub[1],0,2)=="->") { // กรณี ช่วง ->
					$val1 = trim(${$arr_sub[0]});
					$val2 = substr($arr_sub[1],2,strlen($arr_sub[1])-2);
					$st_cond = "(".$arr_sub[0]." >= ".$val1." and ".$arr_sub[0]." <= ".$val2.")";
			}
			else  $st_cond = "";
		} 
		$arr_cond_list[$arr_sub[0]] = $st_cond;
//		echo "$arr_sub[0]:$st_cond<br>";
	}
?>