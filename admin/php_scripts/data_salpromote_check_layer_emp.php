<?
$tmp_count_salpromote = $count_layer_no = $LAYER_NO = $PG_CODE = "";
$LAYERE_SALARY = $LAYERE_SALARY_MIN = $LAYERE_SALARY_MAX = $UP_SALP_LAYER = $SALP_SALARY_NEW = 0;
$MAX_LAYERE_NO = $MAX_LAYERE_SALARY = 0;
// �� PG_CODE ����Ѻ�Թ��͹��дѺ �ͧ�١��ҧ�������˹
/*Query ���*/
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
        //�������� ���Ѵ������� �� 840.2 = 850
            $before_MOD_CMD_SALARY = $SAH_SALARY + $SAH_SALARY_EXTRA;
            $MOD_CMD_SALARY = fmod($before_MOD_CMD_SALARY,10);
            if($MOD_CMD_SALARY!=0){
             $test_SUM_SALARY = ($before_MOD_CMD_SALARY-$MOD_CMD_SALARY)+10;
           //echo "(".$before_MOD_CMD_SALARY." - ".$MOD_CMD_SALARY.")"." + "."10"."<br>";
            }  else {
            $test_SUM_SALARY = $SAH_SALARY;
            }//end �Ѵ���
            
          if($SAH_SALARY){
            $PER_SALARY =  $test_SUM_SALARY;
           // echo "[$PER_NAME]--> ".$PER_SALARY."<br>";
          }else{
            $PER_SALARY = $PER_SALARY;
          }
 }//EXTRA_CH
 //echo "[] ".$PER_NAME."----->>>>".$PER_SALARY."<br>";

  // ��Ǩ�ͺ�Թ��͹���������� ��Т�鹷���˹�������� �����������ͧ�ʴ� alert ������䢡�͹
  $CN_GROUP_SALARY = explode("," ,$GROUP_SALARY); //�ӡ�����ѭ�շ���Ѻ�Թ ���ŧ���
  $cnt=count($CN_GROUP_SALARY);
  $val_pg_code='';
  unset($arr_pg_code);
  for($idx=0;$idx<$cnt;$idx++){
      $code_gr=$CN_GROUP_SALARY[$idx]*1000;
      if($code_gr!=0){
          $arr_pg_code[]=$code_gr;
      }
  }
  /*Query ���*/
  /*select LAYERE_NO, LAYERE_SALARY from PER_LAYEREMP  
where PG_CODE='$PG_CODE' and LAYERE_SALARY= $PER_SALARY order by LAYERE_NO
$tmp_count_salpromote = $db_dpis1->send_cmd($cmd);
$db_dpis1->send_cmd($cmd);*/
 
$val_pg_code = implode(',',$arr_pg_code);
$var_pg = end($CN_GROUP_SALARY)*1000;

$PG_CODE_VAR = $var_pg + 1000;
if($PG_CODE_VAR == 5000) $PG_CODE_VAR = 4000;  
//select �����Ң���ش������ѭ��
$cmd = "select LAYERE_NO, LAYERE_SALARY from PER_LAYEREMP  
			where PG_CODE in($val_pg_code) order by LAYERE_SALARY desc";
	$db_dpis1->send_cmd($cmd);
	$data1 = $db_dpis1->get_array();
	$MAX_SALARY_EMP = $data1[LAYERE_SALARY];
    $LAYERE_NO_EMP = $data1[LAYERE_NO];
        
//�Ң���Թ�����ҡ �Թ��͹�Ѩ�غѹ �ͧ�١��ҧ��Ш� 
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
            <td>�ѵ���Թ��͹������������Т�鹷���˹�</td>
            <td align=\"center\">".$POEM_NO_NAME.$POEM_NO."</td><td>$PER_NAME</td>
            <td> �������� $GROUP_SALARY : �Թ��͹ $PER_SALARY</td>
            <td align=\"center\">&nbsp;<a href='javascript:call_edit_personal($PER_ID);'><img src=\"images/b_edit.png\" border=\"0\" alt=\"��䢢����źؤ��\"></a></td></tr>";

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
        <td>�ѵ���Թ��͹������������Т�鹷���˹�</td>
        <td align=\"center\">".$POEM_NO_NAME.$POEM_NO."</td><td>$PER_NAME</td>
        <td> �������� $GROUP_SALARY  : �Թ��͹ $PER_SALARY</td>
        <td align=\"center\">&nbsp;<a href='javascript:call_edit_personal($PER_ID);'><img src=\"images/b_edit.png\" border=\"0\" alt=\"��䢢����źؤ��\"></a></td></tr>";

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
	// ��Ǩ�ͺ �ѹ�ҵԴ����������
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
		$non_promote_text .= "�������ö����͹����Թ��͹ $PER_NAME �� ���ͧ�ҡ�ҵԴ����������� �Թ�������ҷ���˹�<br>";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;	
		$SALP_REASON = 1;				
	}		// 	if ( ($DAY_SPOUSE > 365) ) 
         
      	// ��Ǩ�ͺ�Թ��͹������
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
			
			// �Թ��͹���������� ������Ѻ�Թ�ͺ᷹�����
			if ($SALQ_TYPE2 == 1) 		$SALP_PERCENT = 2;
			elseif ($SALQ_TYPE2 == 2) 		$SALP_PERCENT = 4;
			$SALP_SPSALARY = (($MAX_LAYERE_SALARY * $SALP_PERCENT) / 100);	
                      
		}
	}*/
                
	// ��Ǩ�ͺ �ѹ�ҡԨ+�һ�������Թ 23 �ѹ 10 ���� �������Թ 18 �ѹ
	//  �һ��� + �ҡԨ
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
		$non_promote_text .= "�������ö����͹����Թ��͹ $PER_NAME �� ���ͧ�ҡ�һ����Թ 23 �ѹ ���� �Թ 10 ����<br>";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;				
		$SALP_REASON = 0;				
	}
	// ��� 
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
		$non_promote_text .= "�������ö����͹����Թ��͹ $PER_NAME �� ���ͧ�ҡ����Թ 18 ����<br>";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;	
		$SALP_REASON = 0;
	}

/*
	// ��Ǩ�ͺ��� ͺ��/�٧ҹ/������
	$cmd = " select PER_ID, TRN_STARTDATE, TRN_ENDDATE from PER_TRAINING where PER_ID=$PER_ID ";
	$db_dpis1->send_cmd($cmd);
	$data1 = $db_dpis1->get_array(); 
	$tmp_date = explode("-", substr($data1[TRN_STARTDATE], 0, 10));
	$TRN_STARTDATE = mktime(0, 0, 0, $tmp_date[1], $tmp_date[2], $tmp_date[0]);
	$tmp_date = explode("-", substr($data1[TRN_ENDDATE], 0, 10));			
	$TRN_ENDDATE = mktime(0, 0, 0, $tmp_date[1], $tmp_date[2], $tmp_date[0]);
	$TIME_TRAINING = ($TRN_ENDDATE - $TRN_STARTDATE) / 2592000; // ���Թҷ���� = 30 �ѹ
	if ($TIME_TRAINING > 12) {
		$non_promote = true;
		$non_promote_text .= "�������ö����͹����Թ��͹ $PER_NAME �� ���ͧ�ҡ��ͺ��/�٧ҹ/������ �Թ�ӹǹ�ѹ����˹�<br>";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;	
		$SALP_REASON = 3;
	}
*/
    
        
	// ��Ǩ�ͺ��Һ�è��Թ 4 ��͹�����ѧ
	$cmd = "	select MOV_CODE, max(POH_EFFECTIVEDATE) as EFFECTIVEDATE from PER_POSITIONHIS 
					where PER_ID=$PER_ID and MOV_CODE in ('101', '10110', '10120', '10130', '10140', '105', '10510', '10520') 
					group by MOV_CODE ";
	$db_dpis1->send_cmd($cmd);
	$data1 = $db_dpis1->get_array();
	$tmp_date1 = explode("-", substr($data1[EFFECTIVEDATE], 0, 10));
	$tmp_date2 = explode("-", date("Y-m-d"));	
	$tmp_date_effect =  mktime(0, 0, 0, $tmp_date1[1], $tmp_date1[2], $tmp_date1[0]);
	$tmp_date_now = mktime(0, 0, 0, $tmp_date2[1], $tmp_date2[2], $tmp_date2[0]);
	$MONTH_FILL = ($tmp_date_now - $tmp_date_effect) / 2592000;		// ���Թҷ���� = 30 �ѹ
	if ($MONTH_FILL < 4) {
		$non_promote = true;
		$non_promote_text .= "�������ö����͹����Թ��͹ $PER_NAME �� ���ͧ�ҡ��è��ѧ����Թ 4 ��͹<br>";
		$SALP_YN = 0;
		$SALP_LEVEL = 0;				
		$SALP_REASON = 4;
	}
        
	// ===== �ӹǹ�š������͹����Թ��͹
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
	// select ��� LAYERE_NO �����������Թ�ӹǹ LAYERE_NO �ͧ LEVEL_NO ��� � �������� 
	// ��Ҩӹǹ��鹷������͹����٧���ҷ���������ԧ ���������֧�� MAX	
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
		$non_promote_text .= "����͹����Թ��͹�ͧ $PER_NAME �����ú����ӹǹ ���ͧ�ҡ�Թ�ӹǹ�Թ��͹������<br>";
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
        //�Ҥ� �дѺ���˹� ����դ���Թ����ش������ѭ��   
    if ($MAX_CH_SALARY >= $MAX_SALARY_EMP ){
// echo"-->>".$PER_NAME."-->". $PER_SALARY." > ".$MAX_CH_SALARY.">>> ".$MAX_SALARY_EMP."|->".$UP_SALARY.">>>".$LEVEL_NO."<br>" ; 
                                  
        if ($PER_SALARY >= $MAX_CH_SALARY ){//������Թ��͹ ��ҡѺ�����ҡ�����ѵ�Ң���٧ �������͹�Թ�����ѭ��
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
                  
			// �Թ��͹���������� ������Ѻ�Թ�ͺ᷹�����
			 if ($PER_SALARY > $UP_SALARY || $SALP_SALARY_OC >= $UP_SALARY || $SALP_SALARY_NEW > $UP_SALARY){ 
                               $SALP_SALARY_NEW = $UP_SALARY;
                                $SALP_YN = 1;
                                $SALP_LEVEL = 0;
                                $SALP_REASON = 2;	

                                // �Թ��͹���������� ������Ѻ�Թ�ͺ᷹�����
                                if ($SALQ_TYPE2 == 1) 		$SALP_PERCENT = 2;
                                elseif ($SALQ_TYPE2 == 2) 		$SALP_PERCENT = 4;
                                $SALP_SPSALARY = (($PER_SALARY * $SALP_PERCENT) / 100);	
                            }
                    
        }else{//������Թ��͹�ѧ���֧�ѵ�Ҥ�Ҩ�ҧ����٧ ���Թ㹡�������ش�����
               //echo $PER_NAME." | ".$PER_SALARY." || ".$MAX_CH_SALARY."||".$UP_SALARY."||".$LEVEL_NO."<<>>"."<br>"; 
              
                            //�Ң�鹤��������������� 
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
                                               //   �Ң��
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
                                        } //end if �ͺ�ͧ    
                                    } 

                        if($SALP_SALARY_CC == $MAX_CH_SALARY ){//����Թ��͹�����͹ ����Ң������� �����Թ��͹��ҡѺ�ѵ�Ҥ�Ҩ�ҧ����٧ ���Թ���������
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

                                           // �Թ��͹���������� ������Ѻ�Թ�ͺ᷹�����
                                           if ($SALQ_TYPE2 == 1) 		$SALP_PERCENT = 2;
                                           elseif ($SALQ_TYPE2 == 2) 		$SALP_PERCENT = 4;
                                           $SALP_SPSALARY = (($PER_SALARY * $SALP_PERCENT) / 100);
                                    }
                        
                        }else{//������Թ��͹�����º����� �Թ��͹�ѧ���֧�ѵҤ�Ҩ�ҧ����٧ 
                        
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

                                           // �Թ��͹���������� ������Ѻ�Թ�ͺ᷹�����
                                           if ($SALQ_TYPE2 == 1) 		$SALP_PERCENT = 2;
                                           elseif ($SALQ_TYPE2 == 2) 		$SALP_PERCENT = 4;
                                           $SALP_SPSALARY = (($PER_SALARY * $SALP_PERCENT) / 100);
                                    }                     
                        }                              
                }
             
 //echo "<pre>".$PER_NAME."�Թ��� ==>".$SALP_SALARY_CC ."�Թ���� ==>". $SALP_SALARY_NEW."��� $LAYERE_NO_CC"." ������ѭ�� $val_pg_code";                    
    }//end �Ҥ� �дѺ���˹� ����դ���Թ����ش������ѭ��  
    
 //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    //������Ѻ�Թ����٧����ش�ѭ�� ����Ѻ�Թ���ش�ѭ��
    $val_end_gr = end($CN_GROUP_SALARY)*1000;
        if($MAX_CH_SALARY < $MAX_SALARY_EMP){ 
        //echo "-->".$PER_NAME."<br>";
                    
                   //�Ң�� ��͹ 
              $cmd =" select LAYERE_NO, LAYERE_SALARY, PG_CODE from PER_LAYEREMP 
                            where PG_CODE in ($val_pg_code) and LAYERE_SALARY >= $PER_SALARY 
                            order by PG_CODE, LAYERE_SALARY" ;
                   $db_dpis1->send_cmd($cmd);
                   $data1 = $db_dpis1->get_array();
                   $LAYERE_NO_CC = $data1[LAYERE_NO]+$SALP_LEVEL;
                   $PG_CODE_CC = trim($data1[PG_CODE]);
                   $LAYERE_A = $data1[LAYERE_SALARY] ;
                   $LAYERE_SALARY_C1 = $data1[LAYERE_SALARY];
                   
                //select ������ ��Ҥ���Թ����º�Ѻ $LAYERE_SALARY_C1    
              $cmd =" select LAYERE_NO, LAYERE_SALARY, PG_CODE from PER_LAYEREMP 
                            where PG_CODE = '$PG_CODE_CC'
                            order by  LAYERE_SALARY DESC" ;
                   $db_dpis1->send_cmd($cmd);
                   $data1 = $db_dpis1->get_array();   
                   $LAYERE_SALARY_C2 = $data1[LAYERE_SALARY];
         
                   //�Թ���������
                   if($LAYERE_SALARY_C1 >= $LAYERE_SALARY_C2){
                   if($SALP_LEVEL == 0.5) $IM = 2;
                   else if($SALP_LEVEL == 1)$IM = 3;
                   
                  // echo"-->>".$PER_NAME."[�Թ��͹]-->".$PER_SALARY." [�Թ��º 1]--> ".$LAYERE_SALARY_C1." >= "."[�Թ��º 2]-->".$LAYERE_SALARY_C2."[�ѵ�Ң���٧]--> ".$MAX_CH_SALARY."[�����]-->  ".$val_pg_code."<br>" ; 
                  
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

                                           // �Թ��͹���������� ������Ѻ�Թ�ͺ᷹�����
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
                 
                                    // �Թ��͹���������� ������Ѻ�Թ�ͺ᷹�����   
                                  if($SALP_SALARY_NEW > $UP_SALARY || $LAYERE_SALARY_C1 >= $UP_SALARY || $PER_SALARY >= $UP_SALARY){ 
                                           $SALP_SALARY_NEW = $UP_SALARY;
                                           $SALP_YN = 1;
                                           $SALP_LEVEL = 0;
                                           $SALP_REASON = 2;	

                                           // �Թ��͹���������� ������Ѻ�Թ�ͺ᷹�����
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
                        $non_promote_text .= "����͹����Թ��͹�ͧ $PER_NAME �����ú����ӹǹ ���ͧ�ҡ�Թ�ӹǹ�Թ��͹������<br>";
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
                                $non_promote_text .= "����͹����Թ��͹�ͧ $PER_NAME �����ú����ӹǹ ���ͧ�ҡ�Թ�ӹǹ�Թ��͹������<br>";
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
	// �ѹ��������ռźѧ�Ѻ��
	if ($SALQ_TYPE2 == 1)			$SALP_DATE = ($SALQ_YEAR - 543) ."-04-01";
	elseif ($SALQ_TYPE2 == 2)		$SALP_DATE = ($SALQ_YEAR - 543) ."-10-01";
	if ($SALQ_TYPE1==1 && $SALQ_TYPE2==1) 			$tmp_SALQ_TYPE = 1;
	elseif ($SALQ_TYPE1==1 && $SALQ_TYPE2==2) 		$tmp_SALQ_TYPE = 2;
	elseif ($SALQ_TYPE1==2 && $SALQ_TYPE2==1) 		$tmp_SALQ_TYPE = 3;
	elseif ($SALQ_TYPE1==2 && $SALQ_TYPE2==2) 		$tmp_SALQ_TYPE = 4;
	elseif ($SALQ_TYPE1==3 && $SALQ_TYPE2==1) 		$tmp_SALQ_TYPE = 5;
	elseif ($SALQ_TYPE1==3 && $SALQ_TYPE2==2) 		$tmp_SALQ_TYPE = 6;	
	$SALP_REMARK = (trim($non_promote_text))? $non_promote_text : "";
	
	// �ѹ�֡������ ŧ table PER_SALPROMOTE 
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

