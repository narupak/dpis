<?
	//*********************************************************************************
	// compute_formula function เพื่อใช้คำนวนหาค่าจากสูตร
	//*********************************************************************************
	function compute_formula($formula, $para) {
//		echo "compute_formula:$formula:".implode("|",$para)."<br>";
		$c = strpos($formula,")"); // หากลุ่มแรก
		if ($c!==false) {
			$c1 = strrpos($formula,"(", -(strlen($formula)-$c));
			if ($c1!==false) {
//				echo "c=$c, c1=$c1<br>";
				$buff = substr($formula,$c1+1,$c-$c1-1);
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
//			echo "no group : $formula=[$c_result]<br>"; stripslashes($c_result);
			$retval = stripslashes(str_replace("'", '', str_replace("\"", '', $c_result)));
		}
//		echo "retval=$retval<br>";
		return $retval;
	}

	//*********************************************************************************
	// sub_compute_formula function ย่อย ส่วนที่จัดการในแต่ละกลุ่ม
	//*********************************************************************************
	function sub_compute_formula($sub_form, $para) {
//		echo "sub_compute_formula:$formula:".implode("|",$para)."<br>";
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
//			echo "4- $c_result<br>";
		}
		
		return $c_result;
	}

	//*********************************************************************************
	// compute_exp function เพื่อคำนวนในส่วนของแต่ละ operater
	//*********************************************************************************
	function compute_exp($exp, $para) {
//		echo "compute_exp:$formula:".implode("|",$para)."<br>";
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
										if ($c===false) { 
											$c = strpos($exp,"=");
											if ($c===false) { 
												$c = strpos(strtolower($exp),"and");
												if ($c===false) { 
													$c = strpos(strtolower($exp),"or");
													if ($c===false) { //  อื่นๆ
														$arr_val = explode(",",find_2_val($exp, $c, $para));
		//												echo "อื่น ๆ = ".implode(",", $arr_val)."<br>";
														$retval = $arr_val[0]; // ถ้าไม่มี op ค่าที่ได้จะอยู่ใน ค่าแรก
													} else { // or
														$arr_val = explode(",",find_2_val($exp, $c, $para));
														if (check_num($arr_val[0])==false || check_num($arr_val[1])==false ) {
															$arr_val[0] = str_replace("'","",$arr_val[0]);
															$arr_val[0] = str_replace("\"","",$arr_val[0]);
															$arr_val[1] = str_replace("'","",$arr_val[1]);
															$arr_val[1] = str_replace("\"","",$arr_val[1]);
															$retval = ($arr_val[0] || $arr_val[1]);
		//													echo "= arr_val=".implode(",",$arr_val)."   retval=$retval<br>";
														} else
															$retval = ((real)$arr_val[0] || (real)$arr_val[1]);
														if ($arr_val[2] || $arr_val[3]) {
															$exp1 = $arr_val[2].$retval.$arr_val[3];
															$retval = compute_exp($exp1, $para);
														}
													}
												} else { // and
													$arr_val = explode(",",find_2_val($exp, $c, $para));
													if (check_num($arr_val[0])==false || check_num($arr_val[1])==false ) {
														$arr_val[0] = str_replace("'","",$arr_val[0]);
														$arr_val[0] = str_replace("\"","",$arr_val[0]);
														$arr_val[1] = str_replace("'","",$arr_val[1]);
														$arr_val[1] = str_replace("\"","",$arr_val[1]);
														$retval = ($arr_val[0] && $arr_val[1]);
	//													echo "= arr_val=".implode(",",$arr_val)."   retval=$retval<br>";
													} else
														$retval = ((real)$arr_val[0] && (real)$arr_val[1]);
													if ($arr_val[2] || $arr_val[3]) {
														$exp1 = $arr_val[2].$retval.$arr_val[3];
														$retval = compute_exp($exp1, $para);
													}
												}
											} else { // =
												$arr_val = explode(",",find_2_val($exp, $c, $para));
												if (check_num($arr_val[0])==false || check_num($arr_val[1])==false ) {
													$arr_val[0] = str_replace("'","",$arr_val[0]);
													$arr_val[0] = str_replace("\"","",$arr_val[0]);
													$arr_val[1] = str_replace("'","",$arr_val[1]);
													$arr_val[1] = str_replace("\"","",$arr_val[1]);
													$retval = ($arr_val[0] == $arr_val[1]);
//													echo "= arr_val=".implode(",",$arr_val)."   retval=$retval<br>";
												} else
													$retval = ((real)$arr_val[0] == (real)$arr_val[1]);
												if ($arr_val[2] || $arr_val[3]) {
													$exp1 = $arr_val[2].$retval.$arr_val[3];
													$retval = compute_exp($exp1, $para);
												}
											}
										} else { // >
											$arr_val = explode(",",find_2_val($exp, $c, $para));
											if (check_num($arr_val[0])==false || check_num($arr_val[1])==false) {
												$arr_val[0] = str_replace("'","",$arr_val[0]);
												$arr_val[0] = str_replace("\"","",$arr_val[0]);
												$arr_val[1] = str_replace("'","",$arr_val[1]);
												$arr_val[1] = str_replace("\"","",$arr_val[1]);
												$retval = ($arr_val[0] > $arr_val[1]);
//												echo "> arr_val=".implode(",",$arr_val)."   retval=$retval<br>";
											} else
												$retval = ((real)$arr_val[0] > (real)$arr_val[1]);
											if ($arr_val[2] || $arr_val[3]) {
												$exp1 = $arr_val[2].$retval.$arr_val[3];
												$retval = compute_exp($exp1, $para);
											}
										} // end if >
									} else { // <
										$arr_val = explode(",",find_2_val($exp, $c, $para));
										if (check_num($arr_val[0])==false || check_num($arr_val[1])==false) {
											$arr_val[0] = str_replace("'","",$arr_val[0]);
											$arr_val[0] = str_replace("\"","",$arr_val[0]);
											$arr_val[1] = str_replace("'","",$arr_val[1]);
											$arr_val[1] = str_replace("\"","",$arr_val[1]);
											$retval = ($arr_val[0] < $arr_val[1]);
//											echo "< arr_val=".implode(",",$arr_val)."   retval=$retval<br>";
										} else
											$retval = ((real)$arr_val[0] < (real)$arr_val[1]);
										if ($arr_val[2] || $arr_val[3]) {
											$exp1 = $arr_val[2].$retval.$arr_val[3];
											$retval = compute_exp($exp1, $para);
										}
									} // end if <
								} else { // <=
									$arr_val = explode(",",find_2_val($exp, $c, $para));
									if (check_num($arr_val[0])==false || check_num($arr_val[1])==false) {
										$arr_val[0] = str_replace("'","",$arr_val[0]);
										$arr_val[0] = str_replace("\"","",$arr_val[0]);
										$arr_val[1] = str_replace("'","",$arr_val[1]);
										$arr_val[1] = str_replace("\"","",$arr_val[1]);
										$retval = ($arr_val[0] <= $arr_val[1]);
//										echo "<= arr_val=".implode(",",$arr_val)."   retval=$retval<br>";
									} else
										$retval = ((real)$arr_val[0] <= (real)$arr_val[1]);
									if ($arr_val[2] || $arr_val[3]) {
										$exp1 = $arr_val[2].$retval.$arr_val[3];
										$retval = compute_exp($exp1, $para);
									}
								} // end if <=
							} else { // >=
								$arr_val = explode(",",find_2_val($exp, $c, $para));
								if (check_num($arr_val[0])==false || check_num($arr_val[1])==false)  {
									$arr_val[0] = str_replace("'","",$arr_val[0]);
									$arr_val[0] = str_replace("\"","",$arr_val[0]);
									$arr_val[1] = str_replace("'","",$arr_val[1]);
									$arr_val[1] = str_replace("\"","",$arr_val[1]);
									$retval = ($arr_val[0] >= $arr_val[1]);
//									echo ">= arr_val=".implode(",",$arr_val)."   retval=$retval<br>";
								} else
									$retval = ((real)$arr_val[0] >= (real)$arr_val[1]);
								if ($arr_val[2] || $arr_val[3]) {
									$exp1 = $arr_val[2].$retval.$arr_val[3];
									$retval = compute_exp($exp1, $para);
								}
							} // end if >=
						} else { // ==
							$arr_val = explode(",",find_2_val($exp, $c, $para));
							if (check_num($arr_val[0])==false || check_num($arr_val[1])==false)  {
								$arr_val[0] = str_replace("'","",$arr_val[0]);
								$arr_val[0] = str_replace("\"","",$arr_val[0]);
								$arr_val[1] = str_replace("'","",$arr_val[1]);
								$arr_val[1] = str_replace("\"","",$arr_val[1]);
								$retval = ($arr_val[0] == $arr_val[1]);
//								echo "== arr_val=".implode(",",$arr_val)."   retval=$retval<br>";
							}else
								$retval = ((real)$arr_val[0] == (real)$arr_val[1]);
							if ($arr_val[2] || $arr_val[3]) {
								$exp1 = $arr_val[2].$retval.$arr_val[3];
								$retval = compute_exp($exp1, $para);
							}
						} // end if ==
					} else { // -
						$arr_val = explode(",",find_2_val($exp, $c, $para));
						if (check_num($arr_val[0])==false || check_num($arr_val[1])==false) 
							$retval = $arr_val[0]." - ".$arr_val[1];	// ออกเป็นค่า string
						else
							$retval = ((real)$arr_val[0] - (real)$arr_val[1]);
						if ($arr_val[2] || $arr_val[3]) {
							$exp1 = $arr_val[2].$retval.$arr_val[3];
							$retval = compute_exp($exp1, $para);
						}
					} // end if -
				} else { // +
					$arr_val = explode(",",find_2_val($exp, $c, $para));
//					echo "++++++++++l_opr:".$arr_val[0]."-".gettype($arr_val[0])."(".check_num($arr_val[0])."), r_opr:".$arr_val[1]."-".gettype($arr_val[1])."(".check_num($arr_val[1]).") ($a1, $a2)<br>";
					if (check_num($arr_val[0])==false || check_num($arr_val[1])==false) {
						$arr_val[0] = str_replace("\"","",$arr_val[0]);
						$arr_val[0] = str_replace("'","",$arr_val[0]);
						$arr_val[1] = str_replace("\"","",$arr_val[1]);
						$arr_val[1] = str_replace("'","",$arr_val[1]);
						$retval = $arr_val[0].$arr_val[1];
//						echo "retval=[$retval]<br>";
					} else
						$retval = ((real)$arr_val[0] + (real)$arr_val[1]);
//					echo "arr_val 0-".$arr_val[0]." 1-".$arr_val[1]." 2-".$arr_val[2]." 3-".$arr_val[3]."<br>";
					if ($arr_val[2] || $arr_val[3]) {
						$exp1 = $arr_val[2].$retval.$arr_val[3];
//						echo "++++++++ exp1=$exp1<br>";
						$retval = compute_exp($exp1, $para);
//						echo "++++++++ retval=$retval<br>";
					}
				} // end if +
			} else { // /
				$arr_val = explode(",",find_2_val($exp, $c, $para));
				if (check_num($arr_val[0])==false || check_num($arr_val[1])==false) 
					$retval = $arr_val[0]." / ".$arr_val[1];
				else
					$retval = number_format(((real)$arr_val[0] / (real)$arr_val[1]),9);
				if ($arr_val[2] || $arr_val[3]) {
					$exp1 = $arr_val[2].$retval.$arr_val[3];
					$retval = compute_exp($exp1, $para);
//					echo "///// compute $exp1=$retval<br>";
				}
			} // end if /
		} else { // *
			$arr_val = explode(",",find_2_val($exp, $c, $para));
//			echo "***** arr_val=".implode(",",$arr_val)."<br>";
			if (check_num($arr_val[0])==false || check_num($arr_val[1])==false) 
				$retval = $arr_val[0]." * ".$arr_val[1];
			else
				$retval = ((real)$arr_val[0] * (real)$arr_val[1]);
			if ($arr_val[2] || $arr_val[3]) {
//				echo "1..arr_val 2=".$arr_val[2]," , retval=".$retval." , arr_val 3=".$arr_val[3]."<br>";
				$exp1 = $arr_val[2].$retval.$arr_val[3];
//				echo "exp1=$exp1<br>";
				$retval = compute_exp($exp1, $para);
//				echo "**** compute $exp1=$retval<br>";
			}
		} // end if *
//		echo "compute ($exp)=$retval<br>";
		
		return $retval;
	}

	//*********************************************************************************
	// find_2_val function สำหรับการหาค่า 2 ค่า ซ้าย ขวา ของ operater
	//*********************************************************************************
	function find_2_val($exp, $c, $para) {
//		echo "find_2_val:$formula:".implode("|",$para)."<br>";
//		echo "exp=$exp<br>";
//		echo "find_2_val=====================================op_position=$c<br>";
		if ($c!==false) {
			$chk_op = substr($exp,$c,3);
			if (strtolower($chk_op)=="and") $op2=2;
			else {
				$chk_op = substr($exp,$c,2);
				if ($chk_op==">=" || $chk_op=="<=" || $chk_op=="==" || strtolower($chk_op)=="or" ) $op2=1; else $op2=0;
			}
			$l_op_pos = find_next_op_pos($exp, $c, -1);
			$r_op_pos = find_next_op_pos($exp, $c+$op2, 1);
			$l_val = substr($exp, $l_op_pos+1, $c-$l_op_pos-1);
			if ($r_op_pos > -1)
				$r_val = substr($exp, $c+1+$op2, $r_op_pos-$c-1+$op2);
			else
				$r_val = substr($exp, $c+1+$op2);
			if ($l_op_pos > -1) $l_rest = substr($exp, 0, $l_op_pos+1); else $l_rest = "";
			if ($r_op_pos > -1) $r_rest = substr($exp, $r_op_pos); else $r_rest = "";
			if ($c > 0)  $op = substr($exp, $c, 1+$op2); else $op = "";
		} else {
			$l_op_pos = -1;
			$r_op_pos = -1;
			$l_val = $exp;
			$r_val = "";
			$l_rest = "";
			$r_rest = "";
			$op = "";
		}
//		echo "find_2_val-------------->$l_val [$op] $r_val [$exp]($l_rest,$r_rest)($l_op_pos, $r_op_pos) [".implode("|",$para)."]<br>";
//		foreach ($para as $key => $value) {
//			echo "Key: $key; Value: $value<br />\n";
//		}
		if (substr($l_val, 0, 1)=="@") {
			$a_l_val = str_replace("@","",$l_val);
//			$a_l_val = $l_val;
//			echo "left val ($a_l_val) = ".$para[$a_l_val]."<br>";
			$lval=$para[$a_l_val];
		} else {
			$lval=$l_val;
		}
		if (substr($r_val, 0, 1)=="@") {
			$a_r_val = str_replace("@","",$r_val);
//			$a_r_val = $r_val;
//			echo "right val ($a_r_val) = ".$para[$a_r_val]."<br>";
			$rval=$para[$a_r_val];
		} else {
			$rval=$r_val;
		}
//		echo "---- |$lval| [$op] |$rval| [$exp]($l_rest,$r_rest)($l_op_pos, $r_op_pos)<br>";
		
		return "$lval,$rval,$l_rest,$r_rest";
	}

	//*********************************************************************************
	// formula2sqlselect function แปลง สูตรให้เป็น sql
	//*********************************************************************************
	function formula2sqlselect($formula) {
		$arr_column = (array) null;
		if (formula2sqlselect1($formula, $arr_column))
			return $arr_column;
		else
			return false;
	}
	function formula2sqlselect1($formula, &$arr_column) {
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
						$retval = formula2sqlselect1($buff1, $arr_column);
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
//		echo "sub_formula2sqlselect--$thisform<br>";
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
//					echo "c_result=$c_result<br>";
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
			$var_f = substr($exp,0,1);
			if ($var_f=="$") { // เป็นตัวแปร variable
				$var_name = substr($exp,1);
				if (count($arr_column[va][$var_name]) == 0)
					$arr_column[va][$var_name][] = $var_name."|";
			} else {
				if (!check_num($exp) && !check_string($exp)) {
					$buff = explode(".",$exp);
					if (count($buff) == 3) { $tbuff=$buff[0].".".$buff[1]; $nbuff=$buff[2]; }
					else { $tbuff=$buff[0]; $nbuff=$buff[1]; }
					if (strlen($tbuff) > 0 && strlen($nbuff) > 0) { // เป็นตัวแปรใน database
//						echo "sub_formula2sqlselect_exp=last one=>".$exp." op=".$op."<br>";
						if (count($arr_column[db]["$exp"]) == 0) {
//							echo "----------------------------------------------last one--------->arr_column db $exp  left---> $exp | $op<br>";
							$arr_column[db]["$exp"][] = $exp."|".$op;
						}
					}
				}
			}
			$retval = "$exp";
		} else {
			$arr_val = explode(",",find_2_column($exp, $c, $arr_column));
//			echo "sub_formula2sqlselect_exp==>arr_val=".implode(",",$arr_val)."<br>";
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
		if ($r_op_pos > -1)
			$r_val = substr($exp, $c+1+$op2, $r_op_pos-$c-1+$op2);
		else
			$r_val = substr($exp, $c+1+$op2);
		if ($l_op_pos > -1) $l_rest = substr($exp, 0, $l_op_pos); else $l_rest = "";
		if ($r_op_pos > -1) $r_rest = substr($exp, $r_op_pos); else $r_rest = "";
		if ($c >= 0)  $op = substr($exp, $c, 1+$op2); else $op = "";
//		echo "find_2_column>>>$c -- $l_val [$op] $r_val [$exp]($l_rest,$r_rest)($l_op_pos, $r_op_pos)<br>";
		$l_val = trim($l_val);
		$var_f = substr($l_val,0,1);
		if ($var_f=="$") { // เป็นตัวแปร variable
			$var_name = substr($l_val,1);
			if (count($arr_column[va][$var_name]) == 0)
				$arr_column[va][$var_name][] = $var_name."|".$op;
		} else {
			if (!check_num($l_val) && !check_string($l_val)) {
				$buff = explode(".",$l_val);
				if (count($buff) == 3) { $tbuff=$buff[0].".".$buff[1]; $nbuff=$buff[2]; }
				else { $tbuff=$buff[0]; $nbuff=$buff[1]; }
				if (strlen($tbuff) > 0 && strlen($nbuff) > 0) { // เป็นตัวแปรใน database
//					echo "find_2_column>>db left>".$tbuff.":".$nbuff." op=".$op." (".count($arr_column[db]["$tbuff"]).")<br>";
					if (count($arr_column[db]["$l_val"]) == 0) {
//						echo "------------------------------------------------------->arr_column db $l_val  left---> $l_val | $op<br>";
						$arr_column[db]["$l_val"][] = $l_val."|".$op;
					}
				}
			}
		}
		$r_val = trim($r_val);
		$var_f = substr($r_val,0,1);
		if ($var_f=="$") { // เป็นตัวแปร variable
			$var_name = substr($r_val,1);
			if (count($arr_column[va][$var_name]) == 0)
				$arr_column[va][$var_name][] = $var_name."|".$op;
		} else {
			if (!check_num($r_val) && !check_string($r_val)) {
				$buff = explode(".",$r_val);
				if (count($buff) == 3) { $tbuff=$buff[0].".".$buff[1]; $nbuff=$buff[2]; }
				else { $tbuff=$buff[0]; $nbuff=$buff[1]; }
				if (strlen($tbuff) > 0 && strlen($nbuff) > 0) { // เป็นตัวแปรใน database
//					echo "find_2_column>>db right>".$tbuff.":".$nbuff." op=".$op." (".count($arr_column[db]["$tbuff"]).")<br>";
					if (count($arr_column[db]["$r_val"]) == 0) {
//						echo "------------------------------------------------------->arr_column db $r_val right---> $r_val | $op<br>";
						$arr_column[db]["$r_val"][] = $r_val."|".$op;
					}
				}
			}
		}
//		echo "$l_val,$r_val,$l_rest,$r_rest<br>";

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
//		echo "map_formyla_var..............................$mapstr, $formula<br>";
		$buff = explode("|",$mapstr);
		$arr_map = explode(",",$buff[2]);
		for($i=0; $i < count($arr_map); $i++) {
			$buff1 = explode("^",$arr_map[$i]);
//			echo "0:".$buff1[0].", 1:".$buff1[1]."<br>";
			$formula = str_replace($buff1[0], $buff1[1], $formula);
			$last_j=0;
			$newformula="";
			for($j=0; $j < strlen($formula); $j++) {
				$ch = substr($formula,$j,1);
				if ($ch=="\"") { $j=strpos($formula, "\"", $j+1); continue; }
				if ($ch=="'") { $j=strpos($formula, "'", $j+1); continue; }
				$c=strpos("+-*/()=<>!?: ", $ch);
				if ($c!==false) {	// ถ้าเป็นอักษรแยกคำทั้งหลาย จะทำการตรวจสอบคำที่ผ่านมา
					if ($j-$last_j==0) $w="";
					else $w = substr($formula,$last_j,$j-$last_j);
//					echo "0000 &&&&&&&&&&&&&&&&&&&&&&&&&&&---->$w==".$buff1[0]."<br />";
					if ($w==$buff1[0]) $newformula .= $buff1[1].$ch; else $newformula .= $w.$ch;
					$last_j = $j+1;
				}
			}
			if ($last_j < $j) {
				$w = substr($formula,$last_j,$j-$last_j);
//				echo "&&&&&&&&&&&&&&&&&&&&&&&&&&&---last w->$w==".$buff1[0]."<br />";
				if ($w==$buff1[0]) $newformula .= $buff1[1]; else $newformula .= $w;
			}
			$formula = $newformula;
//			echo "ret formula=$formula<br>";
		}
		
		return $formula;
	}

	//*********************************************************************************
	// sql_condition_var function สำหรับ map  ตัวแปรใน SQL Condition
	//*********************************************************************************
	function sql_condition_var($condition, $select_list) {
		$buff = explode(" ",$condition);
		$arr_select = explode(",",$select_list);
		$arr_var = (array) null;
		$a_op = array("<>","!=",">=","<=","=",">","<");
		for($i=0; $i < count($buff); $i++) {
			// กรณี ไม่มี space ขั้นระหว่าง column และ @VAR
			if (array_search($buff[$i], $a_op) === false) {
				$v = substr($buff[$i],0,4);
				if ($v=="@VAR") {
					$arr_var[] = $buff[$i];
//					echo "var:@".substr($buff[$i],0,$endpos+1)."<br>";
				} else {
					$founded = false;
					for($j = 0; $j < count($arr_select); $j++) {
						$a_subsel = explode("|", $arr_select[$j]);
						if ($buff[$i]==$a_subsel[0].".".$a_subsel[1]) { $founded=true; break; }
					}
					if ($founded) {	// เจอ
					} else {
						for($j=0; $j < count($a_op); $j++) {
							$abuff = explode($a_op[$j], $buff[$i]);
							if (count($abuff) == 3) { $founded = true; break; }
						}
						if ($founded) {
						}
					}
				}
			}
		}
		
		return implode(",", $arr_var);
	}

	//*********************************************************************************
	// check_num function ตรวจสอบตัวแปรว่าเป็นตัวเลขรึเปล่า
	//*********************************************************************************
	function check_num($mystr) {
		$f_ret = is_numeric(trim($mystr));

		return $f_ret;
	}

	//*********************************************************************************
	// check_string function ตรวจสอบตัวแปรว่าเป็นตัวอักษรรึเปล่า
	//*********************************************************************************
	function check_string($mystr) {
		$mystr=trim($mystr);
		$first_ch = substr($mystr,0,1);
		$last_ch = substr($mystr,strlen($mystr)-1,1);
		if ($first_ch=="'" || $last_ch=="'") $f_ret=true;
		else if ($first_ch=="\"" || $last_ch=="\"") $f_ret=true;
		
		return $f_ret;
	}

	//*********************************************************************************
	// find_next_op_pos function สำหรับหา operater ตัวถัดไปใน กลุ่ม expression หนึ่ง ๆ
	//*********************************************************************************
	function find_next_op_pos($exp, $c, $f) {
//		echo "find_next_op_pos<br>";
		$retpos=-1;
		$exp1 = strtolower($exp);
		if ($f==-1) { // left
			for($i=$c-1; $i >= 0; $i--) {
				$c1 = strpos("+-*/><=rd|&",substr($exp1,$i,1));
//				echo "left : ".$exp1.", i=$i, c1=$c1, ch=".substr($exp1,$i,1)."<br>";
				if ($c1 !== false) {
					if ($c1 > 6) {
						if (substr($exp1,$i-3,4)==" and" || substr($exp1,$i-2,3)==" or" || substr($exp1,$i-1,2)=="&&" || substr($exp1,$i-1,2)=="||") {
							$retpos = $i;
							break;
						}
					} else if ($c1 == 1) { // ถ้าเจอ - ต้อง check ดูว่า เป็นค่าติดลบ หรือ เป็น op
							$pos1 = find_next_op_pos($exp1, $i, $f);
							if ($pos1 == -1) $opr1 = trim(substr($exp1,0,$i));
							else $opr1 = trim(substr($exp1,$pos1+1,$i-$pos1));
							if ($opr1) $retpos = $i; else $retpos = -1;
							break;
					} else {
							$retpos = $i;
							break;
					}
				}
			}
		} else { // right
			for($i=$c+1; $i < strlen($exp); $i++) {
				$c1 = strpos("+-*/><=oa|&",substr($exp1,$i,1));
//				echo "right : ".$exp1.", i=$i, c1=$c1, ch=".substr($exp1,$i,1)."<br>";
				if ($c1 !== false) {
					if ($c1 > 6) {
						if (substr($exp1,$i,4)=="and " || substr($exp1,$i,3)=="or " || substr($exp1,$i,2)=="&&" || substr($exp1,$i,2)=="||") {
							$retpos = $i;
							break;
						}
					} else {
						$retpos = $i;
						break;
					}
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
	
	//***************************************************************************************
	// form2QueryText function สำหรับ แปลง รูปแบบจาก แบบฟอร์มข้อมูล เป็น SQL TEXT
	//***************************************************************************************
	function form2QueryText($SELECTED_COLUMNS, $Q_TABLES, $Q_LINK, $Q_CONDITION, $Q_GROUP, $Q_ORDER) {
                // SELECTED_COLUMNS : table_sign1|column_name1|data_dic_no1,table_sign2|column_name2|data_dic_no2, .............
                $select_list = "";
                if (strlen(trim($SELECTED_COLUMNS)) > 0) {
                    $arr_select_sql = (array) null;
                    $arr_select = (array) null;
                    $arr_checkcol = (array) null;
                    $arr_col = explode(",", trim($SELECTED_COLUMNS));
                    if ($arr_col && count($arr_col) > 0) {
                        for($i = 0; $i < count($arr_col); $i++) {
                            $arr_col_det = explode("|", $arr_col[$i]);
//							$ddictxt = explode("(", $arr_ddic_thai_name[$arr_col_det[2]]);
                            $col_alias = $arr_col_det[1];
                            $k = 0;
                            $abuff = $col_alias;
                            while (array_search($abuff, $arr_checkcol)!==false) {
                                $k++;
                                $abuff = $col_alias."_".$k;
                            }
                            $arr_checkcol[] = $abuff;
                            if ($k > 0) {
                                $arr_select[] = $arr_col_det[0].".".$arr_col_det[1];
                                $arr_select_sql[] = $arr_col_det[0].".".$arr_col_det[1]." as ".$abuff;
                            } else {
                                $arr_select[] = $arr_col_det[0].".".$arr_col_det[1];
                                $arr_select_sql[] = $arr_col_det[0].".".$arr_col_det[1];
                            }
                        } // end for $i loop
                        $select_list = implode(",", $arr_select_sql);
                    } // end if ($arr_col)
				} // end if (strlen(trim($SELECTED_COLUMNS)) > 0)

                // $Q_TABLES : รูปแบบ : tablename1|tablesign1,tablename2|tablesign2,............................
                $table_list = "";
                if (strlen($Q_TABLES) > 0) {
                    $arr_tab = explode(",", $Q_TABLES);
                    if (count($arr_tab) > 0) {
                        $arr_tab1 = (array) null;
                        for($i=0; $i < count($arr_tab); $i++) {
                            $sub_arr_tab = explode("|", $arr_tab[$i]);
                            $arr_tab1[] = $sub_arr_tab[0]." ".$sub_arr_tab[1];
                        }
                        $table_list = implode(",", $arr_tab1);
                    } // end if ($arr_tab)
                } // end if (strlen($Q_TABLES) > 0)

                // $Q_LINK : รูปแบบ : table11@table21@1@join1@linnk1|table12@table22@2@join2@linnk2|............................
                $joinstr = "";
                if (strlen($Q_LINK) > 0) {
                    $arr_link = explode("|", $Q_LINK);
                    $arr_link1 = (array) null;
                    if (count($arr_link) > 0) {
                        for($i=0; $i < count($arr_link); $i++) {
                            $sub_arr_link = explode("@", $arr_link[$i]);
                            if (!$joinstr) {
                                $joinstr = $sub_arr_link[0];
                            }
                            $arr_link1 = explode(",", $sub_arr_link[4]);
                            $column_link = implode(" and ", $arr_link1);
                            // $sub_arr_link[3]==jointype=="1" ตือ left join ถ้าไม่ =="1" คือ join ธรรมดา
                            $joinstr .= ($sub_arr_link[3]=="1"?" left join ":" join ").$sub_arr_link[1]." on (".$column_link.")";
                        }
                    } // end if ($arr_link)
                } // end if (strlen($Q_LINK) > 0)

                // $Q_CONDITION
                if (strlen($Q_CONDITION) > 0) {
                    $Q_VAR = sql_condition_var($Q_CONDITION, $SELECTED_COLUMNS);
                } // end if (strlen($Q_CONDITION) > 0)

                // $Q_GROUP : รูปแบบ : data_selected_index1|aggreate_f1|data_dic_no1,data_selected_index2|aggreate_f2|data_dic_no2,...
                $grp_list = "";
                if (strlen($Q_GROUP) > 0) {
                    $arr_grp = explode(",", $Q_GROUP);
                    if (count($arr_grp) > 0) {
                        for($i=0; $i < count($arr_grp); $i++) {
							$sub_arr_grp = explode("|", $arr_grp[$i]);
							if (!$sub_arr_grp[1] || $sub_arr_grp[1]=="GROUP")
								if ($grp_list)
									$grp_list .= ",".$arr_select_sql[$sub_arr_grp[0]];
								else
									$grp_list = $arr_select_sql[$sub_arr_grp[0]];
							else
								$arr_select_sql[$sub_arr_grp[0]] = $sub_arr_grp[1]."(".$arr_select_sql[$sub_arr_grp[0]].") as ".str_replace(".","_",$sub_arr_grp[1]."_".$arr_select_sql[$sub_arr_grp[0]]);
						}
						$select_list = implode(",", $arr_select_sql);
					} // end if ($arr_grp)
				} // end if (strlen($Q_GROUP) > 0)
        
                // $Q_ORDER : รูปแบบ : column index1|order1,column index2|order2,....
				$ord_list = "";
                if (strlen($Q_ORDER) > 0) {
                    $arr_ord = explode(",", $Q_ORDER);
                    if (count($arr_ord) > 0) {
                        for($i=0; $i < count($arr_ord); $i++) {
                            $sub_arr_ord = explode("|", $arr_ord[$i]);
							$c=strpos($arr_select_sql[$sub_arr_ord[0]], " as ");
							if ($c!==false) $ordername = substr($arr_select_sql[$sub_arr_ord[0]], $c+3);
							else $ordername = $arr_select_sql[$sub_arr_ord[0]];
							if ($ord_list)
								$ord_list .= ",$ordername";
							else
								$ord_list = $ordername;
                        }
                    } // end if ($arr_ord)
                } // end if (strlen($Q_ORDER) > 0)

				$ORIG_QUERY = "select $select_list from ".($joinstr ? $joinstr : $table_list).($Q_CONDITION ? " where ".$Q_CONDITION : "").($grp_list ? " group by ".$grp_list : "").($ord_list ? " order by ".$ord_list : "");
				$QUERY_SET = "$ORIG_QUERY|$select_list|$table_list|$joinstr|$Q_CONDITION|$grp_list|$ord_list";
				
				return $QUERY_SET;
	}
	
	//*********************************************************************************
	// variable value text to array
	//*********************************************************************************
	function vartext2array($para) {
		// variable text format --> a=3,b=5,c=7, d=0
		$p = explode(",", $para);
		$arr = (array) null;
		for($i = 0; $i < count($p); $i++) {
			$p1 = explode("=",$p[$i]);
			$arr[$p1[0]] = $p1[1];
		}
/*
		foreach($arr as $x => $x_value) {
			echo "Key=" . $x . ", Value=" . $x_value;
			echo "<br>";
		}
*/
		return $arr;
	}
?>