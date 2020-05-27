<?
	//*********************************************************************************
	// compute_formula function เพื่อใช้คำนวนหาค่าจากสูตร
	//*********************************************************************************
	function compute_formula($formula, $para) {
		// explode ( )
		$c = strpos($formula,")"); // หากลุ่มแรก
		if ($c!==false) {
			$c1 = strrpos($formula,"(", -(strlen($formula)-$c));
			if ($c1!==false) {
//				echo "c=$c, c1=$c1<br>";
				$buff = trim(substr($formula,$c1+1,$c-$c1-1));
				if (strlen($buff) > 0) {
					$c_result = sub_compute_formula($buff, $para);
//					echo "$buff=$c_result<br>";
					$buff1 = substr($formula,0,$c1).$c_result.substr($formula,$c+1);
//					echo "******* $buff1<br>";
					$retval = compute_formula($buff1, $para);
				}
			} else {
//				echo "*Error*<br>";
				$retval = "*Error*";
			}
		} else {
			$c_result = sub_compute_formula($formula, $para);
//			echo "no group : $formula=$c_result<br>"; stripslashes($c_result);
			$retval = stripslashes(str_replace("'", '', str_replace("\"", '', $c_result)));
		}
		
		return $retval;
	}

	//*********************************************************************************
	// sub_compute_formula function ย่อย ส่วนที่จัดการในแต่ละกลุ่ม
	//*********************************************************************************
	function sub_compute_formula($sub_form, $para) {
		$thisform = $sub_form;
		$c = strpos($thisform,"?");
		if ($c!==false) {
			$c1 = strpos($thisform,"?",$c+1);
			if ($c1!==false) {
				$c2 = strpos($thisform,":",$c+1);
				if ($c1 < $c2) { // exp ที่มี ? อยู่ช่วงหน้า  ? อยู่ก่อน : หา : ถัดไป เพื่อตัดประโยค
					$c3 = strpos($thisform,":",$c2+1); // หา : เพื่อตัดประโยค
					$exp1 = substr($thisform,0,$c);
					$exp2= substr($thisform,$c+1,$c3-$c-1);
					$exp3 = substr($thisform,$c3+1);
//					echo "1- $exp1 : $exp2 : $exp3<br>";
					$sub_result1 = sub_compute_formula($exp2, $para);
					$sub_result2 = sub_compute_formula($exp3, $para);
					$c_result = (compute_exp($exp1, $para)?compute_exp($sub_result1, $para):compute_exp($sub_result2, $para));
				} else { // exp ที่มี ? อยู่ช่วงหลัง  : อยู่ก่อน ? ใช้ : ตัวนี้เพื่อตัดประโยค
					$exp1 = substr($thisform,0,$c);
					$exp2= substr($thisform,$c+1,$c2-$c-1);
					$exp3 = substr($thisform,$c2+1);
//					echo "2- $exp1 : $exp2 : $exp3<br>";
					$sub_result1 = sub_compute_formula($exp2, $para);
					$sub_result2 = sub_compute_formula($exp3, $para);
					$c_result = (compute_exp($exp1, $para)?compute_exp($sub_result1, $para):compute_exp($sub_result2, $para));
				}
			} else { // else if ($c1===false)
				$c1 = strpos($thisform,":",$c+1);
				$exp1 = substr($thisform,0,$c);
				$exp2= substr($thisform,$c+1,$c1-$c-1);
				$exp3 = substr($thisform,$c1+1);
//				echo "3- $exp1 : $exp2 : $exp3<br>";
				$c_result = (compute_exp($exp1, $para)?compute_exp($exp2, $para):compute_exp($exp3, $para));
			}
		} else {
			$c_result = compute_exp($thisform, $para);
		}
		
		return $c_result;
	}

	//*********************************************************************************
	// compute_exp function เพื่อคำนวนในส่วนของแต่ละ operater
	//*********************************************************************************
	function compute_exp($exp, $para) {
		$c = strpos($exp,"*");
		if ($c===false) {
			$c = strpos($exp,"/");
			if ($c===false) {
				$c = strpos($exp,"+");
				if ($c===false) {
					$c = strpos($exp,"-");
					if ($c===false) {
						$c = strpos($exp,"==");
						if ($c===false) {
							$c = strpos($exp,">=");
							if ($c===false) {
								$c = strpos($exp,"<=");
								if ($c===false) {
									$c = strpos($exp,"<");
									if ($c===false) {
										$c = strpos($exp,">");
										if ($c===false) { // =
											$c = strpos($exp,"=");
											if ($c===false) { // อื่น ๆ
												$retval = $exp;
											} else { // =
												$arr_val = explode(",",find_2_val($exp, $c, $para));
												if (check_num($arr_val[0])==false || check_num($arr_val[1])==false ) 
													$retval = ($arr_val[0] == $arr_val[1]);
												else
													$retval = ((real)$arr_val[0] == (real)$arr_val[1]);
												$exp1 = $arr_val[2].$retval.$arr_val[3];
												$retval = compute_exp($exp1, $para);
											}
										} else { // >
											$arr_val = explode(",",find_2_val($exp, $c, $para));
											if (check_num($arr_val[0])==false || check_num($arr_val[1])==false) 
												$retval = ($arr_val[0] > $arr_val[1]);
											else
												$retval = ((real)$arr_val[0] > (real)$arr_val[1]);
											$exp1 = $arr_val[2].$retval.$arr_val[3];
											$retval = compute_exp($exp1, $para);
										} // end if >
									} else { // <
										$arr_val = explode(",",find_2_val($exp, $c, $para));
										if (check_num($arr_val[0])==false || check_num($arr_val[1])==false) 
											$retval = ($arr_val[0] < $arr_val[1]);
										else
											$retval = ((real)$arr_val[0] < (real)$arr_val[1]);
										$exp1 = $arr_val[2].$retval.$arr_val[3];
										$retval = compute_exp($exp1, $para);
									} // end if <
								} else { // <=
									$arr_val = explode(",",find_2_val($exp, $c, $para));
									if (check_num($arr_val[0])==false || check_num($arr_val[1])==false) 
										$retval = ($arr_val[0] <= $arr_val[1]);
									else
										$retval = ((real)$arr_val[0] <= (real)$arr_val[1]);
									$exp1 = $arr_val[2].$retval.$arr_val[3];
									$retval = compute_exp($exp1, $para);
								} // end if <=
							} else { // >=
								$arr_val = explode(",",find_2_val($exp, $c, $para));
								if (check_num($arr_val[0])==false || check_num($arr_val[1])==false) 
									$retval = ($arr_val[0] >= $arr_val[1]);
								else
									$retval = ((real)$arr_val[0] >= (real)$arr_val[1]);
								$exp1 = $arr_val[2].$retval.$arr_val[3];
								$retval = compute_exp($exp1, $para);
							} // end if >=
						} else { // ==
							$arr_val = explode(",",find_2_val($exp, $c, $para));
							if (check_num($arr_val[0])==false || check_num($arr_val[1])==false) 
								$retval = ($arr_val[0] == $arr_val[1]);
							else
								$retval = ((real)$arr_val[0] == (real)$arr_val[1]);
							$exp1 = $arr_val[2].$retval.$arr_val[3];
							$retval = compute_exp($exp1, $para);
						} // end if ==
					} else { // -
						$arr_val = explode(",",find_2_val($exp, $c, $para));
						if (check_num($arr_val[0])==false || check_num($arr_val[1])==false) 
							$retval = ($arr_val[0] - $arr_val[1]);
						else
							$retval = ((real)$arr_val[0] - (real)$arr_val[1]);
						$exp1 = $arr_val[2].$retval.$arr_val[3];
						$retval = compute_exp($exp1, $para);
					} // end if -
				} else { // +
					$arr_val = explode(",",find_2_val($exp, $c, $para));
//					echo "l_opr:".$arr_val[0]."-".gettype($arr_val[0]).", r_opr:".$arr_val[1]."-".gettype($arr_val[1])." ($a1, $a2)<br>";
					if (check_num($arr_val[0])==false || check_num($arr_val[1])==false) {
						if (substr($arr_val[0],0,2)=="\"" || substr($arr_val[0],0,1)=="'") {
							if (substr($arr_val[0],0,2)=="\"") {
								$arr_val[0] = substr($arr_val[0],2);
							} else {
								$arr_val[0] = substr($arr_val[0],1);
							}
						}
						if (substr($arr_val[0],-2,2)=="\"" || substr($arr_val[0],-1,1)=="'") {
							if (substr($arr_val[0],-2,2)=="\"") {
								$arr_val[0] = substr($arr_val[0],0,strlen($arr_val[0])-2);
							} else {
								$arr_val[0] = substr($arr_val[0],0,strlen($arr_val[0])-1);
							}
						}
						if (substr($arr_val[1],0,2)=="\"" || substr($arr_val[1],0,1)=="'") {
							if (substr($arr_val[1],0,2)=="\"") {
								$arr_val[1] = substr($arr_val[1],2);
							} else {
								$arr_val[1] = substr($arr_val[1],1);
							}
						}
						if (substr($arr_val[1],-2,2)=="\"" || substr($arr_val[1],-1,1)=="'") {
							if (substr($arr_val[1],-2,2)=="\"") {
								$arr_val[1] = substr($arr_val[1],0,strlen($arr_val[1])-2);
							} else {
								$arr_val[1] = substr($arr_val[1],0,strlen($arr_val[1])-1);
							}
						}
						$retval = $arr_val[0].$arr_val[1];
					} else
						$retval = ((real)$arr_val[0] + (real)$arr_val[1]);
					$exp1 = $arr_val[2].$retval.$arr_val[3];
					$retval = compute_exp($exp1, $para);
				} // end if +
			} else { // /
				$arr_val = explode(",",find_2_val($exp, $c, $para));
				if (check_num($arr_val[0])==false || check_num($arr_val[1])==false) 
					$retval = ($arr_val[0] / $arr_val[1]);
				else
					$retval = ((real)$arr_val[0] / (real)$arr_val[1]);
				$exp1 = $arr_val[2].$retval.$arr_val[3];
				$retval = compute_exp($exp1, $para);
			} // end if /
		} else { // *
			$arr_val = explode(",",find_2_val($exp, $c, $para));
			if (check_num($arr_val[0])==false || check_num($arr_val[1])==false) 
				$retval = ($arr_val[0] * $arr_val[1]);
			else
				$retval = ((real)$arr_val[0] * (real)$arr_val[1]);
			$exp1 = $arr_val[2].$retval.$arr_val[3];
			$retval = compute_exp($exp1, $para);
		} // end if *
//		echo "=$retval<br>";
		
		return $retval;
	}

	//*********************************************************************************
	// find_2_val function สำหรับการหาค่า 2 ค่า ซ้าย ขวา ของ operater
	//*********************************************************************************
	function find_2_val($exp, $c, $para) {
		$chk_op = substr($exp,$c,2);
		if ($chk_op==">=" || $chk_op=="<=" || $chk_op=="==" ) $op2=1; else $op2=0;
		$l_op_pos = find_next_op_pos($exp, $c, -1);
		$r_op_pos = find_next_op_pos($exp, $c+$op2, 1);
		$l_val = substr($exp, $l_op_pos+1, $c-$l_op_pos-1);
		$r_val = substr($exp, $c+1+$op2, $r_op_pos-$c-1+$op2);
		if ($l_op_pos > -1) $l_rest = substr($exp, 0, $l_op_pos); else $l_rest = "";
		$r_rest = substr($exp, $r_op_pos);
		if ($c > 0)  $op = substr($exp, $c, 1+$op2); else $op = "";
//		echo "$l_val [$op] $r_val [$exp]($l_rest,$r_rest)($l_op_pos, $r_op_pos)<br>";
		if (substr($l_val, 0, 1)=="@") {
//			$lval=$para[substr($l_val, 1)];
			$lval=$para[$l_val];
		} else {
			$lval=$l_val;
		}
		if (substr($r_val, 0, 1)=="@") {
//			$rval=$para[substr($r_val, 1)];
			$rval=$para[$r_val];
		} else {
			$rval=$r_val;
		}
//		echo "$l_val:$lval,$r_val:$rval,$l_rest,$r_rest<br>";
		
		return "$lval,$rval,$l_rest,$r_rest";
	}

	//*********************************************************************************
	// formula2sqlselect function แปลง สูตรให้เป็น sql
	//*********************************************************************************
	function formula2sqlselect($formula) {
		$arr_column = (array) null;
		// explode ( )
		$c = strpos($formula,")"); // หากลุ่มแรก
		if ($c!==false) {
			$c1 = strrpos($formula,"(", -(strlen($formula)-$c));
			if ($c1!==false) {
//				echo "c=$c, c1=$c1<br>";
				$buff = trim(substr($formula,$c1+1,$c-$c1-1));
				if (strlen($buff) > 0) {
					$c_result = sub_formula2sqlselect($buff, $arr_column);
					if ($c_result!="*Error*") {
						$buff1 = substr($formula,0,$c1).$c_result.substr($formula,$c+1);
						$retval = formula2sqlselect($buff1, $arr_column);
					} else {
						$retval = "*Error*";
					}
				}
			} else {
//				echo "*Error*<br>";
				$retval = "*Error*";
			}
		} else {
			$c_result = sub_formula2sqlselect($formula, $arr_column);
//			echo "no group : $formula=$c_result<br>";
			$retval = $c_result;
		}

		if ($retval=="*Error*")
			return false;
		else
			return $arr_column;
	}

	//*********************************************************************************
	// sub_formula2sqlselect function ย่อยเป็นกลุ่ม ๆ
	//*********************************************************************************
	function sub_formula2sqlselect($sub_form, &$arr_column) {
		$thisform = $sub_form;
		$c = strpos($thisform,"?");
		if ($c!==false) {
			$c1 = strpos($thisform,"?",$c+1);
			if ($c1!==false) {
				$c2 = strpos($thisform,":",$c+1);
				if ($c2===false) {
					$c_result="*Error*";
				} else if ($c1 < $c2) { // exp ที่มี ? อยู่ช่วงหน้า  ? อยู่ก่อน : หา : ถัดไป เพื่อตัดประโยค
					$c3 = strpos($thisform,":",$c2+1); // หา : เพื่อตัดประโยค
					if ($c3===false) {
						$c_result="*Error*";
					} else {
						$exp1 = substr($thisform,0,$c);
						$exp2= substr($thisform,$c+1,$c3-$c-1);
						$exp3 = substr($thisform,$c3+1);
//						echo "1- $exp1 : $exp2 : $exp3<br>";
						$sub_result1 = sub_formula2sqlselect($exp2, $arr_column);
						$sub_result2 = sub_formula2sqlselect($exp3, $arr_column);
						$c_result = (sub_formula2sqlselect_exp($exp1, $arr_column)?sub_formula2sqlselect_exp($sub_result1, $arr_column):sub_formula2sqlselect_exp($sub_result2, $arr_column));
					}
				} else { // exp ที่มี ? อยู่ช่วงหลัง  : อยู่ก่อน ? ใช้ : ตัวนี้เพื่อตัดประโยค
					$exp1 = substr($thisform,0,$c);
					$exp2= substr($thisform,$c+1,$c2-$c-1);
					$exp3 = substr($thisform,$c2+1);
//					echo "2- $exp1 : $exp2 : $exp3<br>";
					$sub_result1 = sub_formula2sqlselect($exp2, $arr_column);
					$sub_result2 = sub_formula2sqlselect($exp3, $arr_column);
					$c_result = (sub_formula2sqlselect_exp($exp1, $arr_column)?sub_formula2sqlselect_exp($sub_result1, $arr_column):sub_formula2sqlselect_exp($sub_result2, $arr_column));
				}
			} else { // else if ($c1===false)
				$c1 = strpos($thisform,":",$c+1);
				if ($c1===false) {
					$c_result="*Error*";
				} else {
					$exp1 = substr($thisform,0,$c);
					$exp2= substr($thisform,$c+1,$c1-$c-1);
					$exp3 = substr($thisform,$c1+1);
//					echo "3- $exp1 : $exp2 : $exp3<br>";
					$c_result = (sub_formula2sqlselect_exp($exp1, $arr_column)?sub_formula2sqlselect_exp($exp2, $arr_column):sub_formula2sqlselect_exp($exp3, $arr_column));
				}
			}
		} else {
			$c_result = sub_formula2sqlselect_exp($thisform, $arr_column);
		}
		
		return $c_result;
	}

	//*********************************************************************************
	// sub_formula2sqlselect_exp function ย่อยในแต่ละ expression
	//*********************************************************************************
	function sub_formula2sqlselect_exp($exp, &$arr_column) {
		$arr_op = array("*", "/", "+", "-", "==", ">=", "<=", "<", ">", "=");
//		echo "exp=$exp, arr_column=".implode(",",$arr_column)."<br>";
		for($i=0; $i < count($arr_op); $i++) {
			$c = strpos($exp,$arr_op[$i]);
			if ($c!==false) {
//				echo "arr_op[".$i."]:".$arr_op[$i]."<br>";
				break;
			}
		}
		if ($c===false) {
			$retval = $exp;
		} else {
			$arr_val = explode(",",find_2_column($exp, $c, $arr_column));
			$exp1 = $arr_val[2].$arr_val[3];
			$retval = sub_formula2sqlselect_exp($exp1, $arr_column);
		} // end if ($c===false)
//		echo "retval=$retval<br>";
		
		return $retval;
	}
	
	//*********************************************************************************
	// find_2_column function หา 2 column ซ้าย ขวา ของ operater
	//*********************************************************************************
	function find_2_column($exp, $c, &$arr_column) {
		$chk_op = substr($exp,$c,2);
		if ($chk_op==">=" || $chk_op=="<=" || $chk_op=="==" ) $op2=1; else $op2=0;
		$l_op_pos = find_next_op_pos($exp, $c, -1);
		$r_op_pos = find_next_op_pos($exp, $c+$op2, 1);
		$l_val = substr($exp, $l_op_pos+1, $c-$l_op_pos-1);
		$r_val = substr($exp, $c+1+$op2, $r_op_pos-$c-1+$op2);
		if ($l_op_pos > -1) $l_rest = substr($exp, 0, $l_op_pos); else $l_rest = "";
		$r_rest = substr($exp, $r_op_pos);
		if ($c > 0)  $op = substr($exp, $c, 1+$op2); else $op = "";
		echo "$l_val [$op] $r_val [$exp]($l_rest,$r_rest)($l_op_pos, $r_op_pos)<br>";
		$l_val = trim($l_val);
		$buff = explode(".",$l_val);
		if (strlen($buff[0]) > 0 && strlen($buff[1]) > 0) { // เป็นตัวแปรใน database
			$arr_column[db][$buff[0]][] = $l_val."|".$op;
//			echo "1.>>".$buff[0].":".$buff[1]."<br>";
		} else {
			$var_f = substr($l_val,0,1);
			if ($var_f=="$") { // เป็นตัวแปร variable
				$var_name = substr($l_val,1);
				$arr_column[va][] = $var_name."|".$op;
			}
		}
		$r_val = trim($r_val);
		$buff = explode(".",$r_val);
		if (strlen($buff[0]) > 0 && strlen($buff[1]) > 0) {
			$arr_column[db][$buff[0]][] = $r_val."|".$op;
//			echo "2.>>".$buff[0].":".$buff[1]."<br>";
		} else {
			$var_f = substr($r_val,0,1);
			if ($var_f=="$") { // เป็นตัวแปร variable
				$var_name = substr($r_val,1);
				$arr_column[va][$var_name][] = $var_name."|".$op;
			}
		}
		echo "$l_val,$r_val,$l_rest,$r_rest<br>";

		return "$l_val,$r_val,$l_rest,$r_rest,$op";
	}
	
	//*********************************************************************************
	// select_build function สร้างตัวเลือกใน sql statment
	//*********************************************************************************
	function select_build($arr_tab) {
		$tab = "";
		$col = "";
		$i=0;
		$map_col = (array) null;
		foreach($arr_tab[db] as $key => $value) {
//			echo "key:$key => value:$value<br>";
//			if ($tab) $tab .= ",$key^@$i_str"; else $tab = "$key^@$i_str";
			if ($tab) $tab .= ", $key"; else $tab = "$key";
			foreach($value as $key1 => $value1) {
				$buff = explode("|",$value1);
//				echo "----------> key1:$key1 => value1:$value1<br>";
//				if ($col) $col .= ", @$i_str.".$buff[0]; else $col = "@$i_str.".$buff[0];
				if ($col) $col .= ", ".$buff[0]; else $col = "".$buff[0];
				$i++;
				$i_str = ($i < 10 ? "0".$i : $i);
				$map_col[] = $buff[0]."^@$i_str";
			}
		}

		return $tab."|".$col."|".implode(",",$map_col);
	}

	//*********************************************************************************
	// sql_build function สร้าง sql statment
	//*********************************************************************************
	function sql_build($var_link, $select_tab) {
		$buff = explode("|",$select_tab);
		$sql="select ".$buff[1]." from ";
		$select_part = $buff[1];
		if ($var_link) {
			$arr1 = explode("|",$var_link);
    		for($i=0; $i < count($arr1); $i++) {
    			$arr2 = explode("@",$arr1[$i]);
   	        	$tab_name1[] = $arr2[0];
   	        	$tab_name2[] = $arr2[1];
               	$tab_main[] = $arr2[2];
   	        	$tab_jointype[] = $arr2[3];
       	    	$tab_columnjoin[] = $arr2[4];
    		}
			$sql1 = "";
    		for($i=0; $i < count($tab_name1); $i++) {
				if (strlen($sql1) > 0) {
					if (strpos($sql1, $tab_name1[$i])===false) {
						$err1="Link not founded....";
					} else {
						$sql1 .= ($tab_jointype[$i]==1 ? " left join " : " join ").$tab_name2[$i]." on (";
						$link_explode = explode(",",$tab_columnjoin[$i]);
						$str_link = explode(" and ",$link_explode);
						$sql1 .= $str_link.")";
					}
				} else {
					$sql1 = $tab_name1[$i];
					$sql1 .= ($tab_jointype[$i]==1 ? " left join " : " join ").$tab_name2[$i]." on (";
					$link_explode = explode(",",$tab_columnjoin[$i]);
					$str_link = implode(" and ",$link_explode);
					$sql1 .= $str_link.")";
				}
    		}			
		}
		$link_part = $sql1;
		
//		return $sql.$sql1;
		return $select_part."|".$link_part;
	}

	//*********************************************************************************
	// map_formula_var function สำหรับ map  ตัวแปรในสูตร
	//*********************************************************************************
	function map_formula_var($mapstr, $formula) {
		$buff = explode("|",$mapstr);
		$arr_map = explode(",",$buff[2]);
		for($i=0; $i < count($arr_map); $i++) {
			$buff1 = explode("^",$arr_map[$i]);
			$formula = str_replace($buff1[0], $buff1[1], $formula);
		}
		
		return $formula;
	}

	$cnt_grp_exp = 0;
	$arr_grp_exp = (array) null;
	$new_formula = "";
	$arr_exp = (array) null;
	$arr_var = (array) null;
	$arr_op = (array) null;
	//*********************************************************************************
	// formula2stack function แปลง สูตรให้เป็น stack array
	//*********************************************************************************
	function formula2stack($formula) {
		global $cnt_grp_exp, $arr_grp_exp, $new_formula;
		
		echo "formula=$formula<br>";
		$new_formula = $formula;
		if (grp2stack()) {
			$ret = implode(",", $arr_grp_exp)."|".$new_formula;
			echo ">>$ret<br>";
		} else {
			echo "*** Error in Formula ***<br>";
		}
		
		return $ret;
	}
	
	//*********************************************************************************
	// grp2stack function แปลง สูตรให้เป็น stack array
	//*********************************************************************************
	function grp2stack() {
		global $cnt_grp_exp, $arr_grp_exp, $new_formula;

		// explode ( )
		$c = strpos($new_formula,")"); // หากลุ่มแรก
		if ($c!==false) {
			$c1 = strrpos($new_formula,"(", -(strlen($new_formula)-$c));
			if ($c1!==false) {
//				echo "c=$c, c1=$c1<br>";
				$buff = trim(substr($new_formula,$c1+1,$c-$c1-1));
				if (strlen($buff) > 0) {
					$new_exp_result_name = "_$cnt_grp_exp";
					$arr_grp_exp[$new_exp_result_name] = $buff;
					$cnt_grp_exp++;
					$buff1 = substr($new_formula,0,$c1);
					$buff2 = substr($new_formula,$c+1);
					$new_formula = $buff1.$new_exp_result_name.$buff2;
					$retval = grp2stack();
				} else {
//					echo "*Error*<br>";
					$retval = false;
				}
			} else {
//				echo "*Error*<br>";
				$retval = false;
			}
		} else {
			$retval = true;
		}

		return $retval;
	}

	//*********************************************************************************
	// do_formula_stack function  ประมวลผล formula stack array;
	//*********************************************************************************
	function do_formula_stack($formulaStack) {
		$exp = explode("|", $formulaStack);
		$stack = explode(",", $exp[0]);
		$formula = $exp[1];
		for($i=0; $i < count($formula); $i++) {
			$chrchk = substr();
		}
	}
	
	//*********************************************************************************
	// sub_formula_stack function  ประมวลผล formula stack array;
	//*********************************************************************************
	function sub_formula_stack($exp) {
		
		$c = strpos($exp,"?");
		if ($c!==false) {
			$c1 = strpos($exp,"?",$c+1);
			if ($c1!==false) {
				$c2 = strpos($exp,":",$c+1);
				if ($c2===false) {
					$c_result="*Error*";
				} else if ($c1 < $c2) { // exp ที่มี ? อยู่ช่วงหน้า  ? อยู่ก่อน : หา : ถัดไป เพื่อตัดประโยค
					$c3 = strpos($exp,":",$c2+1); // หา : เพื่อตัดประโยค
					if ($c3===false) {
						$c_result="*Error*";
					} else {
						$exp1 = substr($exp,0,$c);
						$exp2= substr($exp,$c+1,$c3-$c-1);
						$endpos = find_next_op_pos($exp, $c+1, 1);
						if ($endpos)
							$exp3 = substr($exp,$c3+1,$endpos-$c3-1);
						else
							$exp3 = substr($exp,$c3+1,$endpos-$c3-1);
//						echo "1- $exp1 : $exp2 : $exp3<br>";
						$sub_result1 = sub_formula_stack($exp2);
						$sub_result2 = sub_formula_stack($exp3);
						$c_result = (sub_formula_stack_exp($exp1)?sub_formula_stack_exp($sub_result1):sub_formula_stack_exp($sub_result2));
					}
				} else { // exp ที่มี ? อยู่ช่วงหลัง  : อยู่ก่อน ? ใช้ : ตัวนี้เพื่อตัดประโยค
					$exp1 = substr($exp,0,$c);
					$exp2= substr($exp,$c+1,$c2-$c-1);
					$exp3 = substr($exp,$c2+1);
//					echo "2- $exp1 : $exp2 : $exp3<br>";
					$sub_result1 = sub_formula_stack($exp2);
					$sub_result2 = sub_formula_stack($exp3);
					$c_result = (sub_formula_stack_exp($exp1)?sub_formula_stack_exp($sub_result1):sub_formula_stack_exp($sub_result2));
				}
			} else { // else if ($c1===false)
				$c1 = strpos($exp,":",$c+1);
				if ($c1===false) {
					$c_result="*Error*";
				} else {
					$exp1 = substr($exp,0,$c);
					$exp2= substr($exp,$c+1,$c1-$c-1);
					$exp3 = substr($exp,$c1+1);
//					echo "3- $exp1 : $exp2 : $exp3<br>";
					$c_result = (sub_formula_stack_exp($exp1)?sub_formula_stack_exp($exp2):sub_formula_stack_exp($exp3));
				}
			}
		} else {
			$c_result = sub_formula_stack_exp($exp);
		}
		
		return $c_result;
	}

	$varcnt = 0;
//	$arr_var = (array) null;
	//*********************************************************************************
	// sub_formula_stack_exp function ย่อยในแต่ละ expression
	//*********************************************************************************
	function sub_formula_stack_exp($exp) {
		global $varcnt;//, $arr_var;
		
		$arr_op = array("*", "/", "+", "-", "==", ">=", "<=", "<", ">", "=", "and", "&&", "or", "||", "?");
//		echo "exp=$exp, arr_column=".implode(",",$arr_column)."<br>";
		$founded = false;
		for($i=0; $i < count($arr_op); $i++) {
			$oppos=0;
			$oplen = 1;
			while($oppos !== false) {
				$done = false;
				$oppos = strpos(strtolower($exp),$arr_op[$i], $oppos);
				if ($oppos!==false) {
					$done = true;
//					echo "arr_op[".$i."]:".$arr_op[$i]."<br>";
					if ($arr_op[$i]=="and" || $arr_op[$i]=="or") {
						if ($arr_op[$i]=="and") $oplen = 3; else $oplen = 2;
						if ($c-1 >= 0 && $oppos+$oplen < strlen($exp)) // กรณี and $oppos+3 กรณี or $oppos+2
							if (substr($exp,$oppos-1,1) != " " || substr($exp,$oppos+$oplen,1) != " ") {
								// ถ้า ก่อน/หลัง and/or ฝั่งใดฝั่งหนึ่ง ไม่เท่ากับ " "
								$done = false; // ถือว่าไม่ใช่ and/or หาตัวถัดไป
							}
//							else // ทั้งหน้า/หลัง เป็น " " ถือว่าเป็น and/or $oppos จะเท่ากับ true อยู่แล้ว
						else 
							if ($oppos-1 >= 0 && substr($exp,$oppos+$oplen,1) != " ") {
							// ถ้าก่อน อยู่ที่เริ่ม และ หลังไม่เป็น " " ก็ไม่ใช่ and/or
								$done = false; // ถือว่าไม่ใช่ and/or หาตัวถัดไป
							} else 
								if (substr($exp,$oppos-1,1) != " " && $oppos+$oplen < strlen($exp)) {
									// ถ้าก่อน ไม่เป็น " " และ หลัง อยู่สุด string ก็ไม่ใช่ and/or
									$done = false; // ถือว่าไม่ใช่ and/or หาตัวถัดไป
								}
					} else if ($arr_op[$i]=="and" || $arr_op[$i]=="or") {
						$oplen=2;
					} else if ($arr_op[$i]=="?") {
						
					};
					if ($done) {
						$expname = "!".$varcnt;
						$arr_val = explode(",",find_2_opcode($exp, $oppos, $oplen));
						$exp1 = $arr_val[2].$expname.$arr_val[3];
						$arr_exp[$expname] = $arr_val[0].",".$arr_val[4].",".$arr_val[1];
						$arr_exp[] = sub_formula_stack_exp($exp1);
						$founded = true;
					}
					$oppos++; // หาตัวถัดไป
				} // end if ($oppos!==false)
			} // end while
		} // end for
		if ($founded===false) {
			echo "no op exp=$exp<br>";
			$arr_exp[] = $exp;
		} // end if ($c===false)
		$ret = implode("|", $arr_exp);
		echo "retval=$retval<br>";
		
		return  $ret;
	}
	
	//*********************************************************************************
	// find_2_opcode function หา 2 opcode ซ้าย ขวา ของ operater
	//*********************************************************************************
	function find_2_opcode($exp, $c, $len) {

		$chk_op = substr(strtolower($exp),$c,3);
		if ($chk_op=="and") 
			$op2=2; 
		else {
			$chk_op = substr(strtolower($exp),$c,2);
			if ($chk_op==">=" || $chk_op=="<=" || $chk_op=="==" || $chk_op=="&&" || $chk_op=="||" || $chk_op=="or") 
				$op2=1; 
			else $op2=0;
		}
		$l_op_pos = find_next_op_pos($exp, $c, -1);
		$r_op_pos = find_next_op_pos($exp, $c+$op2, 1);
		$l_val = trim(substr($exp, $l_op_pos+1, $c-$l_op_pos-1));
		$r_val = trim(substr($exp, $c+1+$op2, $r_op_pos-$c-1+$op2));
		if ($l_op_pos > -1) $l_rest = trim(substr($exp, 0, $l_op_pos)); else $l_rest = "";
		$r_rest = trim(substr($exp, $r_op_pos));
		if ($c > 0)  $op = trim(substr($exp, $c, 1+$op2)); else $op = "";
		echo "$l_val [$op] $r_val [$exp]($l_rest,$r_rest)($l_op_pos, $r_op_pos)<br>";
//		$buff = explode(".",$l_val);
//		if (strlen($buff[0]) > 0 && strlen($buff[1]) > 0) { // เป็นตัวแปรใน database
//			$arr_column[db][$buff[0]][] = $l_val."|".$op;
//			echo "1.>>".$buff[0].":".$buff[1]."<br>";
//		} else {
//			$var_f = substr($l_val,0,1);
//			if ($var_f=="$") { // เป็นตัวแปร variable
//				$var_name = substr($l_val,1);
//				$arr_column[va][] = $var_name."|".$op;
//			}
//		}
//		$buff = explode(".",$r_val);
//		if (strlen($buff[0]) > 0 && strlen($buff[1]) > 0) {
//			$arr_column[db][$buff[0]][] = $r_val."|".$op;
//			echo "2.>>".$buff[0].":".$buff[1]."<br>";
//		} else {
//			$var_f = substr($r_val,0,1);
//			if ($var_f=="$") { // เป็นตัวแปร variable
//				$var_name = substr($r_val,1);
//				$arr_column[va][$var_name][] = $var_name."|".$op;
//			}
//		}
//		echo "$l_val,$r_val,$l_rest,$r_rest<br>";

		return "$l_val,$r_val,$l_rest,$r_rest,$op";
	}
	
	//*********************************************************************************
	// check_num function ตรวจสอบตัวแปรว่าเป็นตัวเลขรึเปล่า
	//*********************************************************************************
	function check_num($mystr) {
		$strchk = "0123456789";
		
		$f_ret = true;
		for($i = 0; $i < strlen($mystr); $i++) {
			if (strpos($strchk,substr($mystr,$i,1))===false)
				$f_ret = false;
		}
		
		return $f_ret;
	}

	//*********************************************************************************
	// find_next_op_pos function สำหรับหา operater ตัวถัดไปใน กลุ่ม expression หนึ่ง ๆ
	//*********************************************************************************
	function find_next_op_pos($exp, $c, $f) {
		$retpos=-1;
		$exp1 = strtolower($exp);
		if ($f==-1) { // left
			for($i=$c-1; $i >= 0; $i--) {
				$c = strpos("+-*/><=oa|&",substr($exp1,$i,1));
				if ($c !== false) {
					if ($c > 6) {
						if (substr($exp1,$i,4)=="and " || substr($exp1,$i,3)=="or " || substr($exp1,$i,2)=="&&" || substr($exp1,$i,2)=="||")
							$retpos = $i;
					} else {
							$retpos = $i;
					}
					break;
				}
			}
		} else { // right
			for($i=$c+1; $i < strlen($exp); $i++) {
				$c = strpos("+-*/><=rd|&",substr($exp1,$i,1));
				if ($c !== false) {
					if ($c > 6) {
						if (substr($exp1,$i-3,4)==" and" || substr($exp1,$i-2,3)==" or" || substr($exp1,$i-1,2)=="&&" || substr($exp1,$i-1,2)=="||")
							$retpos = $i;
					} else {
							$retpos = $i;
					}
					break;
				}
			}
		}
		
		return $retpos;
	}

	//*********************************************************************************
	// find_next_op function สำหรับหา operater ตัวถัดไปใน กลุ่ม expression หนึ่ง ๆ
	//*********************************************************************************
	function find_next_op($exp, $c, $f) {
		$retop=-1;
		$exp1 = strtolower($exp);
		if ($f==-1) { // left
			for($i=$c-1; $i >= 0; $i--) {
				$c = strpos("+-*/><=rd|&",substr($exp1,$i,1));
				if ($c !== false) {
					if ($c > 6) {
						if (substr($exp1,$i-3,4)==" and" || substr($exp1,$i-2,3)==" or" || substr($exp1,$i-1,2)=="&&" || substr($exp1,$i-1,2)=="||")
							$pos = ($c==7?($i-1):($c==8?($i-2):($c==9?($i-1):($i-1))));
							$retop = $pos.",".($c==7?"or":($c==8?"and":($c==9?"||":"&&"))).",".substr($exp,0,$pos);
					} else {
							$retop = $i.",".substr($exp,$i,1).",".substr($exp,0,$i);
					}
					break;
				}
			}
//			if ($retpos==-1) $retpos = -1;
		} else { // right
			for($i=$c+1; $i < strlen($exp); $i++) {
				$c = strpos("+-*/><=oa|&",substr($exp1,$i,1));
				if ($c !== false) {
					if ($c > 6) {
						if (substr($exp1,$i,4)=="and " || substr($exp1,$i,3)=="or " || substr($exp1,$i,2)=="&&" || substr($exp1,$i,2)=="||")
							$restlen = ($c==7?($i+3):($c==8?($i+4):($c==9?($i+2):($i+2))));
							$retop = $i.",".($c==7?"or":($c==8?"and":($c==9?"||":"&&"))).",".substr($exp,$restlen);
					} else {
							$retop = $i.",".substr($exp,$i,1).",".substr($exp,$i+1);
					}
					break;
				}
			}
		}
		
		return $retop;
	}
	
	function op_compare($op1, $op2) {
		$arr_op_seq = array('*' => 9, '/' => 8, '+' => 7, '-' => 6, 'and' => 5, '&&' => 5, 'or' => 4, '||' => 4);
		
		if ($arr_op_seq[$op1] >= $arr_op_seq[$op2]) return 0; else return 1;
	}
?>