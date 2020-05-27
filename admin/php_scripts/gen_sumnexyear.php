<?php 
include("../../php_scripts/connect_database.php");
include("../php_scripts/function_share.php");
//$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
header("content-type: application/x-javascript; charset=TIS-620");
$SESS_PER_ID  	= $_POST["per_id"];
$VC_YEAR  	= $_POST["sen_nex_year"];
  $debug =1;
            //echo $date_now;
            $CHECK_DATE_NEXT_YEAR = ($VC_YEAR-543)."-10-01"; 
            $CHECK_DATE_BEFORE_YEAR = ($VC_YEAR-1-543)."-10-01";
            $sep_CHECK_DATE = ($VC_YEAR-543)."-09-30";
            $A_CHECK_STARTDATE = ($VC_YEAR-543)."-04-01";
            $tmp_vc_year = $VC_YEAR - 1;

            $A_START_DATE = ($VC_YEAR-1-544)."-10-01";/*เดิม*/ //ปี งบ ก่อนหน้า
            $A_END_DATE = ($VC_YEAR-1-543)."-09-30";/*เดิม*/ //ปี งบ ก่อนหน้า

            $VC_START_DATE_1 = ($VC_YEAR -544) . "-10-01"; 
            $VC_END_DATE_1 = $VC_YEAR-543 . "-03-31";

            $VC_START_DATE_2 = ($VC_YEAR-543) . "-04-01"; 
            $VC_END_DATE_2 = $VC_YEAR-543 . "-09-30"; 
            $PER_CARDNOS = trim($data[PER_CARDNO]);
            $PER_STARTDATE_1 = trim($data[PER_STARTDATE]);
            $A_PER_TYPE = trim($data[PER_TYPE]);
            /*==================================== END ========================================*/

			$cmd_con = " select VC_DAY from PER_VACATION where VC_YEAR = '$VC_YEAR'  and PER_ID = $SESS_PER_ID ";
            $db_dpis->send_cmd($cmd_con);
            if($debug==1){echo  '<br> บรรทัดที่ ->'.__LINE__.'<pre>'.$cmd_con."<br>";}
            $data_con = $db_dpis->get_array();
            if($debug==2){echo  '<br> บรรทัดที่ ->'.__LINE__.'VC_DAY:'.$data_con[VC_DAY];}
            if($data_con[VC_DAY] < 1){
                if($debug==1){echo '<br> บรรทัดที่ ->'.__LINE__.'== '.$A_CHECK_STARTDATE." >= ".$PER_STARTDATE_1."<br>";}
                if ($A_CHECK_STARTDATE >= $PER_STARTDATE_1) { /*บรรจุก่อนวันที่ 1 เมษายน*/

                    if($debug==2){echo '<br>บรรทัดที่ ->'.__LINE__.'CHECK_DATE:'.$CHECK_DATE." , PER_STARTDATE:".$PER_STARTDATE_1."<br>";}

                    $AGE_DIFF = date_vacation($CHECK_DATE, $PER_STARTDATE_1, "full");/* เชค ว่า ฟังชันนี้คำนวณอย่างไร*/
                    $arr_temp = explode(" ", $AGE_DIFF);
                    if($debug==2){echo 'บรรทัดที่ ->'.__LINE__.'arr_temp[0]='.$arr_temp[0]."<br>";}
                    if($debug==2){echo 'บรรทัดที่ ->'.__LINE__.'arr_temp[2]='.$arr_temp[2]."<br>";}
                    if($debug==2){echo 'บรรทัดที่ ->'.__LINE__.'arr_temp[4]='.$arr_temp[4]."<br>";}
                    /*เพิ่มเติม*/
                    $AGE_DIFF_NEXT_YEAR = date_vacation($CHECK_DATE_NEXT_YEAR, $PER_STARTDATE_1, "full");
                    $arr_temp_next_year = explode(" ", $AGE_DIFF_NEXT_YEAR);

                    $AGE_DIFF_BEFORE_YEAR = date_vacation($CHECK_DATE_BEFORE_YEAR, $PER_STARTDATE_1, "full");
                    $arr_temp_before_year = explode(" ", $AGE_DIFF_BEFORE_YEAR);

                    $Governor_Age_Sep = 0;
                    $Governor_Age = ($arr_temp[0]*10000)+($arr_temp[2]*100)+(($arr_temp[4]*1));
                    $Governor_Age_next_year = ($arr_temp_next_year[0]*10000)+($arr_temp_next_year[2]*100)+(($arr_temp_next_year[4]*1));
                    $Governor_Age_before = ($arr_temp_before_year[0]*10000)+($arr_temp_before_year[2]*100)+(($arr_temp_before_year[4]*1));

                    $dd=$arr_temp[4];
                    if(strlen($dd)==1){$dd='0'.$dd;}

                    $Leave_Cumulative=0; // def วันลาสะสม
                    if($debug==2){echo 'บรรทัดที่ ->'.__LINE__.'<h1>เช็คอายุราชการว่า ถึง 6 เดือน หรือ มากกว่า 6 เดือนแล้วหรือไม่ </h1><br>';}
					 /*=========================== เช็คอายุราชการถึง 6 เดือน หรือ เกิน 6 เดือน หรือยัง ============================*/
                    if( $Governor_Age_next_year>=600 || $Governor_Age>=600 ){ //อายุราชการมากกว่าหรือเท่ากับ 6 เดือนหรือไม่ m dd
                        
						// เช็คว่า มีข้อมูลปีที่แล้วหรือยัง
						$tmpVC_YEAR=$VC_YEAR-1;
						$cmd = " select VC_DAY from PER_VACATION 
							where VC_YEAR = '$tmpVC_YEAR'  and PER_ID = $PER_ID AND VC_DAY>0 ";
						$count_data = $db_dpis1->send_cmd($cmd);
						if($debug==1){echo __LINE__.':select = '.$cmd.'<br><br>';}
		
						if ($count_data){
							if($arr_temp[0]==0){ //หลักเดือน
								$cmd = " SELECT AC_DAY, AC_COLLECT FROM PER_ABSENTCOND 
													  WHERE AC_FLAG = 1 AND ($arr_temp[2]/12) < AC_GOV_AGE ";
								$db_dpis1->send_cmd($cmd);
								$data1 = $db_dpis1->get_array();
								$AC_DAY = $data1[AC_DAY];
								$AC_COLLECT = $data1[AC_COLLECT];
								if($debug==1){echo __LINE__.': AC_COLLECT=>'.$AC_COLLECT.'==><pre>'.$cmd.'<br>';}
							}
							if($arr_temp[0]>=1 && $arr_temp[0]<=10){ //between 1-10
								 $AC_DAY = 10;
								 $AC_COLLECT = 20;
								 if($A_PER_TYPE==4){ // ลูกจ้างชั่วคราว ไม่มีสะสม ตัดทิ้งหมด
									$AC_COLLECT = 10;
								 }
								 $employees_year_up=TRUE;
							}
							if($arr_temp[0]>10){ //10up
								 $AC_DAY = 10;
								 $AC_COLLECT = 30;
								 if($A_PER_TYPE==4){ // ลูกจ้างชั่วคราว ไม่มีสะสม ตัดทิ้งหมด
									$AC_COLLECT = 10;
								 }
								 $employees_year_up=TRUE;
							}
						}else{
											
							if(strlen ($arr_temp[2])==1){$XarrM= '0'.$arr_temp[2];}else{$XarrM= $arr_temp[2];}
								$xYM = (int) $arr_temp[0].'.'.$XarrM;
								if($xYM < 0.06){ //หลักเดือน
									$AC_DAY = 0;
									$AC_COLLECT = 0;
									//echo '<br>น้อยกว่า 6 เดือน AC_DAY='.$AC_DAY.'|| AC_COLLECT'.$AC_COLLECT.'<br>';
								}
								if($xYM >= 0.06){ // มากกว่า 6 เดือน น้อยกว่า 1 ปี
									$AC_DAY = 10;
									$AC_COLLECT = 10;
									//echo '<br>6 เดือนน้อยกว่า 1 ปี AC_DAY='.$AC_DAY.'|| AC_COLLECT'.$AC_COLLECT.'<br>';
								}
		
								if(($xYM >= 1)  && $arr_temp[0]<10){ //between 1 ปี -10 ปี
									 $AC_DAY = 10;
									 $AC_COLLECT = 20;
									 if($A_PER_TYPE==4){ // ลูกจ้างชั่วคราว ไม่มีสะสม ตัดทิ้งหมด
										$AC_COLLECT = 10;
									 }
									 //echo '<br>1 ปีน้อยกว่า 10 ปี AC_DAY='.$AC_DAY.'|| AC_COLLECT'.$AC_COLLECT.'<br>';
									 $employees_year_up=TRUE;
								}
								if($arr_temp[0]>=10){ //10up
									 $AC_DAY = 10;
									 $AC_COLLECT = 30;
									 if($A_PER_TYPE==4){ // ลูกจ้างชั่วคราว ไม่มีสะสม ตัดทิ้งหมด
										$AC_COLLECT = 10;
									 }
									 //echo '<br> 10 ปีขึ้นไป AC_DAY='.$AC_DAY.'|| AC_COLLECT'.$AC_COLLECT.'<br>';
									 $employees_year_up=TRUE;
								}
							
							
						}	
							
                        $Leave_Cumulative=10; //A
                        /*กำหนดค่าเริ่มต้น*/
                        if($Governor_Age<600){ //น้อยกว่า 6 เดือน
                           $Leave_Cumulative=0;					
                        }
                        $Before_vc_day=0;//N

                        if($debug==1){echo __LINE__.':Governor_Age '.$Governor_Age.'<br>';}
                        if($Governor_Age>600){ //Y
                           //มี Record เก่าตั้งต้น?ปีที่แล้ว
                            $cmd = " select VC_DAY from PER_VACATION where VC_YEAR='$tmp_vc_year'and PER_ID=$SESS_PER_ID ";
                            if($debug==1){echo __LINE__.':<pre>'.$cmd.'<br>';}
                            $cnt=$db_dpis1->send_cmd($cmd);
                            //$Before_vc_day=10;//N //Comment Release 5.2.1.19 20180403 
                            if($cnt){//Y
                                $data1 = $db_dpis1->get_array();
                                $Before_vc_day = $data1[VC_DAY]; 
                            }else{
                                // เก่าแก้โดย Kiitphat if($Governor_Age>=1600){ //between 1 to 10 =20 day
                                if($Governor_Age>=1600 && $Governor_Age < 100000){ //between 1 to 10 =20 day
                                    $Before_vc_day = 20; 
									if($A_PER_TYPE==4){ // ลูกจ้างชั่วคราว ไม่มีสะสม ตัดทิ้งหมด
										$AC_COLLECT = 10;
									 }
                                }elseif($Governor_Age>=100000){ //10up= 30 day
                                    $Before_vc_day = 30; 
									if($A_PER_TYPE==4){ // ลูกจ้างชั่วคราว ไม่มีสะสม ตัดทิ้งหมด
										$AC_COLLECT = 10;
									 }
                                }
                            }
                        }
                        //จำนวนลาพักผ่อนของปีงบก่อนหน้า
                        $sum_absday= get_sum_absday($db_dpis1,$SESS_PER_ID,$A_START_DATE,$A_END_DATE);
                        if($debug==1){echo __LINE__.':sum_absday==>'.$sum_absday.'<br>';}
                        //สรุปวันลาและวันลาพักผ่อนสะสมของปีงบก่อนหน้า
                        $cmd = " select sum(AB_CODE_04) as abs_day from PER_ABSENTSUM 
                                 where PER_ID=$SESS_PER_ID and START_DATE >= '$A_START_DATE' and END_DATE <= '$A_END_DATE' ";
                        $db_dpis1->send_cmd($cmd);
                        if($debug==1){echo __LINE__.':<pre>'.$cmd.'<br>';}
                        $data1 = $db_dpis1->get_array();
                        $data1 = array_change_key_case($data1, CASE_LOWER);
						
                        if($A_PER_TYPE==4){ // ลูกจ้างชั่วคราว ไม่มีสะสม ตัดทิ้งหมด
							$Before_abs_day_vacation = 0; 
						}else{
							$Before_abs_day_vacation = $data1[abs_day]+0; 
						}
                        if($debug==1){echo __LINE__.':Before_abs_day_vacation==>'.$Before_abs_day_vacation.'<br>';}

                        $Before_vc_day=$Before_vc_day-($sum_absday+$Before_abs_day_vacation);

                        if($debug==1){echo __LINE__.':<font color=red>'.$Before_vc_day.'='.$Before_vc_day.'-('.$sum_absday.'+'.$Before_abs_day_vacation.')</font><br>';}

                        if($debug==1){echo __LINE__.':Before_vc_day==>'.$Before_vc_day.','.$Before_abs_day_vacation.'<br>';}
                        if($debug==1){echo __LINE__.':'.$CHECK_DATE_NEXT_YEAR.' อายุราชการปีที่คลิกประมวลผล ==>'.$Governor_Age.'<br>';}

                        if($Before_vc_day>0){
                            /**/
                            if($debug==1){echo __LINE__.':Before_vc_day==>'.$Before_vc_day.','.$Before_abs_day_vacation.'<br>';}
                            $Leave_Cumulative=$Leave_Cumulative+$Before_vc_day;


                            /**/

                        }
                         if($debug==1){echo __LINE__.':Leave_Cumulative==>'.$Leave_Cumulative.'<br>';}
                    }else{ // N
                        $Leave_Cumulative=0;
                    }
					if($Governor_Age>=100000){ // 10up
                        $AC_COLLECT = 30;
						if($A_PER_TYPE==4){ // ลูกจ้างชั่วคราว ไม่มีสะสม ตัดทิ้งหมด
							$AC_COLLECT = 10;
						 }
                    }
                    if($debug==1){echo __LINE__.':'.$Governor_Age.'>=600 && '.$Leave_Cumulative.'>'.$AC_COLLECT.'<br>';}
                    if($Governor_Age>=600 && $Leave_Cumulative>$AC_COLLECT ){ //อายุราชการมากกว่าหรือเท่ากับ 10 ปีหรือไม่ yy mm dd
                        $Leave_Cumulative=$AC_COLLECT;// 30 สะสมสูงสุดของแต่ละเงื่อนไข
                    }
                    if($debug==2){
                        echo '<br>====================<br><h1>อายุราชการ: '.$Governor_Age.' ,วันลาสะสมที่ควรได้:'.$Leave_Cumulative.' สะสมสูงสุด ['.$AC_COLLECT.']</h1><br>====================<br>';
                    }
                    /*===================================================================================*/
                    if($debug==1){echo __LINE__.':$A_PER_TYPE = '.$A_PER_TYPE.'<br><br>';}
                    if($A_PER_TYPE==3){
                        if($employees_year_up==TRUE){
                            if($Leave_Cumulative>15){
                                $AC_DAY = 15;
                            }else{
                                $AC_DAY = $Leave_Cumulative;
                            }

                        }else{
                            $AC_DAY = $AC_DAY;
                        }

                        if($debug==1){echo __LINE__.':AC_DAY = '.$AC_DAY.'<br><br>';}
                        if($debug==1){echo __LINE__.':อายุพนักงานราชการGovernor_Age = '.$Governor_Age.'<br><br>';}

                    }else{
                        $AC_DAY=$Leave_Cumulative;
                    }
                    if($Governor_Age<600){ //น้อยกว่า 6 เดือน

                       $AC_DAY=0;

                    }
                    $cmd = " select VC_DAY from PER_VACATION where VC_YEAR = '$VC_YEAR'  and PER_ID = $SESS_PER_ID AND VC_DAY>0   ";
                    $chk_count_data = $db_dpis->send_cmd($cmd);
                    if($debug==1){echo '<br> บรรทัดที่ ->'.__LINE__.':select = '.$cmd.'<br><br>';}
                    if (!$chk_count_data) {
						$cmd = " delete from PER_VACATION where VC_YEAR = '$VC_YEAR'  and PER_ID = $SESS_PER_ID ";
						$db_dpis->send_cmd($cmd);
										
                        $cmd = " insert into PER_VACATION (VC_YEAR, PER_ID, VC_DAY, UPDATE_USER, UPDATE_DATE) 
                                                            values ('$VC_YEAR', $SESS_PER_ID, $AC_DAY, $SESS_USERID, '$UPDATE_DATE') ";
                        $db_dpis->send_cmd($cmd);
                        if($debug==1){echo '<br> บรรทัดที่ ->'.__LINE__.':insert = '.$cmd.'<br><br>';}
                        //insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($VC_YEAR)." : ".$SESS_PER_ID."]");
						
						
                    }
                }
				
				// kittiphat comment 09/10/2561
                $cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$SESS_PER_ID and AS_YEAR = '$VC_YEAR' and AS_CYCLE = 1 ";
                $count=$db_dpis->send_cmd($cmd);
                if($debug==1){echo '<br> บรรทัดที่ ->'.__LINE__.':'.$cmd.'<br><br>';}
                //$db_dpis->show_error(); echo "<hr>$cmd<br>";
                if(!$count) { 
                    $cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
                                                        values ($AS_ID, $SESS_PER_ID, '$VC_YEAR', 1, '$VC_START_DATE_1', '$VC_END_DATE_1', '$PER_CARDNOS', $SESS_USERID, '$UPDATE_DATE') ";
                    $db_dpis->send_cmd($cmd);
                    if($debug==1){echo '<br> บรรทัดที่ ->'.__LINE__.': insert = '.$cmd.'<br><br>';}
                    //$db_dpis->show_error();
                    $AS_ID++;
                }
                $cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$SESS_PER_ID and AS_YEAR = '$VC_YEAR' and AS_CYCLE = 2 ";
                $count=$db_dpis->send_cmd($cmd);
                if($debug==1){echo '<br> บรรทัดที่ ->'.__LINE__.':'.$cmd.'<br><br>';}
                //$db_dpis->show_error(); echo "<hr>$cmd<br>";
                if(!$count) { 
                    $cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
                                                    values ($AS_ID, $SESS_PER_ID, '$VC_YEAR', 2, '$VC_START_DATE_2', '$VC_END_DATE_2', '$PER_CARDNOS', $SESS_USERID, '$UPDATE_DATE') ";
                    $db_dpis->send_cmd($cmd);
                    if($debug==1){echo '<br> บรรทัดที่ ->'.__LINE__.': insert = '.$cmd.'<br><br>';}
                    //$db_dpis->show_error();
                    $AS_ID++;
                }
				
            }
        }
        /*=================================================================================================*/

 echo json_encode($data); 
?>