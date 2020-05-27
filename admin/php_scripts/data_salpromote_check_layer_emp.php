<?
$tmp_count_salpromote = $count_layer_no = $LAYER_NO = $PG_CODE = "";
$LAYERE_SALARY = $LAYERE_SALARY_MIN = $LAYERE_SALARY_MAX = $UP_SALP_LAYER = $SALP_SALARY_NEW = 0;
$MAX_LAYERE_NO = $MAX_LAYERE_SALARY = 0;
// เช็ค PG_CODE ว่ารับเงินเดือนในระดับ ของลูกจ้างประเภทไหน
/*Query เดิม*/
/*$cmd = "	select b.PG_CODE, b.PG_NAME_SALARY, c.POEM_NO, c.POEM_NO_NAME, c.PN_CODE  
		from PER_POS_GROUP b, PER_POS_EMP c
		where POEM_ID=$POEM_ID and trim(c.PG_CODE_SALARY)=trim(b.PG_CODE) ";	
$db_dpis1->send_cmd($cmd);
$data1 = $db_dpis1->get_array();
$PG_CODE = trim($data1[PG_CODE]);
$PG_NAME = trim($data1[PG_NAME_SALARY]);
$POEM_NO = trim($data1[POEM_NO]);
$POEM_NO_NAME =  trim($data1[POEM_NO_NAME]);
$PN_CODE = trim($data1[PN_CODE]);*/

$cmd = "	select  c.POEM_NO, c.POEM_NO_NAME, c.PN_CODE, f.GROUP_SALARY, f.MAX_SALARY, f.UP_SALARY, f.MIN_SALARY
		from  PER_POS_EMP c, PER_POS_LEVEL_SALARY f
		where POEM_ID=$POEM_ID and trim(c.PN_CODE)= trim(f.PN_CODE) and trim(f.LEVEL_NO)='$LEVEL_NO' ";	
$db_dpis1->send_cmd($cmd);
$data1 = $db_dpis1->get_array();
$PG_CODE = trim($data1[PG_CODE]);
$PG_NAME = trim($data1[PG_NAME_SALARY]);
$POEM_NO = trim($data1[POEM_NO]);
$POEM_NO_NAME =  trim($data1[POEM_NO_NAME]);
$PN_CODE = trim($data1[PN_CODE]);
$PN_CODE_CH = trim($data1[PN_CODE]);
$GROUP_SALARY = trim($data1[GROUP_SALARY]);
$UP_SALARY = trim($data1[UP_SALARY]);
$MAX_CH_SALARY = trim($data1[MAX_SALARY]);
$MIN_CH_SALARY = trim($data1[MIN_SALARY]);

if($EXTRA_CH == "CHANGE"){ 
        //ถ้ามีเศษ ให้ปัดขึ้นเสมอ เช่น 840.2 = 850
            $before_MOD_CMD_SALARY = $SAH_SALARY + $SAH_SALARY_EXTRA;
            $MOD_CMD_SALARY = fmod($before_MOD_CMD_SALARY,10);
            if($MOD_CMD_SALARY!=0){
             $test_SUM_SALARY = ($before_MOD_CMD_SALARY-$MOD_CMD_SALARY)+10;
           //echo "(".$before_MOD_CMD_SALARY." - ".$MOD_CMD_SALARY.")"." + "."10"."<br>";
            }  else {
            $test_SUM_SALARY = $SAH_SALARY;
            }//end ปัดเศษ
            
          if($SAH_SALARY){
            $PER_SALARY =  $test_SUM_SALARY;
           // echo "[$PER_NAME]--> ".$PER_SALARY."<br>";
          }else{
            $PER_SALARY = $PER_SALARY;
          }
 }//EXTRA_CH
 //echo "[] ".$PER_NAME."----->>>>".$PER_SALARY."<br>";

  // ตรวจสอบเงินเดือนว่าอยู่ในแท่ง และขั้นที่กำหนดหรือไม่ ถ้าไม่อยู่ต้องแสดง alert ให้ไปแก้ไขก่อน
  $CN_GROUP_SALARY = explode("," ,$GROUP_SALARY); //นำกลุ่มบัญชีที่รับเงิน มาแปลงค่า
  $cnt=count($CN_GROUP_SALARY);
  $val_pg_code='';
  unset($arr_pg_code);
  for($idx=0;$idx<$cnt;$idx++){
      $code_gr=$CN_GROUP_SALARY[$idx]*1000;
      if($code_gr!=0){
          $arr_pg_code[]=$code_gr;
      }
  }
  /*Query เดิม*/
  /*select LAYERE_NO, LAYERE_SALARY from PER_LAYEREMP  
where PG_CODE='$PG_CODE' and LAYERE_SALARY= $PER_SALARY order by LAYERE_NO
$tmp_count_salpromote = $db_dpis1->send_cmd($cmd);
$db_dpis1->send_cmd($cmd);*/
 
$val_pg_code = implode(',',$arr_pg_code);
$var_pg = end($CN_GROUP_SALARY)*1000;

$PG_CODE_VAR = $var_pg + 1000;
if($PG_CODE_VAR == 5000) $PG_CODE_VAR = 4000;  
//select เพื่อหาขั้นสุดกลุ่มบัญชี
$cmd = "select LAYERE_NO, LAYERE_SALARY from PER_LAYEREMP  
			where PG_CODE in($val_pg_code) order by LAYERE_SALARY desc";
	$db_dpis1->send_cmd($cmd);
	$data1 = $db_dpis1->get_array();
	$MAX_SALARY_EMP = $data1[LAYERE_SALARY];
    $LAYERE_NO_EMP = $data1[LAYERE_NO];
        
//หาขั้นเงินที่ได้จาก เงินเดือนปัจจุบัน ของลูกจ้างประจำ 
$cmd = "select LAYERE_NO, PG_CODE, LAYERE_SALARY  from PER_LAYEREMP 
                    where PG_CODE in($val_pg_code) and LAYERE_SALARY >= $PER_SALARY  "; 
                    $db_dpis1->send_cmd($cmd);
                    $data1 = $db_dpis1->get_array();
                    $SALP_SALARY_ST = $data1[LAYERE_SALARY];
                    $LAYERE_NO_ST = $data1[LAYERE_NO]+$SALP_LEVEL;
 
    if(!$SALP_SALARY_ST){
        $cmd = "select LAYERE_NO, PG_CODE, LAYERE_SALARY  from PER_LAYEREMP 
                    where PG_CODE in($PG_CODE_VAR) and LAYERE_SALARY >= $PER_SALARY  "; 
                    $db_dpis1->send_cmd($cmd);
                    $data1 = $db_dpis1->get_array();
                    $SALP_SALARY_ST = $data1[LAYERE_SALARY];
                    $LAYERE_NO_ST = $data1[LAYERE_NO];
		//echo "<pre>".$cmd.$PER_NAME;			
    }
if( $MAX_CH_SALARY >= $MAX_SALARY_EMP ){ 
//echo $SALP_SALARY_ST .">=". $UP_SALARY."=".$PER_NAME."<br>";
    if( $SALP_SALARY_ST <= $UP_SALARY ){ 
       $tmp_count_salpromote = 1;
    }else{
    
        if(!$SALP_SALARY_ST|| $SALP_SALARY_ST > $UP_SALARY ){
        //echo $PER_NAME."||".$SALP_SALARY_ST."||".$UP_SALARY."==> ".$val_pg_code."<br>";
         $err_count++;
            $alert_err = true;
            $alert_err_text .= "<tr class='$class' $onmouse_event  style='color:#FF0000'><td align=\"center\">$err_count</td>
            <td>อัตราเงินเดือนไม่อยู่ในแท่งและขั้นที่กำหนด</td>
            <td align=\"center\">".$POEM_NO_NAME.$POEM_NO."</td><td>$PER_NAME</td>
            <td> กลุ่มที่ $GROUP_SALARY : เงินเดือน $PER_SALARY</td>
            <td align=\"center\">&nbsp;<a href='javascript:call_edit_personal($PER_ID);'><img src=\"images/b_edit.png\" border=\"0\" alt=\"แก้ไขข้อมูลบุคคล\"></a></td></tr>";

        }else{
            $data1 = $db_dpis1->get_array();
            $LAYERE_NO = $data1[LAYERE_NO];
            $LAYERE_SALARY = $data1[LAYERE_SALARY];
        }
    }
   
} else{   
  //echo $PER_NAME."||".$PER_SALARY."||".$MAX_CH_SALARY."==> ".$val_pg_code."<br>";
     $cmd = "select LAYERE_NO, LAYERE_SALARY, PG_CODE from PER_LAYEREMP 
              where  PG_CODE in($val_pg_code)and LAYERE_SALARY >= $PER_SALARY ";
      $tmp_count_salpromote = $db_dpis1->send_cmd($cmd);
      $db_dpis1->send_cmd($cmd);
      if($PER_SALARY > $UP_SALARY) $tmp_count_salpromote =0;
/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
    if ($tmp_count_salpromote <= 0) {
        $err_count++;
        $alert_err = true;
        $alert_err_text .= "<tr class='$class' $onmouse_event  style='color:#FF0000'><td align=\"center\">$err_count</td>
        <td>อัตราเงินเดือนไม่อยู่ในแท่งและขั้นที่กำหนด</td>
        <td align=\"center\">".$POEM_NO_NAME.$POEM_NO."</td><td>$PER_NAME</td>
        <td> กลุ่มที่ $GROUP_SALARY  : เงินเดือน $PER_SALARY</td>
        <td align=\"center\">&nbsp;<a href='javascript:call_edit_personal($PER_ID);'><img src=\"images/b_edit.png\" border=\"0\" alt=\"แก้ไขข้อมูลบุคคล\"></a></td></tr>";

        } else {
        $data1 = $db_dpis1->get_array();
        $LAYERE_NO = $data1[LAYERE_NO];
        $LAYERE_SALARY = $data1[LAYERE_SALARY];
        }
}               


$count_data=$err_count;
//echo ":: $PER_ID -> $PER_NAME :: count_LAYERE_NO=$count_LAYERE_NO || LAYERE_NO=$LAYERE_NO || LEVEL_NO=$LEVEL_NO || PG_CODE=$PG_CODE<br>";
//echo ">> LAYERE_SALARY=$LAYERE_SALARY || LAYERE_SALARY_MIN=$LAYERE_SALARY_MIN || LAYERE_SALARY_MAX=$LAYERE_SALARY_MAX<br>";

$non_promote = false; 
$non_promote_text = $SALP_REMARK = "";
$SALP_PERCENT = $SALP_SPSALARY = 0;
if(!$alert_err){
	// ตรวจสอบ วันลาติดตามคู่สมรส
	$cmd = " 	select 		PER_ID, sum(ABS_DAY) as DAY_SPOUSE, count(PER_ID) as TIME_SPOUSE 
					from 			PER_ABSENT
					where 		PER_ID=$PER_ID and AB_CODE = '09'
									$search_monthyear 
					group by 	PER_ID
					having 		(sum(ABS_DAY) > 0) or (count(PER_ID) > 0) ";
	$db_dpis1->send_cmd($cmd);
	$data1 = $db_dpis1->get_array();
	$DAY_SPOUSE = $data1[DAY_SPOUSE];
	$TIME_SPOUSE = $data1[TIME_SPOUSE];
	if ( ($DAY_SPOUSE > 365) ) {
	//$db_dpis1->show_error();	
		$non_promote = true;
		$non_promote_text .= "ไม่สามารถเลื่อนขั้นเงินเดือน $PER_NAME ได้ เนื่องจากลาติดมตามคู่สมรส เกินระยะเวลาที่กำหนด<br>";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;	
		$SALP_REASON = 1;				
	}		// 	if ( ($DAY_SPOUSE > 365) ) 
         
      	// ตรวจสอบเงินเดือนเต็มขั้น
	/*$cmd = "	select LAYERE_NO, LAYERE_SALARY from PER_LAYEREMP  
			where PG_CODE in($val_pg_code) order by LAYERE_SALARY desc ";
	$db_dpis1->send_cmd($cmd);
	$data1 = $db_dpis1->get_array();
	$MAX_LAYERE_NO = $data1[LAYERE_NO];
	$MAX_LAYERE_SALARY = $data1[LAYERE_SALARY] + 0;

	$cmd = "	select MAX_SALARY from PER_POS_LEVEL_SALARY  
			where PN_CODE='$PN_CODE' and LEVEL_NO='$LEVEL_NO' ";
	$db_dpis1->send_cmd($cmd);
	$data1 = $db_dpis1->get_array();
	$MAX_SALARY = $data1[MAX_SALARY] + 0;
       // echo $POEM_ID."<><>".$UP_SALARY."||".$PER_SALARY."||".$MAX_SALARY."||".$MAX_LAYERE_SALARY."<br>";
	if ( $PER_SALARY == $UP_SALARY || $PER_SALARY == $MAX_LAYERE_SALARY)	 {
        
		if ( $MAX_SALARY > $MAX_LAYERE_SALARY)	 {
			$PG_CODE = $val_pg_code + 1000;
			$cmd = "	select LAYERE_NO, LAYERE_SALARY from PER_LAYEREMP  
					where PG_CODE='$PG_CODE' and LAYERE_SALARY > $PER_SALARY order by LAYERE_NO ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$PER_SALARY = $data1[LAYERE_SALARY] + 0;

			$cmd = " update PER_PERSONAL set PER_SALARY = $PER_SALARY where PER_ID=$PER_ID ";
			$db_dpis1->send_cmd($cmd);

			$cmd = " update PER_POS_EMP set PG_CODE_SALARY = '$PG_CODE' where POEM_ID=$POEM_ID ";
			$db_dpis1->send_cmd($cmd);
                                    	
                } else {
               
			$SALP_YN = 1;
			$SALP_LEVEL = 0;
			$SALP_REASON = 2;	
			
			// เงินเดือนเต็มขั้นแล้ว ให้ได้รับเงินตอบแทนพิเศษ
			if ($SALQ_TYPE2 == 1) 		$SALP_PERCENT = 2;
			elseif ($SALQ_TYPE2 == 2) 		$SALP_PERCENT = 4;
			$SALP_SPSALARY = (($MAX_LAYERE_SALARY * $SALP_PERCENT) / 100);	
                      
		}
	}*/
                
	// ตรวจสอบ วันลากิจ+ลาป่วยไม่เกิน 23 วัน 10 ครั้ง สายไม่เกิน 18 วัน
	//  ลาป่วย + ลากิจ
	$cmd = " 	select 		PER_ID, sum(ABS_DAY) as DAY_ILL, count(PER_ID) as TIME_ILL
			from 		PER_ABSENT
			where 		PER_ID=$PER_ID and AB_CODE IN ('01', '03')
						$search_monthyear 							
			group by 		PER_ID
			having 		(sum(ABS_DAY) > 0) or (count(PER_ID) > 0) ";
	$db_dpis1->send_cmd($cmd);
	$data1 = $db_dpis1->get_array();
	$DAY_ILL = $data1[DAY_ILL];
	$TIME_ILL = $data1[TIME_ILL];
	if ( ($DAY_ILL > 45) || ($TIME_ILL > 20) ) {
		$non_promote = true;
		$non_promote_text .= "ไม่สามารถเลื่อนขั้นเงินเดือน $PER_NAME ได้ เนื่องจากลาป่วยเกิน 23 วัน หรือ เกิน 10 ครั้ง<br>";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;				
		$SALP_REASON = 0;				
	}
	// สาย 
	$cmd = " 	select 		PER_ID, count(PER_ID) as TIME_LATE
			from 		PER_ABSENT 
			where 	PER_ID=$PER_ID and AB_CODE IN ('10') 
						$search_monthyear 							
			group by 		PER_ID
			having 		count(PER_ID) > 0 ";
	$db_dpis1->send_cmd($cmd);	
	$data1 = $db_dpis1->get_array();
	$TIME_LATE = $data1[TIME_LATE];
	if ( $TIME_LATE > 36 )  {
		$non_promote = true;
		$non_promote_text .= "ไม่สามารถเลื่อนขั้นเงินเดือน $PER_NAME ได้ เนื่องจากสายเกิน 18 ครั้ง<br>";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;	
		$SALP_REASON = 0;
	}

/*
	// ตรวจสอบการ อบรม/ดูงาน/สัมมนา
	$cmd = " select PER_ID, TRN_STARTDATE, TRN_ENDDATE from PER_TRAINING where PER_ID=$PER_ID ";
	$db_dpis1->send_cmd($cmd);
	$data1 = $db_dpis1->get_array(); 
	$tmp_date = explode("-", substr($data1[TRN_STARTDATE], 0, 10));
	$TRN_STARTDATE = mktime(0, 0, 0, $tmp_date[1], $tmp_date[2], $tmp_date[0]);
	$tmp_date = explode("-", substr($data1[TRN_ENDDATE], 0, 10));			
	$TRN_ENDDATE = mktime(0, 0, 0, $tmp_date[1], $tmp_date[2], $tmp_date[0]);
	$TIME_TRAINING = ($TRN_ENDDATE - $TRN_STARTDATE) / 2592000; // ใช้วินาทีหาร = 30 วัน
	if ($TIME_TRAINING > 12) {
		$non_promote = true;
		$non_promote_text .= "ไม่สามารถเลื่อนขั้นเงินเดือน $PER_NAME ได้ เนื่องจากลาอบรม/ดูงาน/สัมมนา เกินจำนวนวันที่กำหนด<br>";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;	
		$SALP_REASON = 3;
	}
*/
    
        
	// ตรวจสอบว่าบรรจุเกิน 4 เดือนหรือยัง
	$cmd = "	select MOV_CODE, max(POH_EFFECTIVEDATE) as EFFECTIVEDATE from PER_POSITIONHIS 
					where PER_ID=$PER_ID and MOV_CODE in ('101', '10110', '10120', '10130', '10140', '105', '10510', '10520') 
					group by MOV_CODE ";
	$db_dpis1->send_cmd($cmd);
	$data1 = $db_dpis1->get_array();
	$tmp_date1 = explode("-", substr($data1[EFFECTIVEDATE], 0, 10));
	$tmp_date2 = explode("-", date("Y-m-d"));	
	$tmp_date_effect =  mktime(0, 0, 0, $tmp_date1[1], $tmp_date1[2], $tmp_date1[0]);
	$tmp_date_now = mktime(0, 0, 0, $tmp_date2[1], $tmp_date2[2], $tmp_date2[0]);
	$MONTH_FILL = ($tmp_date_now - $tmp_date_effect) / 2592000;		// ใช้วินาทีหาร = 30 วัน
	if ($MONTH_FILL < 4) {
		$non_promote = true;
		$non_promote_text .= "ไม่สามารถเลื่อนขั้นเงินเดือน $PER_NAME ได้ เนื่องจากบรรจุยังไม่เกิน 4 เดือน<br>";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;				
		$SALP_REASON = 4;
	}
        
	// ===== คำนวนผลการเลื่อนขั้นเงินเดือน
	$cmd = " select 	LAYERE_NO, a.LEVEL_NO_SALARY, PER_SALARY, LAYERE_SALARY, b.PG_CODE, a.LEVEL_NO, a.PN_CODE   
			from 	PER_PERSONAL a, PER_LAYEREMP b, PER_POS_EMP c
			where 	a.PER_ID=$PER_ID and a.POEM_ID=c.POEM_ID and 
					trim(b.PG_CODE)in($val_pg_code)  and LAYERE_SALARY>=$PER_SALARY 
			order by 	LAYERE_NO ";
	$db_dpis1->send_cmd($cmd);
	$data1 = $db_dpis1->get_array();	
	$UP_SALP_LAYER = $data1[LAYERE_NO] + $SALP_LEVEL;
	$LEVEL_NO = trim($data1[LEVEL_NO]);
	$PN_CODE = trim($data1[PN_CODE]);
	// select ว่า LAYERE_NO ที่เพิ่มขึ้นเกินจำนวน LAYERE_NO ของ LEVEL_NO นั้น ๆ หรือเปล่า 
	// ถ้าจำนวนขั้นที่เลื่อนขึ้นสูงกว่าที่มีอยู่จริง ให้เพิ่มได้ถึงแค่ MAX	
       $cmd = " select LAYERE_NO, LAYERE_SALARY
			from PER_LAYEREMP where PG_CODE in($val_pg_code) and LAYERE_ACTIVE=1 order by LAYERE_SALARY desc";
	$count_layer_no = $db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$MAX_LAYERE_NO = trim($data2[LAYERE_NO]);
	$MAX_LAYERE_SALARY = trim($data2[LAYERE_SALARY]);
        
	$cmd = " select MAX_SALARY from PER_POS_LEVEL_SALARY where PN_CODE='$PN_CODE' and LEVEL_NO='$LEVEL_NO' ";
	$count_layer_no = $db_dpis2->send_cmd($cmd);
	$data2 = $db_dpis2->get_array();
	$MAX_SALARY = trim($data2[MAX_SALARY]);
	if ($MAX_SALARY && $MAX_SALARY != $MAX_LAYERE_SALARY) {
		$MAX_LAYERE_SALARY = $MAX_SALARY;
		$cmd = " select LAYERE_NO
				from PER_LAYEREMP where PG_CODE in($val_pg_code) and LAYERE_SALARY <= $MAX_LAYERE_SALARY and LAYERE_ACTIVE=1 order by LAYERE_NO desc";
		$count_layer_no = $db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$MAX_LAYERE_NO = trim($data2[LAYERE_NO]);
	}

	/*if ($UP_SALP_LAYER > $MAX_LAYERE_NO) {
		$SALP_LEVEL = $UP_SALP_LAYER - $MAX_LAYERE_NO;
		$UP_SALP_LAYER = $MAX_LAYERE_NO;
		$SALP_PERCENT = 2;
		$SALP_SPSALARY = (($MAX_LAYERE_SALARY * $SALP_PERCENT) / 100);	
		$non_promote_text .= "เลื่อนขั้นเงินเดือนของ $PER_NAME ได้ไม่ครบตามจำนวน เนื่องจากเกินจำนวนเงินเดือนเต็มขั้น<br>";
	}*/ 

                $cmd = " select LAYERE_SALARY from PER_LAYEREMP
				  where PG_CODE in($val_pg_code) and LAYERE_NO='$UP_SALP_LAYER' and LAYERE_SALARY >= $PER_SALARY  
				  order by LAYERE_SALARY ";
	$db_dpis1->send_cmd($cmd);
	$data1 = $db_dpis1->get_array();				
	$SALP_SALARY_NEW = $data1[LAYERE_SALARY] + 0;
       
       
       $cmd = "select MAX_SALARY, LEVEL_NO from  PER_POS_LEVEL_SALARY 
                        where PN_CODE = $PN_CODE_CH and LEVEL_NO = '$LEVEL_NO' and GROUP_SALARY is not null";
                    $db_dpis1->send_cmd($cmd);
                    $data1 = $db_dpis1->get_array();
                    $MAX_SALARY_CH = $data1[MAX_SALARY];
                    

//=============================================================================================================  
        //หาคน ระดับตำแหน่ง ที่มีค่าเงินที่สุดกลุ่มบัญชี   
    if ($MAX_CH_SALARY >= $MAX_SALARY_EMP ){
// echo"-->>".$PER_NAME."-->". $PER_SALARY." > ".$MAX_CH_SALARY.">>> ".$MAX_SALARY_EMP."|->".$UP_SALARY.">>>".$LEVEL_NO."<br>" ; 
                                  
        if ($PER_SALARY >= $MAX_CH_SALARY ){//คนที่เงินเดือน เท่ากับหรือมากกว่าอัตราขั้นสูง ให้เลื่อนเงินข้ามบัญชี
              // echo"->> ".$PER_NAME."||".$MAX_SALARY_EMP.">=".$MAX_CH_SALARY."||".$LEVEL_NO."<br>";
              
                    $PG_CODE_VAR = $var_pg + 1000;
                    if($PG_CODE_VAR == 5000) $PG_CODE_VAR = 4000;
                 $cmd ="select * from 
                                (select LAYERE_NO, PG_CODE, LAYERE_SALARY  from PER_LAYEREMP 
                    where PG_CODE='$PG_CODE_VAR' and LAYERE_SALARY >= $PER_SALARY) where rownum = 1"; 
                    $db_dpis1->send_cmd($cmd);
                    $data1 = $db_dpis1->get_array();
                    $SALP_SALARY_OC = $data1[LAYERE_SALARY];
                    $LAYERE_NO_OC = $data1[LAYERE_NO]+$SALP_LEVEL;
                    
                     
                    
                 $cmd ="select * from 
                                (select LAYERE_NO, LAYERE_SALARY from PER_LAYEREMP 
                    where PG_CODE='$PG_CODE_VAR' and LAYERE_NO = $LAYERE_NO_OC ) where rownum = 1"; 
                    $db_dpis1->send_cmd($cmd);
                    $data1 = $db_dpis1->get_array();
                    $LAYERE_NO_OC = $data1[LAYERE_NO] ;
                    $LAYERE_A = $data1[LAYERE_SALARY] ;
                    $SALP_SALARY_NEW = $data1[LAYERE_SALARY] ;
                  
			// เงินเดือนเต็มขั้นแล้ว ให้ได้รับเงินตอบแทนพิเศษ
			 if ($PER_SALARY > $UP_SALARY || $SALP_SALARY_OC >= $UP_SALARY || $SALP_SALARY_NEW > $UP_SALARY){ 
                               $SALP_SALARY_NEW = $UP_SALARY;
                                $SALP_YN = 1;
                                $SALP_LEVEL = 0;
                                $SALP_REASON = 2;	

                                // เงินเดือนเต็มขั้นแล้ว ให้ได้รับเงินตอบแทนพิเศษ
                                if ($SALQ_TYPE2 == 1) 		$SALP_PERCENT = 2;
                                elseif ($SALQ_TYPE2 == 2) 		$SALP_PERCENT = 4;
                                $SALP_SPSALARY = (($PER_SALARY * $SALP_PERCENT) / 100);	
                            }
                    
        }else{//คนที่เงินเดือนยังไม่ถึงอัตราค่าจ้างขั้นสูง ให้กินในกลุ่มจนสุดกลุ่ม
               //echo $PER_NAME." | ".$PER_SALARY." || ".$MAX_CH_SALARY."||".$UP_SALARY."||".$LEVEL_NO."<<>>"."<br>"; 
              
                            //หาขั้นคนที่ไม่ข้ามกลุ่ม 
                       $cmd ="select * from 
                                              (select LAYERE_NO, LAYERE_SALARY, PG_CODE from PER_LAYEREMP 
                                  where PG_CODE in($val_pg_code) and LAYERE_SALARY >= $PER_SALARY ORDER BY LAYERE_SALARY, PG_CODE ASC) where rownum = 1"; 
                                  $db_dpis1->send_cmd($cmd);
                                  $data1 = $db_dpis1->get_array();
                                  $SALP_SALARY_CC = $data1[LAYERE_SALARY] ;
                                  $PG_CODE_CC  = trim($data1[PG_CODE]);
                                  $LAYERE_NO_CC = $data1[LAYERE_NO]+$SALP_LEVEL;
                           
                          $cmd = "select LAYERE_NO, LAYERE_SALARY, PG_CODE from PER_LAYEREMP 
                                  where PG_CODE in($PG_CODE_CC) and LAYERE_NO = $LAYERE_NO_CC ORDER BY LAYERE_SALARY ASC"; 
                                  $db_dpis1->send_cmd($cmd);
                                  $data1 = $db_dpis1->get_array();
                                  $SALP_SALARY_NEW = $data1[LAYERE_SALARY] ;
                                  $PG_CODE_CC  = trim($data1[PG_CODE]);
                                  $LAYERE_NO_CC = $data1[LAYERE_NO];
                                  
                                  
                                    if(!$SALP_SALARY_NEW){ 
                                          $cmd = "select LAYERE_NO, LAYERE_SALARY, PG_CODE from PER_LAYEREMP 
                                              where PG_CODE in($var_pg) and LAYERE_SALARY >= $SALP_SALARY_CC ORDER BY LAYERE_SALARY, PG_CODE ASC"; 
                                              $db_dpis1->send_cmd($cmd);
                                              $data1 = $db_dpis1->get_array();
                                              $SALP_SALARY_CC = $data1[LAYERE_SALARY] ;
                                                $PG_CODE_CC  = trim($data1[PG_CODE]);
                                                $LAYERE_NO_CC = $data1[LAYERE_NO]+$SALP_LEVEL;
                                                $LAYERE_NO_CHE = $data1[LAYERE_NO]+$SALP_LEVEL;
                                                
                                                $cmd = "select LAYERE_NO, LAYERE_SALARY, PG_CODE from PER_LAYEREMP 
                                                    where PG_CODE in($PG_CODE_CC) and LAYERE_NO = $LAYERE_NO_CC ORDER BY LAYERE_SALARY ASC"; 
                                                    $db_dpis1->send_cmd($cmd);
                                                    $data1 = $db_dpis1->get_array();
                                                    $SALP_SALARY_NEW = $data1[LAYERE_SALARY] ;
                                                    $SALP_SALARY_CHK = $data1[LAYERE_SALARY] ;
                                                    $PG_CODE_CC  = trim($data1[PG_CODE]);
                                                    $LAYERE_NO_CC = $data1[LAYERE_NO];
                                                    
                                               if(!$SALP_SALARY_CHK)   {
                                               //   หาขั้น
                                                $PG_CODE_CC = $var_pg + 1000;
                                                if($PG_CODE_CC == 5000) $PG_CODE_CC = 4000;
                                                   $cmd = "select LAYERE_NO, LAYERE_SALARY, PG_CODE from PER_LAYEREMP 
                                                    where PG_CODE in($PG_CODE_CC) and LAYERE_SALARY >= $SALP_SALARY_CC ORDER BY LAYERE_SALARY ASC"; 
                                                    $db_dpis1->send_cmd($cmd);
                                                    $data1 = $db_dpis1->get_array();
                                                    $SALP_SALARY_NEW = $data1[LAYERE_SALARY] ;
                                                    $SALP_SALARY_CHK = $data1[LAYERE_SALARY] ;
                                                    $PG_CODE_CC  = trim($data1[PG_CODE]);
                                                    $LAYERE_NO_CC = $data1[LAYERE_NO]+$SALP_LEVEL;

                                               }
                                        if($SALQ_TYPE2 == 2 && $LAYERE_NO_CHE > $LAYERE_NO_EMP){
                                                $cmd = "select LAYERE_NO, LAYERE_SALARY from PER_LAYEREMP 
                                                        where PG_CODE in($var_pg) order by LAYERE_NO desc ";
                                                        $db_dpis1->send_cmd($cmd);
                                                        $data1 = $db_dpis1->get_array();
                                                        $LAYERE_A = $data1[LAYERE_NO] ;
                                                        $LAYERE_SALARY_A = $data1[LAYERE_SALARY] ;
                                                        
                                                if($LAYERE_NO_CC != $LAYERE_A){
                                                  $cmd = "select LAYERE_NO, LAYERE_SALARY from PER_LAYEREMP 
                                                        where PG_CODE = '$PG_CODE_CC' and LAYERE_SALARY >= $LAYERE_SALARY_A order by LAYERE_NO asc ";
                                                        $db_dpis1->send_cmd($cmd);
                                                        $data1 = $db_dpis1->get_array();
                                                        $LAYERE_NO_CC = $data1[LAYERE_NO]+0.5 ;
                                                        $LAYERE_SALARY_CC = $data1[LAYERE_SALARY] ;  
                                                }else{
                                              $cmd = "select LAYERE_NO, LAYERE_SALARY from PER_LAYEREMP 
                                                    where PG_CODE = '$PG_CODE_CC' and LAYERE_SALARY >= $LAYERE_SALARY_A order by LAYERE_NO asc ";
                                                    $db_dpis1->send_cmd($cmd);
                                                    $data1 = $db_dpis1->get_array();
                                                    $LAYERE_NO_CC = $data1[LAYERE_NO]+1 ;
                                                    $LAYERE_SALARY_CC = $data1[LAYERE_SALARY] ;  
                                                }  
                                                 $cmd ="select * from 
                                                                (select LAYERE_NO, LAYERE_SALARY from PER_LAYEREMP 
                                                    where PG_CODE = '$PG_CODE_CC' and LAYERE_NO = $LAYERE_NO_CC) where rownum = 1"; 
                                                    $db_dpis1->send_cmd($cmd);
                                                    $data1 = $db_dpis1->get_array();
                                                    $LAYERE_A = $data1[LAYERE_SALARY] ;
                                                    $SALP_SALARY_NEW = $data1[LAYERE_SALARY] ;
                                        } //end if รอบสอง    
                                    } 

                        if($SALP_SALARY_CC == $MAX_CH_SALARY ){//ถ้าเงินเดือนที่เดือน ที่หาขั้นเเล้ว แล้วเงินเดือนเท่ากับอัตราค่าจ้างขั้นสูง ให้กินข้ามกลุ่ม
                             $PG_CODE_CC = $var_pg + 1000;
                              if($PG_CODE_CC == 5000) $PG_CODE_VAR = 4000;
                        //echo $PER_NAME." | ".$PER_SALARY." || ".$SALP_SALARY_NEW."||".$UP_SALARY."<<>>"."<br>"; 
                             $cmd =" select LAYERE_NO, LAYERE_SALARY from PER_LAYEREMP                   
                                  where PG_CODE in($PG_CODE_CC) and LAYERE_SALARY >= $SALP_SALARY_CC"; 
                                  $db_dpis1->send_cmd($cmd);
                                  $data1 = $db_dpis1->get_array();
                                  $LAYERE_NO_A = $data1[LAYERE_NO]+$SALP_LEVEL;
                                  $SALP_SALARY_A = $data1[LAYERE_SALARY];

                                $cmd =" select LAYERE_NO, LAYERE_SALARY from PER_LAYEREMP                   
                                  where PG_CODE in($PG_CODE_CC) and LAYERE_NO = $LAYERE_NO_A"; 
                                  $db_dpis1->send_cmd($cmd);
                                  $data1 = $db_dpis1->get_array();
                                  $LAYERE_A = $data1[LAYERE_SALARY] ;
                                  $SALP_SALARY_NEW = $data1[LAYERE_SALARY] ;
                                  
                                  if($SALP_SALARY_NEW > $UP_SALARY || $SALP_SALARY_CC >= $UP_SALARY || $PER_SALARY >= $UP_SALARY){ 
                                          $SALP_SALARY_NEW = $UP_SALARY;
                                           $SALP_YN = 1;
                                           $SALP_LEVEL = 0;
                                           $SALP_REASON = 2;	

                                           // เงินเดือนเต็มขั้นแล้ว ให้ได้รับเงินตอบแทนพิเศษ
                                           if ($SALQ_TYPE2 == 1) 		$SALP_PERCENT = 2;
                                           elseif ($SALQ_TYPE2 == 2) 		$SALP_PERCENT = 4;
                                           $SALP_SPSALARY = (($PER_SALARY * $SALP_PERCENT) / 100);
                                    }
                        
                        }else{//คนที่เงินเดือนที่เทียบเเล้ว เงินเดือนยังไม่ถึงอัตาค่าจ้างขั้นสูง 
                        
                            if($SALQ_TYPE2 == 2 && $LAYERE_NO_CC > $LAYERE_NO_EMP){
                                $cmd = "select LAYERE_NO, LAYERE_SALARY from PER_LAYEREMP 
                                  where PG_CODE = '$PG_CODE_CC' order by LAYERE_NO desc ";
                                  $db_dpis1->send_cmd($cmd);
                                  $data1 = $db_dpis1->get_array();
                                  $LAYERE_A = $data1[LAYERE_NO] ;
                                  $LAYERE_SALARY_A = $data1[LAYERE_SALARY] ;
                                  
                                  if($LAYERE_NO_CC != $LAYERE_A){
                                        $PG_CODE_CC = $PG_CODE_CC +1000;
                                        if($PG_CODE_CC == 5000) $PG_CODE_CC = 4000;
                                   $cmd = "select LAYERE_NO, LAYERE_SALARY from PER_LAYEREMP 
                                        where PG_CODE = '$PG_CODE_CC' and LAYERE_SALARY >= $LAYERE_SALARY_A order by LAYERE_NO asc ";
                                        $db_dpis1->send_cmd($cmd);
                                        $data1 = $db_dpis1->get_array();
                                        $LAYERE_NO_CC = $data1[LAYERE_NO]+0.5 ;
                                        $LAYERE_SALARY_CC = $data1[LAYERE_SALARY] ;  
                                  }else{
                                        $PG_CODE_CC = $PG_CODE_CC +1000;
                                        if($PG_CODE_CC == 5000) $PG_CODE_CC = 4000;
                                   $cmd = "select LAYERE_NO, LAYERE_SALARY from PER_LAYEREMP 
                                        where PG_CODE = '$PG_CODE_CC' and LAYERE_SALARY >= $LAYERE_SALARY_A order by LAYERE_NO asc ";
                                        $db_dpis1->send_cmd($cmd);
                                        $data1 = $db_dpis1->get_array();
                                        $LAYERE_NO_CC = $data1[LAYERE_NO]+1 ;
                                        $LAYERE_SALARY_CC = $data1[LAYERE_SALARY] ;  
                                  } 
                                  
                                 /* $cmd ="select * from 
                                              (select LAYERE_NO, LAYERE_SALARY from PER_LAYEREMP 
                                  where PG_CODE = '$PG_CODE_CC' and LAYERE_NO = $LAYERE_NO_CC) where rownum = 1"; 
                                  $db_dpis1->send_cmd($cmd);
                                  $data1 = $db_dpis1->get_array();
                                  $LAYERE_A = $data1[LAYERE_SALARY] ;
                                  $SALP_SALARY_NEW = $data1[LAYERE_SALARY] ;*/
                            }
                           
                                  
                                   if($SALP_SALARY_NEW > $UP_SALARY || $SALP_SALARY_CC >= $UP_SALARY ){ 
                                           $SALP_SALARY_NEW = $UP_SALARY;
                                           $SALP_YN = 1;
                                           $SALP_LEVEL = 0;
                                           $SALP_REASON = 2;	

                                           // เงินเดือนเต็มขั้นแล้ว ให้ได้รับเงินตอบแทนพิเศษ
                                           if ($SALQ_TYPE2 == 1) 		$SALP_PERCENT = 2;
                                           elseif ($SALQ_TYPE2 == 2) 		$SALP_PERCENT = 4;
                                           $SALP_SPSALARY = (($PER_SALARY * $SALP_PERCENT) / 100);
                                    }                     
                        }                              
                }
             
 //echo "<pre>".$PER_NAME."เงินเก่า ==>".$SALP_SALARY_CC ."เงินใหม่ ==>". $SALP_SALARY_NEW."ขั้น $LAYERE_NO_CC"." กลุ่มบัญชี $val_pg_code";                    
    }//end หาคน ระดับตำแหน่ง ที่มีค่าเงินที่สุดกลุ่มบัญชี  
    
 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    //คนที่รับเงินขั้นสูงไม่สุดบัญชี ให้รับเงินจนสุดบัญชี
    $val_end_gr = end($CN_GROUP_SALARY)*1000;
        if($MAX_CH_SALARY < $MAX_SALARY_EMP){ 
        //echo "-->".$PER_NAME."<br>";
                    
                   //หาขั้น ก่อน 
              $cmd =" select LAYERE_NO, LAYERE_SALARY, PG_CODE from PER_LAYEREMP 
                            where PG_CODE in ($val_pg_code) and LAYERE_SALARY >= $PER_SALARY 
                            order by PG_CODE, LAYERE_SALARY" ;
                   $db_dpis1->send_cmd($cmd);
                   $data1 = $db_dpis1->get_array();
                   $LAYERE_NO_CC = $data1[LAYERE_NO]+$SALP_LEVEL;
                   $PG_CODE_CC = trim($data1[PG_CODE]);
                   $LAYERE_A = $data1[LAYERE_SALARY] ;
                   $LAYERE_SALARY_C1 = $data1[LAYERE_SALARY];
                   
                //select มาเพื่อ เอาค่าเงินมาเทียบกับ $LAYERE_SALARY_C1    
              $cmd =" select LAYERE_NO, LAYERE_SALARY, PG_CODE from PER_LAYEREMP 
                            where PG_CODE = '$PG_CODE_CC'
                            order by  LAYERE_SALARY DESC" ;
                   $db_dpis1->send_cmd($cmd);
                   $data1 = $db_dpis1->get_array();   
                   $LAYERE_SALARY_C2 = $data1[LAYERE_SALARY];
         
                   //กินข้ามกลุ่ม
                   if($LAYERE_SALARY_C1 >= $LAYERE_SALARY_C2){
                   if($SALP_LEVEL == 0.5) $IM = 2;
                   else if($SALP_LEVEL == 1)$IM = 3;
                   
                  // echo"-->>".$PER_NAME."[เงินเดือน]-->".$PER_SALARY." [เงินเทียบ 1]--> ".$LAYERE_SALARY_C1." >= "."[เงินเทียบ 2]-->".$LAYERE_SALARY_C2."[อัตราขั้นสูง]--> ".$MAX_CH_SALARY."[กลุ่ม]-->  ".$val_pg_code."<br>" ; 
                  
                      $cmd =" select * from (select LAYERE_NO, LAYERE_SALARY,rownum as IM from PER_LAYEREMP 
                                         where PG_CODE in ($val_pg_code) and LAYERE_SALARY >= $LAYERE_SALARY_C1 ) where IM = $IM ";
                            $db_dpis1->send_cmd($cmd);
                            $data1 = $db_dpis1->get_array();
                             $LAYERE_NO_CC = $data1[LAYERE_NO];
                            $SALP_SALARY_NEW = $data1[LAYERE_SALARY]; 
                            
                             //echo "--->>> ".$PER_NAME;
                                   
                                               
                                        if($SALP_SALARY_NEW > $UP_SALARY || $PER_SALARY >= $UP_SALARY || $LAYERE_SALARY_C1 >= $UP_SALARY){ 
                                           $SALP_SALARY_NEW = $UP_SALARY;
                                           $SALP_YN = 1;
                                           $SALP_LEVEL = 0;
                                           $SALP_REASON = 2;	

                                           // เงินเดือนเต็มขั้นแล้ว ให้ได้รับเงินตอบแทนพิเศษ
                                           if ($SALQ_TYPE2 == 1) 		$SALP_PERCENT = 2;
                                           elseif ($SALQ_TYPE2 == 2) 		$SALP_PERCENT = 4;
                                           $SALP_SPSALARY = (($PER_SALARY * $SALP_PERCENT) / 100);
                                    }                     
                   }else{
                        //echo "--->>> ".$PER_NAME;
                            $cmd =" select LAYERE_NO, LAYERE_SALARY from PER_LAYEREMP 
                                        where PG_CODE = '$PG_CODE_CC'  and LAYERE_NO = $LAYERE_NO_CC";
                            $db_dpis1->send_cmd($cmd);
                            $data1 = $db_dpis1->get_array();
                            $LAYERE_NO_OC = $data1[LAYERE_NO];
                            $LAYERE_A = $data1[LAYERE_SALARY] ;
                            $SALP_SALARY_NEW = $data1[LAYERE_SALARY];       
                }       
                 
                                    // เงินเดือนเต็มขั้นแล้ว ให้ได้รับเงินตอบแทนพิเศษ   
                                  if($SALP_SALARY_NEW > $UP_SALARY || $LAYERE_SALARY_C1 >= $UP_SALARY || $PER_SALARY >= $UP_SALARY){ 
                                           $SALP_SALARY_NEW = $UP_SALARY;
                                           $SALP_YN = 1;
                                           $SALP_LEVEL = 0;
                                           $SALP_REASON = 2;	

                                           // เงินเดือนเต็มขั้นแล้ว ให้ได้รับเงินตอบแทนพิเศษ
                                           if ($SALQ_TYPE2 == 1) 		$SALP_PERCENT = 2;
                                           elseif ($SALQ_TYPE2 == 2) 		$SALP_PERCENT = 4;
                                           $SALP_SPSALARY = (($PER_SALARY * $SALP_PERCENT) / 100);
                                    }                     
                    
                                                              
        }  
       
                                 
                            
                            
        if ($SALQ_TYPE2 == 2){  
             //echo $PER_NAME."[$SALP_SALARY_NEW]"."****"."[$UP_SALARY]"."<br>";
            if( !$SALP_SALARY_NEW){
                    if($PER_SALARY == $UP_SALARY){

                        $SALP_SALARY_NEW = $UP_SALARY;
                        $SALP_LEVEL = 0.0;
                        $SALP_PERCENT = 4;
                        $SALP_SPSALARY = (($PER_SALARY * $SALP_PERCENT) / 100);	
                        
                    }else{
                   
                        $SALP_SALARY_NEW = $UP_SALARY;
                        $SALP_LEVEL = 0.5;
                        $SALP_PERCENT = 2;
                        $SALP_SPSALARY = (($PER_SALARY * $SALP_PERCENT) / 100);	
                        $non_promote_text .= "เลื่อนขั้นเงินเดือนของ $PER_NAME ได้ไม่ครบตามจำนวน เนื่องจากเกินจำนวนเงินเดือนเต็มขั้น<br>";
                    }
           
            }else if($LAYERE_A > $UP_SALARY){   
             //echo $PER_NAME."[$SALP_SALARY_NEW]"."****"."[$UP_SALARY]"."<br>";
       $cmd =" select LAYERE_NO, LAYERE_SALARY from PER_LAYEREMP 
                             where PG_CODE = '$PG_CODE_VAR' and LAYERE_SALARY = $UP_SALARY";
                            $db_dpis1->send_cmd($cmd);
                            $data1 = $db_dpis1->get_array();
                            $LAYERE_NO_B = trim($data1[LAYERE_NO]); 
                            $LAYERE_SALARY_B = $data1[LAYERE_SALARY];
                            $sum_salary = $LAYERE_NO_OC - $LAYERE_NO_B;
                            //echo "[$LAYERE_NO_OC]->>".$sum_salary."[$PER_NAME]"."<br>";
                         //echo $PER_NAME."[$SALP_SALARY_NEW]"."****"."[$UP_SALARY]"."<br>";
                          
                          if($sum_salary == 0.5){
                                $SALP_SALARY_NEW = $UP_SALARY;
                                $SALP_LEVEL = 0.5;
                                $SALP_PERCENT = 2;
                                $SALP_SPSALARY = (($PER_SALARY * $SALP_PERCENT) / 100);	
                         
                          }else{
                                $SALP_SALARY_NEW = $UP_SALARY;
                                $SALP_LEVEL = 0.0;
                                $SALP_PERCENT = 4;
                                $SALP_SPSALARY = (($PER_SALARY * $SALP_PERCENT) / 100);	
                                $non_promote_text .= "เลื่อนขั้นเงินเดือนของ $PER_NAME ได้ไม่ครบตามจำนวน เนื่องจากเกินจำนวนเงินเดือนเต็มขั้น<br>";
                          }
                          
            }/*else{ 
                 if($SALP_SALARY_NEW > $SALP_SALARY_NEW){
                          $SALP_SALARY_NEW = $UP_SALARY; 
                }
             }*/
           
 }        
                       
                       
                       
//echo $val_end_gr."<br>";     
//echo ":: $PER_ID -> $PER_NAME :: SALP_YN=$SALP_YN || LAYERE_NO=$LAYERE_NO || LEVEL_NO=$LEVEL_NO <br>";
//echo ">> PER_SALARY=$PER_SALARY || LAYERE_SALARY=$LAYERE_SALARY || UP_SALP_LAYER=$UP_SALP_LAYER || SALP_SALARY_NEW=$SALP_SALARY_NEW<br>";

	if ($SALP_SALARY_NEW == 0) 	$SALP_SALARY_NEW = $PER_SALARY;
	// วันที่คำสั่งมีผลบังคับใช้
	if ($SALQ_TYPE2 == 1)			$SALP_DATE = ($SALQ_YEAR - 543) ."-04-01";
	elseif ($SALQ_TYPE2 == 2)		$SALP_DATE = ($SALQ_YEAR - 543) ."-10-01";
	if ($SALQ_TYPE1==1 && $SALQ_TYPE2==1) 			$tmp_SALQ_TYPE = 1;
	elseif ($SALQ_TYPE1==1 && $SALQ_TYPE2==2) 		$tmp_SALQ_TYPE = 2;
	elseif ($SALQ_TYPE1==2 && $SALQ_TYPE2==1) 		$tmp_SALQ_TYPE = 3;
	elseif ($SALQ_TYPE1==2 && $SALQ_TYPE2==2) 		$tmp_SALQ_TYPE = 4;
	elseif ($SALQ_TYPE1==3 && $SALQ_TYPE2==1) 		$tmp_SALQ_TYPE = 5;
	elseif ($SALQ_TYPE1==3 && $SALQ_TYPE2==2) 		$tmp_SALQ_TYPE = 6;	
	$SALP_REMARK = (trim($non_promote_text))? $non_promote_text : "";
	
	// บันทึกข้อมูล ลง table PER_SALPROMOTE 
        // echo $PER_NAME." | ".$PER_SALARY." || ".$SALP_SALARY_NEW."||".$UP_SALARY."<<>>"."<br>";     
        $cmd = " 	insert into PER_SALPROMOTE
				(SALQ_YEAR, SALQ_TYPE, PER_ID, SALP_YN, SALP_LEVEL, SALP_SALARY_OLD, 
				 SALP_SALARY_NEW, SALP_PERCENT, SALP_SPSALARY, SALP_DATE, SALP_REMARK, 
				 SALP_REASON, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE)
			values 
				('$SALQ_YEAR', $tmp_SALQ_TYPE, $PER_ID, $SALP_YN, $SALP_LEVEL, $PER_SALARY, 
				 $SALP_SALARY_NEW, $SALP_PERCENT, $SALP_SPSALARY, '$SALP_DATE', '$SALP_REMARK', 
				 $SALP_REASON, $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') ";
	$db_dpis1->send_cmd($cmd);
} // end if(!$alert_err){
//echo "==============<br>";
?>

