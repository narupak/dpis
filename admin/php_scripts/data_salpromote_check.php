<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/function_list.php");		
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        $db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if(!trim($SALQ_YEAR) && !trim($SALQ_TYPE1) && !trim($SALQ_TYPE2) && trim($DEPARTMENT_ID)){
                   $cmd = "	select 		SALQ_YEAR, SALQ_TYPE from PER_SALQUOTA 
				where		DEPARTMENT_ID=$DEPARTMENT_ID
				order by		SALQ_YEAR desc, SALQ_TYPE desc	  ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$SALQ_YEAR = $data[SALQ_YEAR];
		$SALQ_TYPE = $data[SALQ_TYPE];
		if ($SALQ_TYPE == 1) {
			$SALQ_TYPE1 = 1;
			$SALQ_TYPE2 = 1;
		} elseif ($SALQ_TYPE == 2) {
			$SALQ_TYPE1 = 1;
			$SALQ_TYPE2 = 2;
		} elseif ($SALQ_TYPE == 3) {
			$SALQ_TYPE1 = 2;
			$SALQ_TYPE2 = 1;	
		} elseif ($SALQ_TYPE == 4) {
			$SALQ_TYPE1 = 2;
			$SALQ_TYPE2 = 2;	
		} elseif ($SALQ_TYPE == 5) {
			$SALQ_TYPE1 = 3;
			$SALQ_TYPE2 = 1;	
		} elseif ($SALQ_TYPE == 6) {
			$SALQ_TYPE1 = 3;
			$SALQ_TYPE2 = 2;	
		}
	} else {
		if ($SALQ_TYPE1 == 1 && $SALQ_TYPE2 == 1) {
			$SALQ_TYPE = 1;
		} elseif ($SALQ_TYPE1 == 1 && $SALQ_TYPE2 == 2) {
			$SALQ_TYPE = 2;
		} elseif ($SALQ_TYPE1 == 2 && $SALQ_TYPE2 == 1) {
			$SALQ_TYPE = 3;
		} elseif ($SALQ_TYPE1 == 2 && $SALQ_TYPE2 == 2) {
			$SALQ_TYPE = 4;
		} elseif ($SALQ_TYPE1 == 3 && $SALQ_TYPE2 == 1) {
			$SALQ_TYPE = 5;
		} elseif ($SALQ_TYPE1 == 3 && $SALQ_TYPE2 == 2) {
			$SALQ_TYPE = 6;
		}		
	}// end if

	$js_var = "";
	$cmd = "	select SALQ_YEAR, SALQ_TYPE, DEPARTMENT_ID from PER_SALQUOTA 
						where DEPARTMENT_ID is not NULL
						order by	DEPARTMENT_ID, SALQ_YEAR desc, SALQ_TYPE desc  ";
	$db_dpis->send_cmd($cmd);
	while($data = $db_dpis->get_array()){
		$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];
		$TMP_SALQ_YEAR = $data[SALQ_YEAR];
		$TMP_SALQ_TYPE = $data[SALQ_TYPE];

		$cmd = " select PER_ID from PER_SALPROMOTE 
					  where SALQ_YEAR='$TMP_SALQ_YEAR' and SALQ_TYPE=$TMP_SALQ_TYPE and DEPARTMENT_ID=$TMP_DEPARTMENT_ID ";
		$count_promote = $db_dpis2->send_cmd($cmd);
		$js_var .= "ARR_SALQUOTA['".$TMP_DEPARTMENT_ID."_".$TMP_SALQ_YEAR."_".$TMP_SALQ_TYPE."'] = ". ($count_promote + 0) ."; ";		
	} // end while
		
	$now_ABS_YEAR = $SALQ_YEAR - 543;
	$old_ABS_YEAR = $SALQ_YEAR - 544;
	$tmp_search = "('$old_ABS_YEAR-10', '$old_ABS_YEAR-11', '$old_ABS_YEAR-12', '$now_ABS_YEAR-01', '$now_ABS_YEAR-02', '$now_ABS_YEAR-03', ";
	$tmp_search.= "'$now_ABS_YEAR-04', '$now_ABS_YEAR-05', '$now_ABS_YEAR-06', '$now_ABS_YEAR-07', '$now_ABS_YEAR-08', '$now_ABS_YEAR-09')";
	if ($DPISDB=="odbc") {
		$search_monthyear = " and (left(ABS_ENDDATE,7) in $tmp_search)";	
	} elseif($DPISDB=="oci8") {
		$search_monthyear = " and (substr(ABS_ENDDATE,1,7) in $tmp_search)";			
	}elseif($DPISDB=="mysql"){
		$search_monthyear = " and (substring(ABS_ENDDATE,1,7) in $tmp_search)";	
	}
	if ($SALQ_TYPE2 == 1)				$SALP_LEVEL = $LEVEL_TIME1;
	elseif ($SALQ_TYPE2 == 2)			$SALP_LEVEL = $LEVEL_TIME2;
	$SALP_LEVEL_DEFAULT = $SALP_LEVEL;
        
        
	// ตรวจสอบข้าราชการทุกคนที่มีสถานะบรรจุ  ว่าสมควรได้เลื่อนขั้นหรือไม่
	if ($command == "CHECK") {
		$cmd = " delete from PER_SALPROMOTE where SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
    
		$err_count = 0;
		$alert_err = $alert_err_text = $non_promote = $non_promote_text = "";
		$cmd = " 	select 		a.PER_ID, a.PN_CODE, a.PER_SPSALARY, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.LEVEL_NO_SALARY, a.PER_SALARY,
							a.POS_ID, a.POEM_ID, a.POEMS_ID, a.PER_CARDNO, b.LEVEL_SEQ_NO 
				from 		PER_PERSONAL a, PER_LEVEL b 
				where 		a.PER_TYPE = $SALQ_TYPE1 and  b.PER_TYPE= $SALQ_TYPE1 and a.PER_STATUS=1 
                                                and a.DEPARTMENT_ID=$DEPARTMENT_ID and a.LEVEL_NO = b.LEVEL_NO
				order by a.PER_NAME, a.PER_SURNAME "; //PER_TYPE=$SALQ_TYPE1 and PER_STATUS=1 and DEPARTMENT_ID=$DEPARTMENT_ID
                                                
		$db_dpis->send_cmd($cmd);
		while ($data = $db_dpis->get_array()) {		
			$alert_err = false;		
			$PER_ID = $data[PER_ID];
			$POS_ID = trim($data[POS_ID]);
			$POEM_ID = trim($data[POEM_ID]);
			$POEMS_ID = trim($data[POEMS_ID]);	
			$PER_CARDNO = trim($data[PER_CARDNO]);
                        $LEVEL_SEQ_NO = trim($data[LEVEL_SEQ_NO]);
                       // echo ">>".$LEVEL_SEQ_NO."<br>";
			$PN_CODE_P = $data[PN_CODE];
			$cmd = " select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE_P'"; 
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$PN_NAME = $data1[PN_NAME];
			$PER_NAME = $PN_NAME . $data[PER_NAME] ." ". $data[PER_SURNAME];
                        $PER_SPSALARY = $data[PER_SPSALARY];
			$LEVEL_NO = $data[LEVEL_NO];
			$LEVEL_NO_SALARY = $data[LEVEL_NO_SALARY];
			$PER_SALARY = $data[PER_SALARY] + 0;
			// echo "PER_ID=$PER_ID -> PER_SALARY=$PER_SALARY<br>";			
			$SALP_LEVEL = $SALP_LEVEL_DEFAULT; 
			$SALP_YN = 1;
			$SALP_REASON = 0;

			$class = "table_body";
			$onmouse_event = " onMouseOver=\"this.className='table_body_over';\"  onMouseOut=\"this.className='$class';\" ";
			   
                        $cmd = "select  psh.PER_ID, SAH_ID, SAH_EFFECTIVEDATE, SAH_SALARY,SAH_SALARY_EXTRA	  				
                                    from PER_SALARYHIS psh, PER_MOVMENT pm 
                                    where psh.PER_ID=$PER_ID and psh.MOV_CODE=pm.MOV_CODE  
                                    and psh.SAH_EFFECTIVEDATE ='2016-10-01'
                                    order by SAH_EFFECTIVEDATE desc, SAH_SEQ_NO desc, SAH_SALARY desc";
                            $db_dpis1->send_cmd($cmd);
                            $data1 = $db_dpis1->get_array();
                            $SAH_SALARY = $data1[SAH_SALARY];
                            $SAH_SALARY_EXTRA = $data1[SAH_SALARY_EXTRA];

                        
                        
			if ($POS_ID) {
					include ("data_salpromote_check_layer.php");
			} elseif ($POEM_ID) {
					include ("data_salpromote_check_layer_emp.php");
                                        if($EXTRA_CH == 1){
                                            $EXTRA_CH = "CHANGE";        
                                         }
			} elseif ($POEMS_ID) {	
					include ("data_salpromote_check_layer_empser.php");			
			}			
		}	// while ($data = $db_dpis->get_array()) 
                
                        
                        
		if (!trim($alert_err_text)) { 
			$alert_success = "<table border='0' width='100%'><tr><td valign='bottom' align='center' class='table_body'> ... เสร็จสิ้นการตรวจสอบผู้สมควร/ไม่สมควรได้เลื่อนขั้นเงินเดือน ... </td></tr></table>";
		}else{
			$cmd = " delete from PER_SALPROMOTE where SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID ";
			//$db_dpis->send_cmd($cmd);
		} // end if
                
	}	// 	if ($command == "CHECK") {
       
        
?>