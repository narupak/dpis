<?	
	include("../php_scripts/connect_database.php");
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");
        //echo "04/08/2560 16:18";
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

	$UPDATE_DATE = date("Y-m-d H:i:s");

	switch($CTRL_TYPE){
		case 2 :
			$OLD_PV_CODE = $PV_CODE;
			$OLD_PV_NAME = $PV_NAME;
			break;
		case 3 :
			$OLD_MINISTRY_ID = $MINISTRY_ID;
			$OLD_MINISTRY_NAME = $MINISTRY_NAME;
			break;
		case 4 :
			$OLD_MINISTRY_ID = $MINISTRY_ID;
			$OLD_MINISTRY_NAME = $MINISTRY_NAME;
			$OLD_DEPARTMENT_ID = $DEPARTMENT_ID;
			$OLD_DEPARTMENT_NAME = $DEPARTMENT_NAME;				
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$OLD_PV_CODE = $PROVINCE_CODE;
			$OLD_PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$OLD_MINISTRY_ID = $MINISTRY_ID;
			$OLD_MINISTRY_NAME = $MINISTRY_NAME;
			break;
		case 4 :
			$OLD_MINISTRY_ID = $MINISTRY_ID;
			$OLD_MINISTRY_NAME = $MINISTRY_NAME;
			$OLD_DEPARTMENT_ID = $DEPARTMENT_ID;
			$OLD_DEPARTMENT_NAME = $DEPARTMENT_NAME;				
			break;
		case 5 :
			$OLD_MINISTRY_ID = $MINISTRY_ID;
			$OLD_MINISTRY_NAME = $MINISTRY_NAME;
			$OLD_DEPARTMENT_ID = $DEPARTMENT_ID;
			$OLD_DEPARTMENT_NAME = $DEPARTMENT_NAME;				
			$OLD_ORG_ID = $ORG_ID;
			$OLD_ORG_NAME = $ORG_NAME;
			break;
	} // end switch case	

	if ($TEMP_ORG_NAME=="กรุงเทพมหานคร" || strstr($TEMP_ORG_NAME,"กรุงเทพมหานคร")) {
		$PER_TYPE = (trim($PER_TYPE))? $PER_TYPE : 1;
	}else{
		$PER_TYPE = (trim($PER_TYPE))? $PER_TYPE : 2;
	}
	$SALQ_TIME = (trim($SALQ_TIME))? $SALQ_TIME : 1;	
	if ($PER_TYPE==1 && $SALQ_TIME==1)				$SALQ_TYPE = 1;
	elseif ($PER_TYPE==1 && $SALQ_TIME==2)			$SALQ_TYPE = 2;	
	elseif ($PER_TYPE==2 && $SALQ_TIME==1)			$SALQ_TYPE = 3;	
	elseif ($PER_TYPE==2 && $SALQ_TIME==2)			$SALQ_TYPE = 4;
	elseif ($PER_TYPE==3 && $SALQ_TIME==1)			$SALQ_TYPE = 5;	
	elseif ($PER_TYPE==3 && $SALQ_TIME==2)			$SALQ_TYPE = 6;
	if (!$SALP_PERCENT) $SALP_PERCENT = 0;	
	if (!$SALP_SPSALARY) $SALP_SPSALARY = 0;	
	
	if ($SALQ_TYPE == 1 || $SALQ_TYPE == 3 || $SALQ_TYPE == 5) {
		$SALQ_TYPE_TIME = 1; 		// ครั้งที่ 1	
		$salq_type_text = "จำนวนคนที่เลื่อน 1 ขั้น";
		$salq_type_text_sum = "รวมจำนวนคนที่เลื่อน 1 ขั้น";
	} elseif ($SALQ_TYPE == 2 || $SALQ_TYPE == 4 || $SALQ_TYPE == 6) {
		$SALQ_TYPE_TIME = 2; 		// ครั้งที่ 2
		$salq_type_text = "จำนวนเงินที่จัดสรร";	
		$salq_type_text_sum = "รวมจำนวนเงินที่ใช้ไป";	
	}
	$tmp_salp_ORG_ID = (trim($ORG_ID))? trim($ORG_ID) : trim($ORG_ID_ASS);

	function list_year_salpromote () {
		global $db_dpis;
		$cmd = " select distinct SALQ_YEAR from PER_SALPROMOTE order by SALQ_YEAR ";
		$db_dpis->send_cmd($cmd);
		echo "<select name='SALQ_YEAR' class='selectbox'>";
		echo "<option value=''>== เลือกปีงบประมาณ ==</option>";
		while ($data = $db_dpis->get_array()) {
			$tmp_year = trim($data[SALQ_YEAR]);
			echo "<option value='$tmp_year'>$tmp_year</option>";
		}	// end while
		echo "</select>";
	}		// end function


	if ($command == "ADD" || $command == "UPDATE") {
		$tmp_SALP_DATE =  save_date($SALP_DATE);
               
	}

	if($command == "ADD" && trim($SALQ_YEAR) and trim($SALQ_TYPE)){
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลการเลื่อนขั้นเงินเดือน [".trim($SALQ_YEAR)." : ".$SALQ_TYPE." : ".$PER_ID." : "."]");
	}
	
        
        
	// =================== START :: เมื่อมีการแก้ไขข้อมูลข้าราชการฯ ที่มีชื่อในการเลื่อนขั้นเงินเดือน ====================
	if($command == "UPDATE" && trim($SALQ_YEAR) && trim($SALQ_TYPE) && trim($DEPARTMENT_ID) && trim($PER_ID)){
         $cmd = " select * from PER_SALPROMOTE a, PER_PERSONAL b, PER_PRENAME c 
		where	SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and 
			a.PER_ID=b.PER_ID and b.PN_CODE=c.PN_CODE and a.PER_ID=$PER_ID";
						 
                                $db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
                                $PER_SALARY_OL =$data1[SALP_SALARY_OLD];
		if ( $SALP_YN==1) {					// สมควรได้เลื่อนขั้นเงินเดือน
			if ($PER_TYPE==1) {
				$MAX_LAYER_NO = $MAX_LAYER_SALARY = $non_promote_text = "";
				// update เงินเดือนใหม่ เมื่อมีการเลื่อนขั้น
				// เช็ค PT_CODE ว่ารับเงินเดือนระดับ ทั่วไป หรือเป็น ผู้บริหารระดับสูง
				$cmd = "	select a.PT_CODE, b.LAYER_TYPE, c.LEVEL_NO from PER_POSITION a, PER_LINE b, PER_PERSONAL c 
						where PER_ID=$PER_ID and a.POS_ID=c.POS_ID and a.PL_CODE=b.PL_CODE ";	
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$PT_CODE = trim($data1[PT_CODE]);
				$LEVEL_NO = trim($data1[LEVEL_NO]);
				// check ค่า $PT_CODE โดยดูจาก table PER_TYPE 
				if ($PT_CODE == 32 && $LEVEL_NO_SALARY=="11")	$LAYER_TYPE = 2;		// ผู้บริหารระดับสูง
				else											$LAYER_TYPE = 1;		// ทั่วไป	
				if ($RPT_N && ($LEVEL_NO=="O3" || $LEVEL_NO=="K5")) $LAYER_TYPE = trim($data1[LAYER_TYPE]);
				
				if ($RPT_N) {
					$cmd = " select 	LAYER_NO, a.LEVEL_NO, a.LEVEL_NO_SALARY, PER_SALARY, LAYER_SALARY, LAYER_SALARY_MIDPOINT, 
							LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, LAYER_SALARY_FULL, LAYER_SALARY_TEMP 
							from 	PER_PERSONAL a, PER_LAYER b
							where 	a.PER_ID=$PER_ID and a.LEVEL_NO=b.LEVEL_NO and LAYER_SALARY=PER_SALARY and 
				 					LAYER_TYPE=$LAYER_TYPE 
							order by 	LAYER_NO ";
					$db_dpis1->send_cmd($cmd);
					$data1 = $db_dpis1->get_array();
					$CAL_LAYER_NO = trim($data1[LAYER_NO]);
					$CAL_LEVEL_NO = trim($data1[LEVEL_NO]);
					$CAL_LEVEL_NO_SALARY = trim($data1[LEVEL_NO_SALARY]);
					$PER_SALARY = $data1[PER_SALARY] + 0;
					$CAL_LAYER_SALARY_MIDPOINT = $data1[LAYER_SALARY_MIDPOINT] + 0;
					$CAL_LAYER_SALARY_MIDPOINT1 = $data1[LAYER_SALARY_MIDPOINT1] + 0;
					$CAL_LAYER_SALARY_MIDPOINT2 = $data1[LAYER_SALARY_MIDPOINT2] + 0;
					$CAL_LAYER_SALARY_FULL = $data1[LAYER_SALARY_FULL] + 0;
					$CAL_LAYER_SALARY_TEMP = $data1[LAYER_SALARY_TEMP] + 0;
					$SALP_SPSALARY = 0;
					if ($SALP_LEVEL==0 && $SALP_PERCENT==0) {
						$SALP_SALARY_NEW = $PER_SALARY;
						$SALP_LEVEL = 0;
						$SALP_PERCENT = 0;
					} elseif ($SALP_LEVEL==0.5 || $SALP_PERCENT==2) {
						$SALP_SALARY_NEW = $CAL_LAYER_SALARY_MIDPOINT;
						if ($CAL_LAYER_SALARY_MIDPOINT <= $PER_SALARY) {
							$SALP_LEVEL = 0;
							$SALP_PERCENT = 2;
						} else {
							$SALP_LEVEL = 0.5;
							$SALP_PERCENT = 0;
							if ($SALQ_TYPE_TIME == 2) $SALP_REMARK = "ข้อ 7 กฎ ก.พ.ว่าด้วยการเลื่อนขั้นเงินเดือน พ.ศ.2544";
						}
						if ($SALP_SALARY_NEW <= $PER_SALARY) {
							$SALP_SPSALARY = $SALP_SALARY_NEW;
							$SALP_SALARY_NEW = $CAL_LAYER_SALARY_FULL;
						}
					} elseif ($SALP_LEVEL==1) {
						$SALP_SALARY_NEW = $CAL_LAYER_SALARY_MIDPOINT1;
						if ($SALP_SALARY_NEW <= $PER_SALARY) {
							$SALP_LEVEL = 0;
							$SALP_PERCENT = 4;
						} elseif ($CAL_LAYER_SALARY_MIDPOINT1 <= $PER_SALARY) {
							$SALP_LEVEL = 0.5;
							$SALP_PERCENT = 2;
							if ($SALQ_TYPE_TIME == 2) 
								if ($COM_LEVEL_SALP == 3 || $COM_LEVEL_SALP == 4) 
									$SALP_REMARK = "เลื่อนเงินเดือน 0.5 ขั้นด้วย";
								else
									$SALP_REMARK = "เลื่อนอีก 0.5 ขั้น เต็มขั้น ให้ได้รับเงินตอบแทนพิเศษอีก 2%";
						} else {
							$SALP_LEVEL = 1;
							$SALP_PERCENT = 0;
							if ($SALQ_TYPE_TIME == 1) $SALP_REMARK = "ข้อ 8 กฎ ก.พ.ว่าด้วยการเลื่อนขั้นเงินเดือน พ.ศ.2544";
						}
						if ($SALP_SALARY_NEW <= $PER_SALARY) {
							$SALP_SPSALARY = $SALP_SALARY_NEW;
							$SALP_SALARY_NEW = $CAL_LAYER_SALARY_FULL;
						}
					} elseif ($SALP_LEVEL==1.5) {
						$SALP_SALARY_NEW = $CAL_LAYER_SALARY_MIDPOINT2;
						if ($SALP_SALARY_NEW <= $PER_SALARY) {
							$SALP_LEVEL = 0;
							$SALP_PERCENT = 6;
						} elseif ($CAL_LAYER_SALARY_MIDPOINT1 <= $PER_SALARY) {
							$SALP_LEVEL = 0.5;
							$SALP_PERCENT = 4;
							if ($COM_LEVEL_SALP == 3 || $COM_LEVEL_SALP == 4) 
								$SALP_REMARK = "เลื่อนเงินเดือน 0.5 ขั้นด้วย";
							else
								$SALP_REMARK = "เลื่อนอีก 0.5 ขั้น เต็มขั้น ให้ได้รับเงินตอบแทนพิเศษอีก 4%";
						} elseif ($CAL_LAYER_SALARY_MIDPOINT2 <= $PER_SALARY) {
							$SALP_LEVEL = 1;
							$SALP_PERCENT = 2;
							if ($COM_LEVEL_SALP == 3 || $COM_LEVEL_SALP == 4) 
								$SALP_REMARK = "เลื่อนเงินเดือน 1 ขั้นด้วย";
							else
								$SALP_REMARK = "เลื่อนอีก 1 ขั้น เต็มขั้น ให้ได้รับเงินตอบแทนพิเศษอีก 2%";
						} else {
							$SALP_LEVEL = 1.5;
							$SALP_PERCENT = 0;
							if ($SALQ_TYPE_TIME == 2) $SALP_REMARK = "ข้อ 11 กฎ ก.พ.ว่าด้วยการเลื่อนขั้นเงินเดือน พ.ศ.2544";
						}
						if ($SALP_SALARY_NEW <= $PER_SALARY) {
							$SALP_SPSALARY = $SALP_SALARY_NEW;
							$SALP_SALARY_NEW = $CAL_LAYER_SALARY_FULL;
						}
					}
				} else {
					$cmd = " select 	LAYER_NO, a.LEVEL_NO_SALARY, PER_SALARY, LAYER_SALARY 
							from 	PER_PERSONAL a, PER_LAYER b
							where 	a.PER_ID=$PER_ID and LAYER_TYPE=$LAYER_TYPE and a.LEVEL_NO_SALARY=b.LEVEL_NO and 
									LAYER_SALARY>=PER_SALARY 
							order by 	LAYER_NO ";
					$db_dpis1->send_cmd($cmd);
					$data1 = $db_dpis1->get_array();
					$UP_SALP_LAYER = $data1[LAYER_NO] + $SALP_LEVEL;
					$LEVEL_NO_SALARY = $data1[LEVEL_NO_SALARY];
                                        
					// select ว่า LAYER_NO ที่เพิ่มขึ้นเกินจำนวน LAYER_NO ของ LEVEL_NO นั้น ๆ หรือเปล่า 
					// ถ้าจำนวนขั้นที่เลื่อนขึ้นสูงกว่าที่มีอยู่จริง ให้เพิ่มได้ถึงแค่ MAX	
                                        
					$cmd = " select LAYER_NO, LAYER_SALARY
							from PER_LAYER where LAYER_TYPE=$LAYER_TYPE and LEVEL_NO='$LEVEL_NO_SALARY' and LAYER_ACTIVE=1 
							order by LAYER_NO desc";
					$count_layer_no = $db_dpis2->send_cmd($cmd);
					$data2 = $db_dpis2->get_array();
					$MAX_LAYER_NO = trim($data2[LAYER_NO]) + 0;
					$MAX_LAYER_SALARY = trim($data2[LAYER_SALARY]);
                        //echo ":: UP_SALP_LAYER=$UP_SALP_LAYER || LEVEL_NO=$data1[LEVEL_NO_SALARY] || LAYER_TYPE=$LAYER_TYPE ||	MAX_LAYER_NO=$MAX_LAYER_NO<br>";
					if ($UP_SALP_LAYER > $MAX_LAYER_NO) {			// ถ้าจำนวนที่ต้องการเลื่อนเกินจำนวน max layer
						$UP_SALP_LAYER = $MAX_LAYER_NO;		
						$non_promote_text .= "alert('เลื่อนขั้นเงินเดือนของ $PER_NAME ได้ไม่ครบตามจำนวน เนื่องจากเกินจำนวนเงินเดือนเต็มขั้น');";
					} else { 											// ถ้าไม่เกินจำนวน max layer
						$cmd = " select LAYER_SALARY from PER_LAYER 
								  where LAYER_TYPE=$LAYER_TYPE and LAYER_NO=$UP_SALP_LAYER and LEVEL_NO='$data1[LEVEL_NO_SALARY]' 
								  order by LAYER_SALARY ";
						$db_dpis1->send_cmd($cmd);
						$data1 = $db_dpis1->get_array();				
						$SALP_SALARY_NEW = $data1[LAYER_SALARY] + 0;
						// echo ":: SALP_SALARY_NEW = $SALP_SALARY_NEW<br>";	
					}	// end if ($UP_SALP_LAYER > $MAX_LAYER_NO) 
				}
			} elseif ($PER_TYPE==2) {
                            $cmd = " select * from PER_SALPROMOTE a, PER_PERSONAL b, PER_PRENAME c 
							where		SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and 
										a.PER_ID=b.PER_ID and b.PN_CODE=c.PN_CODE and a.PER_ID=$PER_ID";		 
                                $db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
                                $SALP_LEVEL_CH = $data1[SALP_LEVEL];
                                $SALP_MON = $data1[SALP_SALARY_NEW];
                                $SALP_OLD = $data1[SALP_SALARY_OLD]; 
                                $LEVEL_NO_C = $data1[LEVEL_NO];
                                $SALP_PERCEN_C = $data1[SALP_PERCENT];
                                $POEM_ID = trim($data1[POEM_ID]);
                                $PER_SALARY = $SALP_OLD;
                                
             $cmd = "  select b.PG_CODE, b.PG_NAME_SALARY, c.POEM_NO, c.POEM_NO_NAME, c.PN_CODE, f.GROUP_SALARY, 
                                    f.MAX_SALARY ,f.UP_SALARY, f.LEVEL_NO
                                    from PER_POS_GROUP b, PER_POS_EMP c, PER_POS_LEVEL_SALARY f
                                    where POEM_ID=$POEM_ID and trim(c.PG_CODE_SALARY)=trim(b.PG_CODE)and trim(c.PN_CODE)= trim(f.PN_CODE) 
                                     and trim(f.LEVEL_NO)='$LEVEL_NO_C'";
                                $db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
                                $PG_CODE    = $data1[PG_CODE];
                                $PN_CODE    = trim($data1[PN_CODE]);
                                $MAX_SALARY = $data1[MAX_SALARY];
                                $UP_SALARY  = $data1[UP_SALARY];
                                $GROUP_SALARY = $data1[GROUP_SALARY];
                                $LEVEL_NO = $data1[LEVEL_NO];
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
                                 $val_pg_code = implode(',',$arr_pg_code);
                                 
                                 $pg_code_p = end($CN_GROUP_SALARY)*1000;
                                 $pg_code_up =($pg_code_p)+1000;
                                if($pg_code_up == 5000) $pg_code_up = 4000;
                               
                                
 //==================================================== START ===================================================                       
                    //หาขั้นเงินเดือน
                  $cmd = "select LAYERE_NO, LAYERE_SALARY,PG_CODE from PER_LAYEREMP  
			where PG_CODE in ($val_pg_code) and LAYERE_SALARY >= $PER_SALARY 
                              order by PG_CODE , LAYERE_NO ASC  ";
                             $db_dpis1->send_cmd($cmd);
                             $data1 = $db_dpis1->get_array();
                             $LAYERE_NO     = $data1[LAYERE_NO];
                             $LAYERE_SALARY = $data1[LAYERE_SALARY];
                             $tm_pg_code = trim($data1[PG_CODE]);
                         
                             
                        if(!$LAYERE_SALARY){                           
                              $cmd = "select LAYERE_NO, LAYERE_SALARY,PG_CODE from PER_LAYEREMP  
                                where PG_CODE in ($pg_code_up) and LAYERE_SALARY >= $PER_SALARY 
                                    order by PG_CODE , LAYERE_NO ASC";
                                     $db_dpis1->send_cmd($cmd);
                                     $data1 = $db_dpis1->get_array();
                                     $LAYERE_NO     = $data1[LAYERE_NO];
                                     $LAYERE_SALARY = $data1[LAYERE_SALARY];
                                         
                         }    
                             //select เพื่อมาเที่ยบ เงินสุดบัญชี
                             $cmd = "select LAYERE_NO, LAYERE_SALARY from PER_LAYEREMP  
                                            where PG_CODE in($val_pg_code) order by LAYERE_SALARY desc";
                                            $db_dpis1->send_cmd($cmd);
                                            $data1 = $db_dpis1->get_array();
                                            $MAX_SALARY_EMP = $data1[LAYERE_SALARY];
                                            $LAYERE_NO_EMP = $data1[LAYERE_NO]; 
                                            
                            if($LAYERE_SALARY == $MAX_SALARY_EMP){                
                                     if ($SALP_LEVEL == 0)  $STEPUP = 1;              
                                elseif($SALP_LEVEL == 0.5)  $STEPUP = 1;
                                else if($SALP_LEVEL == 1)   $STEPUP = 2;
                                else if($SALP_LEVEL == 1.5) $STEPUP = 3;
                           }else{
                                     if ($SALP_LEVEL == 0)  $STEPUP = 1;              
                                elseif($SALP_LEVEL == 0.5)  $STEPUP = 2;
                                else if($SALP_LEVEL == 1)   $STEPUP = 3;
                                else if($SALP_LEVEL == 1.5) $STEPUP = 4;             
                           }     
                          
                        if($LAYERE_SALARY == $MAX_SALARY_EMP){//เงินสุดบัญชีให้เงินไปวิ่งหาขั้นในกลุ่มถัดไป  
                                  $cmd =" select * from (select LAYERE_NO, LAYERE_SALARY,rownum as stepup
                                        from PER_LAYEREMP where PG_CODE =($pg_code_up) and LAYERE_SALARY >= $LAYERE_SALARY                                              
                                        and LAYERE_ACTIVE=1 )where stepup=$STEPUP ";
                                        $db_dpis1->send_cmd($cmd);
                                        $data1 = $db_dpis1->get_array();
                                        $LAYERE_NO = $data1[LAYERE_NO];
                                        $SALP_SALARY_NEW  = $data1[LAYERE_SALARY];
                                        if ($SALP_LEVEL == 1 && $SALQ_TYPE_TIME == 1) $SALP_REMARK = "ข้อ 9 ระเบียบกระทรวงการคลังว่าด้วยการเลื่อนขั้นเงินเดือน พ.ศ.2544";
                                        
                                 if(!$SALP_SALARY_NEW){
                                    $tm_pg_code =($pg_code_up)+1000;
                                    if($tm_pg_code == 5000) $tm_pg_code = 4000;
                                         $cmd =" select * from (select LAYERE_NO, LAYERE_SALARY,rownum as stepup
                                                from PER_LAYEREMP where PG_CODE = '$tm_pg_code' and LAYERE_SALARY >= $LAYERE_SALARY                                               
                                                and LAYERE_ACTIVE=1 )where stepup=$STEPUP ";
                                                $db_dpis1->send_cmd($cmd);
                                                $data1 = $db_dpis1->get_array();
                                                $LAYERE_NO = $data1[LAYERE_NO];
                                                $SALP_SALARY_NEW  = $data1[LAYERE_SALARY];
                                                if ($SALP_LEVEL == 1 && $SALQ_TYPE_TIME == 1) $SALP_REMARK = "ข้อ 9 ระเบียบกระทรวงการคลังว่าด้วยการเลื่อนขั้นเงินเดือน พ.ศ.2544";
                                  }      
                                        
                        }else{
                                    if($LAYERE_SALARY > $MAX_SALARY_EMP){
                                        $pg_code_p = $pg_code_p = $pg_code_up;
                                    }else{
                                        $pg_code_p = $tm_pg_code;
                                    }
                                 $cmd =" select * from (select LAYERE_NO, LAYERE_SALARY,rownum as stepup
                                        from PER_LAYEREMP where PG_CODE = '$pg_code_p' and LAYERE_SALARY >= $LAYERE_SALARY                                               
                                        and LAYERE_ACTIVE=1 )where stepup=$STEPUP ";
                                        $db_dpis1->send_cmd($cmd);
                                        $data1 = $db_dpis1->get_array();
                                        $LAYERE_NO = $data1[LAYERE_NO];
                                        $SALP_SALARY_NEW  = $data1[LAYERE_SALARY];
                                        if ($SALP_LEVEL == 1 && $SALQ_TYPE_TIME == 1) $SALP_REMARK = "ข้อ 9 ระเบียบกระทรวงการคลังว่าด้วยการเลื่อนขั้นเงินเดือน พ.ศ.2544";
     
                                  if(!$SALP_SALARY_NEW){
                                    //ไม่มีค่าเงิน ให้บวกกลุ่มเพิ่ม เพื่อให้เข้า if แจ้งเตือน 
                                   
                                    $tm_pg_code =($tm_pg_code)+1000;
                                    if($tm_pg_code == 5000) $tm_pg_code =4000;
                                    if($tm_pg_code != 2000) $STEPUP = $STEPUP - 1;
                                      $cmd =" select * from (select LAYERE_NO, LAYERE_SALARY,rownum as stepup
                                                from PER_LAYEREMP where PG_CODE = '$tm_pg_code' and LAYERE_SALARY >= $LAYERE_SALARY                                               
                                                and LAYERE_ACTIVE=1 )where stepup=$STEPUP";
                                                $db_dpis1->send_cmd($cmd);
                                                $data1 = $db_dpis1->get_array();
                                                $SALP_SALARY_NEW  = $data1[LAYERE_SALARY];
                                                if ($SALP_LEVEL == 1 && $SALQ_TYPE_TIME == 1) $SALP_REMARK = "ข้อ 9 ระเบียบกระทรวงการคลังว่าด้วยการเลื่อนขั้นเงินเดือน พ.ศ.2544";
                                  }      
                        }     
                  $cmd = " select UP_SALARY from PER_POS_LEVEL_SALARY where PN_CODE = '$PN_CODE'  and LEVEL_NO ='$LEVEL_NO' ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$UP_SALARY = trim($data1[UP_SALARY]);
                                
                      if($SALP_LEVEL == 0)$SALP_SALARY_NEW = $PER_SALARY;             
                               //echo "--->>>>".$SALP_SALARY_NEW." >= ".$UP_SALARY."|| ".$SALP_SPSALARY ."&&"."!".$SALP_SALARY_NEW."||".$PER_SALARY ." > ". $UP_SALARY;
                                if ($SALP_SALARY_NEW > $UP_SALARY || !$SALP_SALARY_NEW) {			// ถ้าจำนวนที่ต้องการเลื่อนเกินจำนวน UP_SALARY
                                    echo "<script> alert('เงินไม่อยู่ในเกณฑ์ [ไม่สามารถคำนวณได้]');exit;</script>";  
					$non_promote_text .= "เลื่อนขั้นเงินเดือนของ $PER_NAME ได้ไม่ครบตามจำนวน เนื่องจากเกินจำนวนเงินเดือนเต็มขั้น";
                                }//end if 
                                
			} elseif ($PER_TYPE==3) {
				// คำนวนผลการเลื่อนขั้นเงินเดือน			
				$cmd = " select c.EG_CODE, LAYERES_NO, LAYERES_SALARY   
							  from PER_PERSONAL a, PER_POS_EMPSER b, PER_EMPSER_POS_NAME c, PER_LAYER_EMPSER d 
							  where a.PER_ID=$PER_ID and a.POEMS_ID=b.POEMS_ID and b.PN_CODE=c.PN_CODE and c.EG_CODE=d.EG_CODE
							  order by LAYERES_NO ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$UP_SALP_LAYER = $data1[LAYERES_NO] + $SALP_LEVEL;

				// select ว่า LAYER_NO ที่เพิ่มขึ้นเกินจำนวน LAYER_NO ของ LEVEL_NO นั้น ๆ หรือเปล่า 
				// ถ้าจำนวนขั้นที่เลื่อนขึ้นสูงกว่าที่มีอยู่จริง ให้เพิ่มได้ถึงแค่ MAX	
				$cmd = " select LAYER_NO, LAYER_SALARY
						from PER_LAYER where LEVEL_NO='$LEVEL_NO' and LAYER_ACTIVE=1 order by LAYER_NO desc";
				$count_layer_no = $db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$MAX_LAYER_NO = trim($data1[LAYER_NO]);
				$MAX_LAYER_SALARY = trim($data1[LAYER_SALARY]);
				if ($UP_SALP_LAYER > $MAX_LAYER_NO) {			// ถ้าจำนวนที่ต้องการเลื่อนเกินจำนวน max layer
					$UP_SALP_LAYER = $MAX_LAYER_NO;
					$non_promote_text .= "เลื่อนขั้นเงินเดือนของ $PER_NAME ได้ไม่ครบตามจำนวน เนื่องจากเกินจำนวนเงินเดือนเต็มขั้น<br>";
				} else {												// ถ้าไม่เกินจำนวน max layer
					$cmd = " select LAYERES_SALARY from PER_LAYER_EMPSER
								  where EG_CODE=$data1[EG_CODE] and LAYERES_NO='$UP_SALP_LAYER'  
								  order by LAYERES_SALARY ";
					$db_dpis1->send_cmd($cmd);
					$data1 = $db_dpis1->get_array();				
					$SALP_SALARY_NEW = $data1[LAYERES_SALARY] + 0;
				}
			}
			//แก้ไขการคำนวณ  09/03/2012
			//if($SALP_SALARY_NEW && $SALP_PERCENT){
				//$SALP_SPSALARY = ($SALP_SALARY_NEW/$SALP_PERCENT)/100;
			//}
			if (!trim($non_promote_text)) {	// ถ้าไม่มีข้อความแจ้งว่าเลื่อนขั้นเงินเดือนเกินแท่งแล้ว จึงจะสามารถ update ได้ 
				$SALP_SALARY_NEW = (trim($SALP_SALARY_NEW))? $SALP_SALARY_NEW : "$SALP_MON"; 
				$SALP_SPSALARY = str_replace(",", "", $SALP_SPSALARY);	
                                                    $cmd = " update PER_SALPROMOTE set 
								SALP_LEVEL = $SALP_LEVEL, 
								SALP_YN = $SALP_YN, 
								SALP_SALARY_NEW = $SALP_SALARY_NEW, 
								SALP_PERCENT = $SALP_PERCENT, 
								SALP_SPSALARY = $SALP_SPSALARY, 
								SALP_DATE = '$tmp_SALP_DATE', 
								SALP_REMARK = '$SALP_REMARK', 
								UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
							  where SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID and PER_ID=$PER_ID ";
			}  else {						// ถ้า update เลื่อนขั้นเงินเดือนไม่ได้ ก็ต้องให้แก้ไขได้ต่อก่อน
				$UPD = 1;
			}
		} elseif ( $SALP_YN==0) {			// ไม่สมควรได้เลื่อนขั้นเงินเดือน
			$SALP_SPSALARY = str_replace(",", "", $SALP_SPSALARY);
                                        $cmd = " update PER_SALPROMOTE set 
							SALP_LEVEL = 0, 
							SALP_YN = 0, 
							SALP_PERCENT = $SALP_PERCENT, 
							SALP_SPSALARY = $SALP_SPSALARY, 
							SALP_DATE = '$tmp_SALP_DATE', 
							SALP_REMARK = '$SALP_REMARK',
                                                        SALP_SALARY_NEW = $PER_SALARY_OL,
							UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
						  where SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID and PER_ID=$PER_ID ";		
		} // if $SALP_YN
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลการเลื่อนขั้นเงินเดือน [".trim($SALQ_YEAR)." : ".$SALQ_TYPE." : ".$PER_ID." : "."]");
	}
	// =================== END :: เมื่อมีการแก้ไขข้อมูลข้าราชการฯ ที่มีชื่อในการเลื่อนขั้นเงินเดือน ====================

	if($command == "DELETE" && trim($SALQ_YEAR) && trim($SALQ_TYPE) && trim($DEPARTMENT_ID)){
		$cmd = " delete from PER_SALPROMOTE where SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID and PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลการเลื่อนขั้นเงินเดือน [".trim($SALQ_YEAR)." : ".$SALQ_TYPE." : ".$PER_ID." : "."]");
	}


// ======================= START :: select ข้อมูลการเลื่อนขั้นเงินเดือนนั้น ๆ =========================
//echo "<br>if ( trim($SALQ_YEAR) && $SALQ_TYPE && $tmp_salp_ORG_ID ) {<br>";
	if ( trim($SALQ_YEAR) && $SALQ_TYPE && $tmp_salp_ORG_ID ) {
		if ($PER_TYPE == 1) {							// ข้าราชการ
			if ($SALQ_TYPE_TIME == 1) {
				$cmd = "	select 		sum(SALP_SALARY_OLD) as SUM_SALA_OLD, sum(SALP_SALARY_NEW) as SUM_SALA_NEW, 
									sum(SALP_SPSALARY) as SUM_SPSALA
						from 		PER_SALPROMOTE a, PER_PERSONAL b, PER_POSITION c
						where 		SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and
									a.DEPARTMENT_ID=$DEPARTMENT_ID and 
									c.ORG_ID=$tmp_salp_ORG_ID and a.PER_ID=b.PER_ID and b.POS_ID=c.POS_ID ";
			} elseif ($SALQ_TYPE_TIME == 2) {
				$cmd = " select 		sum(SALP_SALARY_OLD) as SUM_SALA_OLD, sum(SALP_SALARY_NEW) as SUM_SALA_NEW, 
									sum(SALP_SPSALARY) as SUM_SPSALA 
						from 		PER_SALPROMOTE a, PER_PERSONAL b, PER_POSITION c
						where 		SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and
									SALP_LEVEL=1 and a.DEPARTMENT_ID=$DEPARTMENT_ID and 
									c.ORG_ID=$tmp_salp_ORG_ID and a.PER_ID=b.PER_ID and b.POS_ID=c.POS_ID ";			
			}
		} elseif ($PER_TYPE == 2) {					// ลูกจ้างประจำ
			if ($SALQ_TYPE_TIME == 1) {
				$cmd = "	select 		sum(SALP_SALARY_OLD) as SUM_SALA_OLD, sum(SALP_SALARY_NEW) as SUM_SALA_NEW, 
									sum(SALP_SPSALARY) as SUM_SPSALA
						from 		PER_SALPROMOTE a, PER_PERSONAL b, PER_POS_EMP c
						where 		SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and
									a.DEPARTMENT_ID=$DEPARTMENT_ID and 
									c.ORG_ID=$tmp_salp_ORG_ID and a.PER_ID=b.PER_ID and b.POEM_ID=c.POEM_ID ";
			} elseif ($SALQ_TYPE_TIME == 2) {
				$cmd = " select 		sum(SALP_SALARY_OLD) as SUM_SALA_OLD, sum(SALP_SALARY_NEW) as SUM_SALA_NEW, 
									sum(SALP_SPSALARY) as SUM_SPSALA
						from 		PER_SALPROMOTE a, PER_PERSONAL b, PER_POS_EMP c
						where 		SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and
									SALP_LEVEL=1 and a.DEPARTMENT_ID=$DEPARTMENT_ID and 
									c.ORG_ID=$tmp_salp_ORG_ID and a.PER_ID=b.PER_ID and b.POEM_ID=c.POEM_ID ";
			}

		} elseif ($PER_TYPE == 3) {					// พนักงานราชการ
			if ($SALQ_TYPE_TIME == 1) {
				$cmd = "	select 		sum(SALP_SALARY_OLD) as SUM_SALA_OLD, sum(SALP_SALARY_NEW) as SUM_SALA_NEW, 
									sum(SALP_SPSALARY) as SUM_SPSALA
						from 		PER_SALPROMOTE a, PER_PERSONAL b, PER_POS_EMPSER c
						where 		SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and
									a.DEPARTMENT_ID=$DEPARTMENT_ID and 
									c.ORG_ID=$tmp_salp_ORG_ID and a.PER_ID=b.PER_ID and b.POEMS_ID=c.POEMS_ID ";
			} elseif ($SALQ_TYPE_TIME == 2) {
				$cmd = " select 		sum(SALP_SALARY_OLD) as SUM_SALA_OLD, sum(SALP_SALARY_NEW) as SUM_SALA_NEW, 
									sum(SALP_SPSALARY) as SUM_SPSALA
						from 		PER_SALPROMOTE a, PER_PERSONAL b, PER_POS_EMPSER c
						where 		SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and
									SALP_LEVEL=1 and a.DEPARTMENT_ID=$DEPARTMENT_ID and 
									c.ORG_ID=$tmp_salp_ORG_ID and a.PER_ID=b.PER_ID and b.POEMS_ID=c.POEMS_ID ";
			}
		} 	// end if 
		$count_data = $db_dpis3->send_cmd($cmd);
		$data3 = $db_dpis3->get_array();
		$tmp_sum_old = $data3[SUM_SALA_OLD];
		$tmp_sum_new = $data3[SUM_SALA_NEW];
		$SALA_SUM_OLD = number_format($data3[SUM_SALA_OLD], 2, '.', ',');
		$SALA_SUM_NEW = number_format($data3[SUM_SALA_NEW], 2, '.', ',');
		$SPSALA_SUM = number_format($data3[SUM_SPSALA], 2, '.', ',');				
		$SALA_RISE = $data3[SUM_SALA_NEW] - $data3[SUM_SALA_OLD];
		$SALA_PERCENT = number_format(($SALA_RISE * 100) / $data3[SUM_SALA_OLD], 2, '.', ',');
		$SALA_RISE = number_format($data3[SUM_SALA_NEW] - $data3[SUM_SALA_OLD], 2, '.', ',');
		
		if ($ORG_ID) 				$table_salquotadtl = "PER_SALQUOTADTL1";
		elseif ($ORG_ID_ASS)		$table_salquotadtl = "PER_SALQUOTADTL2";
		$cmd = " 	select SALQD_QTY1, SALQD_QTY2 from $table_salquotadtl 
			  	where SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and ORG_ID=$tmp_salp_ORG_ID and DEPARTMENT_ID=$DEPARTMENT_ID ";
		$db_dpis3->send_cmd($cmd);
		$data3 = $db_dpis3->get_array();
		$tmp_all_level1 = $data3[SALQD_QTY2];
		$SALA_ALL_LEVEL1 = number_format($data3[SALQD_QTY2], 0, '.', ',');

		$where = "";
		if ($SALQ_TYPE==1 || $SALQ_TYPE==3 || $SALQ_TYPE==5) // === select จำนวนคนที่ได้เลื่อน 1 ขั้น === 
			$where = " and SALP_LEVEL=1";
		if ($PER_TYPE == 1) {				// ข้าราชการ
			$cmd = " 	select 	count(c.PER_ID) as SUM_PER, b.ORG_ID 
							from 	PER_PERSONAL a, PER_POSITION b, PER_SALPROMOTE c  
							where 	SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and 
									a.DEPARTMENT_ID=$DEPARTMENT_ID and 
									a.POS_ID=b.POS_ID and  a.PER_ID=c.PER_ID and b.ORG_ID=$ORG_ID $where
							group by b.ORG_ID ";
		} elseif ($PER_TYPE == 2) {		// ลูกจ้างประจำ
			$cmd = " 	select 	sum(c.SALP_SALARY_NEW-c.SALP_SALARY_OLD) as SUM_PER, b.ORG_ID 
							from 	PER_PERSONAL a, PER_POS_EMP b, PER_SALPROMOTE c  
							where 	SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and 
									a.DEPARTMENT_ID=$DEPARTMENT_ID and 							
									a.POEM_ID=b.POEM_ID and  a.PER_ID=c.PER_ID and b.ORG_ID=$ORG_ID $where 
							group by b.ORG_ID ";		
			$db_dpis3->send_cmd($cmd);
			$data3 = $db_dpis3->get_array();
			$tmp_sal_level1 = $data3[SUM_PER];
			$SALA_SAL_LEVEL1 = number_format($data3[SUM_PER], 0, '.', ',');
			$cmd = " 	select 	count(c.PER_ID) as SUM_PER, b.ORG_ID 
							from 	PER_PERSONAL a, PER_POS_EMP b, PER_SALPROMOTE c  
							where 	SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and 
									a.DEPARTMENT_ID=$DEPARTMENT_ID and 							
									a.POEM_ID=b.POEM_ID and  a.PER_ID=c.PER_ID and b.ORG_ID=$ORG_ID $where 
							group by b.ORG_ID ";		
		} elseif ($PER_TYPE == 3) {		// พนักงานราชการ
			$cmd = " 	select 	count(c.PER_ID) as SUM_PER, b.ORG_ID 
					from 	PER_PERSONAL a, PER_POS_EMPSER b, PER_SALPROMOTE c  
					where 	SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and
							a.DEPARTMENT_ID=$DEPARTMENT_ID and 							
							a.POEMS_ID=b.POEMS_ID and  a.PER_ID=c.PER_ID and b.ORG_ID=$ORG_ID $where 
					group by b.ORG_ID ";		
		}
		$db_dpis3->send_cmd($cmd);
		$data3 = $db_dpis3->get_array();
		$tmp_tot_level1 = $data3[SUM_PER];
		if ($SALQ_TYPE==4) {
			$SALA_TOT_LEVEL1 = $SALA_SAL_LEVEL1;
			$SALA_REST_LEVEL1 = number_format($tmp_all_level1 - $tmp_sal_level1, 0, '.', ','); 
		} else {
			$SALA_TOT_LEVEL1 = number_format($data3[SUM_PER], 0, '.', ',');
			$SALA_REST_LEVEL1 = number_format($tmp_all_level1 - $tmp_tot_level1, 0, '.', ','); 
		}
	
		// === select จำนวนคนที่ได้ ร้อยละเงินตอบแทนพิเศษ 2% และ 4% ===
		$SPSALA_PERCENT2 = $SPSALA_PERCENT4 = "0";
		if ($PER_TYPE == 1) {				// ข้าราชการ
			$cmd = " 	select  SALP_PERCENT, count(c.PER_ID) as COUNT_PERCENT 
							from 	PER_PERSONAL a, PER_POSITION b, PER_SALPROMOTE c  
							where 	SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and a.DEPARTMENT_ID=$DEPARTMENT_ID and 
										SALP_PERCENT>0 and a.POS_ID=b.POS_ID and  a.PER_ID=c.PER_ID and b.ORG_ID=$ORG_ID 
							group by b.ORG_ID, SALP_PERCENT 
							order by SALP_PERCENT ";
			$db_dpis3->send_cmd($cmd);
			while ( $data3 = $db_dpis3->get_array()) {
				$SPSALA_PERCENT2 = (trim($data3[SALP_PERCENT]) == 2)? trim($data3[COUNT_PERCENT]) : $SPSALA_PERCENT2;
				$SPSALA_PERCENT4 = (trim($data3[SALP_PERCENT]) == 4)? trim($data3[COUNT_PERCENT]) : $SPSALA_PERCENT4;	
			}
											
		} elseif ($PER_TYPE == 2) {		// ลูกจ้างประจำ
			$cmd = " 	select 	count(c.PER_ID) as SUM_PER, b.ORG_ID 
							from 	PER_PERSONAL a, PER_POS_EMP b, PER_SALPROMOTE c  
							where 	SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and a.DEPARTMENT_ID=$DEPARTMENT_ID and 
										SALP_PERCENT=2 and a.POEM_ID=b.POEM_ID and  a.PER_ID=c.PER_ID and b.ORG_ID=$ORG_ID 
							group by b.ORG_ID ";		
			$db_dpis3->send_cmd($cmd);
			while ( $data3 = $db_dpis3->get_array()) {
				$SPSALA_PERCENT2 = (trim($data3[SALP_PERCENT]) == 2)? trim($data3[COUNT_PERCENT]) : $SPSALA_PERCENT2;
				$SPSALA_PERCENT4 = (trim($data3[SALP_PERCENT]) == 4)? trim($data3[COUNT_PERCENT]) : $SPSALA_PERCENT4;				
			}
							
		} elseif ($PER_TYPE == 3) {		// พนักงานราชการ
			$cmd = " 	select 	count(c.PER_ID) as SUM_PER, b.ORG_ID 
							from 	PER_PERSONAL a, PER_POS_EMPSER b, PER_SALPROMOTE c  
							where 	SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and a.DEPARTMENT_ID=$DEPARTMENT_ID and 
												SALP_PERCENT=2 and a.POEMS_ID=b.POEMS_ID and  a.PER_ID=c.PER_ID and b.ORG_ID=$ORG_ID 
							group by b.ORG_ID ";		
			$db_dpis3->send_cmd($cmd);
			while ( $data3 = $db_dpis3->get_array()) {
				$SPSALA_PERCENT2 = (trim($data3[SALP_PERCENT]) == 2)? trim($data3[COUNT_PERCENT]) : $SPSALA_PERCENT2;
				$SPSALA_PERCENT4 = (trim($data3[SALP_PERCENT]) == 4)? trim($data3[COUNT_PERCENT]) : $SPSALA_PERCENT4;				
			}
		}
	}	// end if 		
// ======================= END :: select ข้อมูลการเลื่อนขั้นเงินเดือนนั้น ๆ =========================

	if($UPD || $VIEW){
            
		$cmd = "	select PN_NAME, PER_NAME, PER_SURNAME from PER_PERSONAL a, PER_PRENAME b
				where a.PER_ID=$PER_ID and a.PN_CODE=b.PN_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = trim($data[PN_NAME]) . trim($data[PER_NAME]) . " " . trim($data[PER_SURNAME]);
		
		$cmd = " 	select SALP_YN, SALP_LEVEL, SALP_PERCENT, SALP_SPSALARY, SALP_DATE, SALP_REMARK, SALP_SALARY_OLD 
				from 	PER_SALPROMOTE 
				where SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID and PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	
		$data = $db_dpis->get_array();
		$SALP_YN = $data[SALP_YN];
		$SALP_LEVEL = number_format($data[SALP_LEVEL], 1, '.', '');
		$SALP_PERCENT = trim($data[SALP_PERCENT]);
		$SALP_SPSALARY = number_format($data[SALP_SPSALARY], 2, '.', ',');
		$SALP_DATE = trim($data[SALP_DATE]);
		$SALP_REMARK = trim($data[SALP_REMARK]);
		$SALP_DATE = show_date_format($data[SALP_DATE], 1);
		$SALP_SALARY_OLD = trim($data[SALP_SALARY_OLD]);
	} // end if	
	
	if( !$UPD && !$VIEW && !$DEL && !$non_promote_text ){
		$PER_ID = "";
		$PER_NAME = "";
		$SALP_YN = 1;
		$SALP_LEVEL = "";
		$SALP_PERCENT = "";
		$SALP_SPSALARY = "";
		$SALP_DATE = "";
		$SALP_REMARK = "";
		$SALP_DATE = "";		
	} // end if
	
	if ($command == "CANCEL") {
		$SALQ_YEAR = "";
		$SALQ_TYPE = "";
		$SALQ_TYPE_TIME = "";
		$PER_TYPE = 1;
		$SALQ_TIME = 1;
		$ORG_ID = "";
		$ORG_NAME = "";
		$ORG_ID_ASS = "";
		$ORG_NAME_ASS = "";
		
		$salq_type_text = "จำนวนคนที่เลื่อน 1 ขั้น";
		$salq_type_text_sum = "รวมจำนวนคนที่เลื่อน 1 ขั้น";
		$SALA_ALL_LEVEL1 = "";
		$SALA_TOT_LEVEL1 = "";
		$SALA_REST_LEVEL1 = "";
		$SALA_SUM_OLD = "";
		$SALA_RISE = "";
		$SALA_SUM_NEW = "";
		$SPSALA_SUM = "";
		$SALA_PERCENT = "";
		$SPSALA_PERCENT2 = "";
		$SPSALA_PERCENT4 = "";
	
		$PER_ID = "";
		$SALP_YN = 1;
		$SALP_LEVEL = "";
		$SALP_PERCENT = "";
		$SALP_SPSALARY = "";
		$SALP_DATE = "";
		$SALP_REMARK = "";
		$SALP_DATE = "";			
	}

?>