<?
	include("../php_scripts/calendar_data.php");

	$NOW_DAY = date("d");
	$NOW_MONTH = date("m");		
	$NOW_YEAR = date("Y");
	$PLUS_TH_YEAR = 543;
	$NOW_DATE = date("Y/m/d");
	$NOW_DATE_str = date("F d, Y");

/*
<script language="JavaScript">
	function checkPageEnter(keycode, tvalue, all_page) {
		num_ret = NumOnly();
		if(num_ret || keycode=='13'){ 
			if(tvalue>0 && tvalue<=all_page && tvalue!=form1.current_page.value) {
				change_current_page(tvalue);
			} else {
				if(tvalue < 0 || tvalue>all_page) {
					num_ret = false;
				}
			}
		}
		return num_ret; 
	}	
</script>
*/
	
	function create_link_page($all_page, $curr_page) {	
	// ใน function นี้มีการเรียก javascript function ชื่อ checkPageEnter ใน function_utiility.js ใช้ด้วย ถ้าจะเรียกใช้ต้องไป include  เข้าไว้ในส่วน  javasscript ด้วย
		global $command;
		if($all_page > 1) {
			$page_link = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"label_normal\">";
	  		$page_link .= "  <tr>";
			$page_link .= "    <td width=\"15%\">".(($curr_page > 1)?"<img src=\"images/previous_button.gif\" onClick=\"change_current_page(".($curr_page-1).");\" style=\"cursor:hand\">":"")."&nbsp;</td>";
//			$page_link .= "    <td align=\"center\"><input type=text OnKeyPress=\"return NumOnly(); if(event.keyCode=='13'){ if(this.value>0&&this.value<=$all_page&&this.value!=form1.current_page.value) {change_current_page(this.value);} }\" size=2 maxlength=3 value=$curr_page> / $all_page หน้า</td>";
			$page_link .= "    <td align=\"center\"><input type=text OnKeyPress=\"return checkPageEnter(event.keyCode, this.value, $all_page);\" size=4 maxlength=4 value=$curr_page> / $all_page หน้า</td>";
			$page_link .= "    <td width=\"15%\" align=\"right\">".(($curr_page < $all_page)?"<img src=\"images/next_button.gif\" onClick=\"change_current_page(".($curr_page+1).");\" style=\"cursor:hand\">":"")."&nbsp;</td>";
			$page_link .= "  </tr>";
			$page_link .= "</table>";
		}
		return $page_link;
	}

	function create_link_page_1more($all_page, $curr_page, $pageidx) {
		// ชื่อเฉพาะ current_page ต้องตามด้วยตัวเลข page ที่มี เช่น ควบคุมหน้า 1 ให้ชื่อ current_page1 ควบคุมหน้า 2 ให้ชื่อ current_page2 ควบคุมหน้า 3 ให้ชื่อ  current_page3 เป็นต้น
		global $command;
		if($all_page > 1) {
			$page_link = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"label_normal\">";
	  		$page_link .= "  <tr>";
			$page_link .= "    <td width=\"15%\">".(($curr_page > 1)?"<img src=\"images/previous_button.gif\" onClick=\"change_current_page(".($curr_page-1).",".$pageidx.");\" style=\"cursor:hand\">":"")."&nbsp;</td>";
			$page_link .= "    <td align=\"center\"><input type=text OnKeyPress=\"return NumOnly(); if(event.keyCode=='13'){ if(this.value>0&&this.value<=$all_page&&this.value!=form1.current_page".$pageidx.".value) {change_current_page(this.value,".$pageidx.");} }\" size=2 maxlength=3 value=$curr_page> / $all_page หน้า</td>";
			$page_link .= "    <td width=\"15%\" align=\"right\">".(($curr_page < $all_page)?"<img src=\"images/next_button.gif\" onClick=\"change_current_page(".($curr_page+1).",".$pageidx.");\" style=\"cursor:hand\">":"")."&nbsp;</td>";
			$page_link .= "  </tr>";
			$page_link .= "</table>";
		}
		return $page_link;
	}

	function create_link_page_data($all_page, $curr_page,$formName) {
		global $command;
		if($all_page > 1) {
			$page_link = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" class=\"label_normal\">";
	  		$page_link .= "  <tr>";
			$page_link .= "    <td width=\"15%\">".(($curr_page > 1)?"<img src=\"images/previous_button.gif\" onClick=\"change_current_page_data(".($curr_page-1).",'".$formName."');\" style=\"cursor:hand\">":"")."&nbsp;</td>";
			//เดิม
                        //$page_link .= "    <td align=\"center\"><input type=text OnKeyPress=\"return NumOnly(); if(event.keyCode=='13'){ if(this.value>0&&this.value<=$all_page&&this.value!=eval(".$formName.").current_page.value) {change_current_page_data(this.value,'".$formName."');} }\" size=2 maxlength=3 value=$curr_page> / $all_page หน้า</td>";
                        
                        //Release 5.0.0.43 Begin Edit By Pitak.
                        $page_link .= "    <td align=\"center\"><input type=text OnKeyPress=\"return checkPageDetailEnter(event.keyCode, this.value, $all_page,'".$formName."');\" size=2 maxlength=3 value=$curr_page> / $all_page หน้า</td>";
                        //Release 5.0.0.43 End 
                        
			$page_link .= "    <td width=\"15%\" align=\"right\">".(($curr_page < $all_page)?"<img src=\"images/next_button.gif\" onClick=\"change_current_page_data(".($curr_page+1).",'".$formName."');\" style=\"cursor:hand\">":"")."&nbsp;</td>";
			$page_link .= "  </tr>";
			$page_link .= "</table>";
		}
		return $page_link;
	}

	function create_list_box($cmd,$name,$selected=0,$firstline="",$optional="",$isreturn=0,$height=1) {
		global $db_type,$db_host, $db_name, $db_user, $db_pwd;
		$db = new connect_db($db_host, $db_name, $db_user, $db_pwd);

		if(trim($cmd)){	
			$create_list="";
			$cmd=strtoupper($cmd);
			$count=$db->send_cmd($cmd);		
			while($row=$db->get_array()){	
				if($row['MENU_ID'])	$value=$row['MENU_ID'];
				if($row['MENU_LABEL'])	$label=$row['MENU_LABEL'];
				if($row['GROUP_ID'])	$value=$row['GROUP_ID'];
				if($row['GROUP_LABEL'])	$label=$row['GROUP_LABEL'];
				if($row['ID'])	$value=$row['ID'];
				if($row['NAME_TH'])	$label=$row['NAME_TH'];
				
				if($height == 1) {
					if(!$selected && $isreturn) $selected = $value;
					if(trim($value) == trim($selected)) {
						$create_list .= "<option value=\"$value\" selected>$label</option>";
					} else {
						$create_list .=  "<option value=\"$value\">$label</option>";
					}
				} else {
					if(in_array($value,$selected)) {
						$create_list .=  "<option value=\"$value\" selected>$label</option>";
					} else {
						$create_list .=  "<option value=\"$value\">$label</option>";
					}
				}
			}
		}
//		echo "SELECTED :: $selected";
		if($height > 1) {
			$multi = "multiple";
			$temp_selected = explode(',',$selected);
			unset($selected);
			$selected = $temp_selected;
		}
		echo "<select class=\"selectbox\" name=\"$name\" size=\"$height\" $multi $optional>";
		if($height == 1 && !$isreturn){ 
			if($selected != "") echo "<option value=\"\"> $firstline</option>";
			else echo "<option value=\"\" selected> $firstline</option>";
		} // end if
		if(trim($create_list)){	echo $create_list; }
/*****			
	while(list($value,$label)=$db->get_data()) {
			if($height == 1) {
				if(!$selected && $isreturn) $selected = $value;
				if(trim($value) == trim($selected)) {
					echo "<option value=\"$value\" selected>$label</option>";
				} else {
					echo "<option value=\"$value\">$label</option>";
				}
			} else {
				if(in_array($value,$selected)) {
					echo "<option value=\"$value\" selected>$label</option>";
				} else {
					echo "<option value=\"$value\">$label</option>";
				}
			}
		} //end while*******/	
		echo "</select> ";
		if($isreturn) return($selected);
	}

	function create_dpis_list_box($cmd,$name,$selected=0,$firstline="",$optional="",$isreturn=0,$height=1) {
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd,$L_ENG_NAME;
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		
		$field_list = substr($cmd, 7, (strpos($cmd, " from ") - 7));
		$arr_temp = explode(",", $field_list);

		if (substr($selected,0,1)=="'" && substr($selected,strlen($selected)-1,1)=="'") {
			$selected = str_replace("'","",$selected);
			$f_type = "s";
		} else if (substr($selected,0,1)=="\"" && substr($selected,strlen($selected)-1,1)=="\"") {
			$selected = str_replace("\"","",$selected);
			$f_type = "s";
		} else $f_type = "";
//		echo "selected=$selected, f_type=$f_type<br>";

		$db_dpis->send_cmd($cmd);				
//		$db_dpis->show_error();
//		echo "cmd=$cmd, SELECTED :: $selected";
		if($height > 1) {
			$multi = "multiple";
			$temp_selected = explode(',',$selected);
			unset($selected);
			$selected = $temp_selected;
		}

		echo "<select class=\"selectbox\" name=\"$name\" size=\"$height\" $multi $optional>";
		if($height == 1 && !$isreturn){ 
			if($selected != "") echo "<option value=\"\"> $firstline</option>";
			else echo "<option value=\"\" selected> $firstline</option>";
		} // end if
		while($data = $db_dpis->get_array()) {
			$value = $data[trim($arr_temp[0])];
			$label = $data[trim($arr_temp[1])];
			$label2 = $data[trim($arr_temp[2])];
			if($name=="PN_CODE"){	//ใช้สำหรับเฉพาะคำนำหน้าชื่อภาษาอังกฤษเท่านั้น	
				$L_ENG_NAME[$value] = $label2;
			}
			
			if($height == 1) {
				if(!$selected && $isreturn) $selected = $value;
				if ($f_type=="s" && trim($value) == trim($selected) && strlen(trim($value)) == strlen(trim($selected))) {
					echo "<option value=\"$value\" selected>$label</option>";
				} else if(!$f_type && trim($value) == trim($selected)) {
					echo "<option value=\"$value\" selected>$label</option>";
				} else {
					echo "<option value=\"$value\">$label</option>";
				}
			} else {
				if(in_array($value,$selected)) {
					echo "<option value=\"$value\" selected>$label</option>";
				} else {
					echo "<option value=\"$value\">$label</option>";
				}
			}
		} //end while
		echo "</select>";
		if($isreturn) return($selected);
	}

	function create_array_list_box($map_array_str,$name,$selected=0,$firstline="",$optional="",$isreturn=0,$height=1) {
		
		$arr_data = (array) null;
		$arr_buff = explode(",", $map_array_str);
		if (count($arr_buff) > 0) {
			for($i = 0; $i < count($arr_buff); $i++) {
				$arr_sub_buff = explode(">",$arr_buff[$i]);
				if (count($arr_sub_buff)>1) $arr_data[$arr_sub_buff[0]] = $arr_sub_buff[1];
				else $arr_data[$i] = $arr_sub_buff[0];
			}
				
			if($height > 1) {
				$multi = "multiple";
				$temp_selected = explode(',',$selected);
				unset($selected);
				$selected = $temp_selected;
			}
	
			echo "<select class=\"selectbox\" name=\"$name\" size=\"$height\" $multi $optional>";
			if($height == 1 && !$isreturn){ 
				if($selected != "") echo "<option value=\"\"> $firstline</option>";
				else echo "<option value=\"\" selected> $firstline</option>";
			} // end if
			foreach($arr_data as $key => $value) {
				$val = $key;
				$label = $value;
				
				if($height == 1) {
					if(!$selected && $isreturn) $selected = $value;
					if(trim($val) == trim($selected)) {
						echo "<option value=\"$val\" selected>$label</option>";
					} else {
						echo "<option value=\"$val\">$label</option>";
					}
				} else {
					if(in_array($val,$selected)) {
						echo "<option value=\"$val\" selected>$label</option>";
					} else {
						echo "<option value=\"$val\">$label</option>";
					}
				}
			} //end while
			echo "</select>";
			if($isreturn) return($selected);
		}
	}

	function get_name($id,$table,$field='name_th',$field_id='id') {
		global $db_host, $db_name, $db_user, $db_pwd;
		$db = new connect_db($db_host, $db_name, $db_user, $db_pwd);
		$cmd = "select $field from $table where $field_id = '$id'";
		$db->send_cmd($cmd);
		list($name)=$db->get_data();

		return($name);
	}

	function get_phone_no($phone_no){
		if($phone_no){
			$v_phone_no		=		explode("#",$phone_no);
			$phone_no		=		"$v_phone_no[1] $v_phone_no[2]";
			if($v_phone_no[3])	$phone_no	 .=	" ext.$v_phone_no[3]";
			return $phone_no;
		}else
			return $phone_no;
	}
	
	function show_phone_no($phone_no){
		if($phone_no) return "+66(0) $phone_no";
		else return $phone_no;
	}
	
	function show_date($date) {
		global $SESS_LANG;
		if ($date!="0000-00-00" && $date!="0000/00/00" && $date!="" && $date!="-") {
			$date = substr($date,0,10);
			$date = str_replace('-','/',$date);
			$array_date = explode('/',$date);
//			if ((int)$array_date[0] > 2000) { // รูปแบบเป็น yyyy/mm/dd
				$count_date = count($array_date);
				if($count_date == 2) {
					$day = '00';
					$month = $array_date[1];
					$year = $array_date[0];
				} elseif($count_date == 1) {
					$day = '00';
					$month = '00';
					$year = $array_date[0];
				} else {
					$day = $array_date[2];
					$month = $array_date[1];
					$year = $array_date[0];
				} 
				if ($SESS_LANG=="BDH") {
					$year = $year + 543;
				}
				return("$day/$month/$year");
//			} else return("$date");
		} //end if
		else { return("");}
	}
/*
	function show_date($date) {
	   global $SESS_LANG;
	   if ($date!="0000-00-00" && $date!="0000/00/00") {
				if ($SESS_LANG=="BDH") {
						if (!$date) { return $date; }
						else {return substr($date,8,2)."/".substr($date,5,2)."/".(substr($date,0,4)+543);}
				}//end if
				else {
						if (!$date) { return $date; }
						else {return substr($date,8,2)."/".substr($date,5,2)."/".(substr($date,0,4));}
				} //end else
		} //end if
		else { return("");}
	}    
*/	
	$days_per_year = 365.2425;
	$days_per_month = 30.436875;
	$seconds_per_day = 86400;				// 60 * 60 * 24

	function display_date($date,$full) {
	   global $month_abbr, $month_full, $BKK_FLAG, $SESS_DEPARTMENT_NAME;
		if ($date!="0000-00-00" && $date!="0000/00/00" && $date!="" && $date!="-") {
			$array_date = explode('-',substr($date, 0, 10));
			$day = $array_date[2];
			$month = $array_date[1];
			$year = $array_date[0];
			if ($SESS_DEPARTMENT_NAME=="กรมชลประทาน" || $BKK_FLAG==1) $day = $day + 0;
			if($day == '00') $day = "";
			if($month == '00') $month = "";
			else 
				if ($full==1 || $full==3) $month = $month_full[($month+0)][TH];
				else $month =	$month_abbr[($month+0)][TH];
			$year = $year + 543;
			if ($full==2) $year = substr($year,2,2);
			elseif ($full==3) $year = "พ.ศ. $year";
			return("$day $month $year");
		} //end if
		else { return("");}
	}

	function calculate_sec($date_d, $date_m, $date_y){
		global $days_per_year, $days_per_month, $seconds_per_day;
		
		$time_sec = 0;
		$time_sec += ($date_y * $days_per_year * $seconds_per_day);
		$time_sec += ($date_m * $days_per_month * $seconds_per_day);
		$time_sec += ($date_d * $seconds_per_day);
		return $time_sec;
	} 
	
	function date_adjust($input_date, $adjust_type, $adjust_value){
		list($year, $month, $date) = split("-", $input_date, 3);
		switch(strtolower($adjust_type)){
			case "y" :
			case "year" :
				$year += $adjust_value;
				if($month==2 && $date==29 && !isLeapYear($year)) $date = 28;
				break;
			case "m" :
			case "month" :
				$month += $adjust_value;
				if($month > 12){
					$year += floor($month / 12);
					if(($month % 12) == 0) $month = floor($month / 12);
					else $month = ($month % 12);
				}elseif($month < 1){
					$month = 12 + $month;
					$year -= 1;
				} // end if

				if($month==2 && $date==29 && !isLeapYear($year)) $date = 28;
				break;
			case "d" :
			case "date" :
				$date += $adjust_value;
				$max_date = get_max_date($month, $year);
				while($date > $max_date){
					$month++;
					$date -= $max_date;
					$max_date = get_max_date($month, $year);
				} // end while

				if($date < 1){
					$max_date = get_max_date(($month - 1), $year);
					$date = $max_date + $date;
					$month -= 1;
				} // end if
				
				if($month > 12){
					$year += floor($month / 12);
					if(($month % 12) == 0) $month = floor($month / 12);
					else $month = ($month % 12);
				}elseif($month < 1){
					$month = 12 + $month;
					$year -= 1;
				} // end if

				if($month==2 && $date==29 && !isLeapYear($year)) $date = 28;
				break;
		} // end switch case
		
		$return_date = $year ."-". str_pad($month, 2, "0", STR_PAD_LEFT) ."-". str_pad($date, 2, "0", STR_PAD_LEFT);
		return $return_date;
	} 
	
        /*เพิ่มเติม*/
        function get_datetime_db($input_date1, $input_date2,$rtn_year,$rtn_mm,$rtn_dd){
            global $db_type,$db_host, $db_name, $db_user, $db_pwd;
            $db = new connect_db($db_host, $db_name, $db_user, $db_pwd);
            $cmd = " select '$input_date2' b, 
                        '$input_date1' t, 
                        trunc(months_between(to_date('$input_date1','yyyy-mm-dd'),to_date('$input_date2','yyyy-mm-dd')) / 12) as years, 
                        trunc(months_between(to_date('$input_date1','yyyy-mm-dd'),to_date('$input_date2','yyyy-mm-dd')) - 
                          (trunc(months_between(to_date('$input_date1','yyyy-mm-dd'),to_date('$input_date2','yyyy-mm-dd')) / 12) * 12)) as months, 
                            trunc(to_date('$input_date1','yyyy-mm-dd')) - add_months(to_date('$input_date2','yyyy-mm-dd'),  
                          trunc(months_between(to_date('$input_date1','yyyy-mm-dd'),to_date('$input_date2','yyyy-mm-dd')))) as days 
                      from dual ";
            
            $db->send_cmd($cmd);
            $data = $db->get_array();
            $data = array_change_key_case($data, CASE_LOWER);
            $rtn_year= $data[years];
            $rtn_mm= $data[months];
            $rtn_dd= $data[days];
        }
        /**/
        
	function date_difference($input_date1, $input_date2, $diff_type){
		//$input_date1 = วันที่ปัจจุบัน
		//$input_date2 = วันที่นำมาคำนวณ
		global $days_per_year, $days_per_month, $seconds_per_day;
                /*Release 5.2.1.6*/
                global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $dpisdb_port;
                
                //echo $dpisdb_host.', '.$dpisdb_name.', '.$dpisdb_user.', '.$dpisdb_pwd.'<br>';
//		$db_chkage = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
                

		$db_chkage = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $dpisdb_port);
                /*Release 5.2.1.6*/
		
		if($input_date2 > $input_date1){/*เดิม*/
			// swap parameter
			$temp = $input_date1;
			$input_date1 = $input_date2;
			$input_date2 = $temp;
		} // end if
                
                if($diff_type=="full"){
                    $temp1=$input_date2;
                    $temp2=$input_date1;
                    
                    $input_date1 = $temp1;
                    $input_date2 = $temp2;
                }
		
		list($year1, $month1, $date1) = split("-", $input_date1, 3);
		list($year2, $month2, $date2) = split("-", $input_date2, 3);

		$date_diff = calculate_sec($date1, $month1, $year1) - calculate_sec($date2, $month2, $year2);
                //echo '>>'.$date_diff.'<br>';
                
                /**/
                /*
                 * 
                 * select '2016-01-13' b, 
                    '2017-01-12' t, 
                    trunc(months_between(to_date('2017-01-12','yyyy-mm-dd'),to_date('2016-01-13','yyyy-mm-dd')) / 12) as years, 
                    trunc(months_between(to_date('2017-01-12','yyyy-mm-dd'),to_date('2016-01-13','yyyy-mm-dd')) - 
                      (trunc(months_between(to_date('2017-01-12','yyyy-mm-dd'),to_date('2016-01-13','yyyy-mm-dd')) / 12) * 12)) as months, 
                        trunc(to_date('2017-01-12','yyyy-mm-dd')) - add_months(to_date('2016-01-13','yyyy-mm-dd'),  
                      trunc(months_between(to_date('2017-01-12','yyyy-mm-dd'),to_date('2016-01-13','yyyy-mm-dd')))) as days 
                  from dual
                 *  
                */
                /**/            $xyear=0;
                              // echo get_datetime_db($input_date1, $input_date2,$xyear,$xmonth,$xday);
                               // echo $xyear.','.$xmonth.','.$xday;
                                /**/
                
		switch(strtolower($diff_type)){
			case "y" :
			case "year" :
				$result = $date_diff / ($seconds_per_day * $days_per_year);
				break;
			case "m" :
			case "month" :
				$result = $date_diff / ($seconds_per_day * $days_per_month);
				break;
			case "d" :
			case "date" :
				$result = $date_diff / $seconds_per_day;
				break;
			case "full" : 
                            $date_begin=substr($input_date1,0,10);//2016-01-13
                            //$date_to=substr($input_date1,0,10);//2017-01-12
                            $date_to=substr($input_date2,0,10);//2017-01-12
                            /*เดิม*/    
                            /*$sql="select trunc(sysdate),
                                        trunc(months_between(trunc(sysdate),to_date('".$date_begin."','YYYY-MM-DD'))/12) YEARS,
                                        trunc(mod(months_between(trunc(sysdate),to_date('".$date_begin."','YYYY-MM-DD')),12)) MONTHS,
                                        trunc(trunc(sysdate)-(add_months(to_date('".$date_begin."','YYYY-MM-DD'),
                                        trunc(months_between(trunc(sysdate),to_date('".$date_begin."','YYYY-MM-DD'))/12)*12+trunc(mod(months_between(trunc(sysdate),to_date('".$date_begin."','YYYY-MM-DD')),12))))) DAYS 
                                        from dual ";*/
                            $sql="select trunc(sysdate),
                                        trunc(months_between(to_date('".$date_to."','YYYY-MM-DD'),to_date('".$date_begin."','YYYY-MM-DD'))/12) YEARS,
                                        trunc(mod(months_between(to_date('".$date_to."','YYYY-MM-DD'),to_date('".$date_begin."','YYYY-MM-DD')),12)) MONTHS,
                                        trunc(to_date('".$date_to."','YYYY-MM-DD')-(add_months(to_date('".$date_begin."','YYYY-MM-DD'),
                                        trunc(months_between(to_date('".$date_to."','YYYY-MM-DD'),to_date('".$date_begin."','YYYY-MM-DD'))/12)*12+trunc(mod(months_between(to_date('".$date_to."','YYYY-MM-DD'),to_date('".$date_begin."','YYYY-MM-DD')),12))))) DAYS 
                                        from dual ";
                                //$db_chkage;
                                $db_chkage->send_cmd($sql);
                                //echo '<per>'.$sql;
                                while($row=$db_chkage->get_array()){
                                    $total_year=$row['YEARS'];
                                    $total_month=$row['MONTHS'];
                                    $total_day=$row['DAYS'];
                                }
                               // echo $total_day."/". $total_month."/".$total_year;
                               // $result = $total_year." ปี ".$total_month." เดือน ".$total_day." วัน";
							  $result = (($total_year > 0)?"$total_year ปี ":"").(($total_month > 0)?"$total_month เดือน ":"").(($total_day > 0)?"$total_day วัน":"");
                            break;
			case "ymd" :
				$total_year = $year1 - $year2;
				if($year1 != $year2){
					if($month1 < $month2){ 
						$total_year--;
					}elseif($month1 == $month2){
						if($date1 < $date2) $total_year--;
					} // end if
				} // end if
				
				$total_month = 12 + ($month1 - $month2);
				if($date1 < $date2) $total_month--;
				$total_month = $total_month % 12;
				
				$total_day = $date1 - $date2;
				if($date1 < $date2) $total_day = (get_max_date((($month1==1)?12:($month1 - 1)), $year1) - $date2) + $date1 + 1;
//				if($date1 < $date2) $total_day = (get_max_date($month2, $year2) - $date2) + $date1 + 1;
				elseif($date1 > $date2) $total_day++;
				
				if($total_day >= 30){ 
					$total_month++;
					$total_day = $total_day % 30;
				} // end if
				
				if($total_month >= 12){
					$total_year++;
					$total_month = $total_month % 12;
				} // end if

//				$result = (($total_year > 0)?"$total_year ปี ":"").(($total_month > 0)?"$total_month เดือน ":"").(($total_day > 0)?"$total_day วัน":"");
                                /* Release 5.2.1.6*/
                                $date_begin=substr($input_date1,0,10);//2016-01-13
                                $date_to=substr($input_date2,0,10);//2017-01-12
                                
                                $sql="select '".$date_begin."' B, 
                                        '".$date_to."' T, 
                                        trunc(months_between(to_date('".$date_to."','yyyy-mm-dd'),to_date('".$date_begin."','yyyy-mm-dd')) / 12) as YEARS, 
                                        trunc(months_between(to_date('".$date_to."','yyyy-mm-dd'),to_date('".$date_begin."','yyyy-mm-dd')) - 
                                          (trunc(months_between(to_date('".$date_to."','yyyy-mm-dd'),to_date('".$date_begin."','yyyy-mm-dd')) / 12) * 12)) as MONTHS, 
                                            trunc(to_date('".$date_to."','yyyy-mm-dd')) - add_months(to_date('".$date_begin."','yyyy-mm-dd'),  
                                          trunc(months_between(to_date('".$date_to."','yyyy-mm-dd'),to_date('".$date_begin."','yyyy-mm-dd'))))+1 as DAYS 
                                      from dual";
                                //$db_chkage;
                                //echo '<pre>'.$sql;
                                $db_chkage->send_cmd($sql);		
                                while($row=$db_chkage->get_array()){
                                    $total_year=$row['YEARS'];
                                    $total_month=$row['MONTHS'];
                                    $total_day=$row[DAYS];
                                    if($total_day>0){$total_day=$total_day;}
                                    //echo $total_day.'<<< <br>';
                                }
                                //$result = $total_year." ปี ".$total_month." เดือน ".$total_day." วัน";
                                /* Release 5.2.1.6*/
				$result = ($total_year > 0 || $total_month > 0 || $total_day > 0)?"$total_year ปี $total_month เดือน $total_day วัน":"";/*เดิม*/
				break;
			default :
				$total_year = $year1 - $year2;
				if($year1 != $year2){
					if($month1 < $month2){ 
						$total_year--;
					}elseif($month1 == $month2){
						if($date1 < $date2) $total_year--;
					} // end if
				} // end if
				
				$total_month = 12 + ($month1 - $month2);
				if($date1 < $date2) $total_month--;
				$total_month = $total_month % 12;
				
				$total_day = $date1 - $date2 ;
//				if($date1 < $date2) $total_day = (get_max_date((($month1==1)?12:($month1 - 1)), $year1) - $date2) + $date1 + 1;
				if($date1 < $date2) $total_day = (get_max_date($month2, $year2) - $date2) + $date1 + 1;
				elseif($date1 > $date2) $total_day++;
                                
                               /* Release 5.2.1.6*/
                                $date_begin=substr($input_date2,0,10);//2016-01-13
                                $date_to=substr($input_date1,0,10);//2017-01-12
                                
                                $sql="select '".$date_begin."' B, 
                                        '".$date_to."' T, 
                                        trunc(months_between(to_date('".$date_to."','yyyy-mm-dd'),to_date('".$date_begin."','yyyy-mm-dd')) / 12) as YEARS, 
                                        trunc(months_between(to_date('".$date_to."','yyyy-mm-dd'),to_date('".$date_begin."','yyyy-mm-dd')) - 
                                          (trunc(months_between(to_date('".$date_to."','yyyy-mm-dd'),to_date('".$date_begin."','yyyy-mm-dd')) / 12) * 12)) as MONTHS, 
                                            trunc(to_date('".$date_to."','yyyy-mm-dd')) - add_months(to_date('".$date_begin."','yyyy-mm-dd'),  
                                          trunc(months_between(to_date('".$date_to."','yyyy-mm-dd'),to_date('".$date_begin."','yyyy-mm-dd'))))+1 as DAYS 
                                      from dual";
                                //$db_chkage;
                                $db_chkage->send_cmd($sql);
                                //echo '<per>'.$sql;
                                while($row=$db_chkage->get_array()){
                                    $total_year=$row['YEARS'];
                                    $total_month=$row['MONTHS'];
                                    $total_day=$row['DAYS'];
                                }
                                
                               // $result = $total_year." ปี ".$total_month." เดือน ".$total_day." วัน";
                                
                                /* Release 5.2.1.6*/
                                
				$result = (($total_year > 0)?"$total_year ปี ":"").(($total_month > 0)?"$total_month เดือน ":"").(($total_day > 0)?"$total_day วัน":"");/*เดิม*/
		} // end switch case
		
		return $result;
	} 
//<<------------------------------------------------ ใช้ในงานสะสมวันลา ---------------------------------------------------------------------->>

function date_vacation($input_date1, $input_date2, $diff_type){
		global $days_per_year, $days_per_month, $seconds_per_day;
                /*Release 5.2.1.6*/
                global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $dpisdb_port;
                
                //echo $dpisdb_host.', '.$dpisdb_name.', '.$dpisdb_user.', '.$dpisdb_pwd.'<br>';
//		$db_chkage = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
                

		$db_chkage = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $dpisdb_port);
                /*Release 5.2.1.6*/
		
		if($input_date2 > $input_date1){/*เดิม*/
			// swap parameter
			$temp = $input_date1;
			$input_date1 = $input_date2;
			$input_date2 = $temp;
		} // end if
                
                if($diff_type=="full"){
                    $temp1=$input_date2;
                    $temp2=$input_date1;
                    
                    $input_date1 = $temp1;
                    $input_date2 = $temp2;
                }
		
		list($year1, $month1, $date1) = split("-", $input_date1, 3);
		list($year2, $month2, $date2) = split("-", $input_date2, 3);

		$date_diff = calculate_sec($date1, $month1, $year1) - calculate_sec($date2, $month2, $year2);
                //echo '>>'.$date_diff.'<br>';
                
                /**/
                /*
                 * 
                 * select '2016-01-13' b, 
                    '2017-01-12' t, 
                    trunc(months_between(to_date('2017-01-12','yyyy-mm-dd'),to_date('2016-01-13','yyyy-mm-dd')) / 12) as years, 
                    trunc(months_between(to_date('2017-01-12','yyyy-mm-dd'),to_date('2016-01-13','yyyy-mm-dd')) - 
                      (trunc(months_between(to_date('2017-01-12','yyyy-mm-dd'),to_date('2016-01-13','yyyy-mm-dd')) / 12) * 12)) as months, 
                        trunc(to_date('2017-01-12','yyyy-mm-dd')) - add_months(to_date('2016-01-13','yyyy-mm-dd'),  
                      trunc(months_between(to_date('2017-01-12','yyyy-mm-dd'),to_date('2016-01-13','yyyy-mm-dd')))) as days 
                  from dual
                 *  
               
                
                /**/
                /**/            $xyear=0;
                              // echo get_datetime_db($input_date1, $input_date2,$xyear,$xmonth,$xday);
                               // echo $xyear.','.$xmonth.','.$xday;
                                /**/
                
		switch(strtolower($diff_type)){
			case "y" :
			case "year" :
				$result = $date_diff / ($seconds_per_day * $days_per_year);
				break;
			case "m" :
			case "month" :
				$result = $date_diff / ($seconds_per_day * $days_per_month);
				break;
			case "d" :
			case "date" :
				$result = $date_diff / $seconds_per_day;
				break;
			case "full" : 
                            $date_begin=substr($input_date1,0,10);//2016-01-13
                            $date_to=substr($input_date1,0,10);//2017-01-12
                                $sql="select trunc(sysdate),
                                        trunc(months_between(trunc(sysdate),to_date('".$date_begin."','YYYY-MM-DD'))/12) YEARS,
                                        trunc(mod(months_between(trunc(sysdate),to_date('".$date_begin."','YYYY-MM-DD')),12)) MONTHS,
                                        trunc(trunc(sysdate)-(add_months(to_date('".$date_begin."','YYYY-MM-DD'),
                                        trunc(months_between(trunc(sysdate),to_date('".$date_begin."','YYYY-MM-DD'))/12)*12+trunc(mod(months_between(trunc(sysdate),to_date('".$date_begin."','YYYY-MM-DD')),12))))) DAYS 
                                        from dual ";
                                //$db_chkage;
                                $db_chkage->send_cmd($sql);
                                //echo '<per>'.$sql;
                                while($row=$db_chkage->get_array()){
                                    $total_year=$row['YEARS'];
                                    $total_month=$row['MONTHS'];
                                    $total_day=$row['DAYS'];
                                }
                                
                                    $result = $total_year." ปี ".$total_month." เดือน ".$total_day." วัน";
                               
                            break;
			case "ymd" :
				$total_year = $year1 - $year2;
				if($year1 != $year2){
					if($month1 < $month2){ 
						$total_year--;
					}elseif($month1 == $month2){
						if($date1 < $date2) $total_year--;
					} // end if
				} // end if
				
				$total_month = 12 + ($month1 - $month2);
				if($date1 < $date2) $total_month--;
				$total_month = $total_month % 12;
				
				$total_day = $date1 - $date2;
				if($date1 < $date2) $total_day = (get_max_date((($month1==1)?12:($month1 - 1)), $year1) - $date2) + $date1 + 1;
//				if($date1 < $date2) $total_day = (get_max_date($month2, $year2) - $date2) + $date1 + 1;
				elseif($date1 > $date2) $total_day++;
				
				if($total_day >= 30){ 
					$total_month++;
					$total_day = $total_day % 30;
				} // end if
				
				if($total_month >= 12){
					$total_year++;
					$total_month = $total_month % 12;
				} // end if

//				
                                $date_begin=substr($input_date1,0,10);//2016-01-13
                                $date_to=substr($input_date2,0,10);//2017-01-12
                                
                                $sql="select '".$date_begin."' B, 
                                        '".$date_to."' T, 
                                        trunc(months_between(to_date('".$date_to."','yyyy-mm-dd'),to_date('".$date_begin."','yyyy-mm-dd')) / 12) as YEARS, 
                                        trunc(months_between(to_date('".$date_to."','yyyy-mm-dd'),to_date('".$date_begin."','yyyy-mm-dd')) - 
                                          (trunc(months_between(to_date('".$date_to."','yyyy-mm-dd'),to_date('".$date_begin."','yyyy-mm-dd')) / 12) * 12)) as MONTHS, 
                                            trunc(to_date('".$date_to."','yyyy-mm-dd')) - add_months(to_date('".$date_begin."','yyyy-mm-dd'),  
                                          trunc(months_between(to_date('".$date_to."','yyyy-mm-dd'),to_date('".$date_begin."','yyyy-mm-dd'))))+1 as DAYS 
                                      from dual";
                                //$db_chkage;
                                //echo '<pre>'.$sql;
                                $db_chkage->send_cmd($sql);		
                                while($row=$db_chkage->get_array()){
                                    $total_year=$row['YEARS'];
                                    $total_month=$row['MONTHS'];
                                    $total_day=$row[DAYS];
                                    //echo $total_day.'<<< <br>';
                                }
                               
                                    $result = $total_year." ปี ".$total_month." เดือน ".$total_day." วัน";
                               
                         
				//$result = ($total_year > 0 || $total_month > 0 || $total_day > 0)?"$total_year ปี $total_month เดือน $total_day วัน":"";/*เดิม*/
				break;
			default :
				$total_year = $year1 - $year2;
				if($year1 != $year2){
					if($month1 < $month2){ 
						$total_year--;
					}elseif($month1 == $month2){
						if($date1 < $date2) $total_year--;
					} // end if
				} // end if
				
				$total_month = 12 + ($month1 - $month2);
				if($date1 < $date2) $total_month--;
				$total_month = $total_month % 12;
				
				$total_day = $date1 - $date2 ;
//				if($date1 < $date2) $total_day = (get_max_date((($month1==1)?12:($month1 - 1)), $year1) - $date2) + $date1 + 1;
				if($date1 < $date2) $total_day = (get_max_date($month2, $year2) - $date2) + $date1 + 1;
				elseif($date1 > $date2) $total_day++;
                                
                               /* Release 5.2.1.6*/
                                $date_begin=substr($input_date2,0,10);//2016-01-13
                                $date_to=substr($input_date1,0,10);//2017-01-12
                                
                                $sql="select '".$date_begin."' B, 
                                        '".$date_to."' T, 
                                        trunc(months_between(to_date('".$date_to."','yyyy-mm-dd'),to_date('".$date_begin."','yyyy-mm-dd')) / 12) as YEARS, 
                                        trunc(months_between(to_date('".$date_to."','yyyy-mm-dd'),to_date('".$date_begin."','yyyy-mm-dd')) - 
                                          (trunc(months_between(to_date('".$date_to."','yyyy-mm-dd'),to_date('".$date_begin."','yyyy-mm-dd')) / 12) * 12)) as MONTHS, 
                                            trunc(to_date('".$date_to."','yyyy-mm-dd')) - add_months(to_date('".$date_begin."','yyyy-mm-dd'),  
                                          trunc(months_between(to_date('".$date_to."','yyyy-mm-dd'),to_date('".$date_begin."','yyyy-mm-dd'))))+1 as DAYS 
                                      from dual";
                                //$db_chkage;
                                $db_chkage->send_cmd($sql);
                                //echo '<per>'.$sql;
                                while($row=$db_chkage->get_array()){
                                    $total_year=$row['YEARS'];
                                    $total_month=$row['MONTHS'];
                                    $total_day=$row['DAYS'];
                                }
                                
                               
                                    
                                    $result = $total_year." ปี ".$total_month." เดือน ".$total_day." วัน";
                               
                 
                                
				//$result = (($total_year > 0)?"$total_year ปี ":"").(($total_month > 0)?"$total_month เดือน ":"").(($total_day > 0)?"$total_day วัน":"");/*เดิม*/
		} // end switch case
		
		return $result;
	} 


//<<------------------------------------------------------------------------>>
	function isLeapYear($year){
		if(($year % 4) == 0){
			if(($year % 100) != 0){
				return true;
			}else{ 
				if(($year % 400) == 0){
					return true;
				}else{
					return false;
				} // end if
			} // end if	
		}else{
			return false;
		} // end if
	} 
	
	function get_max_date($month, $year){
		if($month > 12){
			$year += floor($month / 12);
			if(($month % 12) == 0) $month = floor($month / 12);
			else $month = ($month % 12);
		} // end if
		
		switch($month){
			case 1 :
			case 3 :
			case 5 :
			case 7 :
			case 8 :
			case 10 :
			case 12 :
				return 31;
				break;
			case 4 :
			case 6 :
			case 9 :
			case 11 :
				return 30;
				break;
			case 2 :
				if(isLeapYear($year)) return 29;
				else return 28;
				break;
		} // end switch case
	} 

	function insert_log($log_detail){
		global $db, $db_type, $SESS_USERID, $SESS_USERNAME, $SESS_FIRSTNAME;
		
		$log_detail = str_replace("'","",$log_detail); //คำสั่งจะ error ถ้าไม่ตัด ' ที่เข้ามาในตัวแปร $log_detail
		
		if($db_type=="mysql") $log_date = "NOW()";
		elseif($db_type=="mssql") $log_date = "GETDATE()";
		elseif($db_type=="oci8" || $db_type=="odbc") 	{ $log_date = date("Y-m-d H:i:s"); $log_date = "'$log_date'"; }
		
		$cmd = " select max(log_id) as max_id from user_log ";
		$db->send_cmd($cmd);
		$data = $db->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$log_id = $data[max_id] + 1;
		
		$cmd = " insert into user_log (log_id, user_id, username, fullname, log_detail, log_date) values ($log_id, $SESS_USERID, '$SESS_USERNAME', '$SESS_FIRSTNAME', '$log_detail', $log_date) ";
		$count = $db->send_cmd($cmd);
		//$db->show_error();
		//ho "$count - $cmd";
	}

	function money2speech($money){
		$number2speech[1] = "หนึ่ง";				$number2speech[2] = "สอง";				$number2speech[3] = "สาม";
		$number2speech[4] = "สี่";					$number2speech[5] = "ห้า";				$number2speech[6] = "หก";
		$number2speech[7] = "เจ็ด";				$number2speech[8] = "แปด";			$number2speech[9] = "เก้า";
		
		$speech_suffix[1] = "";							$speech_suffix[2] = "สิบ";					$speech_suffix[3] = "ร้อย";					$speech_suffix[4] = "พัน";
		$speech_suffix[5] = "หมื่น";					$speech_suffix[6] = "แสน";					$speech_suffix[7] = "ล้าน";
		
		$speech = "";
		$money_text = number_format($money, 2, ".", "");
		$arr_temp = explode(".", $money_text);
		$money = $arr_temp[0];
		$decimal = substr($arr_temp[1], 0, 2);
		
		if($money > 0) :
			$money_length = strlen($money);
			for($i=0; $i<$money_length; $i++){
				if($money[$i] > 0){
					if(($money_length - $i) < 7) :
						if(($i==($money_length - 1)) && ($money[$i]==1) && ($money_length > 1)) $speech .= "เอ็ด";
						elseif(($i==($money_length - 2)) && ($money[$i]==1)) $speech .= $speech_suffix[$money_length - $i];
						elseif(($i==($money_length - 2)) && ($money[$i]==2)) $speech .= "ยี่" . $speech_suffix[$money_length - $i];
						else $speech .= $number2speech[$money[$i]] . $speech_suffix[$money_length - $i];
					else :
						if((($money_length - $i)==7) && ($money[$i]==1) && ($money_length > 7)) $speech .= "เอ็ด";
						elseif((($money_length - $i)==8) && ($money[$i]==1)) $speech .= $speech_suffix[($money_length - $i)%6];
						elseif((($money_length - $i)==8) && ($money[$i]==2)) $speech .= "ยี่" . $speech_suffix[($money_length - $i)%6];
						else $speech .= $number2speech[$money[$i]] . $speech_suffix[($money_length - $i)%6];

						if(($money_length - $i)==7) $speech .= "ล้าน";
					endif; 
				} // end if
			} // end for
			$speech .= "บาท";
		endif;
		
		if($decimal > 0) :
			$decimal_length = strlen($decimal);
			for($i=0; $i<$decimal_length; $i++){
				if($decimal[$i] > 0){
					if(($i==($decimal_length - 1)) && ($decimal[$i]==1) && ($decimal_length > 1)) $speech .= "เอ็ด";
					elseif(($i==($decimal_length - 2)) && ($decimal[$i]==1)) $speech .= $speech_suffix[$decimal_length - $i];
					elseif(($i==($decimal_length - 2)) && ($decimal[$i]==2)) $speech .= "ยี่" . $speech_suffix[$decimal_length - $i];
					else $speech .= $number2speech[$decimal[$i]] . $speech_suffix[$decimal_length - $i];
				} // end if
			} // end for
			$speech .= "สตางค์";
		else :
			$speech .= "ถ้วน";
		endif;
		
		return $speech;
	}

	function number2speech($number){
		$number2speech[1] = "หนึ่ง";				$number2speech[2] = "สอง";				$number2speech[3] = "สาม";
		$number2speech[4] = "สี่";					$number2speech[5] = "ห้า";				$number2speech[6] = "หก";
		$number2speech[7] = "เจ็ด";				$number2speech[8] = "แปด";			$number2speech[9] = "เก้า";
		
		$speech_suffix[1] = "";							$speech_suffix[2] = "สิบ";					$speech_suffix[3] = "ร้อย";					$speech_suffix[4] = "พัน";
		$speech_suffix[5] = "หมื่น";					$speech_suffix[6] = "แสน";					$speech_suffix[7] = "ล้าน";
		
		$speech = "";
		$number_text = number_format($number, 2, ".", "");
		$arr_temp = explode(".", $number_text);
		$numeral = $arr_temp[0];
		$decimal = substr($arr_temp[1], 0, 2);
		
		if(substr($numeral, 0, 1) == "-") :
			$numeral = substr($numeral, 1);
			$speech .= "ลบ";
		endif;

		if($numeral > 0) :
			$numeral_length = strlen($numeral);
			for($i=0; $i<$numeral_length; $i++){
				if($numeral[$i] > 0){
					if(($numeral_length - $i) < 7) :
						if(($i==($numeral_length - 1)) && ($numeral[$i]==1) && ($numeral_length > 1)) $speech .= "เอ็ด";
						elseif(($i==($numeral_length - 2)) && ($numeral[$i]==1)) $speech .= $speech_suffix[$numeral_length - $i];
						elseif(($i==($numeral_length - 2)) && ($numeral[$i]==2)) $speech .= "ยี่" . $speech_suffix[$numeral_length - $i];
						else $speech .= $number2speech[$numeral[$i]] . $speech_suffix[$numeral_length - $i];
					else :
						if((($numeral_length - $i)==7) && ($numeral[$i]==1) && ($numeral_length > 7)) $speech .= "เอ็ด";
						elseif((($numeral_length - $i)==8) && ($numeral[$i]==1)) $speech .= $speech_suffix[($numeral_length - $i)%6];
						elseif((($numeral_length - $i)==8) && ($numeral[$i]==2)) $speech .= "ยี่" . $speech_suffix[($numeral_length - $i)%6];
						else $speech .= $number2speech[$numeral[$i]] . $speech_suffix[($numeral_length - $i)%6];

						if(($numeral_length - $i)==7) $speech .= "ล้าน";
					endif; 
				} // end if
			} // end for
		endif;
		
		if($decimal > 0) :
			if($numeral == 0 || $numeral == "") $speech .= "ศูนย์";
			$speech .= "จุด";
			
			$decimal_length = strlen($decimal);
			for($i=0; $i<$decimal_length; $i++){
				if($decimal[$i] > 0) $speech .= $number2speech[$decimal[$i]];
				else $speech .= "ศูนย์";
			} // end for
		endif;
		
		return $speech;
	}
	
	function level_no_format($level_no, $display_format){
		global $BKK_FLAG, $MFA_FLAG;
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $dpisdb_port;

		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $dpisdb_port);
		$level_no = trim($level_no);
		$cmd = " select LEVEL_NAME, PER_TYPE, LEVEL_SHORTNAME, POSITION_TYPE, POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$level_no' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$TMP_LEVEL_NAME = $data2[LEVEL_NAME];
		$TMP_PER_TYPE = $data2[PER_TYPE];
		$TMP_LEVEL_SHORTNAME = $data2[LEVEL_SHORTNAME];
		$TMP_POSITION_TYPE = $data2[POSITION_TYPE];
		$TMP_POSITION_LEVEL = $data2[POSITION_LEVEL];
		if(!$display_format)	$display_format=2;
		if(is_numeric($level_no)){ 
			$level_no += 0;
		}else{
			if($level_no && $display_format==1){ 
				$level_no = $TMP_LEVEL_NAME; // ประเภทบริหาร ระดับสูง
			}elseif($level_no && $display_format==2){
				if ($level_no=="D1" || $level_no=="D2" || $level_no=="M1" || $level_no=="M2"){
					$level_no = $TMP_POSITION_TYPE.$TMP_POSITION_LEVEL; // บริหารสูง
				}else{
					$level_no = $TMP_POSITION_LEVEL; // สูง
				}
			}elseif($level_no && $display_format==3){
				$level_no = $TMP_POSITION_LEVEL; // สูง
			}elseif($level_no && $display_format==4){
				$level_no = $TMP_LEVEL_SHORTNAME; // บส.
			}
		} // end if
		return $level_no;
	}
	
	function pl_name_format($pl_name, $pm_name, $pt_name, $level_no, $display_format, $org_name, $org_name_1){
		global $BKK_FLAG, $MFA_FLAG, $RPT_N;

//		echo "$pl_name-$pm_name-$pt_name-$level_no";
		if ($pl_name==$pm_name) $pm_name = "";
		if(!$display_format)	$display_format=1;
//		if ($display_format==2 || $display_format==3) {
			$level_name = level_no_format($level_no, 3);
//		}
		if ($org_name && ($pm_name=="ผู้ว่าราชการจังหวัด" || $pm_name=="รองผู้ว่าราชการจังหวัด" || $pm_name=="ปลัดจังหวัด" || $pm_name=="หัวหน้าสำนักงานจังหวัด")) {
			$pm_name .= $org_name;
			$pm_name = str_replace("จังหวัดจังหวัด", "จังหวัด", $pm_name); 
			$pm_name = str_replace("สำนักงานจังหวัดสำนักงานจังหวัด", "สำนักงานจังหวัด", $pm_name); 
		} elseif ($org_name_1 && ($pm_name=="นายอำเภอ")) {
			$pm_name .= $org_name_1;
			$pm_name = str_replace("อำเภอที่ทำการปกครองอำเภอ", "อำเภอ", $pm_name); 
		}
		if ($display_format==3 && $level_no > "11") { // ไม่เอาระดับตำแหน่ง (แท่ง) ต่อท้ายตำแหน่งในสายงาน
			if (substr($pl_name,0,9)=="นักการทูต" || substr($pl_name,0,17)=="เจ้าหน้าที่การทูต" || substr($pl_name,0,15)=="นักบริหารการทูต")
				$pl_name = $pl_name . (($pt_name != "ทั่วไป" && $level_no >= "06"&& $level_no <= "11")?"$pt_name":"") . (trim($pm_name)?" (":"") . (trim($pm_name)?" (":"") . (trim($pm_name)?$pm_name:"") . (trim($pm_name)?")":"");
			else
				$pl_name = (trim($pm_name)?"$pm_name (":"") . (trim($pl_name)?$pl_name:"") . (($pt_name != "ทั่วไป" && $level_no >= "06"&& $level_no <= "11")?"$pt_name":"") . (trim($pm_name)?")":"");
		} else {
			if (substr($pl_name,0,9)=="นักการทูต" || substr($pl_name,0,17)=="เจ้าหน้าที่การทูต" || substr($pl_name,0,15)=="นักบริหารการทูต")
				$pl_name = "$pl_name$level_name" . (($pt_name != "ทั่วไป" && $level_no >= "06"&& $level_no <= "11")?"$pt_name":"") . (trim($pm_name)?" (":"") . (trim($pm_name)?" (":"") . (trim($pm_name)?$pm_name:"") . (trim($pm_name)?")":"");
			else
				$pl_name = (trim($pm_name)?"$pm_name (":"") . (trim($pl_name)?"$pl_name$level_name":"") . (($pt_name != "ทั่วไป" && $level_no >= "06"&& $level_no <= "11")?"$pt_name":"") . (trim($pm_name)?")":"");
		}
		return $pl_name;
	}
	
	function convert2thaidigit($str_input){
		$str_result = $str_input;
		$str_result = str_replace("0", "๐", $str_result);
		$str_result = str_replace("1", "๑", $str_result);
		$str_result = str_replace("2", "๒", $str_result);
		$str_result = str_replace("3", "๓", $str_result);
		$str_result = str_replace("4", "๔", $str_result);
		$str_result = str_replace("5", "๕", $str_result);
		$str_result = str_replace("6", "๖", $str_result);
		$str_result = str_replace("7", "๗", $str_result);
		$str_result = str_replace("8", "๘", $str_result);
		$str_result = str_replace("9", "๙", $str_result);
		return $str_result;
	}

	function convert_thaidigit2arabic($str_input){
		$str_result = $str_input;
		$str_result = str_replace("๐", "0", $str_result);
		$str_result = str_replace("๑", "1", $str_result);
		$str_result = str_replace("๒", "2", $str_result);
		$str_result = str_replace("๓", "3", $str_result);
		$str_result = str_replace("๔", "4", $str_result);
		$str_result = str_replace("๕", "5", $str_result);
		$str_result = str_replace("๖", "6", $str_result);
		$str_result = str_replace("๗", "7", $str_result);
		$str_result = str_replace("๘", "8", $str_result);
		$str_result = str_replace("๙", "9", $str_result);
		return $str_result;
	}

	function convert2rtfascii($InString){
//		echo "<br>IN :: $InString :: ".utf8_encode($InString);
		// convert to utf-8 before process
		$InString = utf8_encode(trim($InString));

		$OutString = "";
		for($CharPosition=0; $CharPosition<strlen($InString); $CharPosition++){
			$Char = $InString [$CharPosition];
			$AsciiChar = ord ($Char);
		
			if ($AsciiChar < 128){ 
				//1 7 0bbbbbbb (127)
//				echo "<br>1 : $AsciiChar => ".$Char;
				$OutString .= ($AsciiChar!=32)?("\\'".dechex($AsciiChar)):chr($AsciiChar);
			}else if ($AsciiChar >> 5 == 6){ 
				//2 11 110bbbbb 10bbbbbb (2047)
				$FirstByte = ($AsciiChar & 31);
				$CharPosition++;
				$Char = $InString [$CharPosition];
				$AsciiChar = ord ($Char);
				$SecondByte = ($AsciiChar & 63);
				$AsciiChar = ($FirstByte * 64) + $SecondByte;

//				echo "<br>2 : $AsciiChar => ".chr($AsciiChar);
				$OutString .= "\\'".dechex($AsciiChar);
			}else if ($AsciiChar >> 4  == 14){
				//3 16 1110bbbb 10bbbbbb 10bbbbbb
				$FirstByte = ($AsciiChar & 31);
				$CharPosition++;
				$Char = $InString [$CharPosition];
				$AsciiChar = ord ($Char);
				$SecondByte = ($AsciiChar & 63);
				$CharPosition++;
				$Char = $InString [$CharPosition];
				$AsciiChar = ord ($Char);
				$ThidrByte = ($AsciiChar & 63);
				$AsciiChar = ((($FirstByte * 64) + $SecondByte) * 64) + $ThidrByte;
				
//				echo "<br>3 : $AsciiChar => ".chr($AsciiChar);
				$OutString .= "\\'".dechex($AsciiChar);
			}else if ($AsciiChar >> 3 == 30){ 
				//4 21 11110bbb 10bbbbbb 10bbbbbb 10bbbbbb
				$FirstByte = ($AsciiChar & 31);
				$CharPosition++;
				$Char = $InString [$CharPosition];
				$AsciiChar = ord ($Char);
				$SecondByte = ($AsciiChar & 63);
				$CharPosition++;
				$Char = $InString [$CharPosition];
				$AsciiChar = ord ($Char);
				$ThidrByte = ($AsciiChar & 63);
				$CharPosition++;
				$Char = $InString [$CharPosition];
				$AsciiChar = ord ($Char);
				$FourthByte = ($AsciiChar & 63);
				$AsciiChar = ((((($FirstByte * 64) + $SecondByte) * 64) + $ThidrByte) * 64) + $FourthByte;
			
//				echo "<br>4 : $AsciiChar => ".chr($AsciiChar);
				$OutString .= "\\'".dechex($AsciiChar);
			}
		} // end for
		
//		echo "<br>OUT :: $OutString";
		return trim($OutString);
	}
	
	function  replaceRTF($VarString, $ReplaceString, $RTFContent){
		$CtrlString0 = "\\fcs1";
		$CtrlString1 = "\\rtlch";
		$CtrlString2 = "\\insrsid";
		$ReplaceCtrlString = "{\\rtlch\\fcs0 \\af266\\afs32 \\ltrch\\fcs1 \\f266\\fs32\\lang1054\\insrsid13386347";
		$VarPos = strpos($RTFContent, $VarString, 0);
		while($VarPos !== false){
			$tmpContent = substr($RTFContent, 0, $VarPos);
			$CtrlPos1 = strrpos($tmpContent, "{");
			$CtrlPos2 = strpos($tmpContent, "\\", $CtrlPos1+4); // เผื่อให้พ้นจาก blank เช่น {  \fcs1
			$stchform = trim(substr($tmpContent,$CtrlPos1+1,$CtrlPos2-$CtrlPos1-1));
//			echo "<br>($VarString) VarPos :: $VarPos || CtrlPos1 = $CtrlPos1 || $stchform";
			if ($stchform==$CtrlString0 || $stchform==$CtrlString1) {
//				echo "<br>str1-".substr($RTFContent, $CtrlPos1, ($VarPos + strlen($VarString)) - $CtrlPos1);
				$CtrlPos2 = strpos($RTFContent, $CtrlString2, $CtrlPos1);
				if (!$CtrlPos2) $CtrlPos1+strlen($CtrlString1);
				$CtrlPos2 = strpos($RTFContent, " ", $CtrlPos2);
				$RTFContent = substr_replace($RTFContent, "$ReplaceCtrlString", $CtrlPos1, ($CtrlPos2 - $CtrlPos1));
				$VarPos = strpos($RTFContent, $VarString, $CtrlPos1);
				$RTFContent = substr_replace($RTFContent, "$ReplaceString", $VarPos, strlen($VarString));
//				echo "<br>str2-".substr($RTFContent, $CtrlPos1, ($VarPos + strlen($VarString)) - $CtrlPos1)."<br>";
			} // end if
			
			$VarPos = strpos($RTFContent, $VarString, $VarPos + strlen($VarString));
		} // end while
		return $RTFContent;
	} 

	function array_sort(&$array, $sort = array()) {
		// ============================== use for sort array multi dimension ===========================
		// example 
		//		Input Array :
		// 		$product_update[$i]["province_id"] = $data->province_id;
		//		$product_update[$i]["province_title"] = $data->province_title;
		//		$product_update[$i]["count_supplier"] = $data->count_sup + 0;
		//		$product_update[$i]["total_product"] = $new_data->count_product + 0;
		//		$product_update[$i]["update_product"] = $new_data->count_product + 0;
		//		$product_update[$i]["progress_percent"] = ($product_update[$i]["update_product"] / $product_update[$i]["total_product"]) * 100;
		//		$product_update[$i]["progress_percent"] = number_format($product_update[$i]["progress_percent"],2,".",",");
		//
		//		Sort Array :
		//		$sort_arr[0]['name'] = "progress_percent";
		//		$sort_arr[0]['sort'] = "DESC";
		//		$sort_arr[0]['case'] = FALSE; //  Case sensitive
		//		
		//		$sort_arr[1]['name'] = "total_product";
		//		$sort_arr[1]['sort'] = "DESC";
		//		$sort_arr[1]['case'] = FALSE;
		//		
		//		array_sort($product_update, $sort_arr);		
		// ====================================================================================
	
	   $function = '';
	   while (list($key) = each($sort)) {
		if (isset($sort[$key]['case'])&&($sort[$key]['case'] == TRUE)) {
		   $function .= 'if (strtolower($a["' . $sort[$key]['name'] . '"])<>strtolower($b["' . $sort[$key]['name'] . '"])) { return (strtolower($a["' . $sort[$key]['name'] . '"]) ';
		 } else {
		   $function .= 'if ($a["' . $sort[$key]['name'] . '"]<>$b["' . $sort[$key]['name'] . '"]) { return ($a["' . $sort[$key]['name'] . '"] ';
		}
		 if (isset($sort[$key]['sort'])&&($sort[$key]['sort'] == "DESC")) {
		   $function .= '<';
		 } else {
		   $function .= '>';
		 }
		 if (isset($sort[$key]['case'])&&($sort[$key]['case'] == TRUE)) {
		 $function .= ' strtolower($b["' . $sort[$key]['name'] . '"])) ? 1 : -1; } else';
		 } else {
		   $function .= ' $b["' . $sort[$key]['name'] . '"]) ? 1 : -1; } else';
		}
	   }
	   $function .= ' { return 0; }';
	   if (isset($array))
	   usort($array, create_function('$a, $b', $function));
	}

	function show_quote($text){
		$text = str_replace('\'', '&#39;', $text);
		$text = str_replace('"', '&quot;', $text);
		return trim($text);
	}

	function save_quote($text){
		$text=str_replace("'","''",$text);
		return trim($text);
	}

	function card_no_format($card_no, $display_format){
		$card_no = trim($card_no);
		if(!$display_format)	$display_format=1;
		if($card_no && $display_format==2){ 
			$card_no = substr($card_no,0,1)." ".substr($card_no,1,4)." ".substr($card_no,5,5)." ".substr($card_no,10,2)." ".substr($card_no,12,1);
		}elseif($card_no && $display_format==3){
			$card_no = substr($card_no,0,1)."-".substr($card_no,1,4)."-".substr($card_no,5,5)."-".substr($card_no,10,2)."-".substr($card_no,12,1);
		} // end if
		return $card_no;
	}
	
	function pos_no_format($pos_no, $display_format){
		$pos_no = trim($pos_no);
		if(!$display_format)	$display_format=1;
		if($pos_no && $display_format==2){ 
			$pos_no = substr($pos_no,0,2)."-".substr($pos_no,2,4)."-".substr($pos_no,6,3);
		} // end if
		return $pos_no;
	}
	
	function show_date_format($date, $display_format){
		global $SESS_LANG;
		$date = substr(trim($date),0,10);
		if(!$display_format)	$display_format=1;
		if(trim($date) && $date!="-"){
			if($display_format==1){ //31/01/2553
				$SESS_LANG="BDH";
				$date=show_date($date);
			}elseif($display_format==2){ //31 ม.ค. 2553
				$date=display_date($date,'');
			}elseif($display_format==3){//31 มกราคม 2553
				$date=display_date($date,1);
			}elseif($display_format==4){ //31 ม.ค. 53
				$date=display_date($date,2);
			}elseif($display_format==5){//31 มกราคม พ.ศ. 2553
				$date=display_date($date,3);
			}elseif($display_format==6){ //31/01/2010
				$SESS_LANG="";
				$date=show_date($date);
			} // end if
		}else{
			$date = "";
		}
		return $date;
	}
	
	function save_date($date){
		$date = substr(trim($date),0,10);
		$date = str_replace('-','/',$date);
		if(trim($date)){
			$array_date = explode('/',$date);
			$date = ($array_date)? ($array_date[2] > 2400 ? $array_date[2] - 543 : $array_date[2]) ."-". $array_date[1] ."-". $array_date[0] : ""; 
		}else{
			$date = "";
		}
		return $date;
	}
	
	function show_text($text){
		$text = str_replace("\n", "  ", $text);
		$text = str_replace("\r", "  ", $text);
		$text = str_replace("\n\r", "  ", $text);
		$text = str_replace("\r\n", "  ", $text);
		return trim($text);
	}

	function personal_gen_name($pername, $persurname) {
		// fine first 2 middle char
		$name2 = "";
		preg_match_all("/([ก-ฮ]|[A-Z][a-z])/", $pername, $match, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($match as $key) {
			if ($i==1) {
//				echo $key[0].$key[count($key)-1]."<br>";
				$name2=$key[0].$key[count($key)-1];
			}
			$i++;
		}
		$surname2 = "";
		preg_match_all("/([ก-ฮ]|[A-Z][a-z])/", $persurname, $match, PREG_PATTERN_ORDER);
		$i = 0;
		foreach ($match as $key) {
			if ($i==1) {
//				echo $key[0].$key[count($key)-1]."<br>";
				$surname2=$key[0].$key[count($key)-1];
			}
			$i++;
		}
		
		return $name2.$surname2;
	}

//	function show_org_name($ORG_ID, $ORG_ID_1, $ORG_ID_2){
//		global $BKK_FLAG, $MFA_FLAG;
//		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $dpisdb_port;
//
//		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $dpisdb_port);
//		$OT_CODE = $ORG_NAME = $ORG_ID_REF = "";
//		if($ORG_ID){
//		$cmd = " select OT_CODE, ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID = $ORG_ID";
//		$db_dpis2->send_cmd($cmd);
////			$db_dpis2->show_error();
//		$data2 = $db_dpis2->get_array();
//		$OT_CODE = trim($data2[OT_CODE]);
//		$ORG_NAME = trim($data2[ORG_NAME]);
//		$ORG_ID_REF = trim($data2[ORG_ID_REF]);
//		}
//		if ($ORG_NAME=="-") $ORG_NAME = "";
//		$DEPARTMENT_NAME = "";
//		if($ORG_ID_REF){
//			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_REF ";
//			$db_dpis2->send_cmd($cmd);
//	//			$db_dpis2->show_error();
//			$data2 = $db_dpis2->get_array();
//			$DEPARTMENT_NAME = trim($data2[ORG_NAME]);
//		}
//		$ORG_NAME_1 = "";
//		if ($ORG_ID_1) {
//			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_1 ";
//			$db_dpis2->send_cmd($cmd);
//	//			$db_dpis2->show_error();
//			$data2 = $db_dpis2->get_array();
//			$ORG_NAME_1 = trim($data2[ORG_NAME]);
//			if ($ORG_NAME_1=="-") $ORG_NAME_1 = "";
//		}
//
//		$ORG_NAME_2 = "";
//		if ($ORG_ID_2) {
//			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_2 ";
//			$db_dpis2->send_cmd($cmd);
//	//			$db_dpis2->show_error();
//			$data2 = $db_dpis2->get_array();
//			$ORG_NAME_2 = trim($data2[ORG_NAME]);
//			if ($ORG_NAME_2=="-") $ORG_NAME_2 = "";
//		}
//
//		$ORG_NAME = trim($ORG_NAME_2." ".$ORG_NAME_1." ".$ORG_NAME);
//		if (($OT_CODE == "01" && $DEPARTMENT_NAME=="กรมการปกครอง") || $BKK_FLAG==1 || ($MFA_FLAG==1 && $ORG_NAME=="สำนักงานเลขานุการกรม")) 
//			$ORG_NAME = trim($ORG_NAME." ".$DEPARTMENT_NAME);
//
//		return $ORG_NAME;
//	}

function show_org_name($ORG_ID, $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, $ORG_ID_4, $ORG_ID_5){ 
		global $BKK_FLAG, $MFA_FLAG, $SESS_ORG_SETLEVEL;
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $dpisdb_port;

		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd, $dpisdb_port);
		$OT_CODE = $ORG_NAME = $ORG_ID_REF = "";
		if($ORG_ID){
		$cmd = " select OT_CODE, ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID = $ORG_ID";
		$db_dpis2->send_cmd($cmd);
//			$db_dpis2->show_error();
		$data2 = $db_dpis2->get_array();
		$OT_CODE = trim($data2[OT_CODE]);
		$ORG_NAME = trim($data2[ORG_NAME]);
		$ORG_ID_REF = trim($data2[ORG_ID_REF]);
		}
		if ($ORG_NAME=="-") $ORG_NAME = "";
		$DEPARTMENT_NAME = "";
		if($ORG_ID_REF){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_REF ";
			$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$DEPARTMENT_NAME = trim($data2[ORG_NAME]);
		}
		$ORG_NAME_1 = "";
		if ($ORG_ID_1) {
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_1 ";
			$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_1 = trim($data2[ORG_NAME]);
			if ($ORG_NAME_1=="-") $ORG_NAME_1 = "";
		}

		$ORG_NAME_2 = "";
		if ($ORG_ID_2) {
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_2 ";
			$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_2 = trim($data2[ORG_NAME]);
			if ($ORG_NAME_2=="-") $ORG_NAME_2 = "";
		}
  //----------------------------create by kong----------------------------------------  
        $ORG_NAME_3 = "";
		if ($ORG_ID_3) {
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_3 ";
			$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_3 = trim($data2[ORG_NAME]);
			if ($ORG_NAME_3=="-") $ORG_NAME_3 = "";
		}
        
        $ORG_NAME_4 = "";
		if ($ORG_ID_4) {
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_4 ";
			$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_4 = trim($data2[ORG_NAME]);
			if ($ORG_NAME_4=="-") $ORG_NAME_4 = "";
		}
        $ORG_NAME_5 = "";
		if ($ORG_ID_5) {
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_5 ";
			$db_dpis2->send_cmd($cmd);
	//			$db_dpis2->show_error();
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_5 = trim($data2[ORG_NAME]);
			if ($ORG_NAME_5=="-") $ORG_NAME_5 = "";
		}
    switch ($SESS_ORG_SETLEVEL){//ระดับสำนักกองจาก C06 ประกาศเป็น Gobal
        case 2 : 
             $ORG_NAME_3 = "";
             $ORG_NAME_4 = "";
             $ORG_NAME_5 = "";
            break;
        case 3 : 
             $ORG_NAME_4 = "";
             $ORG_NAME_5 = "";
            break;
        case 4 : 
             $ORG_NAME_5 = "";
        break;     
    }// end switch case
 //---------------------------------------------------------------------------------
		$ORG_NAME = trim($ORG_NAME_5." ".$ORG_NAME_4." ".$ORG_NAME_3." ".$ORG_NAME_2." ".$ORG_NAME_1." ".$ORG_NAME);
		if (($OT_CODE == "01" && $DEPARTMENT_NAME=="กรมการปกครอง") || $BKK_FLAG==1 || ($MFA_FLAG==1 && $ORG_NAME=="สำนักงานเลขานุการกรม")) 
			$ORG_NAME = trim($ORG_NAME." ".$DEPARTMENT_NAME);

		return $ORG_NAME;
	}

	function valid_char19_date_yyyymmdd($date_str, $lang="") {		// ตรวจสอบรูปแบบวันที่ กำหนดเป็น CHAR 19 [yyyy-mm-dd hh:mm:ss]  yyyy default เป็น ค.ศ.
		$err = "";
		$first_check = explode(" ", $date_str);
		if ( preg_match("/[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]/",$first_check[0],$match) ) {
			$dd = explode("-",$first_check[0]);
			$n_dd = (int)$dd[2];
			$n_mm = (int)$dd[1];
			if (strtolower($lang)=="bhu")
				$n_yy = (int)$dd[0]-543;
			else 	$n_yy = (int)$dd[0];
			if($n_mm==1 || $n_mm==3|| $n_mm==5 || $n_mm==7 || $n_mm==8 || $n_mm==10 || $n_mm==12) {	// ลงท้ายด้วย คม
				if(!($n_dd>0 && $n_dd<=31)) {
					$err = "วันไม่ถูกต้อง";
				}
			} else if($n_mm==4 || $n_mm==6 || $n_mm==9 || $n_mm==11) {	// ลงท้ายด้วย ยน
				if(!($n_dd>0 && $n_dd<=30)) {
					$err = "วันไม่ถูกต้อง";
				}
			} else if($n_mm==2) {
				$leap_yy=false;
				if($n_yy % 4 ==0 && $n_yy % 100 ==0) {
					if($n_yy % 400 ==0) {
						$leap_yy=true;
					}				
				} else if($n_yy % 4 ==0) {
					$leap_yy=true;
				}			
				if(!($n_dd>0 && $n_dd<=29)) {
					$err = "วันไม่ถูกต้อง";
				} else if($n_dd==29) {
					if(!$leap_yy) {
						$err = "วันไม่ถูกต้อง";
					}
				}
			} else {
				$err = "เดือนไม่ถูกต้อง";
			} //End if 
//			echo "yyyymmdd  err=$err (".$first_check[0].") (".$first_check[1].")<br>";
			// ตรวจสอบเวลา
			if ( trim($first_check[1]) ) {
				if ( preg_match("/[0-9][0-9]:[0-9][0-9]:[0-9][0-9]/",$first_check[1],$match) ) {
					$tt = explode(":",$first_check[1]);
					$n_hh = (int)$tt[0];
					$n_mi = (int)$tt[1];
					$n_ss = (int)$tt[2];
//					echo "$n_hh:$n_mi:$n_ss<br>";
					if(!($n_hh >=0 && $n_mm<=23)) {
						$err = "ชม.ไม่ถูกต้อง";
					}
					if(!($n_mi >=0 && $n_mi<=59)) {
						$err = "นาทีไม่ถูกต้อง";
					}
					if(!($n_ss >=0 && $n_ss<=59)) {
						$err = "วินาทีไม่ถูกต้อง";
					}
//					echo "time err::$err<br>";
					if ($err) return "NO";
					else 	return "DT";
				} else {
					return "NO";
				}
			} else {
				if ($err) return "NO";
				else 	return "D";
			}
		} else {
			return "NO";
		} 
	}	// end function

	function valid_char19_date_ddmmyyyy($date_str, $lang="BHU") {		// ตรวจสอบรูปแบบวันที่ กำหนดเป็น CHAR 19 [yyyy-mm-dd hh:mm:ss]  yyyy default เป็น พ.ศ.
		$err = "";
		$first_check = explode(" ", $date_str);
		$first_check[0] = str_replace("/","-",$first_check[0]);
		if ( preg_match("/[0-9][0-9]-[0-9][0-9]-[0-9][0-9][0-9][0-9]/",$first_check[0],$match) ) {
			$dd = explode("-",$first_check[0]);
			$n_dd = (int)$dd[0];
			$n_mm = (int)$dd[1];
			if (strtolower($lang)=="bhu")
				$n_yy = (int)$dd[2]-543;
			else 	$n_yy = (int)$dd[2];
			if($n_mm==1 || $n_mm==3|| $n_mm==5 || $n_mm==7 || $n_mm==8 || $n_mm==10 || $n_mm==12) {	// ลงท้ายด้วย คม
				if(!($n_dd>0 && $n_dd<=31)) {
					$err = "วันไม่ถูกต้อง";
				}
			} else if($n_mm==4 || $n_mm==6 || $n_mm==9 || $n_mm==11) {	// ลงท้ายด้วย ยน
				if(!($n_dd>0 && $n_dd<=30)) {
					$err = "วันไม่ถูกต้อง";
				}
			} else if($n_mm==2) {
				$leap_yy=false;
				if($n_yy % 4 ==0 && $n_yy % 100 ==0) {
					if($n_yy % 400 ==0) {
						$leap_yy=true;
					}				
				} else if($n_yy % 4 ==0) {
					$leap_yy=true;
				}			
				if(!($n_dd>0 && $n_dd<=29)) {
					$err = "วันไม่ถูกต้อง";
				} else if($n_dd==29) {
					if(!$leap_yy) {
						$err = "วันไม่ถูกต้อง";
					}
				}
			} else {
				$err = "เดือนไม่ถูกต้อง";
			} //End if 
//			echo "ddmmyyyy  err=$err (".$first_check[0].") (".$first_check[1].")<br>";
			// ตรวจสอบเวลา
			if ( trim($first_check[1]) ) {
				if ( preg_match("/[0-9][0-9]:[0-9][0-9]:[0-9][0-9]/",$first_check[1],$match) ) {
					$tt = explode(":",$first_check[1]);
					$n_hh = (int)$tt[0];
					$n_mi = (int)$tt[1];
					$n_ss = (int)$tt[2];
					if(!($n_hh >=0 && $n_mm<=23)) {
						$err = "ชม.ไม่ถูกต้อง";
					}
					if(!($n_mi >=0 && $n_mi<=59)) {
						$err = "นาทีไม่ถูกต้อง";
					}
					if(!($n_ss >=0 && $n_ss<=59)) {
						$err = "วินาทีไม่ถูกต้อง";
					}
					if ($err) return "NO";
					else 	return "DT";
				} else {
					return "NO";
				}
			} else {
				if ($err) return "NO";
				else 	return "D";
			}
		} else {
			return "NO";
		} 
	}	// end function

	function get_user_login($USERID,$PER_ID){
		global $DPISDB,$db_dpis,$SESS_FIRSTNAME,$SESS_LASTNAME;
	
		if($PER_ID){ 	// ผู้ล็อกอินเข้ามา
			if($DPISDB=="odbc"){	
				$cmd = " select 	a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, 
												b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
												a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
												c.PL_CODE, d.PN_CODE, e.EP_CODE, a.PER_SALARY, a.LEVEL_NO,
												c.POS_NO_NAME , d.POEM_NO_NAME , e.POEMS_NO_NAME
								 from 		PER_PRENAME b
												inner join (
													((
														PER_PERSONAL a
														left join PER_POSITION c on a.POS_ID = c.POS_ID
													) 	left join PER_POS_EMP d on a.POEM_ID = d.POEM_ID
													) 	left join PER_POS_EMPSER e on a.POEMS_ID = e.POEMS_ID 
												) on a.PN_CODE = b.PN_CODE
								where		a.PER_ID = $PER_ID ";
			}elseif($DPISDB=="oci8"){
				$cmd = "	select 		a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, 
													b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
													a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
													c.PL_CODE, d.PN_CODE, e.EP_CODE, a.PER_SALARY, a.LEVEL_NO,
												c.POS_NO_NAME , d.POEM_NO_NAME , e.POEMS_NO_NAME
									  from 		PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e
									  where 	a.PN_CODE=b.PN_CODE and a.POS_ID=c.POS_ID(+) and a.POEM_ID=d.POEM_ID(+) and a.POEMS_ID=e.POEMS_ID(+)  
													and a.PER_ID=$PER_ID ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select 	a.PER_ID, a.POS_ID, c.POS_NO, d.POEM_NO as EMP_POS_NO, e.POEMS_NO as EMPS_POS_NO, 
												b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_TYPE, 
												a.PER_CARDNO, a.PER_BIRTHDATE, c.ORG_ID, d.ORG_ID as EMP_ORG_ID, e.ORG_ID as EMPS_ORG_ID,
												c.PL_CODE, d.PN_CODE, e.EP_CODE, a.PER_SALARY, a.LEVEL_NO,
												c.POS_NO_NAME , d.POEM_NO_NAME , e.POEMS_NO_NAME
								 from 		PER_PERSONAL a
												inner join PER_PRENAME b on a.PN_CODE = b.PN_CODE
												left join PER_POSITION c on a.POS_ID = c.POS_ID
												left join PER_POS_EMP d on a.POEM_ID = d.POEM_ID
												left join PER_POS_EMPSER e on a.POEMS_ID = e.POEMS_ID 
								where		a.PER_ID = $PER_ID ";
			} // end if	
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
//echo "$USERID : $PER_ID -> $cmd";
//$db_dpis->show_error();
	
			$PER_NAME = $data[PN_NAME] . $data[PER_NAME]." ". $data[PER_SURNAME];				
			$PER_TYPE = $data[PER_TYPE];
			if($PER_TYPE==1){
				$ORG_ID = $data[ORG_ID];				$POS_NO_NAME = $data[POS_NO_NAME];		$POS_NO = $data[POS_NO];				
			}elseif($PER_TYPE==2) {
				$ORG_ID = $data[EMP_ORG_ID];	$POS_NO_NAME = $data[POEM_NO_NAME];	$POS_NO = $data[EMP_POS_NO];		
			}elseif ($PER_TYPE == 3) {
				$ORG_ID = $data[EMPS_ORG_ID];	$POS_NO_NAME = $data[POEMS_NO_NAME];	$POS_NO = $data[EMPS_POS_NO];	
			}
			if($POS_NO_NAME)		$POS_NO = $POS_NO_NAME.$POS_NO;
			/*$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME = $data2[ORG_NAME];*/

			if(!$PER_NAME)  	$PER_NAME = "$SESS_FIRSTNAME $SESS_LASTNAME";	
			if($POS_NO){
				$LOGIN_NAME = "$PER_NAME (เลขที่ตำแหน่ง ".$POS_NO.")";
			}else{
				$LOGIN_NAME = $PER_NAME;
			}
		}else{
			$LOGIN_NAME = "$SESS_FIRSTNAME $SESS_LASTNAME";
		}
		
	return $LOGIN_NAME;
	} //end function
	
	function 	get_ref_filename(){
		$arraySplitted = split("/" , $_SERVER['HTTP_REFERER']);
		$point = count($arraySplitted) - 1;
		$tmpFileName = $arraySplitted[ $point ];
		$arrFileName = explode("?",$tmpFileName);
		$FileName = $arrFileName[0];
	return $FileName;
	} // end function
	
	function showdata_connect($cmd_all,$datashow) {
		 global $db_dpis1;
		 $db_dpis1->send_cmd($cmd_all);
		 $data_cut = $db_dpis1->get_array();
		 $detail_id_1 =  trim($data_cut[$datashow]);
		 //if (!is_numeric($TMP_LOG_DETAIL_cut)) $detail_id_1 = "error ค่าที่ตัดออกมาไม่ใช่ตัวเลขทั้งหมด";
		 return $detail_id_1;
		}
		
	function cut_string($TMP_LOG_DETAIL,$ar_cut,$cut1,$ar_cut1,$cut2,$ar_cut2,$cut3,$ar_cut3){
		$array_date=explode('[',$TMP_LOG_DETAIL);
		$id_detail=$array_date[$ar_cut];
		
		if(($cut1!="") && ($ar_cut1!="")){
		$array_date_2=explode($cut1,$id_detail);
		$id_detail=$array_date_2[$ar_cut1];
		}
		
		if(($cut2!="") && ($ar_cut2!="")){
		$array_date_3=explode($cut2,$id_detail);
		$id_detail=$array_date_3[$ar_cut2];
		}
		
		if(($cut3!="") && ($ar_cut3!="")){
		$array_date_4=explode($cut3,$id_detail);
		$id_detail=$array_date_4[$ar_cut3];
		}
		
		$detail_2 = str_replace("]","",$id_detail);
		$TMP_LOG_DETAIL_cut = trim($detail_2);
		
		return $TMP_LOG_DETAIL_cut;
	}	
	
	function show_image($PER_ID,$case_db){
		//if(!$data2) $data2 = $data2;
			global $db_dpis2,$db_dpis3,$db_dpis4;

			//global $db_dpis2,$db_dpis3;
			//$cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID and PIC_SHOW='1' and (PIC_SIGN = 0 or PIC_SIGN is NULL)";/*เดิม*/
                        $cmd = " select * from PER_PERSONALPIC where PER_ID=$PER_ID AND PIC_SHOW='1' AND (PIC_SIGN <>1 or PIC_SIGN is null) ";
//			echo "IMG:$cmd<br>";
			//$cnt = $db_dpis2->send_cmd($cmd);
			if($case_db == 1) 
			$cnt = $db_dpis2->send_cmd($cmd);
			else 
			$cnt = $db_dpis3->send_cmd($cmd);
			 if ($cnt){
				if($case_db == 1) 
				$data2 = $db_dpis2->get_array();
				else 
				$data2 = $db_dpis3->get_array();
				
				$TMP_PER_CARDNO = trim($data2[PER_CARDNO]);
				$PER_GENNAME = trim($data2[PER_GENNAME]);
				$PIC_PATH = trim($data2[PER_PICPATH]);
				$PIC_SEQ = trim($data2[PER_PICSEQ]);
				$T_PIC_SEQ = substr("000",0,3-strlen("$PIC_SEQ"))."$PIC_SEQ";
				$PIC_SERVER_ID = trim($data2[PIC_SERVER_ID]);
				$PER_PICSAVEDATE = trim($data2[PER_PICSAVEDATE]);
				if($PIC_SERVER_ID > 0){
					if($PIC_SERVER_ID==99){		// $PIC_SERVER_ID 99 = ip จากตั้งค่าระบบ C06				 ใช้ \ 
						// หา # กรณี server อื่น เปลี่ยน # ให้เป็น \ เพื่อใช้ในการอัพโหลดรูป
						$PIC_PATH = $IMG_PATH_DISPLAY."#".$PIC_PATH;
						$PIC_PATH = str_replace("#","'",$PIC_PATH);
						$PIC_PATH = addslashes($PIC_PATH);
						$PIC_PATH = str_replace("'","",$PIC_PATH);

						$img_file = $PIC_PATH;
					}else{  // other server
						$cmd = " select * from OTH_SERVER where SERVER_ID=$PIC_SERVER_ID ";
						//$cnt_other = $db_dpis3->send_cmd($cmd);
						if($case_db == 1) 
						$cnt_other = $db_dpis3->send_cmd($cmd);
						else 
						$cnt_other = $db_dpis4->send_cmd($cmd);
						if ($cnt_other) { 
							//$data3 = $db_dpis3->get_array();
							if($case_db == 1) 
							$data3 = $db_dpis3->get_array();
							else 
							$data3 = $db_dpis4->get_array();
							
							$PIC_SERVER_NAME = trim($data3[SERVER_NAME]);
							$ftp_server = trim($data3[FTP_SERVER]);
							$ftp_username = trim($data3[FTP_USERNAME]);
							$ftp_password = trim($data3[FTP_PASSWORD]);
							$main_path = trim($data3[MAIN_PATH]);
							$http_server = trim($data3[HTTP_SERVER]);
							if ($http_server) {
								//echo "1.".$http_server."/".$img_file."<br>";
								$fp = @fopen($http_server."/".$img_file, "r");
								if ($fp !== false) $img_file = $http_server."/".$img_file;
								else $img_file=$IMG_PATH."shadow.jpg";
								fclose($fp);
							} else {
//								echo "2.".$img_file."<br>";
								$img_file = file_exists("../".$img_file)?("../".$img_file):$IMG_PATH."shadow.jpg";
							}
						}
					}
				}else{ // localhost  $PIC_SERVER_ID == 0
					$img_file =  "../".$PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";		//$PIC_PATH.($TMP_PER_CARDNO=="NULL"?$PER_GENNAME:$TMP_PER_CARDNO)."-".$T_PIC_SEQ.".jpg";
				}
			}else{
                            $img_file = $IMG_PATH."shadow.jpg";
                        }
		return $img_file;
	}
	
	function chk_master_table($command,$cmd,$table,$ID_chk,$ID_data){ //กรณีเช็คคีย์ตัวเดียว 
			
			global $db_dpis;
			$cmd1 = " select $ID_chk from $table where $ID_chk = '$ID_data'"; 
			$count_data = $db_dpis->send_cmd($cmd1);
			if($command == "ADD")
				if (!$count_data) $Miss_sql = "insert......$cmd<br>";
			else if($command == "DELETE")
				if ($count_data) $Miss_sql = "delete......$cmd<br>";
				
			return $Miss_sql;
	}
	
	function add_field($table_name, $field_name, $data_type, $size, $null_format){
		global $db_dpis, $db, $DPISDB, $BKK_FLAG, $MFA_FLAG, $RPT_N;
//		echo "$table_name-$field_name-$data_type-$size";
		$cmd = " SELECT $field_name FROM $table_name ";
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		if (!$count_data) {
			if ($data_type=="VARCHAR") {
				if($DPISDB=="odbc" || $DPISDB=="mysql") 
					$cmd = " ALTER TABLE $table_name ADD $field_name VARCHAR($size) $null_format ";
				elseif($DPISDB=="oci8") 
					$cmd = " ALTER TABLE $table_name ADD $field_name VARCHAR2($size) $null_format ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} elseif ($data_type=="NUMBER") {
				if($DPISDB=="odbc") 
					$cmd = " ALTER TABLE $table_name ADD $field_name NUMBER $null_format ";
				elseif($DPISDB=="oci8") 
					$cmd = " ALTER TABLE $table_name ADD $field_name NUMBER($size) $null_format ";
				elseif($DPISDB=="mysql")
					$cmd = " ALTER TABLE $table_name ADD $field_name DECIMAL($size) $null_format ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} elseif ($data_type=="SINGLE") {
				if($DPISDB=="odbc") 
					$cmd = " ALTER TABLE $table_name ADD $field_name SINGLE $null_format ";
				elseif($DPISDB=="oci8") 
					$cmd = " ALTER TABLE $table_name ADD $field_name NUMBER($size) $null_format ";
				elseif($DPISDB=="mysql")
					$cmd = " ALTER TABLE $table_name ADD $field_name SMALLINT($size) $null_format ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} elseif ($data_type=="INTEGER") {
				if($DPISDB=="odbc") 
					$cmd = " ALTER TABLE $table_name ADD $field_name INTEGER $null_format ";
				elseif($DPISDB=="oci8") 
					$cmd = " ALTER TABLE $table_name ADD $field_name NUMBER($size) $null_format ";
				elseif($DPISDB=="mysql")
					$cmd = " ALTER TABLE $table_name ADD $field_name INTEGER($size) $null_format ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} elseif ($data_type=="INTEGER2") {
				if($DPISDB=="odbc") 
					$cmd = " ALTER TABLE $table_name ADD $field_name INTEGER2 $null_format ";
				elseif($DPISDB=="oci8") 
					$cmd = " ALTER TABLE $table_name ADD $field_name NUMBER($size) $null_format ";
				elseif($DPISDB=="mysql")
					$cmd = " ALTER TABLE $table_name ADD $field_name SMALLINT($size) $null_format ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} elseif ($data_type=="MEMO") {
				if($DPISDB=="odbc") 
					$cmd = " ALTER TABLE $table_name ADD $field_name MEMO $null_format ";
				elseif($DPISDB=="oci8") 
					$cmd = " ALTER TABLE $table_name ADD $field_name VARCHAR2($size) $null_format ";
				elseif($DPISDB=="mysql")
					$cmd = " ALTER TABLE $table_name ADD $field_name TEXT $null_format ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			} elseif ($data_type=="CHAR") {
				$cmd = " ALTER TABLE $table_name ADD $field_name CHAR($size) $null_format ";
				$db_dpis->send_cmd($cmd);
				//$db_dpis->show_error();
			}
		}
		return $count_data;
	}
	
	// คำนวน วันหยุด สำหรับเรื่องการลาหยุด
	function get_day($STARTDATE, $STARTPERIOD, $ENDDATE, $ENDPERIOD, $AB_CODE) {
		global $DPISDB;
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
			
		$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

		//	นับจำนวนวันลา โดยไม่รวมวันหยุดเสาร์ - อาทิตย์ และวันหยุดราชการ (table) X
		//วันเริ่มต้น
		$arr_temp = explode("-", $STARTDATE);
		$START_DAY = $arr_temp[2];
		$START_MONTH = $arr_temp[1];
		$START_YEAR = $arr_temp[0];
		$STARTDATE_ce_era = $START_YEAR ."-". $START_MONTH ."-". $START_DAY;
		
		//ถึงวันที่
		$arr_temp = explode("-", $ENDDATE);
		$END_DAY = $arr_temp[2];
		$END_MONTH = $arr_temp[1];
		$END_YEAR = $arr_temp[0];
		$ENDDATE = $END_YEAR ."-". $END_MONTH ."-". $END_DAY;
		$ENDDATE_ce_era = $END_YEAR ."-". $END_MONTH ."-". $END_DAY;	

		// หาวันเริ่มต้นเป็นวันหยุดหรือไม่ ?
		if($DPISDB=="odbc") 
			$cmd = " select  AB_COUNT from PER_ABSENTTYPE where AB_CODE=$AB_CODE ";
		elseif($DPISDB=="oci8") 
			$cmd = " select  AB_COUNT from PER_ABSENTTYPE where AB_CODE=$AB_CODE ";
		elseif($DPISDB=="mysql")
			$cmd = " select  AB_COUNT from PER_ABSENTTYPE where AB_CODE=$AB_CODE ";
		$cntAB = $db_dpis3->send_cmd($cmd);
		$data = $db_dpis3->get_array();
		$AB_COUNT = $data[AB_COUNT];
//		echo "get_day...STARTDATE=$STARTDATE, STARTPERIOD=$STARTPERIOD, ENDDATE=$ENDDATE, ENDPERIOD=$ENDPERIOD, AB_CODE=$AB_CODE (AB_COUNT=$AB_COUNT) ($cmd)<br>";
		
//		echo "st ($STARTDATE_ce_era) - en ($ENDDATE_ce_era)<br>";
//		if(!$INVALID_STARTDATE && !$INVALID_ENDDATE){
			//$ABSENT_DAY = 1;
			$TMP_STARTDATE = date("Y-m-d", mktime(0, 0, 0, $START_MONTH, $START_DAY, $START_YEAR));
	
			if($AB_COUNT == 2){		//ไม่นับวันหยุดเท่านั้น***		// PERIOD 1 = ครึ่งเช้า / 2 = ครึ่งบ่าย / 3 = ทั้งวัน
				$DAY_START_NUM = date("w", mktime(0, 0, 0, $START_MONTH, $START_DAY, $START_YEAR));
				$DAY_END_NUM = date("w", mktime(0, 0, 0, $END_MONTH, $END_DAY, $END_YEAR));
				if(($DAY_START_NUM == 0 || $DAY_START_NUM == 6)  && ($DAY_END_NUM == 0 || $DAY_END_NUM == 6)){			// เริ่มต้น / สิ้นสุด เป็นวันเสาร์ - อาทิตย์  (หานอก loop)
					$ABSENT_START_DAY = 0;
				}else if(($DAY_START_NUM == 0 || $DAY_START_NUM == 6)  && ($DAY_END_NUM != 0 || $DAY_END_NUM != 6)){	// เริ่มต้น เป็นวันเสาร์ - อาทิตย์  / สิ้นสุด เป็นวันธรรมดา (หานอก loop)
					$ABSENT_START_DAY = 0;
				}else if(($DAY_START_NUM != 0 || $DAY_START_NUM != 6)  && ($DAY_END_NUM == 0 || $DAY_END_NUM == 6)){	// เริ่มต้น  เป็นวันธรรมดา / สิ้นสุด เป็นวันเสาร์ - อาทิตย์  (หานอก loop)
					$ABSENT_START_DAY = 0;	
				}else if(($DAY_START_NUM != 0 && $DAY_START_NUM != 6)  && ($DAY_END_NUM != 0 && $DAY_END_NUM != 6)){	 // เริ่มต้น / สิ้นสุด ไม่เป็นวันเสาร์ - อาทิตย์  (หานอก loop)
	//				$ABSENT_START_DAY = 1;	// ลองเอาออกก่อน ใน loop นับวันมันนับทุกวันอยู่แล้ว และ เช็ควันหยุดอยู่ใน loop อยู่แล้ว ไม่รู้ว่าส่วนนี้ทำทำไม
					$ABSENT_START_DAY = 0;
				}
				
				// หาวันเริ่มต้นเป็นวันหยุดหรือไม่ ?
				if($DPISDB=="odbc") 
					$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
				elseif($DPISDB=="oci8") 
					$cmd = " select HOL_NAME from PER_HOLIDAY where SUBSTR(HOL_DATE, 1, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
				elseif($DPISDB=="mysql")
					$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
				$IS_HOLIDAY_START = $db_dpis3->send_cmd($cmd);
				
				// หาวันสิ้นสุดเป็นวันหยุดหรือไม่ ?
				if($DPISDB=="odbc") 
					$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$END_YEAR-$END_MONTH-$END_DAY' ";
				elseif($DPISDB=="oci8") 
					$cmd = " select HOL_NAME from PER_HOLIDAY where SUBSTR(HOL_DATE, 1, 10)='$END_YEAR-$END_MONTH-$END_DAY' ";
				elseif($DPISDB=="mysql")
					$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$END_YEAR-$END_MONTH-$END_DAY' ";
				$IS_HOLIDAY_END = $db_dpis3->send_cmd($cmd);
				if($IS_HOLIDAY_START || $IS_HOLIDAY_END){		// ถ้าวันเริ่ม / สิ้นสุดเป็นวันหยุดราชการ  ก็ไม่นับตรงนี้
					$ABSENT_START_DAY = 0;
				}
	
				// หักครึ่งวัน	// PERIOD 1 = ครึ่งเช้า / 2 = ครึ่งบ่าย / 3 = ทั้งวัน
				// คิดคำนวณครึ่งวัน กรณีนี้ไม่นับครึ่งวันเพราะวันเริ่มต้น / สิ้นสุดเป็นวันหยุดอยู่แล้ว ไม่เอามาคิด	
				// ถ้า ว / ด / ป เริ่มและสิ้นสุดเป็นวันเดียวกันและเป็นครึ่งวัน ทั้งเช้า / บ่ายไม่ต้องหัก 0.5
				$period_err = 0;
				$ABSENT_2_STARTPERIOD = 0;
				$ABSENT_2_ENDPERIOD = 0;
				if(trim("$START_YEAR-$START_MONTH-$START_DAY")==trim("$END_YEAR-$END_MONTH-$END_DAY")){
					if($STARTPERIOD == 1 && $ENDPERIOD == 2){		// เริ่มต้น เป็นครึ่งเช้า และสิ้นสุด เป็นครึ่งบ่าย
						$period_err = 1;	//	"วันเดียวกัน เริ่มครึ่งเช้า สิ้นสุดครึ่งบ่าย ถือเป็น 1 วัน โปรดแก้ไขเป็น 'ทั้งวัน'";
					} else if($STARTPERIOD == 2 && $ENDPERIOD == 1){		// เริ่มต้นและสิ้นสุด เป็นครึ่งวันทั้งคู่  [ให้ตัวใดตัวหนึ่งเป็น 0.5]
						$period_err = 2;	//	"วันเดียวกัน เริ่มครึ่งบ่าย สิ้นสุดครึ่งเช้า ผิดรูปแบบ โปรดแก้ไขแก้ไข";
					} else if($STARTPERIOD != 3 && $ENDPERIOD != 3){		// เริ่มต้นและสิ้นสุด เป็นครึ่งวันทั้งคู่ และเท่ากัน ก็คือ ครึ่งวัน เช้าหรือบ่าย  [ให้ตัวใดตัวหนึ่งเป็น 0.5]
						$ABSENT_2_STARTPERIOD = 0.5;
					}else{
						if($STARTPERIOD == 3 && ($ENDPERIOD == 1 || $ENDPERIOD == 2))		
							$period_err = 3; // "วันเดียวกัน เริ่มทั้งวัน สิ้นสุดครึ่งบ่าย ถือเป็น 1 วัน โปรดแก้ไขสิ้นสุดเป็น 'ทั้งวัน'";
						if($ENDPERIOD == 3 && ($STARTPERIOD == 1 || $STARTPERIOD == 2))	
							$period_err = 4; // "วันเดียวกัน เริ่มครึ่งเช้า สิ้นสุดทั้งวัน ถือเป็น 1 วัน โปรดแก้ไขเริ่มเป็น 'ทั้งวัน'";
					}
				}else{
					if($STARTPERIOD == 1){
						$period_err = 5;	// "เริ่มวันแรกเป็นครึ่งเช้า เว้นบ่าย ไม่ถูกต้อง โปรดทำแยกเป็นลาอีก 1 รายการ";
					} else if($STARTPERIOD == 2){		//  เริ่มวันแรกครึ่งบ่าย
						if(($DAY_START_NUM == 0 || $DAY_START_NUM == 6) || $IS_HOLIDAY_START){	// ถ้าเป็นวันหยุดราชการหรือนักขัตฤกษ์ ไม่นับวัน
							$ABSENT_2_STARTPERIOD = 0;
						}else{  // case ปกติ วันธรรมดา
							$ABSENT_2_STARTPERIOD = 0.5;
						}
					}
					if($ENDPERIOD == 2){
						$period_err = 6;	// "สิ้นสุดจบที่ครึ่งบ่าย เว้นเช้า ไม่ถูกต้อง โปรดทำแยกเป็นลาอีก 1 รายการ";
					} else if($ENDPERIOD == 1){
						if(($DAY_END_NUM == 0 || $DAY_END_NUM == 6) || $IS_HOLIDAY_END){	// ถ้าเป็นวันหยุดราชการหรือนักขัตฤกษ์ ไม่นับวัน
							$ABSENT_2_ENDPERIOD = 0;
						}else{  // case ปกติ วันธรรมดา
							$ABSENT_2_ENDPERIOD = 0.5;
						}
					}
				}
			}else if($AB_COUNT == 1){		// นับวันหยุดเท่านั้น
				$ABSENT_START_DAY = 1; 
				// ถ้า ว / ด / ป เริ่มและสิ้นสุดเป็นวันเดียวกันและเป็นครึ่งวัน ทั้งเช้า / บ่ายไม่ต้องหัก 0.5
				$period_err = "";
				$ABSENT_2_STARTPERIOD = 0;
				$ABSENT_2_ENDPERIOD = 0;
				if(trim("$START_YEAR-$START_MONTH-$START_DAY")==trim("$END_YEAR-$END_MONTH-$END_DAY")){
					if($STARTPERIOD == 1 && $ENDPERIOD == 2){		// เริ่มต้น เป็นครึ่งเช้า และสิ้นสุด เป็นครึ่งบ่าย
						$period_err = 1;	// "วันเดียวกัน เริ่มครึ่งเช้า สิ้นสุดครึ่งบ่าย ถือเป็น 1 วัน\nโปรดแก้ไขเป็น 'ทั้งวัน'";
					} else if($STARTPERIOD == 2 && $ENDPERIOD == 1){		// เริ่มต้นและสิ้นสุด เป็นครึ่งวันทั้งคู่  [ให้ตัวใดตัวหนึ่งเป็น 0.5]
						$period_err = 2;	// "วันเดียวกัน เริ่มครึ่งบ่าย สิ้นสุดครึ่งเช้า ผิดรูปแบบ\nโปรดแก้ไขให้ถูกต้อง";
					} else if($STARTPERIOD != 3 && $ENDPERIOD != 3){		// เริ่มต้นและสิ้นสุด เป็นครึ่งวันทั้งคู่ และเท่ากัน ก็คือ ครึ่งวัน เช้าหรือบ่าย  [ให้ตัวใดตัวหนึ่งเป็น 0.5]
						$ABSENT_2_STARTPERIOD = 0.5;
					}else{
						if($STARTPERIOD == 3 && ($ENDPERIOD == 1 || $ENDPERIOD == 2))		
							$period_err = 3;	// "วันเดียวกัน เริ่มทั้งวัน สิ้นสุดครึ่งบ่าย ถือเป็น 1 วัน\nโปรดแก้ไขสิ้นสุดเป็น 'ทั้งวัน'";
						if($ENDPERIOD == 3 && ($STARTPERIOD == 1 || $STARTPERIOD == 2))	
							$period_err = 4;	// "วันเดียวกัน เริ่มครึ่งเช้า สิ้นสุดทั้งวัน ถือเป็น 1 วัน\nโปรดแก้ไขเริ่มเป็น 'ทั้งวัน'";
					}
				} else {	// วันไม่เท่ากัน
					if($STARTPERIOD == 1){		// เริ่มวันแรกเป็นครึ่งเช้า เว้นบ่าย ไม่ถูกต้อง โปรดทำแยกเป็นลาอีก 1 รายการ
						$period_err = 5;	// "เริ่มวันแรกเป็นครึ่งเช้า เว้นบ่าย ไม่ถูกต้อง\nโปรดทำแยกเป็นลาอีก 1 รายการ";
					} else if($ENDPERIOD == 2){		// สิ้นสุด เป็นครึ่งวันหลัง
						$period_err = 6;	// "สิ้นสุดจบที่ครึ่งบ่าย เว้นเช้า ไม่ถูกต้อง โปรดทำแยกเป็นลาอีก 1 รายการ";
					} else {
						if($STARTPERIOD == 2){		// เริ่มต้น เป็นครึ่งวันหลัง
							$ABSENT_2_STARTPERIOD = 0.5;	
						}
						if($ENDPERIOD == 1){		// สิ้นสุด เป็นครึ่งวันแรก
							$ABSENT_2_ENDPERIOD = 0.5;
						}
					}            
				}
			}
			
			$ABSENT_START_DAY = 0;	// loop ต่อไปนี้นับใหม่หมดทุกวันแล้ว ไม่จำเป็นต้องใช้ ABSENT_START_DAY อีก
			$TMP_ENDDATE = date("Y-m-d", mktime(0, 0, 0, $END_MONTH, ($END_DAY + 1), $END_YEAR));
			$arr_temp = explode("-", $TMP_ENDDATE);
			$END_DAY1 = $arr_temp[2];
			$END_MONTH1 = $arr_temp[1];
			$END_YEAR1 = $arr_temp[0];
//			echo "bf loop day....$START_YEAR-$START_MONTH-$START_DAY==$END_YEAR1-$END_MONTH1-$END_DAY1...count=$ABSENT_DAY..";
			while($START_YEAR!=$END_YEAR1 || $START_MONTH!=$END_MONTH1 || $START_DAY!=$END_DAY1){
				$DAY_NUM = date("w", mktime(0, 0, 0, $START_MONTH, $START_DAY, $START_YEAR));
	
				if($AB_COUNT == 2){		//ไม่นับวันหยุดเท่านั้น***
					if($DPISDB=="odbc") 
						$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
					elseif($DPISDB=="oci8") 
						$cmd = " select HOL_NAME from PER_HOLIDAY where SUBSTR(HOL_DATE, 1, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
					elseif($DPISDB=="mysql")
						$cmd = " select HOL_NAME from PER_HOLIDAY where LEFT(HOL_DATE, 10)='$START_YEAR-$START_MONTH-$START_DAY' ";
					$IS_HOLIDAY = $db_dpis3->send_cmd($cmd);
	
					if($DAY_NUM > 0 && $DAY_NUM < 6 && !$IS_HOLIDAY) $ABSENT_DAY++;
//					echo "loop cnt day.AB_COUNT=2...$START_YEAR-$START_MONTH-$START_DAY==$END_YEAR1-$END_MONTH1-$END_DAY1...count=$ABSENT_DAY..";
				}elseif($AB_COUNT == 1){	//นับ
					$ABSENT_DAY++;
//					echo "loop cnt day.AB_COUNT=1...$START_YEAR-$START_MONTH-$START_DAY==$END_YEAR1-$END_MONTH1-$END_DAY1...count=$ABSENT_DAY..";
				} // end if
	
				$TMP_STARTDATE = date("Y-m-d", mktime(0, 0, 0, $START_MONTH, ($START_DAY + 1), $START_YEAR));
				$arr_temp = explode("-", $TMP_STARTDATE);
				$START_DAY = $arr_temp[2];
				$START_MONTH = $arr_temp[1];
				$START_YEAR = $arr_temp[0];
			} // end while
	
			// สรุปรวมวันสุดท้าย //
			$ABSENT_DAY = ($ABSENT_DAY + $ABSENT_START_DAY);		
	
			// หักครึ่งวัน	// PERIOD 1 = ครึ่งเช้า / 2 = ครึ่งบ่าย / 3 = ทั้งวัน	//ถ้าไม่นับวันหยุด ให้ check ว่าวันที่เริ่มต้น / สิ้นสุดที่ลาครึ่งวัน(เช้า/บ่าย)นั้น เป็นวันหยุดราชการ/ เสาร์-อาทิตย์? ถ้าเป็นไม่ต้องเอามาคิด
			$ABSENT_DAY = ($ABSENT_DAY -  ($ABSENT_2_STARTPERIOD + $ABSENT_2_ENDPERIOD));
//			echo "ABSENT_DAY=$ABSENT_DAY<br>";
//		} // end if
		return $ABSENT_DAY;
	} // end function get_day()

?>
