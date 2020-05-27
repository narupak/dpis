<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");	
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        $db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        
        ini_set("max_execution_time", 0);
        
        $debug=0;/*0=close,1=open*/
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
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	//if(!$search_vc_year)	$search_vc_year = $KPI_BUDGET_YEAR;/*���*/
        /*Release 5.2.1.1 begin*/
        $cmdCycle = "CASE WHEN  to_char(sysdate,'YYYYmmdd') BETWEEN to_char(sysdate,'YYYY')||'1001' AND to_char(sysdate,'YYYY')+1||'0930'
THEN to_char(sysdate,'YYYY')+544 ELSE to_char(sysdate,'YYYY')+543 END AS newyear";
        $db_dpis2->send_cmd($cmdCycle);
        $datasysdate = $db_dpis2->get_array_array();
        $year_sysdate =  $datasysdate[0];
        if(!$search_vc_year)	$search_vc_year = $year_sysdate;/*Release 5.2.1.1*/
        /*Release 5.2.1.1 end*/
        
	if(!isset($search_vc_year)){
		if(date("Y-m-d") <= date("Y")."-10-01") $search_vc_year = date("Y") + 543;
		else $search_vc_year = (date("Y") + 543) + 1;
	} // end if
	
	$UPDATE_DATE = date("Y-m-d H:i:s");	

	if($command == "UPDATE_SCORE"){
		foreach($VC_DAY as $update_id => $update_point) {
			$cmd = " select 	VC_DAY
					  		 from 		PER_VACATION 
							 where 	VC_YEAR='$search_vc_year' and PER_ID = $update_id ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$VC_DAY = $data[VC_DAY];

			if ($VC_DAY != $update_point) {
				$cmd = " update PER_VACATION set 
								VC_DAY = $update_point,
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE' 
							 where VC_YEAR='$search_vc_year' and PER_ID=$update_id ";
				$db_dpis->send_cmd($cmd);											
//				$db_dpis->show_error();
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ����ѹ�Ҿѡ��͹���� [".trim($search_vc_year)." : ".$update_id."]");
			}
		}
	}
	
	if($command == "DELETE" && trim($VC_YEAR) && trim($PER_ID)){
		$cmd = " select PN_CODE, PER_NAME, PER_SURNAME from PER_PERSONAL where PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_CODE = $data[PN_CODE];
		$PER_NAME = $data[PER_NAME];
		$PER_SURNAME = $data[PER_SURNAME];
			
		$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_NAME = $data[PN_NAME];
			
		$PER_FULLNAME = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;
		
		$cmd = " delete from PER_VACATION where VC_YEAR='$VC_YEAR' and PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź������ [". $VC_YEAR." ".$PER_ID." ".$PER_FULLNAME."]");
	}
	
	if($command == 'COPY_ALL') {
		$debug=0;
		if($search_org_id){
			if($select_org_structure==0){
				$search_con = " and b.ORG_ID = $search_org_id ";
			}else if($select_org_structure==1){
				$search_con = " and a.ORG_ID = $search_org_id ";
			}
		}elseif($search_department_id){
			$search_con = " and a.DEPARTMENT_ID = $search_department_id ";
		}
		if($search_per_name)	$search_con .= " and a.PER_NAME like '$search_per_name%' ";
		if($search_per_surname) $search_con .= " and a.PER_SURNAME like '$search_per_surname%' ";

		if ($command == 'COPY_ALL') {
                    
			if($search_per_type==1 || $search_per_type==5) 
				if ($SESS_DEPARTMENT_NAME=="�����û���ͧ")
					$cmd = " delete from PER_VACATION where VC_YEAR = '$search_vc_year'  and PER_ID in (select a.PER_ID from PER_PERSONAL a, PER_POSITION b 
										where a.PAY_ID = b.POS_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con) ";
				else
					$cmd = " delete from PER_VACATION where VC_YEAR = '$search_vc_year'  and PER_ID in (select a.PER_ID from PER_PERSONAL a, PER_POSITION b 
										where a.POS_ID = b.POS_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con) ";
			elseif($search_per_type==2) 
				$cmd = " delete from PER_VACATION where VC_YEAR = '$search_vc_year'  and PER_ID in (select a.PER_ID from PER_PERSONAL a, PER_POS_EMP b 
									where a.POEM_ID = b.POEM_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con) ";
			elseif($search_per_type==3) 
				$cmd = " delete from PER_VACATION where VC_YEAR = '$search_vc_year'  and PER_ID in (select a.PER_ID from PER_PERSONAL a, PER_POS_EMPSER b 
									where a.POEMS_ID = b.POEMS_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con) ";
			elseif($search_per_type==4) 
				$cmd = " delete from PER_VACATION where VC_YEAR = '$search_vc_year'  and PER_ID in (select a.PER_ID from PER_PERSONAL a, PER_POS_TEMP b 
									where a.POT_ID = b.POT_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con) ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			
		}

		$cmd = " select max(AS_ID) as max_id from PER_ABSENTSUM ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$AS_ID = $data[max_id] + 1;
				
		if($search_per_type==1 || $search_per_type==5) 
			if ($SESS_DEPARTMENT_NAME=="�����û���ͧ")
				$cmd = "	select a.PER_ID, a.PER_STARTDATE, PER_CARDNO 
									from PER_PERSONAL a, PER_POSITION b where a.PAY_ID = b.POS_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con ";
			else
				$cmd = "	select a.PER_ID, a.PER_STARTDATE, PER_CARDNO 
									from PER_PERSONAL a, PER_POSITION b where a.POS_ID = b.POS_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con ";
		elseif($search_per_type==2) 
			$cmd = "	select a.PER_ID, a.PER_STARTDATE, PER_CARDNO  
								from PER_PERSONAL a, PER_POS_EMP b where a.POEM_ID = b.POEM_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con ";
		elseif($search_per_type==3) 
			$cmd = "	select a.PER_ID, a.PER_STARTDATE, PER_CARDNO  
								from PER_PERSONAL a, PER_POS_EMPSER b where a.POEMS_ID = b.POEMS_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con ";
		elseif($search_per_type==4) 
			$cmd = "	select a.PER_ID, a.PER_STARTDATE, PER_CARDNO  
								from PER_PERSONAL a, PER_POS_TEMP b where a.POT_ID = b.POT_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con ";
		$db_dpis->send_cmd($cmd);
                
                if($debug==1){echo '<pre>'.$cmd;}
		//$db_dpis->show_error();
		
		$CHECK_DATE = ($search_vc_year-544)."-10-01"; /*��Ѻ����*/
                $CHECK_DATE_NEXT_YEAR = ($search_vc_year-543)."-10-01"; /*��Ѻ����*/
                $CHECK_DATE_BEFORE_YEAR = ($search_vc_year-1-543)."-10-01"; /*��Ѻ����*/
                //$CHECK_DATE = ($search_vc_year-543)."-10-01";/*���*/
                $sep_CHECK_DATE = ($search_vc_year-543)."-09-30";/*�������*/
                
		$TMP_START_DATE = ($search_vc_year-1-544)."-10-01";/*���*/ //�� �� ��͹˹��
		$TMP_END_DATE = ($search_vc_year-1-543)."-09-30";/*���*/ //�� �� ��͹˹��
                
                
		$CHECK_STARTDATE = ($search_vc_year-543)."-04-01";
		$tmp_vc_year = $search_vc_year - 1;
                
		//$START_DATE_1 = ($search_vc_year - 1) . "-10-01"; //<<<=���
                //$END_DATE_1 = $search_vc_year . "-03-31";//<<<=���
                $START_DATE_1 = ($search_vc_year -544) . "-10-01"; //��Ѻ��ا
		$END_DATE_1 = $search_vc_year-543 . "-03-31";//��Ѻ��ا
                
		//$START_DATE_2 = $search_vc_year . "-04-01"; //<<<=���
                //$END_DATE_2 = $search_vc_year . "-09-30"; //<<<=���
                $START_DATE_2 = ($search_vc_year-543) . "-04-01"; //��Ѻ��ا
		$END_DATE_2 = $search_vc_year-543 . "-09-30"; //��Ѻ��ا
                if($debug==1){echo '<meta http-equiv="Content-Type" content="text/html; charset=windows-874">';}
                
		
                while($data = $db_dpis->get_array()){
			$PER_ID = $data[PER_ID];
			$PER_CARDNO = trim($data[PER_CARDNO]);
			$PER_STARTDATE = trim($data[PER_STARTDATE]);
                        
                        if($debug==1){echo __LINE__.': '.$CHECK_STARTDATE." >= ".$PER_STARTDATE."<br>";}
			if ($CHECK_STARTDATE >= $PER_STARTDATE) { /*��èء�͹�ѹ��� 1 ����¹*/
                               // echo 'today : '.date('Y-m-d').',CHECK_DATE:'.$CHECK_DATE.'<br>';
                                if($debug==1){echo __LINE__.':CHECK_DATE:'.$CHECK_DATE." , PER_STARTDATE:".$PER_STARTDATE."<br>";}
				//$AGE_DIFF = date_vacation('2020-10-01', '2018-04-07', "full");/* ત ��� �ѧ�ѹ���ӹǳ���ҧ��*/
				$AGE_DIFF = date_vacation($CHECK_DATE, $PER_STARTDATE, "full");/* ત ��� �ѧ�ѹ���ӹǳ���ҧ��*/
				if($debug==1){echo __LINE__.'<br><br>AGE_DIFF='. $AGE_DIFF."<br>";}
				$arr_temp = explode(" ", $AGE_DIFF);
                                if($debug==1){echo __LINE__.':arr_temp[0]='.$arr_temp[0]."<br>";}
                                if($debug==1){echo __LINE__.':arr_temp[2]='.$arr_temp[2]."<br>";}
                                if($debug==1){echo __LINE__.':arr_temp[4]='.$arr_temp[4]."<br>";}
                                /*�������*/
                                $AGE_DIFF_NEXT_YEAR = date_vacation($CHECK_DATE_NEXT_YEAR, $PER_STARTDATE, "full");
                                $arr_temp_next_year = explode(" ", $AGE_DIFF_NEXT_YEAR);
                                
                                $AGE_DIFF_BEFORE_YEAR = date_vacation($CHECK_DATE_BEFORE_YEAR, $PER_STARTDATE, "full");
                                $arr_temp_before_year = explode(" ", $AGE_DIFF_BEFORE_YEAR);
                                
                                /*============================��Ѻ����==========================*/
                                $test =1;/*0=close,1=test*/
                                $employees_year_up=FALSE;
                                if($test==1){
                                    $Governor_Age_Sep = 0;
                                    $Governor_Age = ($arr_temp[0]*10000)+($arr_temp[2]*100)+(($arr_temp[4]*1));
                                    $Governor_Age_next_year = ($arr_temp_next_year[0]*10000)+($arr_temp_next_year[2]*100)+(($arr_temp_next_year[4]*1));
                                    $Governor_Age_before = ($arr_temp_before_year[0]*10000)+($arr_temp_before_year[2]*100)+(($arr_temp_before_year[4]*1));
                                    
                                    if($debug==1){echo __LINE__.':<h1>���� '.$arr_temp[0].'�� '.$arr_temp[2].'��͹ '.$arr_temp[4].'�ѹ</h1><br>';}
                                    
                                    $dd=$arr_temp[4];
                                    if(strlen($dd)==1){$dd='0'.$dd;}
                                   
                                    $Leave_Cumulative=0; // def �ѹ������
									
                                    if($debug==1){echo __LINE__.': Governor_Age_next_year=>'.$Governor_Age_next_year.',Age==>'.$Governor_Age.'<br>';}
                                    
                                    if( $Governor_Age_next_year>=600 || $Governor_Age>=600 ){ //�����Ҫ����ҡ����������ҡѺ 6 ��͹������� m dd
                                        
                                        // ����� �բ����Żշ�����������ѧ
										$tmpVC_YEAR=$search_vc_year-1;
										$cmd = " select VC_DAY from PER_VACATION 
											where VC_YEAR = '$tmpVC_YEAR'  and PER_ID = $PER_ID AND VC_DAY>0 ";
										$count_data = $db_dpis1->send_cmd($cmd);
										if($debug==1){echo __LINE__.':select = '.$cmd.'<br><br>';}
										//�Է�����ѹ������ �. �Ҥ��
										if($chk_ex_absen_day=='Y'){
											$ex_absen_day= intval($ex_absen_day);
										}else{
											$ex_absen_day=0;
										}
										
										if ($count_data){
											if($arr_temp[0]==0){ //��ѡ��͹
												$cmd = " SELECT AC_DAY, AC_COLLECT FROM PER_ABSENTCOND 
															WHERE AC_FLAG = 1 AND ($arr_temp[2]/12) < AC_GOV_AGE ";
												$db_dpis1->send_cmd($cmd);
												$data1 = $db_dpis1->get_array();
												$AC_DAY = $data1[AC_DAY]+$ex_absen_day;
												$AC_COLLECT = $data1[AC_COLLECT];
												if($search_per_type==4){ // �١��ҧ���Ǥ��� ��������� �Ѵ������
													$AC_COLLECT = 10+$ex_absen_day;
												}
												if($debug==1){echo __LINE__.': AC_COLLECTx=>'.$AC_COLLECT.'==><pre>'.$cmd.'<br>';}
											}
											if($arr_temp[0]>=1 && $arr_temp[0]<10){ //between 1-10
											
												 $AC_DAY = 10+$ex_absen_day;
												 $AC_COLLECT = 20+$ex_absen_day;
												 if($search_per_type==4){ // �١��ҧ���Ǥ��� ��������� �Ѵ������
													$AC_COLLECT = 10+$ex_absen_day;
												 }
												 $employees_year_up=TRUE;
												 if($debug==1){echo __LINE__.': AC_COLLECTy=>'.$AC_COLLECT.'==><pre><br>';}
											}
											if($arr_temp[0]>=10){ //10up
												 $AC_DAY = 10+$ex_absen_day;
												 $AC_COLLECT = 30+$ex_absen_day;
												 if($search_per_type==4){ // �١��ҧ���Ǥ��� ��������� �Ѵ������
													$AC_COLLECT = 10+$ex_absen_day;
												 }
												 $employees_year_up=TRUE;
												 if($debug==1){echo __LINE__.': AC_COLLECTz=>'.$AC_COLLECT.'==><pre><br>';}
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
													$AC_DAY = 10+$ex_absen_day;
													$AC_COLLECT = 10+$ex_absen_day;
													//echo '<br>6 ��͹���¡��� 1 �� AC_DAY='.$AC_DAY.'|| AC_COLLECT'.$AC_COLLECT.'<br>';
												}
						
												if(($xYM >= 1)  && $arr_temp[0]<10){ //between 1 �� -10 ��
													 $AC_DAY = 10+$ex_absen_day;
													 $AC_COLLECT = 20+$ex_absen_day;
													 if($search_per_type==4){ // �١��ҧ���Ǥ��� ��������� �Ѵ������
														$AC_COLLECT = 10+$ex_absen_day;
													 }
													 //echo '<br>1 �չ��¡��� 10 �� AC_DAY='.$AC_DAY.'|| AC_COLLECT'.$AC_COLLECT.'<br>';
													 $employees_year_up=TRUE;
												}
												if($arr_temp[0]>=10){ //10up
													 $AC_DAY = 10+$ex_absen_day;
													 $AC_COLLECT = 30+$ex_absen_day;
													 if($search_per_type==4){ // �١��ҧ���Ǥ��� ��������� �Ѵ������
														$AC_COLLECT = 10+$ex_absen_day;
													 }
													 //echo '<br> 10 �բ��� AC_DAY='.$AC_DAY.'|| AC_COLLECT'.$AC_COLLECT.'<br>';
													 $employees_year_up=TRUE;
												}
											
											
										}
										
                                        /*$cmd = " SELECT AC_DAY, AC_COLLECT FROM PER_ABSENTCOND 
                                                                WHERE AC_FLAG = 1 AND $arr_temp[2] < AC_GOV_AGE ";
                                        $db_dpis1->send_cmd($cmd);
                                        $data1 = $db_dpis1->get_array();
                                        $AC_DAY = $data1[AC_DAY];
                                        $AC_COLLECT = $data1[AC_COLLECT];*/
                                        
                                        
                                        $Leave_Cumulative=10+$ex_absen_day; //A
                                        /*��˹�����������*/
										
                                        if($Governor_Age<600){ //���¡��� 6 ��͹
										
                                           $Leave_Cumulative=0;					
                                        }
										// ������� Kiitphat ���л� �����Ѻ�ѹ�Ҿѡ��͹�� 10 �ѹ��ҹ��
										// ������� Kiitphat if($Governor_Age>=1600){ //between 1 to 10 =20 day
                                       /* if($Governor_Age>=1600 && $Governor_Age < 100000){ //between 1 to 10 =20 day
                                            $Leave_Cumulative=10;
                                        }elseif($Governor_Age>=100000){ //10up= 30 day
                                            $Leave_Cumulative = 20; 
                                        }*/
                                        /*==========================*/
                                        
                                        
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
													if($search_per_type==4){ // �١��ҧ���Ǥ��� ��������� �Ѵ������
														$AC_COLLECT = 10+$ex_absen_day;
													}
                                                }elseif($Governor_Age>=100000){ //10up= 30 day
                                                    $Before_vc_day = 30; 
													if($search_per_type==4){ // �١��ҧ���Ǥ��� ��������� �Ѵ������
														$AC_COLLECT = 10+$ex_absen_day;
													}
                                                }
                                            }
                                        }
                                        //�ӹǹ�Ҿѡ��͹�ͧ�է���͹˹��
                                        $sum_absday= get_sum_absday($db_dpis1,$PER_ID,$TMP_START_DATE,$TMP_END_DATE);
                                        if($debug==1){echo __LINE__.':sum_absday==>'.$sum_absday.'<br>';}
                                        //��ػ�ѹ������ѹ�Ҿѡ��͹�����ͧ�է���͹˹��
                                        $cmd = " select sum(AB_CODE_04) as abs_day from PER_ABSENTSUM 
                                                 where PER_ID=$PER_ID and START_DATE >= '$TMP_START_DATE' and END_DATE <= '$TMP_END_DATE' ";
                                        $db_dpis1->send_cmd($cmd);
                                        if($debug==1){echo __LINE__.':<pre>'.$cmd.'<br>';}
                                        $data1 = $db_dpis1->get_array();
                                        $data1 = array_change_key_case($data1, CASE_LOWER);
                                        
										if($search_per_type==4){ // �١��ҧ���Ǥ��� ��������� �Ѵ������
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
                                        $AC_COLLECT = 30+$ex_absen_day;
										if($search_per_type==4){ // �١��ҧ���Ǥ��� ��������� �Ѵ������
											$AC_COLLECT = 10+$ex_absen_day;
										}
                                    }
                                    if($debug==1){echo __LINE__.':'.$Governor_Age.'>=600 && '.$Leave_Cumulative.'>'.$AC_COLLECT.'<br>';}
                                    if($Governor_Age>=600 && $Leave_Cumulative>$AC_COLLECT ){ //�����Ҫ����ҡ����������ҡѺ 10 ��������� yy mm dd
                                        $Leave_Cumulative=$AC_COLLECT;// 30 �����٧�ش�ͧ�������͹�
                                    }
                                    if($debug==1){
                                        echo '<br>====================<br><h1>�����Ҫ���: '.$Governor_Age.' ,�ѹ�������������:'.$Leave_Cumulative.' �����٧�ش ['.$AC_COLLECT.']</h1><br>====================<br>';
                                    }
                                    
                                }
                                /*===================================================================================*/
				
                                
                                /*=======================Begin Ẻ����������ҡ�͹==================================*/
//                                $total_year = $arr_temp[0]-1; //*���*/
//				$total_month = $arr_temp[2];
//				$total_day = $arr_temp[4];
//				if ($total_month > 0) $total_year += $total_month / 12;
//				if ($total_day > 0) $total_year += $total_day / 365;
//                                //�Ѻ�Ҫ������֧ 6 ��͹*�Ѻ�Ҫ�������Թ 10 ��
//				$cmd = " SELECT AC_DAY, AC_COLLECT FROM PER_ABSENTCOND 
//								  WHERE AC_FLAG = 1 AND $total_year < AC_GOV_AGE ";
//				$count_data = $db_dpis1->send_cmd($cmd);
//				if($debug==1){echo __LINE__.': ��Ǩ�ͺ�����Ҫ���'.$cmd.'['.$count_data.']<br><br>';}
//                                
//                                if($debug==1){echo __LINE__.':�����Ҫ��� � ���� 1 ���Ҥ� => '.$ageTenUp.'<br><br>';}
//                                //�Ѻ�Ҫ��������¡��� 10 ��
//				if (!$count_data || $ageTenUp ) { //if (!$count_data) { ���
//					$cmd = " SELECT AC_DAY, AC_COLLECT FROM PER_ABSENTCOND 
//									  WHERE AC_FLAG = 2 AND $total_year >= AC_GOV_AGE ";
//					$count_data = $db_dpis1->send_cmd($cmd);
//					if($debug==1){echo __LINE__.':�Ѻ�Ҫ��������¡��� 10 �� = '.$cmd.'<br><br>';}
//					//$db_dpis1->show_error();
//				}
//                                
//				$data1 = $db_dpis1->get_array();
//				$AC_DAY = $data1[AC_DAY];
//				$AC_COLLECT = $data1[AC_COLLECT];
//                                if($debug==1){echo __LINE__.':AC_COLLECT = '.$AC_COLLECT.'<br><br>';}
//                                //�Ҿѡ��͹
//				$cmd = " select sum(ABS_DAY) as abs_day from PER_ABSENTHIS 
//                                        where PER_ID=$PER_ID and AB_CODE='04' and ABS_STARTDATE >= '$TMP_START_DATE' and ABS_ENDDATE <= '$TMP_END_DATE' ";
//				$db_dpis1->send_cmd($cmd);
//				if($debug==1){echo __LINE__.':�Ҿѡ��͹ = '.$cmd.'<br><br>';}
//                                $data1 = $db_dpis1->get_array();
//				$data1 = array_change_key_case($data1, CASE_LOWER);
//				$TMP_AB_CODE_04 = $data1[abs_day]+0; 
//                                if($debug==1){echo __LINE__.':�ѹ����� = '.$TMP_AB_CODE_04.'<br><br>';}
//                                //��ػ�ѹ������ѹ�Ҿѡ��͹����
//				$cmd = " select sum(AB_CODE_04) as abs_day from PER_ABSENTSUM 
//					 where PER_ID=$PER_ID and START_DATE >= '$TMP_START_DATE' and END_DATE <= '$TMP_END_DATE' ";
//				$db_dpis1->send_cmd($cmd);
//                                if($debug==1){echo __LINE__.':��ػ�ѹ������ѹ�Ҿѡ��͹���� = '.$cmd.'<br><br>';}
//				$data1 = $db_dpis1->get_array();
//				$data1 = array_change_key_case($data1, CASE_LOWER);
//				$TMP_AB_CODE_04 += $data1[abs_day]+0; 
//                                if($debug==1){echo __LINE__.':��ػ���� = '.$TMP_AB_CODE_04.'<br><br>';}
//				$cmd = " select VC_DAY from PER_VACATION 
//								where VC_YEAR='$tmp_vc_year'and PER_ID=$PER_ID ";
//				$db_dpis1->send_cmd($cmd);
//                                if($debug==1){echo __LINE__.':PER_VACATION = '.$cmd.'<br><br>';}
//				$data1 = $db_dpis1->get_array();
//				$VC_DAY = $data1[VC_DAY]; 
//                                if($debug==1){echo __LINE__.':VC_DAY = '.$VC_DAY.'<br><br>';}
//				
//				
//                                
//                                if($search_per_type==3){
//                                    $AC_DAY = $AC_DAY;
//                                }else{
//                                    if ($VC_DAY && ($VC_DAY - $TMP_AB_CODE_04) > 0) $AC_DAY += $VC_DAY - $TMP_AB_CODE_04;
//                                    if($debug==1){echo __LINE__.':TMP_AB_CODE_04 = '.$TMP_AB_CODE_04.'<br><br>';}
//                                }
//                                if($debug==1){echo __LINE__.':AC_DAY > AC_COLLECT = '.$AC_DAY.' > '.$AC_COLLECT.'<br><br>';}
//                                if ($AC_DAY > $AC_COLLECT) $AC_DAY = $AC_COLLECT; /*���*/
                                /*=======================End Ẻ����������ҡ�͹==================================*/
                                if($debug==1){echo __LINE__.':search_per_type = '.$search_per_type.' Leave_Cumulative: '.$Leave_Cumulative.'<br><br>';}
                                if($search_per_type==3){
                                    if($employees_year_up==TRUE){
                                        if($Leave_Cumulative>15 && $chk_ex_absen_day==''){
                                            $AC_DAY = 15;
											
                                        }else if($Leave_Cumulative> (15+$ex_absen_day) && $chk_ex_absen_day=='Y'){
                                            $AC_DAY = 15+$ex_absen_day;
											
                                        }else{
                                            $AC_DAY = $Leave_Cumulative;
                                        }
                                        
                                    }else{
                                        $AC_DAY = $AC_DAY;
                                    }
                                    
                                    if($debug==1){echo __LINE__.':AC_DAY = '.$AC_DAY.'<br><br>';}
                                    if($debug==1){echo __LINE__.':���о�ѡ�ҹ�Ҫ���Governor_Age = '.$Governor_Age.'<br><br>';}
                                    
                                }else{
                                    $AC_DAY=$Leave_Cumulative;
                                }
							//	if($chk_ex_absen_day=='Y')$AC_DAY = $AC_DAY+; //������Է 3 ����Ѵ����ഹ�� ������ if ��������ӹǹ�ѹ�зӧҹ�Դ
                                
                                        if($Governor_Age<600){ //���¡��� 6 ��͹
										
                                           $AC_DAY=0;
											
                                        }
                                
				$cmd = " select VC_DAY from PER_VACATION 
								  where VC_YEAR = '$search_vc_year'  and PER_ID = $PER_ID ";
				$count_data = $db_dpis1->send_cmd($cmd);
                                if($debug==1){echo __LINE__.':insert = '.$cmd.'<br><br>';}

				if (!$count_data) {
					$cmd = " insert into PER_VACATION (VC_YEAR, PER_ID, VC_DAY, UPDATE_USER, UPDATE_DATE) 
									values ('$search_vc_year', $PER_ID, $AC_DAY, $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis1->send_cmd($cmd);
                                        if($debug==1){echo __LINE__.':insert = '.$cmd.'<br><br>';}
					insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ���������� [".trim($search_vc_year)." : ".$PER_ID."]");
				} 
			}
			$cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$PER_ID and AS_YEAR = '$search_vc_year' and AS_CYCLE = 1 ";
			$count=$db_dpis1->send_cmd($cmd);
                        if($debug==1){echo __LINE__.':'.$cmd.'<br><br>';}
			//$db_dpis1->show_error(); echo "<hr>$cmd<br>";
			if(!$count) { 
				$cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
								values ($AS_ID, $PER_ID, '$search_vc_year', 1, '$START_DATE_1', '$END_DATE_1', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis1->send_cmd($cmd);
                                if($debug==1){echo __LINE__.': insert = '.$cmd.'<br><br>';}
				//$db_dpis1->show_error();
				$AS_ID++;
			}
			$cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$PER_ID and AS_YEAR = '$search_vc_year' and AS_CYCLE = 2 ";
			$count=$db_dpis1->send_cmd($cmd);
                       // echo "299: $cmd<br>";
			//$db_dpis1->show_error(); echo "<hr>$cmd<br>";
			if(!$count) { 
				$cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
								values ($AS_ID, $PER_ID, '$search_vc_year', 2, '$START_DATE_2', '$END_DATE_2', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis1->send_cmd($cmd);
                                if($debug==1){echo __LINE__.': insert = '.$cmd.'<br><br>';}
				//$db_dpis1->show_error();
				$AS_ID++;
			}
		} // while
	}
	
	// Start By Kittiphat  21/02/2560
    if($debug==1){echo __LINE__.': command = '.$command.'<br><br>';}
	
	if($command == 'COPY_NEW') {
		$debug=0;
		if($search_org_id){
			if($select_org_structure==0){
				$search_con = " and b.ORG_ID = $search_org_id ";
			}else if($select_org_structure==1){
				$search_con = " and a.ORG_ID = $search_org_id ";
			}
		}elseif($search_department_id){
			$search_con = " and a.DEPARTMENT_ID = $search_department_id ";
		}
		if($search_per_name)	$search_con .= " and a.PER_NAME like '$search_per_name%' ";
		if($search_per_surname) $search_con .= " and a.PER_SURNAME like '$search_per_surname%' ";


		$cmd = " select max(AS_ID) as max_id from PER_ABSENTSUM ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$AS_ID = $data[max_id] + 1;
				
		if($search_per_type==1 || $search_per_type==5) 
			if ($SESS_DEPARTMENT_NAME=="�����û���ͧ")
				$cmd = "	select a.PER_ID, a.PER_STARTDATE, PER_CARDNO 
									from PER_PERSONAL a, PER_POSITION b where a.PAY_ID = b.POS_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con ";
			else
				$cmd = "	select a.PER_ID, a.PER_STARTDATE, PER_CARDNO 
									from PER_PERSONAL a, PER_POSITION b where a.POS_ID = b.POS_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con ";
		elseif($search_per_type==2) 
			$cmd = "	select a.PER_ID, a.PER_STARTDATE, PER_CARDNO  
								from PER_PERSONAL a, PER_POS_EMP b where a.POEM_ID = b.POEM_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con ";
		elseif($search_per_type==3) 
			$cmd = "	select a.PER_ID, a.PER_STARTDATE, PER_CARDNO  
								from PER_PERSONAL a, PER_POS_EMPSER b where a.POEMS_ID = b.POEMS_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con ";
		elseif($search_per_type==4) 
			$cmd = "	select a.PER_ID, a.PER_STARTDATE, PER_CARDNO  
								from PER_PERSONAL a, PER_POS_TEMP b where a.POT_ID = b.POT_ID and PER_TYPE = $search_per_type and PER_STATUS = 1 $search_con ";
		$db_dpis->send_cmd($cmd);
                
                if($debug==1){echo  '<br>'.__LINE__.'<pre>'.$cmd;}
		//$db_dpis->show_error();
		
		//$CHECK_DATE = '2019-10-08'; /*��Ѻ����*/	
		
		$CHECK_DATE = date("Y-m-d"); /*��Ѻ����*/
		$CHECK_DATE_NEXT_YEAR = ($search_vc_year-543)."-10-01"; /*��Ѻ����*/
		$CHECK_DATE_BEFORE_YEAR = ($search_vc_year-1-543)."-10-01"; /*��Ѻ����*/
		//$CHECK_DATE = ($search_vc_year-543)."-10-01";/*���*/
		$sep_CHECK_DATE = ($search_vc_year-543)."-09-30";/*�������*/
         
		$TMP_START_DATE = ($search_vc_year-1-544)."-10-01";/*���*/ //�� �� ��͹˹��
		$TMP_END_DATE = ($search_vc_year-1-543)."-09-30";/*���*/ //�� �� ��͹˹��
		//$TMP_START_DATE = ($search_vc_year-544)."-10-01";/*���*/ //�� �� ��͹˹��
		//$TMP_END_DATE = ($search_vc_year-543)."-09-30";/*���*/ //�� �� ��͹˹��
                
                
		$CHECK_STARTDATE = ($search_vc_year-543)."-04-01";
		$tmp_vc_year = $search_vc_year - 1;
                
		//$START_DATE_1 = ($search_vc_year - 1) . "-10-01"; //<<<=���
                //$END_DATE_1 = $search_vc_year . "-03-31";//<<<=���
        $START_DATE_1 = ($search_vc_year -544) . "-10-01"; //��Ѻ��ا
		$END_DATE_1 = $search_vc_year-543 . "-03-31";//��Ѻ��ا
                
		//$START_DATE_2 = $search_vc_year . "-04-01"; //<<<=���
                //$END_DATE_2 = $search_vc_year . "-09-30"; //<<<=���
        $START_DATE_2 = ($search_vc_year-543) . "-04-01"; //��Ѻ��ا
		$END_DATE_2 = $search_vc_year-543 . "-09-30"; //��Ѻ��ا
		
		

		
		
        if($debug==1){echo '<meta http-equiv="Content-Type" content="text/html; charset=windows-874">';}
                
		
        while($data = $db_dpis->get_array()){
			$PER_ID = $data[PER_ID];
			$PER_CARDNO = trim($data[PER_CARDNO]);
			$PER_STARTDATE = trim($data[PER_STARTDATE]);
                        
            $cmd_con = " select VC_DAY from PER_VACATION 
								  where VC_YEAR = '$search_vc_year'  and PER_ID = $PER_ID ";
            $db_dpis2->send_cmd($cmd_con);
            if($debug==1){echo  '<br>'.__LINE__.'<pre>'.$cmd_con;}
            
            $data_con = $db_dpis2->get_array();
            
            //if($data_con){echo '<font color=green>yes</font>';}else{echo '<font color=red>no rec,</font>';}
            
            if($debug==1){echo  '<br>'.__LINE__.'VC_DAY:'.$data_con[VC_DAY];}
            if($data_con[VC_DAY] < 1){
			
				if($debug==1){echo '<br>'.__LINE__.'<br><br>'.$CHECK_STARTDATE." >= ".$PER_STARTDATE."<br>";}
				if ($CHECK_STARTDATE >= $PER_STARTDATE) { /*��èء�͹�ѹ��� 1 ����¹*/
								    //echo 'today : '.date('Y-m-d').',CHECK_DATE:'.$CHECK_DATE.'<br>';
									if($debug==1){echo __LINE__.'<br>CHECK_DATE:'.$CHECK_DATE." , PER_STARTDATE:".$PER_STARTDATE."<br>";}
					$AGE_DIFF = date_vacation($CHECK_DATE, $PER_STARTDATE, "full");/* ત ��� �ѧ�ѹ���ӹǳ���ҧ��*/
                                        if($debug==1){echo __LINE__.'<br><br>AGE_DIFF='. $AGE_DIFF."<br>";}
					$arr_temp = explode(" ", $AGE_DIFF);
                                        if($debug==1){echo __LINE__.'<br><br>arr_temp[0]='.$arr_temp[0]."<br>";}
                                        if($debug==1){echo __LINE__.'<br><br>arr_temp[2]='.$arr_temp[2]."<br>";}
                                        if($debug==1){echo __LINE__.'<br><br>arr_temp[4]='.$arr_temp[4]."<br>";}
									/*�������*/
									$AGE_DIFF_NEXT_YEAR = date_vacation($CHECK_DATE_NEXT_YEAR, $PER_STARTDATE, "full");
									$arr_temp_next_year = explode(" ", $AGE_DIFF_NEXT_YEAR);
									
									$AGE_DIFF_BEFORE_YEAR = date_vacation($CHECK_DATE_BEFORE_YEAR, $PER_STARTDATE, "full");
									$arr_temp_before_year = explode(" ", $AGE_DIFF_BEFORE_YEAR);
									
									/*============================��Ѻ����==========================*/
									$test =1;/*0=close,1=test*/
                                                                        $employees_year_up=FALSE;
									if($test==1){
										$Governor_Age_Sep = 0;
										$Governor_Age = ($arr_temp[0]*10000)+($arr_temp[2]*100)+(($arr_temp[4]*1));
										$Governor_Age_next_year = ($arr_temp_next_year[0]*10000)+($arr_temp_next_year[2]*100)+(($arr_temp_next_year[4]*1));
										$Governor_Age_before = ($arr_temp_before_year[0]*10000)+($arr_temp_before_year[2]*100)+(($arr_temp_before_year[4]*1));
										
										if($debug==1){echo __LINE__.'<br><h1>����'.$arr_temp[0].'�� '.$arr_temp[2].'��͹ '.$arr_temp[4].'�ѹ</h1><br>';}
										
										$dd=$arr_temp[4];
										if(strlen($dd)==1){$dd='0'.$dd;}
									   
										$Leave_Cumulative=0; // def �ѹ������
										if($debug==1){echo 'Governor_Age_next_year=>'.$Governor_Age_next_year.',Age==>'.$Governor_Age;}
										
										if( $Governor_Age_next_year>=600 || $Governor_Age>=600 ){ //�����Ҫ����ҡ����������ҡѺ 6 ��͹������� m dd
											//�Է�����ѹ������ �. �Ҥ��
										if($chk_ex_absen_day=='Y'){
											$ex_absen_day= intval($ex_absen_day);
										}else{
											$ex_absen_day=0;
										}
											//echo "xxxxxxxxxxxxxxxxxxxx".$ex_absen_day;
											// ����� �բ����Żշ�����������ѧ
											$tmpVC_YEAR=$search_vc_year-1;
											$cmd = " select VC_DAY from PER_VACATION 
									  			where VC_YEAR = '$tmpVC_YEAR'  and PER_ID = $PER_ID AND VC_DAY>0 ";
											$count_data = $db_dpis1->send_cmd($cmd);
											if($debug==1){echo __LINE__.':insert = '.$cmd.'<br><br>';}
							
											if ($count_data){
												if($arr_temp[0]==0){ //��ѡ��͹
													$cmd = " SELECT AC_DAY, AC_COLLECT FROM PER_ABSENTCOND 
																WHERE AC_FLAG = 1 AND ($arr_temp[2]/12) < AC_GOV_AGE ";
													$db_dpis1->send_cmd($cmd);
													$data1 = $db_dpis1->get_array();
													$AC_DAY = $data1[AC_DAY]+$ex_absen_day;
													$AC_COLLECT = $data1[AC_COLLECT]+$ex_absen_day;
													if($debug==1){echo __LINE__.': AC_COLLECTx=>'.$AC_COLLECT.'==><pre>'.$cmd.'<br>';}
												}
												if($arr_temp[0]>=1 && $arr_temp[0]<10){ //between 1-10
													 $AC_DAY = 10+$ex_absen_day;
													 $AC_COLLECT = 20+$ex_absen_day;
													 if($search_per_type==4){ // �١��ҧ���Ǥ��� ��������� �Ѵ������
														$AC_COLLECT = 10+$ex_absen_day;
													 }
													 $employees_year_up=TRUE;
													 if($debug==1){echo __LINE__.': AC_COLLECTy=>'.$AC_COLLECT.'==><pre><br>';}
												}
												if($arr_temp[0]>=10){ //10up
													 $AC_DAY = 10+$ex_absen_day;
													 $AC_COLLECT = 30+$ex_absen_day;
													 if($search_per_type==4){ // �١��ҧ���Ǥ��� ��������� �Ѵ������
														$AC_COLLECT = 10+$ex_absen_day;
													 }
													 $employees_year_up=TRUE;
													 if($debug==1){echo __LINE__.': AC_COLLECTz=>'.$AC_COLLECT.'==><pre><br>';}
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
														$AC_DAY = 10+$ex_absen_day;
														$AC_COLLECT = 10+$ex_absen_day;
														//echo '<br>6 ��͹���¡��� 1 �� AC_DAY='.$AC_DAY.'|| AC_COLLECT'.$AC_COLLECT.'<br>';
													}
							
													if(($xYM >= 1)  && $arr_temp[0]<10){ //between 1 �� -10 ��
														 $AC_DAY = 10+$ex_absen_day;
														 $AC_COLLECT = 20+$ex_absen_day;
														 if($search_per_type==4){ // �١��ҧ���Ǥ��� ��������� �Ѵ������
															$AC_COLLECT = 10;
														 }
														 //echo '<br>1 �չ��¡��� 10 �� AC_DAY='.$AC_DAY.'|| AC_COLLECT'.$AC_COLLECT.'<br>';
														 $employees_year_up=TRUE;
													}
													if($arr_temp[0]>=10){ //10up
														 $AC_DAY = 10+$ex_absen_day;
														 $AC_COLLECT = 30+$ex_absen_day;
														 if($search_per_type==4){ // �١��ҧ���Ǥ��� ��������� �Ѵ������
															$AC_COLLECT = 10+$ex_absen_day;
														 }
														 //echo '<br> 10 �բ��� AC_DAY='.$AC_DAY.'|| AC_COLLECT'.$AC_COLLECT.'<br>';
														 $employees_year_up=TRUE;
													}
												
												
											}

											
											$Leave_Cumulative=10+$ex_absen_day; //A
											/*��˹�����������*/
											if($Governor_Age<600){ //���¡��� 6 ��͹
												$Leave_Cumulative=0;
											}
											
											/*if($Governor_Age>=1600 && $Governor_Age < 100000){ //between 1 to 10 =20 day
												$Leave_Cumulative=10;
											}elseif($Governor_Age>=100000){ //10up= 30 day
												$Leave_Cumulative = 20; 
											}*/
											/*==========================*/
											
											
											$Before_vc_day=0;//N
											if($Governor_Age>600){ //Y
											   //�� Record ��ҵ�駵�?�շ������
												$cmd = " select VC_DAY from PER_VACATION where VC_YEAR='$tmp_vc_year'and PER_ID=$PER_ID ";
												$cnt=$db_dpis1->send_cmd($cmd);
												$Before_vc_day=0;//N
												if($cnt){//Y
													$data1 = $db_dpis1->get_array();
													$Before_vc_day = $data1[VC_DAY]; 
													if($debug==1){echo  '<br>'.__LINE__.'�������շ������x:'.$Before_vc_day.'<br>';}
												}else{
													if($Governor_Age>=1600 && $Governor_Age < 100000){ //between 1 to 10 =20 day
														$Before_vc_day = 20; 
														if($search_per_type==4){ // �١��ҧ���Ǥ��� ��������� �Ѵ������
															$AC_COLLECT = 10+$ex_absen_day;
														 }
														if($debug==1){echo  '<br>'.__LINE__.'�������շ������y:'.$Before_vc_day.'<br>';}
													}elseif($Governor_Age>=100000){ //10up= 30 day
														$Before_vc_day = 30; 
														if($search_per_type==4){ // �١��ҧ���Ǥ��� ��������� �Ѵ������
															$AC_COLLECT = 10+$ex_absen_day;
														 }
														if($debug==1){echo  '<br>'.__LINE__.'�������շ������z:'.$Before_vc_day.'<br>';}
													}
												}
											}
											//�ӹǹ�Ҿѡ��͹�ͧ�է���͹˹��
											$sum_absday= get_sum_absday($db_dpis1,$PER_ID,$TMP_START_DATE,$TMP_END_DATE);
											if($debug==1){echo __LINE__.'<br>sum_absday==>'.$sum_absday.'<br>';}
											//��ػ�ѹ������ѹ�Ҿѡ��͹�����ͧ�է���͹˹��
											$cmd = " select sum(AB_CODE_04) as abs_day from PER_ABSENTSUM 
													 where PER_ID=$PER_ID and START_DATE >= '$TMP_START_DATE' and END_DATE <= '$TMP_END_DATE' ";
											$db_dpis1->send_cmd($cmd);
											if($debug==1){echo __LINE__.'<br><pre>'.$cmd.'<br>';}
											$data1 = $db_dpis1->get_array();
											$data1 = array_change_key_case($data1, CASE_LOWER);
											
											if($search_per_type==4){ // �١��ҧ���Ǥ��� ��������� �Ѵ������
												$Before_abs_day_vacation = 0; 
											}else{
												$Before_abs_day_vacation = $data1[abs_day]+0; 
											}
											
											$Before_vc_day=$Before_vc_day-($sum_absday+$Before_abs_day_vacation);
	
											if($debug==1){echo __LINE__.':Before_vc_day==>'.$Before_vc_day.','.$Before_abs_day_vacation.'<br>';}
											if($debug==1){echo __LINE__.':'.$CHECK_DATE_NEXT_YEAR.' �����Ҫ��ûշ���ԡ�����ż� ==>'.$Governor_Age.'<br>';}
											
											if($Before_vc_day>0){
												$Leave_Cumulative=$Leave_Cumulative+$Before_vc_day;
											}
											if($debug==1){echo __LINE__.'<br>Leave_Cumulative==>'.$Leave_Cumulative.'<br>';}
										}else{ // N
											$Leave_Cumulative=0;
										}
										if($Governor_Age>=100000){ // 10up
											$AC_COLLECT = 30+$ex_absen_day;
											if($search_per_type==4){ // �١��ҧ���Ǥ��� ��������� �Ѵ������
												$AC_COLLECT = 10+$ex_absen_day;
											 }
										}
										if($debug==1){echo __LINE__.'<br>'.$Governor_Age.'>=600 && '.$Leave_Cumulative.'>'.$AC_COLLECT.'<br>';}
										if($Governor_Age>=600 && $Leave_Cumulative>$AC_COLLECT ){ //�����Ҫ����ҡ����������ҡѺ 10 ��������� yy mm dd
											if($search_per_type==3){
												$Leave_Cumulative=15+$ex_absen_day;// 15 �����٧�ش�ͧ�������͹�
											}else{
												$Leave_Cumulative=$AC_COLLECT;// 30 �����٧�ش�ͧ�������͹�
											}
											
										}
										if($debug==1){
											echo '<br>====================<br><h1>�����Ҫ���>>>: '.$Governor_Age.' ,�ѹ�������������:'.$Leave_Cumulative.' �����٧�ش ['.$AC_COLLECT.']</h1><br>====================<br>';
										}
										
									}
									/*===================================================================================*/
					
									if($search_per_type==3){
										if($employees_year_up==TRUE){
                                        if($Leave_Cumulative>15 && $chk_ex_absen_day==''){
                                            $AC_DAY = 15;
											
                                        }else if($Leave_Cumulative> (15+$ex_absen_day) && $chk_ex_absen_day=='Y'){
                                            $AC_DAY = 15+$ex_absen_day;
											
                                        }else{
                                            $AC_DAY = $Leave_Cumulative;
                                        }
                                        
                                    }else{
											$AC_DAY = $AC_DAY;
										}

										if($debug==1){echo __LINE__.':AC_DAY1 = '.$AC_DAY.'<br><br>';}
									}else{
										$AC_DAY=$Leave_Cumulative;
                                        if($debug==1){echo __LINE__.':AC_DAY2 = '.$AC_DAY.'<br><br>';}
									}
									
									if($Governor_Age<600){ //���¡��� 6 ��͹
												$AC_DAY=0;
									}
									
					$cmd = " select VC_DAY from PER_VACATION 
									  where VC_YEAR = '$search_vc_year'  and PER_ID = $PER_ID ";
					$count_data = $db_dpis1->send_cmd($cmd);
                    if($debug==1){echo __LINE__.':insert = '.$cmd.'<br><br>';}
	
					if (!$count_data) {
						$cmd = " insert into PER_VACATION (VC_YEAR, PER_ID, VC_DAY, UPDATE_USER, UPDATE_DATE) 
										values ('$search_vc_year', $PER_ID, $AC_DAY, $SESS_USERID, '$UPDATE_DATE') ";
						$db_dpis1->send_cmd($cmd);
                                                if($debug==1){echo __LINE__.':insert = '.$cmd.'<br><br>';}
						insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ���������� [".trim($search_vc_year)." : ".$PER_ID."]");
					}else{
						$cmd = " update PER_VACATION set 
										VC_DAY=$AC_DAY, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'
										WHERE VC_YEAR='$search_vc_year' AND PER_ID=$PER_ID ";
						$db_dpis1->send_cmd($cmd);
						
					}
				}
				$cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$PER_ID and AS_YEAR = '$search_vc_year' and AS_CYCLE = 1 ";
				$count=$db_dpis1->send_cmd($cmd);
							if($debug==1){echo __LINE__.':'.$cmd.'<br><br>';}
				//$db_dpis1->show_error(); echo "<hr>$cmd<br>";
				if(!$count) { 
					$cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
									values ($AS_ID, $PER_ID, '$search_vc_year', 1, '$START_DATE_1', '$END_DATE_1', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis1->send_cmd($cmd);
									if($debug==1){echo __LINE__.': insert = '.$cmd.'<br><br>';}
					//$db_dpis1->show_error();
					$AS_ID++;
				}
				$cmd="select   AS_YEAR from PER_ABSENTSUM where PER_ID=$PER_ID and AS_YEAR = '$search_vc_year' and AS_CYCLE = 2 ";
				$count=$db_dpis1->send_cmd($cmd);
						   // echo "299: $cmd<br>";
				//$db_dpis1->show_error(); echo "<hr>$cmd<br>";
				if(!$count) { 
					$cmd = " insert into PER_ABSENTSUM (AS_ID, PER_ID, AS_YEAR, AS_CYCLE, START_DATE,  END_DATE, PER_CARDNO, UPDATE_USER, UPDATE_DATE)
									values ($AS_ID, $PER_ID, '$search_vc_year', 2, '$START_DATE_2', '$END_DATE_2', '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE') ";
					$db_dpis1->send_cmd($cmd);
									if($debug==1){echo __LINE__.': insert = '.$cmd.'<br><br>';}
					//$db_dpis1->show_error();
					$AS_ID++;
				}
			}
		} // while
	}
	
	// End By Kittiphat  21/02/2560
	
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
?>