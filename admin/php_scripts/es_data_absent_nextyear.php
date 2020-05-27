<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");			

        $db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd); 
        $db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd); 
        
        $debug = 0; /*0=�Դ ,1=�Դ ����ʴ� SQL*/
        
	switch($CTRL_TYPE){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
		case 5 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			break;
		case 6 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			$search_org_id_1 = $ORG_ID_1;
			$search_org_name_1 = $ORG_NAME_1;
			break;
	} // end switch case

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

	//	��˹���� default timezone		//phpinfo();
	function set_default_timezone($timezone){
		if (version_compare(phpversion(), '5', '>=')){
			if(function_exists('date_default_timezone_set')) { 
				$result_set = date_default_timezone_set($timezone); 	// PHP  >= 5.1.0
				//echo date_default_timezone_get();	// PHP  >= 5.1.0
			} 
		}else{		// < version 5
			$result_set = ini_set('date.timezone',$timezone);
		}
	return $result_set;
	}
	
	set_default_timezone('Asia/Bangkok');	// ���������������⫹����˹�
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$CREATE_DATE = date("Y-m-d H:i A", time());

	
	
	
	function get_sum_absday($db_dpis1,$PER_ID,$TMP_START_DATE,$TMP_END_DATE){
            /*
            :PER_ID, 
            :FIRST_DATE='2015-10-01'		�ѹ���������鹻է�����ҳ
            :END_DATE='2016-09-30'		�ѹ�������ش�է�����ҳ
            */
                $cmd="
            WITH
            PakPonInFiscalYear AS
            (
              select * from PER_ABSENTHIS pa where pa.PER_ID=:PER_ID and trim(ab_code) = '04' and
                pa.abs_startdate between cast(to_char(TO_DATE(:FIRST_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) and 
                                         cast(to_char(TO_DATE(:END_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) and
                pa.abs_enddate between cast(to_char(TO_DATE(:FIRST_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) and 
                                       cast(to_char(TO_DATE(:END_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19))
            ),
            SeparateBegin AS
            (
              select * from PER_ABSENTHIS pa where pa.PER_ID=:PER_ID and trim(ab_code) = '04' and
                pa.abs_startdate < cast(to_char(TO_DATE(:FIRST_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) and 
                pa.abs_enddate between cast(to_char(TO_DATE(:FIRST_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) and
                                  cast(to_char(TO_DATE(:END_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) 
            ),
            SeparateEnd AS
            (
              select * from PER_ABSENTHIS pa where pa.PER_ID=:PER_ID and trim(ab_code) = '04' and
                pa.abs_enddate > cast(to_char(TO_DATE(:END_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) and 
                pa.abs_startdate between cast(to_char(TO_DATE(:FIRST_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) and
                                  cast(to_char(TO_DATE(:END_DATE, 'YYYY-MM-DD'),'YYYY-MM-DD') as char(19)) 
            )

            select nvl(sum(abs_day),0) sum_abs  from (
              select 1 a,ABS_STARTDATE,abs_day 
                from PakPonInFiscalYear 
              union all
                select 2,WORK_DATE,case when WORK_DATE=trim(b.ABS_STARTDATE) then 
                        case when b.ABS_STARTPERIOD=3 then 1 else 0.5 end
                      else case when WORK_DATE=trim(b.ABS_ENDDATE) then 
                              case when b.ABS_ENDPERIOD=3 then 1 else 0.5 end
                            else 1
                           end
                  end ABS_DAY
                from SeparateBegin b
                left join (
                  SELECT TO_CHAR((TO_DATE((select min(abs_startdate) from SeparateBegin), 'YYYY-MM-DD'))-1+ ROWNUM,'YYYY-MM-DD') AS WORK_DATE FROM ALL_OBJECTS
                        WHERE (TO_DATE((select min(abs_startdate) from SeparateBegin), 'YYYY-MM-DD'))-1+ ROWNUM 
                            <= TO_DATE((select max(abs_enddate) from SeparateBegin), 'YYYY-MM-DD')
                            and  TO_CHAR((TO_DATE((select min(abs_startdate) from SeparateBegin), 'YYYY-MM-DD'))-1+ ROWNUM, 'DY', 'NLS_DATE_LANGUAGE=ENGLISH') NOT IN ('SAT', 'SUN')
                  ) d on (not exists (select null from PER_HOLIDAY 
                                      where HOL_DATE = d.WORK_DATE))
                and WORK_DATE between :FIRST_DATE and :END_DATE
              union all
                select 3,WORK_DATE,case when WORK_DATE=trim(e.ABS_STARTDATE) then 
                        case when e.ABS_STARTPERIOD=3 then 1 else 0.5 end
                      else case when WORK_DATE=trim(e.ABS_ENDDATE) then 
                              case when e.ABS_ENDPERIOD=3 then 1 else 0.5 end
                            else 1
                           end
                  end ABS_DAY
                from SeparateEnd e
                left join (
                  SELECT TO_CHAR((TO_DATE((select min(abs_startdate) from SeparateEnd), 'YYYY-MM-DD'))-1+ ROWNUM,'YYYY-MM-DD') AS WORK_DATE FROM ALL_OBJECTS
                        WHERE (TO_DATE((select min(abs_startdate) from SeparateEnd), 'YYYY-MM-DD'))-1+ ROWNUM 
                            <= TO_DATE((select max(abs_enddate) from SeparateEnd), 'YYYY-MM-DD')
                            and  TO_CHAR((TO_DATE((select min(abs_startdate) from SeparateEnd), 'YYYY-MM-DD'))-1+ ROWNUM, 'DY', 'NLS_DATE_LANGUAGE=ENGLISH') NOT IN ('SAT', 'SUN')
                  ) d on (not exists (select null from PER_HOLIDAY 
                                      where HOL_DATE = d.WORK_DATE))
                and WORK_DATE between :FIRST_DATE and :END_DATE
            )";
                $cmd=str_replace(":PER_ID",$PER_ID,$cmd);
                $cmd=str_replace(":FIRST_DATE","'".$TMP_START_DATE."'",$cmd);
                $cmd=str_replace(":END_DATE","'".$TMP_END_DATE."'",$cmd);
                $db_dpis1->send_cmd_fast($cmd);
                //echo  '<br>'.$cmd.'<br>';
                $data1 = $db_dpis1->get_array_array();
                $sum_absday = $data1[0]; //C
                return $sum_absday;
        }
        
    if( $command == "ADD"){    
        //========================================= 05/09/2561 ==============================================
        //======================== �ӹǳ �����ѹ�������ѵ��ѵ� ����Ͷ֧���� �Թ 6 ��͹ =================================
        $tests=0; //0=�����ҹ , 1=��ҹ
        $debug=0; //0=�����ҹ , 1=��ҹ
        //if($tests==1 && ($PER_ID == 16435 || $PER_ID == 16501)){
        if($PER_ID){
            if($debug==1){echo  '<br>  ��÷Ѵ��� ->'.__LINE__.'<br>    - PER_ID: '.$PER_ID;}

            // �� max_id PER_ABSENTSUM 
            $cmd = " select max(AS_ID) as max_id from PER_ABSENTSUM ";
            $db_dpis->send_cmd($cmd);
            $data = $db_dpis->get_array();
            $data = array_change_key_case($data, CASE_LOWER);
            $AS_ID = $data[max_id] + 1;


            //============================== ������ PERSONAL ===================================
            $cmd = " select PER_ID, PER_STARTDATE, PER_CARDNO ,PER_TYPE
                       from PER_PERSONAL  where PER_STATUS = 1  and PER_ID = $PER_ID";

            $db_dpis->send_cmd($cmd);
            $data = $db_dpis->get_array();
            if($debug==1){echo  '<br> ��÷Ѵ��� ->'.__LINE__.'<pre>'.$cmd;}
            //$db_dpis->show_error();

            $temp_date = explode("/", $PER_STARTDATE);
			$CHECK_DATE = $temp_date[2] ."-". $temp_date[1] ."-". $temp_date[0];
            //echo $CHECK_DATE;
			
            $VC_YEAR=$temp_date[2]; 
            if($temp_date[2].$temp_date[1].$temp_date[0]>=$temp_date[2].'1001'){ 
              $VC_YEAR=$temp_date[2]+1; 
            }
            //echo $date_now;
            $CHECK_DATE_NEXT_YEAR = ($VC_YEAR-543)."-10-01"; 
            $CHECK_DATE_BEFORE_YEAR = ($VC_YEAR-1-543)."-10-01";
            $sep_CHECK_DATE = ($VC_YEAR-543)."-09-30";
            $A_CHECK_STARTDATE = ($VC_YEAR-543)."-04-01";
            $tmp_vc_year = $VC_YEAR - 1;

            $A_START_DATE = ($VC_YEAR-1-544)."-10-01";//���/ �� �� ��͹˹��
            $A_END_DATE = ($VC_YEAR-1-543)."-09-30";//���/ �� �� ��͹˹��

            $VC_START_DATE_1 = ($VC_YEAR -544) . "-10-01"; 
            $VC_END_DATE_1 = $VC_YEAR-543 . "-03-31";

            $VC_START_DATE_2 = ($VC_YEAR-543) . "-04-01"; 
            $VC_END_DATE_2 = $VC_YEAR-543 . "-09-30"; 
            $PER_CARDNOS = trim($data[PER_CARDNO]);
            $PER_STARTDATE_1 = trim($data[PER_STARTDATE]);
            $A_PER_TYPE = trim($data[PER_TYPE]);
            //==================================== END ========================================

            if($debug==1){echo '<meta http-equiv="Content-Type" content="text/html; charset=windows-874">';}
            
            $cmd_con = " select VC_DAY from PER_VACATION where VC_YEAR = '$VC_YEAR'  and PER_ID = $PER_ID ";
            $db_dpis2->send_cmd($cmd_con);
            if($debug==1){echo  '<br> ��÷Ѵ��� ->'.__LINE__.'<pre>'.$cmd_con."<br>";}
            $data_con = $db_dpis2->get_array();
            if($debug==2){echo  '<br> ��÷Ѵ��� ->'.__LINE__.'VC_DAY:'.$data_con[VC_DAY];}
            if($data_con[VC_DAY] < 1){
                if($debug==1){echo '<br> ��÷Ѵ��� ->'.__LINE__.'== '.$A_CHECK_STARTDATE." >= ".$PER_STARTDATE_1."<br>";}
                if ($A_CHECK_STARTDATE >= $PER_STARTDATE_1) { //��èء�͹�ѹ��� 1 ����¹

                    if($debug==2){echo '<br>��÷Ѵ��� ->'.__LINE__.'CHECK_DATE:'.$CHECK_DATE." , PER_STARTDATE:".$PER_STARTDATE_1."<br>";}

                    $AGE_DIFF = date_vacation($CHECK_DATE, $PER_STARTDATE_1, "full");// ત ��� �ѧ�ѹ���ӹǳ���ҧ��
                    $arr_temp = explode(" ", $AGE_DIFF);
                    if($debug==2){echo '��÷Ѵ��� ->'.__LINE__.'arr_temp[0]='.$arr_temp[0]."<br>";}
                    if($debug==2){echo '��÷Ѵ��� ->'.__LINE__.'arr_temp[2]='.$arr_temp[2]."<br>";}
                    if($debug==2){echo '��÷Ѵ��� ->'.__LINE__.'arr_temp[4]='.$arr_temp[4]."<br>";}
                    //�������
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

                    $Leave_Cumulative=0; // def �ѹ������
                    if($debug==2){echo '��÷Ѵ��� ->'.__LINE__.'<h1>�������Ҫ������ �֧ 6 ��͹ ���� �ҡ���� 6 ��͹����������� </h1><br>';}
                    if($debug==1){echo '��÷Ѵ��� ->'.__LINE__.'<h1>�����Ҫ���'.$arr_temp[0].'�� '.$arr_temp[2].'��͹ '.$arr_temp[4].'�ѹ</h1><br>';}

                    //================================================================================================
                    //=========================== �������Ҫ��ö֧ 6 ��͹ ���� �Թ 6 ��͹ �����ѧ ============================
                    if( $Governor_Age_next_year>=600 || $Governor_Age>=600 ){ //�����Ҫ����ҡ����������ҡѺ 6 ��͹������� m dd
                        
						// ����� �բ����Żշ�����������ѧ
						$tmpVC_YEAR=$VC_YEAR-1;
						$cmd = " select VC_DAY from PER_VACATION 
							where VC_YEAR = '$tmpVC_YEAR'  and PER_ID = $PER_ID AND VC_DAY>0 ";
						$count_data = $db_dpis1->send_cmd($cmd);
						if($debug==1){echo __LINE__.':select = '.$cmd.'<br><br>';}
		
						if ($count_data){
							if($arr_temp[0]==0){ //��ѡ��͹
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
								 if($A_PER_TYPE==4){ // �١��ҧ���Ǥ��� ��������� �Ѵ������
									$AC_COLLECT = 10;
								 }
								 $employees_year_up=TRUE;
							}
							if($arr_temp[0]>10){ //10up
								 $AC_DAY = 10;
								 $AC_COLLECT = 30;
								 if($A_PER_TYPE==4){ // �١��ҧ���Ǥ��� ��������� �Ѵ������
									$AC_COLLECT = 10;
								 }
								 $employees_year_up=TRUE;
							}
						}else{
											
							if(strlen ($arr_temp[2])==1){$XarrM= '0'.$arr_temp[2];}else{$XarrM= $arr_temp[2];}
								$xYM = (int) $arr_temp[0].'.'.$XarrM;
								if($xYM < 0.06){ //��ѡ��͹
									$AC_DAY = 0;
									$AC_COLLECT = 0;
									//echo '<br>���¡��� 6 ��͹ AC_DAY='.$AC_DAY.'|| AC_COLLECT'.$AC_COLLECT.'<br>';
								}
								if($xYM >= 0.06){ // �ҡ���� 6 ��͹ ���¡��� 1 ��
									$AC_DAY = 10;
									$AC_COLLECT = 10;
									//echo '<br>6 ��͹���¡��� 1 �� AC_DAY='.$AC_DAY.'|| AC_COLLECT'.$AC_COLLECT.'<br>';
								}
		
								if(($xYM >= 1)  && $arr_temp[0]<10){ //between 1 �� -10 ��
									 $AC_DAY = 10;
									 $AC_COLLECT = 20;
									 if($A_PER_TYPE==4){ // �١��ҧ���Ǥ��� ��������� �Ѵ������
										$AC_COLLECT = 10;
									 }
									 //echo '<br>1 �չ��¡��� 10 �� AC_DAY='.$AC_DAY.'|| AC_COLLECT'.$AC_COLLECT.'<br>';
									 $employees_year_up=TRUE;
								}
								if($arr_temp[0]>=10){ //10up
									 $AC_DAY = 10;
									 $AC_COLLECT = 30;
									 if($A_PER_TYPE==4){ // �١��ҧ���Ǥ��� ��������� �Ѵ������
										$AC_COLLECT = 10;
									 }
									 //echo '<br> 10 �բ��� AC_DAY='.$AC_DAY.'|| AC_COLLECT'.$AC_COLLECT.'<br>';
									 $employees_year_up=TRUE;
								}
							
							
						}	
							
                        $Leave_Cumulative=10; //A
                        //��˹�����������
                        if($Governor_Age<600){ //���¡��� 6 ��͹
                           $Leave_Cumulative=0;					
                        }
                        $Before_vc_day=0;//N

                        if($debug==1){echo __LINE__.':Governor_Age '.$Governor_Age.'<br>';}
                        if($Governor_Age>600){ //Y
                           //�� Record ��ҵ�駵�?�շ������
                            $cmd = " select VC_DAY from PER_VACATION where VC_YEAR='$tmp_vc_year'and PER_ID=$PER_ID ";
                            if($debug==1){echo __LINE__.':<pre>'.$cmd.'<br>';}
                            $cnt=$db_dpis1->send_cmd($cmd);
                            //$Before_vc_day=10;//N //Comment Release 5.2.1.19 20180403 
                            if($cnt){//Y
                                $data1 = $db_dpis1->get_array();
                                $Before_vc_day = $data1[VC_DAY]; 
                            }else{
                                // ������� Kiitphat if($Governor_Age>=1600){ //between 1 to 10 =20 day
                                if($Governor_Age>=1600 && $Governor_Age < 100000){ //between 1 to 10 =20 day
                                    $Before_vc_day = 20; 
									if($A_PER_TYPE==4){ // �١��ҧ���Ǥ��� ��������� �Ѵ������
										$AC_COLLECT = 10;
									 }
                                }elseif($Governor_Age>=100000){ //10up= 30 day
                                    $Before_vc_day = 30; 
									if($A_PER_TYPE==4){ // �١��ҧ���Ǥ��� ��������� �Ѵ������
										$AC_COLLECT = 10;
									 }
                                }
                            }
                        }
                        //�ӹǹ�Ҿѡ��͹�ͧ�է���͹˹��
                        $sum_absday= get_sum_absday($db_dpis1,$PER_ID,$A_START_DATE,$A_END_DATE);
                        if($debug==1){echo __LINE__.':sum_absday==>'.$sum_absday.'<br>';}
                        //��ػ�ѹ������ѹ�Ҿѡ��͹�����ͧ�է���͹˹��
                        $cmd = " select sum(AB_CODE_04) as abs_day from PER_ABSENTSUM 
                                 where PER_ID=$PER_ID and START_DATE >= '$A_START_DATE' and END_DATE <= '$A_END_DATE' ";
                        $db_dpis1->send_cmd($cmd);
                        if($debug==1){echo __LINE__.':<pre>'.$cmd.'<br>';}
                        $data1 = $db_dpis1->get_array();
                        $data1 = array_change_key_case($data1, CASE_LOWER);
						
                        if($A_PER_TYPE==4){ // �١��ҧ���Ǥ��� ��������� �Ѵ������
							$Before_abs_day_vacation = 0; 
						}else{
							$Before_abs_day_vacation = $data1[abs_day]+0; 
						}
                        if($debug==1){echo __LINE__.':Before_abs_day_vacation==>'.$Before_abs_day_vacation.'<br>';}

                        $Before_vc_day=$Before_vc_day-($sum_absday+$Before_abs_day_vacation);

                        if($debug==1){echo __LINE__.':<font color=red>'.$Before_vc_day.'='.$Before_vc_day.'-('.$sum_absday.'+'.$Before_abs_day_vacation.')</font><br>';}

                        if($debug==1){echo __LINE__.':Before_vc_day==>'.$Before_vc_day.','.$Before_abs_day_vacation.'<br>';}
                        if($debug==1){echo __LINE__.':'.$CHECK_DATE_NEXT_YEAR.' �����Ҫ��ûշ���ԡ�����ż� ==>'.$Governor_Age.'<br>';}

                        if($Before_vc_day>0){
                           
                            if($debug==1){echo __LINE__.':Before_vc_day==>'.$Before_vc_day.','.$Before_abs_day_vacation.'<br>';}
                            $Leave_Cumulative=$Leave_Cumulative+$Before_vc_day;


                          

                        }
                         if($debug==1){echo __LINE__.':Leave_Cumulative==>'.$Leave_Cumulative.'<br>';}
                    }else{ // N
                        $Leave_Cumulative=0;
                    }
                    if($Governor_Age>=100000){ // 10up
                        $AC_COLLECT = 30;
						if($A_PER_TYPE==4){ // �١��ҧ���Ǥ��� ��������� �Ѵ������
							$AC_COLLECT = 10;
						 }
                    }
                    if($debug==1){echo __LINE__.':'.$Governor_Age.'>=600 && '.$Leave_Cumulative.'>'.$AC_COLLECT.'<br>';}
                    if($Governor_Age>=600 && $Leave_Cumulative>$AC_COLLECT ){ //�����Ҫ����ҡ����������ҡѺ 10 ��������� yy mm dd
                        $Leave_Cumulative=$AC_COLLECT;// 30 �����٧�ش�ͧ�������͹�
                    }
                    if($debug==2){
                        echo '<br>====================<br><h1>�����Ҫ���: '.$Governor_Age.' ,�ѹ�������������:'.$Leave_Cumulative.' �����٧�ش ['.$AC_COLLECT.']</h1><br>====================<br>';
                    }
                    //===================================================================================
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
                        if($debug==1){echo __LINE__.':���ؾ�ѡ�ҹ�Ҫ���Governor_Age = '.$Governor_Age.'<br><br>';}

                    }else{
                        $AC_DAY=$Leave_Cumulative;
                    }
                    if($Governor_Age<600){ //���¡��� 6 ��͹

                       $AC_DAY=0;

                    }
                    $cmd = " select VC_DAY from PER_VACATION where VC_YEAR = '$VC_YEAR'  and PER_ID = $PER_ID AND VC_DAY>0   ";
                    $chk_count_data = $db_dpis->send_cmd($cmd);
                    if($debug==1){echo '<br> ��÷Ѵ��� ->'.__LINE__.':select = '.$cmd.'<br><br>';}
                    if (!$chk_count_data) {
						$cmd = " delete from PER_VACATION where VC_YEAR = '$VC_YEAR'  and PER_ID = $PER_ID ";
						$db_dpis->send_cmd($cmd);
										
                        $cmd = " insert into PER_VACATION (VC_YEAR, PER_ID, VC_DAY, UPDATE_USER, UPDATE_DATE) 
                                                            values ('$VC_YEAR', $PER_ID, $AC_DAY, $SESS_USERID, '$UPDATE_DATE') ";
                        $db_dpis->send_cmd($cmd);
                        if($debug==1){echo '<br> ��÷Ѵ��� ->'.__LINE__.':insert = '.$cmd.'<br><br>';}
                        //insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ���������� [".trim($VC_YEAR)." : ".$PER_ID."]");
						
						
                    }
                }
				
				// kittiphat comment 09/10/2561
                $cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$PER_ID and AS_YEAR = '$VC_YEAR' and AS_CYCLE = 1 ";
                $count=$db_dpis->send_cmd($cmd);
                if($debug==1){echo '<br> ��÷Ѵ��� ->'.__LINE__.':'.$cmd.'<br><br>';}
                //$db_dpis->show_error(); echo "<hr>$cmd<br>";
                if(!$count) { 
                    $cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
                                                        values ($AS_ID, $PER_ID, '$VC_YEAR', 1, '$VC_START_DATE_1', '$VC_END_DATE_1', '$PER_CARDNOS', $SESS_USERID, '$UPDATE_DATE') ";
                    $db_dpis->send_cmd($cmd);
                    if($debug==1){echo '<br> ��÷Ѵ��� ->'.__LINE__.': insert = '.$cmd.'<br><br>';}
                    //$db_dpis->show_error();
                    $AS_ID++;
                }
                $cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$PER_ID and AS_YEAR = '$VC_YEAR' and AS_CYCLE = 2 ";
                $count=$db_dpis->send_cmd($cmd);
                if($debug==1){echo '<br> ��÷Ѵ��� ->'.__LINE__.':'.$cmd.'<br><br>';}
                //$db_dpis->show_error(); echo "<hr>$cmd<br>";
                if(!$count) { 
                    $cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
                                                    values ($AS_ID, $PER_ID, '$VC_YEAR', 2, '$VC_START_DATE_2', '$VC_END_DATE_2', '$PER_CARDNOS', $SESS_USERID, '$UPDATE_DATE') ";
                    $db_dpis->send_cmd($cmd);
                    if($debug==1){echo '<br> ��÷Ѵ��� ->'.__LINE__.': insert = '.$cmd.'<br><br>';}
                    //$db_dpis->show_error();
                    $AS_ID++;
                }
				
            }
        }
		
		$command = "";
		$returnValue = "$PER_ID<::>$PER_STARTDATE<::>$AC_DAY<::>$send_by";
		echo "<script>parent.refresh_opener('".$returnValue."');</script>";
		
	}
        /*=================================================================================================*/
        /*============================== END ��÷ӧҹ �ӹǳ�ѹ�Ҿѡ��͹����  =================================*/
        
        
	
        
	
?>