<?
			include ("../php_scripts/function_repgen.php");

			function do_aggregate($arr_data, $arr_func_aggreg, $arr_column_sel, $arr_column_map) {
				// กำหนด agregate function with parameter for calling outsite open_tab

					$arr_column_aggreg = (array) null;
					$arr_grp = (array) null;
					$arr_ff = (array) null;
					$arr_sum = (array) null;
					for($i=0; $i < count($arr_data); $i++) {
						$arr_ff[] = "";
						$arr_sum[] = 0;
						$chk_merge = strpos($arr_data[$i],"<**");
						if ($chk_merge!==false) {
							$c = strpos($arr_data[$i],"**>");
							if ($c!==false) {
								$mgrp = substr($arr_data[$i], $chk_merge, $c-$chk_merge+3);
								$buff = str_replace($mgrp,"",$arr_data[$i]);
								if (is_numeric($buff))	{ $arr_data[$i] = $buff; $arr_grp[] = $mgrp; }
								else { $arr_data[$i] = ""; $arr_grp[] = $mgrp.$buff; }
							} else {
								$arr_grp[] = "";
							}
						} else {	// no group
							$arr_grp[] = "";
						}
					}
//					echo "data>".implode(",",$arr_data)." , ".implode(",",$arr_grp)."<br>";
					$arr_column_aggreg = (array) null;
//					echo ">map>".implode(",",$arr_column_map)."<br>";
					if (count($arr_func_aggreg) > 0) {
						for($kk=0; $kk < count($arr_func_aggreg); $kk++) {
							if ($arr_column_sel[$arr_column_map[$kk]]==1) {	// 1 = แสดง column นี้
//								echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-".$arr_func_aggreg[$arr_column_map[$kk]]."<br>";
								$a_sub_func = explode("|",$arr_func_aggreg[$arr_column_map[$kk]]);
								for($ii = 0; $ii < count($a_sub_func); $ii++) {
//									echo "a_sub_func [$ii]=".$a_sub_func[$ii]."<br>";
									$c = strpos($a_sub_func[$ii],"SUM-");
									if ($c !== false) {
										$arr_ff[$kk] = "S";
										$buff = substr($a_sub_func[$ii],4);
										$a = explode("-",$buff);
										$sum = 0;
										$havedata = false;
										for($kkk=0; $kkk < count($a); $kkk++) {
											if ($arr_column_sel[$a[$kkk]]==1) {	// 1 = แสดง column นี้ ใช้การเลือกค่าทำ sum จาก link ต้นฉบับ ไม่ต้อง map ถ้า map จะผิด
												$ndata = trim($arr_data[$a[$kkk]]);
												if ($ndata!="") $havedata = true;
//												echo "sum kk=$kk--kkk=$kkk--a[$kkk]=".$a[$kkk]."--map=".$arr_column_map[$a[$kkk]]."-->ndata=$ndata, havedata=$havedata<br>";
												$sum += (float)$ndata;
											}
										}
										if ($havedata)	$lastval = (string)$sum;	else		$lastval = chr(96);	 // chr(96) แทนช่องว่าง
//										if ($havedata) echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-sum-".$sum."-lastval=$lastval<br>";
									} else {
										$c = strpos($a_sub_func[$ii],"PERC");
										if ($c !== false) {
											$arr_ff[$kk] = "P";
											$c1=strpos($a_sub_func[$ii],"-");
											if ($c1==4) $forcol=-1;
											else $forcol=substr($a_sub_func[$ii],4,$c1-4);
											$buff = substr($a_sub_func[$ii],$c1+1);
											$a = explode("-",$buff);
											$thisdata = -1;
											$sum = 0;
											$cnt = 0;
											$havedata = false;
											for($kkk=0; $kkk < count($a); $kkk++) {
												if ($arr_column_sel[$a[$kkk]]==1) {	// 1 = แสดง column นี้ ใช้การเลือกค่าทำ sum จาก link ต้นฉบับ ไม่ต้อง map ถ้า map จะผิด
													$ndata = trim($arr_data[$a[$kkk]]);
													if ($ndata!="") $havedata = true;
//													echo "perc kk=$kk--kkk=$kkk--(".$arr_column_map[$kkk].")--a[$kkk]=".$a[$kkk]."--map=".$arr_column_map[$a[$kkk]]."-->ndata=$ndata, forcol=$forcol, havedata=$havedata<br>";
													if ($forcol==$a[$kkk]) $thisdata = (float)$ndata;
													$sum += (float)$ndata;
													$cnt++;
												}
											}
											if ($havedata)	
												if ($sum > 0) {
													if ($thisdata == -1) $thisdata = $sum;	// กรณีที่ column ที่กำหนด function นี้ ไม่เป็นสมาชิกในกลุ่มที่นำมารวม ก็จะถือว่าเป็น column สรุปรวม
													$lastval = (string)($thisdata / $sum * 100);	// ทำเป็น percent
	//												echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-perc=$thisdata / $sum * 100<br>";
												} else $lastval="0";
											else $lastval = chr(96);	 // chr(96) แทนช่องว่าง
										} else {
											$c = strpos($a_sub_func[$ii],"AVG-");
											if ($c !== false) {
												$buff = substr($a_sub_func[$ii],4);
												$arr_ff[$kk] = "A";
												$a = explode("-",$buff);
												$thisdata = 0;
												$sum = 0;
												$cnt = 0;
												$havedata = false;
												for($kkk=0; $kkk < count($a); $kkk++) {
													if ($arr_column_sel[$a[$kkk]]==1) {	// 1 = แสดง column นี้
														$ndata = trim($arr_data[$a[$kkk]]);
														if ($ndata!="") $havedata = true;
														$sum += (float)$ndata;
														$cnt++;
													}
												}
												if ($havedata)
													$lastval = (string)($sum / $cnt);	// ทำเป็น ค่าเฉลี่ย
												else 	$lastval = chr(96);	 // chr(96) แทนช่องว่าง
//												echo "function aggregate avg-".($sum / $cnt)."<br>";
											} else {
												$c = strpos($a_sub_func[$ii],"FORM");
												if ($c !== false) {
													$buff = substr($a_sub_func[$ii],4);
													$arr_ff[$kk] = "F";
//													echo "buff=$buff, len=".strlen($buff)."<br>";
													preg_match_all("/([+|-|*|\/|\(|\)])/", $buff, $matches, PREG_OFFSET_CAPTURE);
//													echo "data=".implode(",",$arr_data)."<br>";
													$para = (array) null;
													foreach($matches[0] as $key1 => $val1) {
//														echo "......key1=$key1 => ".$matches[0][$key1][0]."";
														if ($key1==0) $str = substr($buff, 0, $matches[0][$key1][1]);
														else if ((int)$matches[0][$key1][1] > 0) $str = substr($buff, $matches[0][$key1-1][1]+1, $matches[0][$key1][1]-$matches[0][$key1-1][1]-1);
//														echo ">>".$matches[0][$key1][1]."=>$str<br>";
														if (strpos($str,"@")!==false) { 
//															if ($ii > 0) $ndata = trim($arr_column_aggreg[$arr_column_map[(int)substr($str,1)]]);
//															else $ndata = (string)trim($arr_data[$arr_column_map[(int)substr($str,1)]]);
															if ($ii > 0) $ndata = trim($arr_column_aggreg[(int)substr($str,1)]);
															else $ndata = (string)trim($arr_data[(int)substr($str,1)]);
//															echo "1..parameter [$str]=$ndata map:".$arr_column_map[(int)substr($str,1)]."<br>";
															$para[$str] = $ndata;
														}
														if (((int)$matches[0][$key1+1][1] == 0)) {
															$str = substr($buff, $matches[0][$key1][1]+1);
//															echo "...last key =>".$matches[0][$key1][0].">>".$matches[0][$key1][1]."=>$str<br>";
															if (strpos($str,"@")!==false) { 
//																if ($ii > 0) $ndata = trim($arr_column_aggreg[$arr_column_map[(int)substr($str,1)]]);
//																else $ndata = (string)trim($arr_data[$arr_column_map[(int)substr($str,1)]]);
																if ($ii > 0) $ndata = trim($arr_column_aggreg[(int)substr($str,1)]);
																else $ndata = (string)trim($arr_data[(int)substr($str,1)]);
//																echo "2..parameter [$str]=$ndata map:".$arr_column_map[(int)substr($str,1)]."<br>";
																$para[$str] = $ndata;
															}
														}
													}
//													print_r($para);
													$val = compute_formula($buff, $para);
//													echo "<br>compute=$val<br>";
													$lastval = (string)$val;	// ทำเป็น ค่าเฉลี่ย
//													echo "function aggregate formula-".($sum / $cnt)."<br>";
												} else {	// ถ้าไม่มีค่าอะไร
													if ($ii > 0) $ndata = $arr_column_aggreg[$arr_column_map[$kk]];
													else $ndata = (string)$arr_data[$arr_column_map[$kk]];
													$lastval = $ndata;
//													echo "no function(kk($kk)-->".$arr_column_map[$kk].")-".$lastval.", ndata=$ndata<br>";
												} // end if FORMULA
											} // end if AVG
										} // end if PERC
									} // end if SUM
//									echo "$ii-".$a_sub_func[$ii]." , lastval=$lastval<br>";
									$arr_column_aggreg[$arr_column_map[$kk]] = $lastval;
								} // end for loop $ii
							} else { // else if ($arr_column_sel[$iii]==1)
								$arr_column_aggreg[$arr_column_map[$kk]] = "";
							} // end if ($arr_column_sel[$iii]==1)
						} // end loop for $kk

//						echo "1..aggreg=".implode(",",$arr_column_aggreg)." , func=".implode(",",$arr_func_aggreg)." , grp=".implode(",",$arr_grp)." , ff=".implode(",",$arr_ff)."<br>";

						if (count($arr_column_aggreg) > 0) {
							$mergesum = 0; $grp = "";
							for($i=0; $i < count($arr_column_aggreg); $i++) {
//								echo "grp=".$arr_grp[$i]." && ff=".$arr_ff[$i]." && val=".$arr_column_aggreg[$i]."<br>";
								if ($arr_grp[$i] && $arr_ff[$i] && is_numeric($arr_column_aggreg[$i])) {
									if ($arr_grp[$i] != $grp) {
//										echo "1..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
										if ($mergesum)
											$arr_sum[$last_i] = $mergesum;
										$grp = $arr_grp[$i];
										$mergesum = $arr_column_aggreg[$i];
										$last_i = $i;
									}  else {
										$mergesum += $arr_column_aggreg[$i];
//										echo "2..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
									}
								} else {
//									echo "3..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
									if ($mergesum) {
										$arr_sum[$last_i] = $mergesum;
										$last_i = -1;
									}
									$mergesum = 0;
									$grp = "";
								}
							} // end loop for
//							echo "4..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
							if ($mergesum)
								$arr_sum[$last_i] = $mergesum;
								
							$grp = ""; $ss = 0;
							for($i=0; $i < count($arr_column_aggreg); $i++) {
//								echo "$i .. arr_grp ..".$arr_grp[$i].",  grp=$grp<br>";
								if ($arr_sum[$i])
									if ($arr_grp[$i] && $arr_grp[$i] != $grp) {
										$ss = $arr_sum[$i];
										$arr_column_aggreg[$i] = $ss;
										$grp = $arr_grp[$i];									
//										echo "$i .1. set ss=$ss<br>";
									} else {
										if ($arr_grp[$i]) {
											$arr_column_aggreg[$i] = $ss;
	//										echo "$i .2. loop ss=$ss<br>";										
										}
									}
							} // end loop for
						}
//						echo "2..aggreg=".implode(",",$arr_column_aggreg)." , func=".implode(",",$arr_func_aggreg)." , sum=".implode(",",$arr_sum)."<br>";

						// รอบสองแปลง format  TNUM , ENUM
						for($kk=0; $kk < count($arr_func_aggreg); $kk++) {
							if ($arr_column_sel[$arr_column_map[$kk]]==1) {	// 1 = แสดง column นี้
//								echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-".$arr_func_aggreg[$arr_column_map[$kk]]."<br>";
								$a_sub_func = explode("|",$arr_func_aggreg[$arr_column_map[$kk]]);
								for($ii = 0; $ii < count($a_sub_func); $ii++) {
//									echo "a_sub_func [$ii]=".$a_sub_func[$ii]."<br>";
									$c = strpos($a_sub_func[$ii],"TNUM");
									if ($c !== false) {
										$ndata = $arr_column_aggreg[$arr_column_map[$kk]];
//										echo "TNUM - $ii - ".$a_sub_func[$ii]." , $ndata<br>";
										if ($ndata==chr(96)) {	 // chr(96) แทนช่องว่าง
											$lastval = " ";
//											echo "TNUM space ($ndata) (".chr(96).") logic(".($ndata==chr(96)).")<br>";
										} else {
//											echo "TNUM $ndata<br>";
											$zero = (string)substr($a_sub_func[$ii], 4, 1);
											if ($zero=="0")  $c1=$c+6;
											else {
//												if (!$ndata) echo "null data [$ndata] (is_numeric:".is_numeric($ndata).") (float:".((float)$ndata==0).") (".($ndata=="").")<br>";
												if (is_numeric($ndata) && (float)$ndata==0) $ndata="-";
//												else if ($ndata=="")
												$c1=$c+5;
											}
											$nocomma = (strtolower(substr($a_sub_func[$ii], $c1,1))=="x"?true:false);
//											echo "function-".$a_sub_func[$ii]." nocomma=$nocomma<br>";
											if ($nocomma) 
												$dig = substr($a_sub_func[$ii], $c1+1);
											else
												$dig = substr($a_sub_func[$ii], $c1);
											if (!is_numeric($ndata)) 
												$lastval = convert2thaidigit($ndata);
											else if ((int)$dig > 0)
												if ($nocomma)
													$lastval = convert2thaidigit($ndata,$dig);
												else
													$lastval = convert2thaidigit(number_format($ndata,$dig));
											else {
												if ($dig == "z") {
													$numd = explode(".",$ndata);
													$digstr = $numd[1];
													$ndig = 0;
													for($i=strlen($digstr)-1; $i >= 0; $i--) {
														if (substr($digstr,$i,1)!="0") { $ndig = $i+1; break; }
													}
//													echo "first part:".$numd[0]." second part:".$numd[1]." ndig:$ndig<br>";
													if ($ndig == 0) 
														if ($nocomma)
															$lastval = convert2thaidigit($ndata);
														else
															$lastval = convert2thaidigit(number_format($ndata));
													else
														if ($nocomma)
															$lastval = convert2thaidigit($ndata,$ndig);
														else
															$lastval = convert2thaidigit(number_format($ndata,$ndig));
												} else 
													if ($nocomma)
														$lastval = convert2thaidigit($ndata);
													else
														$lastval = convert2thaidigit(number_format($ndata));
											}
										}
										$arr_column_aggreg[$arr_column_map[$kk]] = str_replace("*Enter*","\n",$lastval);
//										echo "function TNUM $kk-(map=".$arr_column_map[$kk].")-tnum-".$lastval.", ndata=$ndata, dig=$dig<br>";
									} else {
										$c = strpos($a_sub_func[$ii],"ENUM");
										if ($c !== false) {
											$ndata = $arr_column_aggreg[$arr_column_map[$kk]];
//											echo "ENUM - $ii - ".$a_sub_func[$ii]." , $ndata<br>";
											if ($ndata==chr(96)) {  // chr(96) แทนช่องว่าง
												$lastval = " ";
											} else {
												$zero = (string)substr($a_sub_func[$ii], 4, 1);
												if ($zero=="0")  $c1=$c+6;
												else {
													if (is_numeric($ndata) && (float)$ndata==0) $ndata="-";
													$c1=$c+5;
												}
												$nocomma = (strtolower(substr($a_sub_func[$ii], $c1,1))=="x"?true:false);
//												echo "function-".$a_sub_func[$ii]." nocomma=$nocomma<br>";
												if ($nocomma) 
													$dig = substr($a_sub_func[$ii], $c1+1);
												else
													$dig = substr($a_sub_func[$ii], $c1);
												if (!is_numeric($ndata)) 
													$lastval = $ndata;
												else if ((int)$dig > 0)
													if ($nocomma)
														$lastval = $ndata;
													else
														$lastval = number_format($ndata,$dig);
												else {
													if ($dig == "z") {
														$numd = explode(".",$ndata);
														$digstr = $numd[1];
														$ndig = 0;
														for($i=strlen($digstr)-1; $i >= 0; $i--) {
															if (substr($digstr,$i,1)!="0") { $ndig = $i+1; break; }
														}
//														echo "first part:".$numd[0]." second part:".$numd[1]." ndig:$ndig<br>";
														if ($ndig == 0) 
															if ($nocomma)
																$lastval = $ndata;
															else
																$lastval = number_format($ndata);
														else
															if ($nocomma)
																$lastval = $ndata;
															else
																$lastval = number_format($ndata,$ndig);
													} else 
														if ($nocomma)
															$lastval = $ndata;
														else
															$lastval = number_format($ndata);
												}
											}
											$arr_column_aggreg[$arr_column_map[$kk]] = str_replace("*Enter*","\n",$lastval);
//											echo "function enum-".$lastval.", ndata=$ndata, dig=$dig<br>";
//											echo "function ENUM $kk-(map=".$arr_column_map[$kk].")-tnum-".$lastval.", ndata=$ndata, dig=$dig<br>";
										} // end if ENUM
									} // end if TNUM
								} // end for loop $ii
							} // end if ($this->arr_column_sel[$iii]==1)
						} // end loop for $kk

//						echo "3..aggreg=".implode(",",$arr_column_aggreg)."<br>";
					
					} // end if if (count($arr_func_aggreg) > 0)

					if (count($arr_column_aggreg) > 0) {
						for($i=0; $i < count($arr_column_aggreg); $i++) {
							if ($arr_column_aggreg[$i])
								$arr_column_aggreg[$i] = $arr_grp[$i].$arr_column_aggreg[$i];
							else $arr_column_aggreg[$i] = $arr_grp[$i];
						}
					}
//					echo "4..aggreg=".implode(",",$arr_column_aggreg)."<br>";
					// จบการกำหนด agregate function
					return (count($arr_column_aggreg) > 0 ? implode("|", $arr_column_aggreg) : "");
			}

			function doo_merge_cell($ndata, $border, $merge, $pgrp, $f_lastcolumn, $align) {
				// กรณี merge cell  ถ้าส่ง align เข้ามาเป็น "L" merge จะ= 0 ถ้า align เป็น "C" merge = 1 ถ้าไม่ส่งเข้ามา ค่า merge จะเหมือนที่กำหนด
				$buff = $ndata;
				$f_noborder = !trim($border);
//				echo "ndata=$ndata<br>";
				$c = strpos($buff, "<**");
				if ($c!==false) {	// ถ้าเป็น กลุ่ม
					$c1 = strpos($buff, ">");
					$grp = substr($buff, $c, $c1-$c+1);
					$grp1 = $grp;
//					echo "grp=$grp, pgrp=$pgrp, align=$align<br>";
					if (strpos($grp,"^") !== false) {
						$f_mergeup = 1;
						$grp = str_replace("^","",$grp);
					} else $f_mergeup = 0;
					if ($grp != $pgrp) {
						$ndata = str_replace($grp1, "", $buff);
						if (!trim($ndata) && $pgrp) $ndata = " ";	// สำหรับ excel ที่ column ต่อไป เป็น ไม่มีค่า จะกลายเป็น merge รวมกับ cell หน้า ต้องใส่ space ลงไป
						if ($align) {
							if ($align=="R")	$merge = 0;
							elseif ($align=="L")	$merge = 0;
							else $merge = 1;
//							echo "*1****************มี**>align=$align,merge=$merge<br>";
//						} else {
//							echo "*1****************ไม่มี**>align=$align,merge=$merge<br>";
						}
//						if ($SESS_DEPARTMENT_NAME!="กรมชลประทาน") $border .= (strpos(strtoupper($border),"L")===false?"L":"");	// ไม่มี "L" เติม "L"
						if ($f_mergeup==1) if (strpos(strtoupper($border),"T")!==false) str_replace("T","",$border);	// เอากรอบบนออก ถ้า เป็น mergeup ^
						$border = str_replace("R","",$border);	// มี "R" ตัด "R"
//						echo "1..cell merge $grp==$pgrp data=$buff, ndata=$ndata, merge=$merge, border=$border<br>";
						$pgrp = $grp;
					} else {	// ถ้ายังเป็นกลุ่มเดิมอยู่
						$ndata = "";
						if ($align) {
							if ($align=="R")	$merge = 0;
							elseif ($align=="L")	$merge = 0;
							else $merge = 1;
//							echo "*2****************มี**>align=$align,merge=$merge<br>";
//						} else {
//							echo "*2****************ไม่มี**>align=$align,merge=$merge<br>";
						}
						$border = str_replace("L","",$border);	// มี "L" ตัด "L"
						if (!$f_lastcolumn) $border = str_replace("R","",$border);	// มี "R" ตัด "R"
					}
				} else {	// ถ้าไม่เป็น กลุ่ม
//					if ($pgrp) if (strpos($border,"L")===false) $border .= "L";	// ถ้าตัวก่อนหน้าเป็นกลุ่ม และตัวนี้ไม่เป็นกลุ่ม ให้เติม border L เข้าไป
					$grp = "";
					$merge = 0;
					$ndata = $buff;
					if (!trim($ndata)) $ndata=" ";
					$pgrp = $grp;
//					echo "2..cell merge $grp==$pgrp data=$buff, ndata=$ndata, merge=$merge, border=$border<br>";
				}
				# image
				$c1 = strpos($ndata,"<*img*");
				if ($c1!==false) {
					$c2 = strpos($ndata,"*img*>");
					if ($c2!==false) {
						$img_name = substr($ndata, $c1+6, $c2-($c1+6));
						$ndata = trim(str_replace("<*img*".$img_name."*img*>","",$ndata));
					}
				}
//				if ($SESS_DEPARTMENT_NAME!="กรมชลประทาน") if ($f_lastcolumn) $border .= (strpos(strtoupper($border),"R")===false?"R":"");	// ถ้าเป็น column สุดท้าย แล้วไม่มี "R" เติม "R"
				if ($f_noborder) $border = "";	// กรณีที่ ไม่มีกรอบส่งมา ก็ให้ไม่มีกรอบกลับไป
				
				return $ndata."|".$border."|".$merge."|".$pgrp.($img_name ? "|".$img_name : "");
			}	// end function do_merge_cell

			function wswrite_aggregate_merge($workbook, $worksheet, $xlsRow, $xlsFmt, $arr_fontFmt, $arr_data, $arr_func_aggreg, $arr_column_sel, $arr_column_map, $arr_border, $arr_merge, $arr_align, $arr_wraptext, $arr_rotate, $arr_color, $arr_bgcolor) {
					global ${$xlsFmt};
			
					$FormatName = ${$xlsFmt};

					// กำหนด agregate function with parameter for calling outsite open_tab

					$arr_column_aggreg = (array) null;
//					echo "1..data>".implode(",",$arr_data)."<br>";

					$arr_grp = (array) null;
					$arr_text = (array) null;
					$arr_ff = (array) null;
					$arr_sum = (array) null;
					
					/**************************************************************************/
					/**** แยก รหัสกลุ่ม ข้อมูลตัวเลข ข้อมูลตัวอักษร ออกจากกัน ลงใน array ****/
					/**************************************************************************/
					for($i=0; $i < count($arr_data); $i++) {
						$arr_ff[] = "";
						$arr_sum[] = 0;
						$chk_merge = strpos($arr_data[$i],"<**");
						if ($chk_merge!==false) {
							$c = strpos($arr_data[$i],"**>");
							if ($c!==false) {
								$mgrp = substr($arr_data[$i], $chk_merge, $c-$chk_merge+3);
								$buff = str_replace($mgrp,"",$arr_data[$i]);
//								if (is_numeric($buff))	{ $arr_data[$i] = $buff; $arr_grp[] = $mgrp; }
//								else { $arr_data[$i] = "$buff"; $arr_grp[] = $mgrp; }
//								if (is_numeric($buff))	{ $arr_data[$i] = $buff; $arr_grp[] = $mgrp; }
//								else { $arr_data[$i] = ""; $arr_grp[] = $mgrp.$buff; }
								if (is_numeric($buff))	{ $arr_data[$i] = $buff; $arr_grp[] = $mgrp; $arr_text[] = ""; }
								else { $arr_data[$i] = ""; $arr_grp[] = $mgrp;  $arr_text[] = $buff; }
							} else {
								$arr_grp[] = ""; $arr_text[] = "";
							}
						} else {	// no group
							$arr_grp[] = ""; $arr_text[] = "";
						}
					}
					/**************************************************************************/
					/**** แยก รหัสกลุ่ม ข้อมูลตัวเลข ข้อมูลตัวอักษร ออกจากกัน ลงใน array ****/
					/***************************** End Block *********************************/
//					echo "0..data>".implode(",",$arr_data)." & ".implode(",",$arr_grp)."& ".implode(",",$arr_text)."<br>";
					$arr_column_aggreg = (array) null;
					for($kk=0; $kk < count($arr_func_aggreg); $kk++) {
						$arr_column_aggreg[] = "";
					}
//					echo ">map>".implode(",",$arr_column_map).">>".implode(",",$arr_column_aggreg)."<br>";

					if (count($arr_func_aggreg) > 0) {
						/**************************************************************************/
						/****** compute argregate function $arr_data is numeric only *********/
						/**************************************************************************/
						for($kk=0; $kk < count($arr_func_aggreg); $kk++) {
							if ($arr_column_sel[$arr_column_map[$kk]]==1) {	// 1 = แสดง column นี้
//								echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-".$arr_func_aggreg[$arr_column_map[$kk]]."<br>";
								$a_sub_func = explode("|",$arr_func_aggreg[$arr_column_map[$kk]]);
								for($ii = 0; $ii < count($a_sub_func); $ii++) {
//									echo "a_sub_func [$ii]=".$a_sub_func[$ii]."<br>";
									$c = strpos($a_sub_func[$ii],"SUM-");
									if ($c !== false) {
										$arr_ff[$kk] = "S";
										$buff = substr($a_sub_func[$ii],4);
										$a = explode("-",$buff);
										$sum = 0;
										$havedata = false;
										$pgrp = "";
										for($kkk=0; $kkk < count($a); $kkk++) {
											if ($arr_column_sel[$a[$kkk]]==1) {	// 1 = แสดง column นี้ ใช้การเลือกค่าทำ sum จาก link ต้นฉบับ ไม่ต้อง map ถ้า map จะผิด
												$ndata = trim($arr_data[$a[$kkk]]);
												if ($ndata!="") $havedata = true;
//												echo "sum kk=$kk--kkk=$kkk--a[$kkk]=".$a[$kkk]."--map=".$arr_column_map[$a[$kkk]]."-->ndata=$ndata, havedata=$havedata<br>";
												if (!$arr_grp[$a[$kkk]] || $pgrp != $arr_grp[$a[$kkk]]) {
													$sum += (float)$ndata;
													$pgrp = $arr_grp[$a[$kkk]];
												}
											}
										}
										if ($havedata)	$lastval = (string)$sum;	else		$lastval = chr(96);	 // chr(96) แทนช่องว่าง
//										if ($havedata) echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-sum-".$sum."-lastval=$lastval<br>";
									} else {
										$c = strpos($a_sub_func[$ii],"PERC");
										if ($c !== false) {
											$arr_ff[$kk] = "P";
											$c1=strpos($a_sub_func[$ii],"-");
											if ($c1==4) $forcol=-1;
											else $forcol=substr($a_sub_func[$ii],4,$c1-4);
											$buff = substr($a_sub_func[$ii],$c1+1);
											$a = explode("-",$buff);
											$thisdata = -1;
											$sum = 0;
											$cnt = 0;
											$havedata = false;
											$pgrp = "";
											for($kkk=0; $kkk < count($a); $kkk++) {
												if ($arr_column_sel[$a[$kkk]]==1) {	// 1 = แสดง column นี้ ใช้การเลือกค่าทำ sum จาก link ต้นฉบับ ไม่ต้อง map ถ้า map จะผิด
													$ndata = trim($arr_data[$a[$kkk]]);
													if ($ndata!="") $havedata = true;
//													echo "perc kk=$kk--kkk=$kkk--(".$arr_column_map[$kkk].")--a[$kkk]=".$a[$kkk]."--map=".$arr_column_map[$a[$kkk]]."-->ndata=$ndata, forcol=$forcol, havedata=$havedata<br>";
													if ($forcol==$a[$kkk]) $thisdata = (float)$ndata;
													if (!$arr_grp[$a[$kkk]] || $pgrp != $arr_grp[$a[$kkk]]) {
														$sum += (float)$ndata;
														$pgrp = $arr_grp[$a[$kkk]];
													}
													$cnt++;
												}
											}
											if ($havedata)	
												if ($sum > 0) {
													if ($thisdata == -1) $thisdata = $sum;	// กรณีที่ column ที่กำหนด function นี้ ไม่เป็นสมาชิกในกลุ่มที่นำมารวม ก็จะถือว่าเป็น column สรุปรวม
													$lastval = (string)($thisdata / $sum * 100);	// ทำเป็น percent
	//												echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-perc=$thisdata / $sum * 100<br>";
												} else $lastval="0";
											else $lastval = chr(96);	 // chr(96) แทนช่องว่าง
										} else {
											$c = strpos($a_sub_func[$ii],"AVG-");
											if ($c !== false) {
												$buff = substr($a_sub_func[$ii],4);
												$arr_ff[$kk] = "A";
												$a = explode("-",$buff);
												$thisdata = 0;
												$sum = 0;
												$cnt = 0;
												$havedata = false;
												$pgrp = "";
												for($kkk=0; $kkk < count($a); $kkk++) {
													if ($arr_column_sel[$a[$kkk]]==1) {	// 1 = แสดง column นี้
														$ndata = trim($arr_data[$a[$kkk]]);
														if ($ndata!="") $havedata = true;
														if (!$arr_grp[$a[$kkk]] || $pgrp != $arr_grp[$a[$kkk]]) {
															$sum += (float)$ndata;
															$pgrp = $arr_grp[$a[$kkk]];
														}
														$cnt++;
													}
												}
												if ($havedata)
													$lastval = (string)($sum / $cnt);	// ทำเป็น ค่าเฉลี่ย
												else 	$lastval = chr(96);	 // chr(96) แทนช่องว่าง
//												echo "function aggregate avg-".($sum / $cnt)."<br>";
											} else {
												$c = strpos($a_sub_func[$ii],"FORM");
												if ($c !== false) {
													$buff = substr($a_sub_func[$ii],4);
													$arr_ff[$kk] = "F";
//													echo "buff=$buff, len=".strlen($buff)."<br>";
													preg_match_all("/([+|-|*|\/|\(|\)])/", $buff, $matches, PREG_OFFSET_CAPTURE);
//													echo "data=".implode(",",$arr_data)."<br>";
													$para = (array) null;
													foreach($matches[0] as $key1 => $val1) {
//														echo "......key1=$key1 => ".$matches[0][$key1][0]."";
														if ($key1==0) $str = substr($buff, 0, $matches[0][$key1][1]);
														else if ((int)$matches[0][$key1][1] > 0) $str = substr($buff, $matches[0][$key1-1][1]+1, $matches[0][$key1][1]-$matches[0][$key1-1][1]-1);
//														echo ">>".$matches[0][$key1][1]."=>$str<br>";
														if (strpos($str,"@")!==false) { 
//															if ($ii > 0) $ndata = trim($arr_column_aggreg[$arr_column_map[(int)substr($str,1)]]);
//															else $ndata = (string)trim($arr_data[$arr_column_map[(int)substr($str,1)]]);
															if ($ii > 0) $ndata = trim($arr_column_aggreg[(int)substr($str,1)]);
															else $ndata = (string)trim($arr_data[(int)substr($str,1)]);
//															echo "1..parameter [$str]=$ndata map:".$arr_column_map[(int)substr($str,1)]."<br>";
															$para[$str] = $ndata;
														}
														if (((int)$matches[0][$key1+1][1] == 0)) {
															$str = substr($buff, $matches[0][$key1][1]+1);
//															echo "...last key =>".$matches[0][$key1][0].">>".$matches[0][$key1][1]."=>$str<br>";
															if (strpos($str,"@")!==false) { 
//																if ($ii > 0) $ndata = trim($arr_column_aggreg[$arr_column_map[(int)substr($str,1)]]);
//																else $ndata = (string)trim($arr_data[$arr_column_map[(int)substr($str,1)]]);
																if ($ii > 0) $ndata = trim($arr_column_aggreg[(int)substr($str,1)]);
																else $ndata = (string)trim($arr_data[(int)substr($str,1)]);
//																echo "2..parameter [$str]=$ndata map:".$arr_column_map[(int)substr($str,1)]."<br>";
																$para[$str] = $ndata;
															}
														}
													}
//													print_r($para);
													$val = compute_formula($buff, $para);
//													echo "<br>compute=$val<br>";
													$lastval = (string)$val;	// ทำเป็น ค่าเฉลี่ย
//													echo "function aggregate formula-".($sum / $cnt)."<br>";
												} else {	// ถ้าไม่มีค่าอะไร
													if ($ii > 0) $ndata = $arr_column_aggreg[$arr_column_map[$kk]];
													else $ndata = (string)$arr_data[$arr_column_map[$kk]];
													$lastval = $ndata;
		//											echo "no function-".$lastval.", ndata=$ndata<br>";
												} // end if FORMULA
											} // end if AVG
										} // end if PERC
									} // end if SUM
//									$arr_column_aggreg[$arr_column_map[$kk]] = $lastval;
									$arr_column_aggreg[$arr_column_map[$kk]] = $lastval;
								} // end for loop $ii
							} else { // else if ($arr_column_sel[$iii]==1)
								$arr_column_aggreg[$arr_column_map[$kk]] = "";
							} // end if ($arr_column_sel[$iii]==1)
						} // end loop for $kk
						/**************************************************************************/
						/****** compute argregate function $arr_data is numeric only *********/
						/****************************** End Block ********************************/

//						echo "1..aggreg=".implode(",",$arr_column_aggreg)." & func=".implode(",",$arr_func_aggreg)." & grp=".implode(",",$arr_grp)." & text=".implode(",",$arr_text)." & ff=".implode(",",$arr_ff)."<br>";
						
						/**************************************************************************/
						/****** compute argregate function สำหรับการ merge ค่าตัวเลข *********/
						/**************************************************************************/
						if (count($arr_column_aggreg) > 0) {
							$mergesum = 0; $grp = "";
							for($i=0; $i < count($arr_column_aggreg); $i++) {
//								echo "grp=".$arr_grp[$i]." && ff=".$arr_ff[$i]." && val=".$arr_column_aggreg[$i]."<br>";
								if ($arr_grp[$i] && $arr_ff[$i] && is_numeric($arr_column_aggreg[$i])) {
									if ($arr_grp[$i] != $grp) {
//										echo "1..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
										if ($mergesum)
											$arr_sum[$last_i] = $mergesum;
										$grp = $arr_grp[$i];
										$mergesum = $arr_column_aggreg[$i];
										$last_i = $i;
									}  else {
										$mergesum += $arr_column_aggreg[$i];
//										echo "2..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
									}
								} else {
//									echo "3..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
									if ($mergesum) {
										$arr_sum[$last_i] = $mergesum;
										$last_i = -1;
									}
									$mergesum = 0;
									$grp = "";
								}
							} // end loop for
//							echo "4..i=$i, grp=$grp, mergesum=$mergesum, last_i=$last_i<br>";
							if ($mergesum)
								$arr_sum[$last_i] = $mergesum;
								
							$grp = ""; $ss = 0;
//							echo " aggreg-cnt-".count($arr_column_aggreg)."-data-".implode(",",$arr_column_aggreg)."-merge_sum=$mergesum<br>";
							for($i=0; $i < count($arr_column_aggreg); $i++) {
//								echo "$i .. arr_grp ..".$arr_grp[$i].",  grp=$grp<br>";
								if ($arr_sum[$i])
									if ($arr_grp[$i] && $arr_grp[$i] != $grp) {
										$ss = $arr_sum[$i];
										$arr_column_aggreg[$i] = $ss;
										$grp = $arr_grp[$i];									
//										echo "$i .1. set ss=$ss<br>";
									} else {
										if ($arr_grp[$i]) {
											$arr_column_aggreg[$i] = $ss;
//											echo "$i .2. loop ss=$ss<br>";										
										}
									}
								if (!$arr_column_aggreg[$i] || ($arr_column_aggreg[$i]==chr(96) && $arr_text[$i])) $arr_column_aggreg[$i] = $arr_text[$i];
							} // end loop for
						}
						/**************************************************************************/
						/****** compute argregate function สำหรับการ merge ค่าตัวเลข *********/
						/****************************** End Block ********************************/

//						echo "2..aggreg=".implode(",",$arr_column_aggreg)." , func=".implode(",",$arr_func_aggreg)." , sum=".implode(",",$arr_sum)."<br>";

						/***************************************************************************/
						/******************* รอบสองแปลง format  TNUM , ENUM *****************/
						/***************************************************************************/
						for($kk=0; $kk < count($arr_func_aggreg); $kk++) {
							if ($arr_column_sel[$arr_column_map[$kk]]==1) {	// 1 = แสดง column นี้
//								echo "function aggregate $kk-(map=".$arr_column_map[$kk].")-".$arr_func_aggreg[$arr_column_map[$kk]]."<br>";
								$a_sub_func = explode("|",$arr_func_aggreg[$arr_column_map[$kk]]);
								for($ii = 0; $ii < count($a_sub_func); $ii++) {
//									echo "a_sub_func [$ii]=".$a_sub_func[$ii]."<br>";
									$c = strpos($a_sub_func[$ii],"TNUM");
									if ($c !== false) {
										$ndata = $arr_column_aggreg[$arr_column_map[$kk]];
//										echo "TNUM - $ii - ".$a_sub_func[$ii]." , $ndata<br>";
										if ($ndata==chr(96)) {	 // chr(96) แทนช่องว่าง
											$lastval = " ";
//											echo "TNUM space ($ndata) (".chr(96).") logic(".($ndata==chr(96)).")<br>";
										} else {
//											echo "TNUM $ndata<br>";
											$zero = (string)substr($a_sub_func[$ii], 4, 1);
											if ($zero=="0")  $c1=$c+6;
											else {
//												if (!$ndata) echo "null data [$ndata] (is_numeric:".is_numeric($ndata).") (float:".((float)$ndata==0).") (".($ndata=="").")<br>";
												if (is_numeric($ndata) && (float)$ndata==0) $ndata="-";
//												else if ($ndata=="")
												$c1=$c+5;
											}
											$nocomma = (strtolower(substr($a_sub_func[$ii], $c1,1))=="x"?true:false);
//											echo "function-".$a_sub_func[$ii]." nocomma=$nocomma<br>";
											if ($nocomma) 
												$dig = substr($a_sub_func[$ii], $c1+1);
											else
												$dig = substr($a_sub_func[$ii], $c1);
											if (!is_numeric($ndata)) 
												$lastval = convert2thaidigit($ndata);
											else if ((int)$dig > 0)
												if ($nocomma)
													$lastval = convert2thaidigit($ndata,$dig);
												else
													$lastval = convert2thaidigit(number_format($ndata,$dig));
											else {
												if ($dig == "z") {
													$numd = explode(".",$ndata);
													$digstr = $numd[1];
													$ndig = 0;
													for($i=strlen($digstr)-1; $i >= 0; $i--) {
														if (substr($digstr,$i,1)!="0") { $ndig = $i+1; break; }
													}
//													echo "first part:".$numd[0]." second part:".$numd[1]." ndig:$ndig<br>";
													if ($ndig == 0) 
														if ($nocomma)
															$lastval = convert2thaidigit($ndata);
														else
															$lastval = convert2thaidigit(number_format($ndata));
													else
														if ($nocomma)
															$lastval = convert2thaidigit($ndata,$ndig);
														else
															$lastval = convert2thaidigit(number_format($ndata,$ndig));
												} else 
													if ($nocomma)
														$lastval = convert2thaidigit($ndata);
													else
														$lastval = convert2thaidigit(number_format($ndata));
											}
										}
										$arr_column_aggreg[$arr_column_map[$kk]] = str_replace("*Enter*","\n",$lastval);
//										echo "function TNUM $kk-(map=".$arr_column_map[$kk].")-tnum-".$lastval.", ndata=$ndata, dig=$dig<br>";
									} else {
										$c = strpos($a_sub_func[$ii],"ENUM");
										if ($c !== false) {
											$ndata = $arr_column_aggreg[$arr_column_map[$kk]];
//											echo "ENUM - $ii - ".$a_sub_func[$ii]." , $ndata<br>";
											if ($ndata==chr(96)) {  // chr(96) แทนช่องว่าง
												$lastval = " ";
											} else {
												$zero = (string)substr($a_sub_func[$ii], 4, 1);
												if ($zero=="0")  $c1=$c+6;
												else {
													if (is_numeric($ndata) && (float)$ndata==0) $ndata="-";
													$c1=$c+5;
												}
												$nocomma = (strtolower(substr($a_sub_func[$ii], $c1,1))=="x"?true:false);
//												echo "function-".$a_sub_func[$ii]." nocomma=$nocomma<br>";
												if ($nocomma) 
													$dig = substr($a_sub_func[$ii], $c1+1);
												else
													$dig = substr($a_sub_func[$ii], $c1);
												if (!is_numeric($ndata)) 
													$lastval = $ndata;
												else if ((int)$dig > 0)
													if ($nocomma)
														$lastval = $ndata;
													else
														$lastval = number_format($ndata,$dig);
												else {
													if ($dig == "z") {
														$numd = explode(".",$ndata);
														$digstr = $numd[1];
														$ndig = 0;
														for($i=strlen($digstr)-1; $i >= 0; $i--) {
															if (substr($digstr,$i,1)!="0") { $ndig = $i+1; break; }
														}
//														echo "first part:".$numd[0]." second part:".$numd[1]." ndig:$ndig<br>";
														if ($ndig == 0) 
															if ($nocomma)
																$lastval = $ndata;
															else
																$lastval = number_format($ndata);
														else
															if ($nocomma)
																$lastval = $ndata;
															else
																$lastval = number_format($ndata,$ndig);
													} else 
														if ($nocomma)
															$lastval = $ndata;
														else
															$lastval = number_format($ndata);
												}
											}
											$arr_column_aggreg[$arr_column_map[$kk]] = str_replace("*Enter*","\n",$lastval);
//											echo "function enum-".$lastval.", ndata=$ndata, dig=$dig<br>";
//											echo "function ENUM $kk-(map=".$arr_column_map[$kk].")-tnum-".$lastval.", ndata=$ndata, dig=$dig<br>";
										} // end if ENUM
									} // end if TNUM
								} // end for loop $ii
							} // end if ($this->arr_column_sel[$iii]==1)
						} // end loop for $kk
						/***************************************************************************/
						/******************* รอบสองแปลง format  TNUM , ENUM *****************/
						/******************************* End Block ********************************/

//						echo "3..aggreg=".implode(",",$arr_column_aggreg)."<br>";
					
					} // end if if (count($arr_func_aggreg) > 0)

					/***************************************************************************/
					/************ ทำการ Merge column และ Write ออก WorkSheet ************/
					/***************************************************************************/
					$cntcolumn = count($arr_column_aggreg);
					
					if ($cntcolumn > 0) {
						$cntshow = 0;
						for($i=0; $i < $cntcolumn; $i++) {
							if ($arr_column_aggreg[$i])
								$arr_column_aggreg[$i] = $arr_grp[$i].$arr_column_aggreg[$i];
							else $arr_column_aggreg[$i] = $arr_grp[$i];
							if ($arr_column_sel[$arr_column_map[$i]]==1) $cntshow++;
						}
						
//						echo "@@@1..".implode(",",$arr_column_aggreg)."<br>";
						
						$pgrp = "";  $last_align = "";  $last_val = "";
						$colseq = 0;
						$arr_dat = (array) null;
						$arr_attr = (array) null;
						for($i=0; $i < $cntcolumn; $i++) {
							if ($arr_column_sel[$arr_column_map[$i]]==1) {
								if ($arr_column_aggreg[$i]) $ndata = $arr_column_aggreg[$i];	// arr_aggreg_data ผ่านการ map มาเรียบร้อยแล้ว
								else $ndata = $arr_data[$arr_column_map[$i]];

								$buff = explode("|",doo_merge_cell($ndata, $arr_border[$arr_column_map[$i]], $arr_merge[$arr_column_map[$i]], $pgrp, ($colseq == $cntshow-1), $arr_align[$arr_column_map[$i]]));
/*								if ($pgrp != $buff[3])	{ // ถ้าเปลี่ยนกลุ่ม
									if (!$buff[3])	{  // ถ้ารายการนี้ไม่เป็นกลุ่ม แต่รายการก่อนเป็นกลุ่ม แสดงว่าจบกลุ่มก่อน เริ่ม column ใหม่ที่ไม่ใช่กลุ่ม
										if ($last_align == "R")	 // จบกลุ่มก่อน และ align ที่แล้ว เป็น "R" จะเก็บค่าที่ ค่าสุดท้ายทางขวาของกลุ่ม
											$arr_dat[$i-1] = $last_val;
										$last_val = "";
										$last_align = "";
										$ndata = $buff[0];
									} else {	// ถ้ารายการนี้เป็นกลุ่ม แสดงว่า ขึ้นกลุ่มใหม่
										if ($pgrp && $last_align == "R") {	 // กรณีที่ align ตัวก่อน เป็น "R" และข้อมูลก่อนหน้าเป็นข้อมูลกลุ่ม จะเก็บค่าที่ ค่าสุดท้ายทางขวาของกลุ่ม
											$arr_dat[$i-1] = $last_val;
											$ndata = "";
										} else {	 // กรณีที่ ข้อมูลก่อนหน้าไม่เป็นข้อมูลกลุ่ม หรือ ก่อนหน้าเป็นกลุ่ม แต่ align ก่อนหน้าไม่เป็น "R"
											$ndata = $buff[0];
										}
										$last_val = $buff[0];
										$last_align = $arr_align[$arr_column_map[$i]];
									}
								} else if ($buff[3]) {	// ถ้าเป็นกลุ่มเดิม
									$ndata = $buff[0];
								} else { // ถ้าไม่เป็นกลุ่ม
									$ndata = $buff[0];
								}*/
								if ($last_align == "R")	{ // ถ้าเป็นกลุ่มมาก่อน และมี align เป็น "R"
									if ($pgrp != $buff[3])	{ // ถ้าเปลี่ยนกลุ่ม
										if (!$buff[3])	{  // ถ้าเปลี่ยนกลุ่มไปเป็นแบบไม่เป็นกลุ่ม
											$arr_dat[$i-1] = $last_val;
											$last_val = "";
											$last_align = "";
											$ndata = $buff[0];
//											echo "1<br>";
										} else {	// ถ้าเปลี่ยนกลุ่ม ขึ้นกลุ่มใหม่
											$arr_dat[$i-1] = $last_val;
											$last_val = $buff[0];
											$last_align = $arr_align[$arr_column_map[$i]];
											if ($last_align == "R")
												$ndata = "";
											else $ndata = $buff[0];
//											echo "2, last_align=$last_align<br>";
										}
									} else {
										if ($pgrp) // ถ้าเป็น กลุ่มเดิมต่อเนื่อง และ align เป็น R
											$ndata = "";
										else $ndata = $buff[0]; // ไม่เป็นกลุ่ม ก่อนหน้าก็ไม่เป็นกลุ่ม
//										echo "3, last_align=$last_align<br>"; 
									}
								} else {	// ถ้า $last_align ไม่ใช่ "R"
									if ($pgrp != $buff[3])	{ // ถ้าเปลี่ยนกลุ่ม ขึ้นกลุ่มใหม่ กรณีนี้ $pgrp ต้องไม่มีค่า
										$last_val = $buff[0];
										$last_align = $arr_align[$arr_column_map[$i]];
										if ($arr_align[$arr_column_map[$i]]=="R" && $buff[3])	{	// ถ้าเริ่มกลุ่ม และเป็น "R"
											$ndata = "";
//											echo "5, align=R && ".$buff[3]."<br>";
										} else { 
											$ndata = $buff[0]; 
//											echo "6 <br>"; 
										}
									} else { 
										$ndata = $buff[0]; 
//										echo "7 <br>"; 
									}
								}
								$border1 = $buff[1]; $colmerge1 = $buff[2]; $pgrp = $buff[3];

								$arr_dat[] = $ndata;
								$arr_attr[] = $arr_fontFmt[$arr_column_map[$i]]."|".$arr_align[$arr_column_map[$i]]."|".$border1."|".$colmerge1."|".($arr_wraptext[$arr_column_map[$i]]?$arr_wraptext[$arr_column_map[$i]]:0)."|".($arr_rotate[$arr_column_map[$i]]?$arr_rotate[$arr_column_map[$i]]:0)."|".$arr_color[$arr_column_map[$i]]."|".$arr_bgcolor[$arr_column_map[$i]];
//								echo "$i..map=".$arr_column_map[$i].".. data ($ndata) ..align=".$arr_align[$arr_column_map[$i]]."..border=".$border1."..merge=".$colmerge1."..rotate=".($arr_rotate[$arr_column_map[$i]]?$arr_rotate[$arr_column_map[$i]]:0)."<br>";
								$colseq++;
							}
						} // end for loop $i

//						echo "@@@2..dat=".implode(",",$arr_dat)." attr=".implode(",",$arr_attr)."<br>";
						
						$colseq = 0;
						$xlsfmt_cnt = 0;
						for($i=0; $i < count($arr_dat); $i++) {
							$ndata = $arr_dat[$i];
							$buff = explode("|",$arr_attr[$i]);
							$fontFmt = $buff[0]; $align1 = $buff[1]; $border = $buff[2]; $merge = $buff[3]; $wraptext = $buff[4]; $rotate = $buff[5]; $color = $buff[6]; $bgcolor = $buff[7];
							// valign = top, vcenter, bottom, vjustify, vequal_space.
							if (strtolower($align1)=="cc") { $align="C"; $valign="vcenter"; }
							else if (strtolower($align1)=="c") { $align="C"; $valign="vcenter"; }
							else {
								// align
								if (strpos(strtolower($align1),"l")!==false) { $align="L"; }
								else if (strpos(strtolower($align1),"r")!==false) { $align="R"; }
								else if (strpos(strtolower($align1),"c")!==false) { $align="C"; }
								// valign
								if (strpos(strtolower($align1),"t")!==false) { $valign="top"; }
								else if (strpos(strtolower($align1),"b")!==false) { $valign="bottom"; }
								else if (strpos(strtolower($align1),"j")!==false) { $valign="vjustify"; }
								else if (strpos(strtolower($align1),"s")!==false) { $valign="vequal_space"; }
								else if (strpos(strtolower($align1),"c")!==false) { $valign="vcenter"; }
							}
//							echo "..align..(".$align.").. valign..(".$valign.")<br>";

							$xlsfmt_cnt++;
							$buff = explode("^", get_format_font($xlsFmt, $fontFmt, $align, $border));
//							echo "format ($xlsFmt)=".(implode(",",$buff))."<br>";
							$xfont = $buff[0];
							$xfontsize = $buff[1];
//							echo "bf...$i--font=".$xfont." size=".$xfontsize." fgcolor=".$color." bgcolor=".$bgcolor." wraptext=$wraptext<br>";
							if (!$color) $color=$buff[2];
							if (!$color) $color=0;
							if (!$bgcolor) $bgcolor=$buff[3];
							if (!$bgcolor) $bgcolor=0;
//							echo "af...$i--font=".$xfont." size=".$xfontsize." fgcolor=".$color." bgcolor=".$bgcolor." wraptext=$wraptext<br>";
//							if ($wraptext==1) {
								$para_format = "alignment=$align&border=$border&isMerge=$merge";
								$para_format .= "&fontName=$xfont&fontSize=$xfontsize&fontStyle=".$fontFmt."&wrapText=$wraptext&setRotation=$rotate";
								$para_format .= "&fgColor=$color&bgColor=$bgcolor";
								$para_format .= "&valignment=$valign";
//								echo "para_format=$para_format<br>";
								${"thisFormat".$xlsfmt_cnt} = set_free_format_new( $workbook, $para_format );
//							}
							$worksheet->write($xlsRow, $colseq, $ndata, ${"thisFormat".$xlsfmt_cnt});

//							echo "===>... r=$xlsRow , c=$colseq , wraptext=$wraptext , rotate=$rotate , data=".str_replace("\n"," ",$ndata)."<br>";
//							echo "===>... r=$xlsRow , c=$colseq , wraptext=$wraptext , rotate=$rotate<br>";
//							$spec_fmt = set_format($xlsFmt, $fontFmt, "T".$align, $border, $merge, $wraptext, $rotate, $color, $bgcolor);
/*							$spec_fmt->set_align("top");
							if ((int)$rotate > 0) {
								$spec_fmt->set_rotation((int)$rotate); // 1.แนวตั้ง ตัวอักษรปกติ 2. แนวตั้งตัวอักษรนอนซ้าย
//								echo "rotate ... r=$xlsRow , c=$colseq , wraptext=$wraptext , rotate=$rotate , data=$ndata<br>";
							}
							if ($wraptext==1) {
								$spec_fmt->set_text_wrap();
//								echo "wraptext ... r=$xlsRow , c=$colseq , wraptext=$wraptext , rotate=$rotate , data=$ndata<br>";
							}*/
//							$worksheet->write($xlsRow, $colseq, $ndata, $spec_fmt);
//							$worksheet->write($xlsRow, $colseq, $ndata, set_format($xlsFmt, $fontFmt, $align, $border, $merge));
							$colseq++;
						} // end for loop $i
					} // end if ($cntcolumn > 0)
					/***************************************************************************/
					/************ ทำการ Merge column และ Write ออก WorkSheet ************/
					/******************************* End Block ********************************/
					
//					echo "4..aggreg_merge=".implode(",",$arr_aggreg_merge)."<br>";
					// จบการกำหนด agregate function
					$f_return = true;
					
					return $f_return;
			}

?>