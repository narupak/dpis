<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/function_list.php");		
	include("php_scripts/load_per_control.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$SALQDTL_TYPE = (isset($SALQDTL_TYPE))? $SALQDTL_TYPE : 1 ;
	$pass_value_salq_type1 = (trim($pass_value_salq_type1))? $pass_value_salq_type1 : $SALQ_TYPE1;
	$pass_value_salq_type2 = (trim($pass_value_salq_type2))? $pass_value_salq_type2 : $SALQ_TYPE2;
	$SALQ_TYPE1 = (isset($SALQ_TYPE1))? $SALQ_TYPE1 : $pass_value_salq_type1;
	$SALQ_TYPE2 = (isset($SALQ_TYPE2))? $SALQ_TYPE2 : $pass_value_salq_type2;

	if ($SALQ_TYPE1 == 1 && $SALQ_TYPE2 == 1)			$SALQ_TYPE = 1; 
	elseif ($SALQ_TYPE1 == 1 && $SALQ_TYPE2 == 2)		$SALQ_TYPE = 2; 
	elseif ($SALQ_TYPE1 == 2 && $SALQ_TYPE2 == 1)		$SALQ_TYPE = 3; 
	elseif ($SALQ_TYPE1 == 2 && $SALQ_TYPE2 == 2)		$SALQ_TYPE = 4; 
	elseif ($SALQ_TYPE1 == 3 && $SALQ_TYPE2 == 1)		$SALQ_TYPE = 5; 
	elseif ($SALQ_TYPE1 == 3 && $SALQ_TYPE2 == 2)		$SALQ_TYPE = 6; 

	// ================================================================== //
	// ลบข้อมูล
	if ( $command == "DELETE" && trim($SALQ_YEAR) && trim($SALQ_TYPE1) && trim($SALQ_TYPE2)) {
		$cmd = " delete from PER_SALQUOTADTL1 where SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID ";
		$db_dpis2->send_cmd($cmd);

		$cmd = " delete from PER_SALQUOTADTL2 where SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID ";
		$db_dpis2->send_cmd($cmd);

		$cmd = " delete from PER_SALPROMOTE where SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID ";
		$db_dpis2->send_cmd($cmd);
	
		$cmd = " delete from PER_SALQUOTA where SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID ";
		$db_dpis2->send_cmd($cmd);
	}
	
	// ================================================================== //
	// ปรับปรุงข้อมูล
	if ($UPD) {
		if ($SALQDTL_TYPE == 1)	{				// โครงสร้างตามกฎหมาย
			for ($i=0; $i<count($COF_ORG_ID); $i++) {
				$temp = str_replace(",", "", ${"COUNT_RPER$COF_ORG_ID[$i]"});
				$cmd = " update PER_SALQUOTADTL1 set SALQD_QTY2=". $temp ." 
							  where SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID and ORG_ID=$COF_ORG_ID[$i] ";
				$db_dpis2->send_cmd($cmd);
			}	// for
		} elseif ($SALQDTL_TYPE == 2) {				// โครงสร้างตามมอบหมายงาน
			for ($i=0; $i<count($COF_ORG_ID); $i++) {
				$temp = str_replace(",", "", ${"COUNT_RPER$COF_ORG_ID[$i]"});
				$cmd = " update PER_SALQUOTADTL2 set SALQD_QTY2=". $temp ." 
							  where SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID and ORG_ID=$COF_ORG_ID[$i] ";
				$db_dpis2->send_cmd($cmd);
			}	// for	
		}		// if ($SALQDTL_TYPE == 1)
	}
	
	if( $command == "CANCEL" || $command == "DELETE" ){
		$SALQ_YEAR = "";
		$SALQ_PERCENT = "";
		$SALQ_DATE = "";
		$SALQ_TYPE1 = 1;
		$SALQ_TYPE2 = 1;
		unset($pass_value_salq_type1, $pass_value_salq_type2);
		$SALQDTL_TYPE = 1;		
	} // end if		

	// ========================================================= //
	// ประมวลผล
	if( $command == "PROCESS" && trim($SALQ_YEAR) && trim($SALQ_TYPE1) && trim($SALQ_TYPE2) ){
		$cmd =" select ORG_ID from PER_ORG where ORG_ID_REF=$DEPARTMENT_ID and OL_CODE='03' and ORG_ACTIVE=1 order by ORG_NAME ";
		$COUNT_DATA = $db_dpis->send_cmd($cmd);
//		echo "PROCESS--cmd=$cmd ($COUNT_DATA)<br>";
		//$db_dpis->show_error();
		$total_page = ceil( $COUNT_DATA / $data_per_page );
		$page_link = create_link_page($total_page, $current_page);
		$limit_data = "";	

		$cmd = " delete from PER_SALQUOTADTL1 where SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID ";
		$db_dpis2->send_cmd($cmd);
		//$db_dpis2->show_error();
	
		$cmd = " delete from PER_SALQUOTADTL2 where SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID ";
		$db_dpis2->send_cmd($cmd);
		//$db_dpis2->show_error();	

		$cmd = " delete from PER_SALQUOTA where SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID ";
		$db_dpis2->send_cmd($cmd);
		//$db_dpis2->show_error();
	
		$temp_SALQ_DATE =  save_date($SALQ_DATE);
		$cmd = "	insert into PER_SALQUOTA
						(SALQ_YEAR, SALQ_TYPE, SALQ_PERCENT, SALQ_DATE, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE) 
						values
						('$SALQ_YEAR', $SALQ_TYPE, $SALQ_PERCENT, '$temp_SALQ_DATE', $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') ";		
		$db_dpis2->send_cmd($cmd);
		//$db_dpis2->show_error();
	
		// ============= โครงสร้างตามกฏหมาย ============== //
		$cmd = "	select ORG_ID from PER_ORG where ORG_ID_REF=$DEPARTMENT_ID and OL_CODE='03' and ORG_ACTIVE=1 order by 	ORG_SEQ_NO, ORG_CODE ";
		$db_dpis->send_cmd($cmd);
		while ( $data = $db_dpis->get_array()) {
			$TMP_ORG_ID = $data[ORG_ID];
			if ($SALQ_TYPE2 == 1) {				// เลื่อนครั้งที่ 1
				if ($SALQ_TYPE1 == 1) {			// ข้าราชการ
					$cmd = "	select count(PER_ID) as temp_count_data from PER_PERSONAL a, PER_POSITION b 
									where a.POS_ID=b.POS_ID and b.ORG_ID=$TMP_ORG_ID";
				} elseif ($SALQ_TYPE1 == 2) {		// ลูกจ้างประจำ
					$cmd = "	select count(PER_ID) as temp_count_data from PER_PERSONAL a, PER_POS_EMP b 
									where a.POEM_ID=b.POEM_ID and b.ORG_ID=$TMP_ORG_ID";		
				} elseif ($SALQ_TYPE1 == 3) {		// พนักงานราชการ 
					$cmd = "	select count(PER_ID) as temp_count_data from PER_PERSONAL a, PER_POS_EMPSER b 
									where a.POEMS_ID=b.POEMS_ID and b.ORG_ID=$TMP_ORG_ID";		
				}
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$data2 = array_change_key_case($data2, CASE_LOWER);		
				$temp_count_per = $data2[temp_count_data];
				if ($SALQ_YEAR > 2552)
					$COUNT_PER = round ((( $temp_count_per * $SALQ_PERCENT ) / 100), 2);
				else
					$COUNT_PER = number_format(round ((( $temp_count_per * $SALQ_PERCENT ) / 100), 2), 2, '.', '');
				$SUM_COUNT_PER += $COUNT_PER;
				${"COUNT_RPER$TMP_ORG_ID"}  = round ($COUNT_PER);
				$SUM_COUNT_RPER += ${"COUNT_RPER$TMP_ORG_ID"};					
				
			} elseif ($SALQ_TYPE2 == 2) {			// เลื่อนครั้งที่ 2
				if ($SALQ_TYPE1 == 1) {
					$cmd = "	select sum(PER_SALARY) as temp_count_data from PER_PERSONAL a, PER_POSITION b 
									where a.POS_ID=b.POS_ID and b.ORG_ID=$TMP_ORG_ID";
				} elseif ($SALQ_TYPE1 == 2) {
					$cmd = "	select sum(PER_SALARY) as temp_count_data from PER_PERSONAL a, PER_POS_EMP b 
									where a.POEM_ID=b.POEM_ID and b.ORG_ID=$TMP_ORG_ID";		
				} elseif ($SALQ_TYPE1 == 3) {
					$cmd = "	select sum(PER_SALARY) as temp_count_data from PER_PERSONAL a, PER_POS_EMPSER b 
									where a.POEMS_ID=b.POEMS_ID and b.ORG_ID=$TMP_ORG_ID";		
				}		
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$data2 = array_change_key_case($data2, CASE_LOWER);
				$temp_count_per = $data2[temp_count_data];	
				$COUNT_PER = round ((( $temp_count_per * $SALQ_PERCENT ) / 100), 2);
				$SUM_COUNT_PER += $COUNT_PER;
				${"COUNT_RPER$TMP_ORG_ID"}  = round ($COUNT_PER);
				$SUM_COUNT_RPER += ${"COUNT_RPER$TMP_ORG_ID"};					
			}		
	
			// insert รายละเอียดโควตาเลื่อนขั้นเงินเดือน โครงสร้างตามกฎหมาย
			$cmd = "	insert into PER_SALQUOTADTL1
							(SALQ_YEAR, SALQ_TYPE, ORG_ID, SALQD_QTY1, SALQD_QTY2, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE) 
							values
							('$SALQ_YEAR', $SALQ_TYPE, $TMP_ORG_ID, $COUNT_PER, ${"COUNT_RPER$TMP_ORG_ID"}, $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') ";		
			$db_dpis2->send_cmd($cmd);		
			//$db_dpis2->show_error();		
		} // while
		
		// ============= โครงสร้างตามมอบหมายงาน ============== //
		//$cmd =" select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$DEPARTMENT_ID and OL_CODE='03' and ORG_ACTIVE=1 order by ORG_NAME ";
		$cmd =" select ORG_ID from PER_ORG_ASS where OL_CODE='03' and ORG_ACTIVE=1 order by ORG_NAME ";
		$db_dpis->send_cmd($cmd);
		while ( $data = $db_dpis->get_array()) {
			$TMP_ORG_ID = $data[ORG_ID];
			if ($SALQ_TYPE2 == 1) {
				if ($SALQ_TYPE1 == 1) {
					$cmd = "	select count(PER_ID) as temp_count_data from PER_PERSONAL where (POS_ID is not NULL or POS_ID!=0) and ORG_ID=$TMP_ORG_ID";
					$temp_count_per = $db_dpis2->send_cmd($cmd);
				} elseif ($SALQ_TYPE1 == 2) {
					$cmd = "	select count(PER_ID) as temp_count_data from PER_PERSONAL where (POEM_ID is not NULL or POEM_ID!=0) and ORG_ID=$TMP_ORG_ID";			
				} elseif ($SALQ_TYPE1 == 3) {
					$cmd = "	select count(PER_ID) as temp_count_data from PER_PERSONAL where (POEMS_ID is not NULL or POEMS_ID!=0) and ORG_ID=$TMP_ORG_ID";			
				}
				$db_dpis2->send_cmd($cmd);		
				$data2 = $db_dpis2->get_array();
				$data2 = array_change_key_case($data2, CASE_LOWER);		
				$temp_count_per = $data2[temp_count_data];
				if ($SALQ_YEAR > 2552)
					$COUNT_PER = round ((( $temp_count_per * $SALQ_PERCENT ) / 100), 2);
				else
					$COUNT_PER = number_format(round ((( $temp_count_per * $SALQ_PERCENT ) / 100), 2), 2, '.', '');
				$SUM_COUNT_PER += $COUNT_PER;
				${"COUNT_RPER$TMP_ORG_ID"}  = round ($COUNT_PER);
				$SUM_COUNT_RPER += ${"COUNT_RPER$TMP_ORG_ID"};					
				
			} elseif ($SALQ_TYPE2 == 2) {
				if ($SALQ_TYPE1 == 1) {
					$cmd = "	select sum(PER_SALARY) as temp_count_data from PER_PERSONAL where (POS_ID is not NULL or POS_ID!=0) and ORG_ID=$TMP_ORG_ID";
					$temp_count_per = $db_dpis2->send_cmd($cmd);
				} elseif ($SALQ_TYPE1 == 2) {
					$cmd = "	select sum(PER_SALARY) as temp_count_data from PER_PERSONAL where (POEM_ID is not NULL or POEM_ID!=0) and ORG_ID=$TMP_ORG_ID";			
				} elseif ($SALQ_TYPE1 == 3) {
					$cmd = "	select sum(PER_SALARY) as temp_count_data from PER_PERSONAL where (POEMS_ID is not NULL or POEMS_ID!=0) and ORG_ID=$TMP_ORG_ID";			
				}		
				$db_dpis2->send_cmd($cmd);		
				$data2 = $db_dpis2->get_array();
				$data2 = array_change_key_case($data2, CASE_LOWER);		
				$temp_count_per = $data2[temp_count_data];
				$COUNT_PER = round ((( $temp_count_per * $SALQ_PERCENT ) / 100), 2);
				$SUM_COUNT_PER += $COUNT_PER;
				${"COUNT_RPER$TMP_ORG_ID"}  = round ($COUNT_PER);
				$SUM_COUNT_RPER += ${"COUNT_RPER$TMP_ORG_ID"};					
			}

			// insert รายละเอียดโควตาเลื่อนขั้นเงินเดือน โครงสร้างตามมอบหมายงาน		
			$cmd = "	insert into PER_SALQUOTADTL2
					(SALQ_YEAR, SALQ_TYPE, ORG_ID, SALQD_QTY1, SALQD_QTY2, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE) 
					values
					('$SALQ_YEAR', $SALQ_TYPE, $TMP_ORG_ID, $COUNT_PER, ${"COUNT_RPER$TMP_ORG_ID"}, $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') ";		
			$db_dpis2->send_cmd($cmd);
			//$db_dpis2->show_error();			
		}	// end while 
		$command = "VIEW";			
	} 			// end if

	
	// ========================================================= //
	// ดูข้อมูล
	if( $command == "VIEW" && trim($SALQ_YEAR) && trim($SALQ_TYPE1) && trim($SALQ_TYPE2) && $DEPARTMENT_ID){
		$VIEW = 1;
		
		if($CTRL_TYPE==1 || $CTRL_TYPE==2){
			$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$MINISTRY_ID = $data[ORG_ID_REF];
			
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$MINISTRY_NAME = $data[ORG_NAME];
		} // end if
		
		if($CTRL_TYPE != 4){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$DEPARTMENT_NAME = $data[ORG_NAME];
		} // end if
		
		if($SALQDTL_TYPE == 1){		//ตาม กม.
			$cmd =" select count(ORG_ID) as COUNT_DATA from PER_ORG where ORG_ID_REF=$DEPARTMENT_ID and OL_CODE='03' and ORG_ACTIVE=1 ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			$total_page = ceil( $COUNT_DATA / $data_per_page );
			$page_link = create_link_page($total_page, $current_page);
			$limit_data = "";			

			if($DPISDB=="odbc"){
				/*		
				$cmd = "	select 	ORG_ID 
						from 	PER_ORG 
						where 	ORG_ID_REF=$DEPARTMENT_ID and OL_CODE='03' and ORG_ACTIVE=1 
								$limit_data
						order by 	ORG_SEQ_NO, ORG_CODE ";	
				*/		
				$cmd = "	select 	sum(SALQD_QTY1) as SUM1, sum(SALQD_QTY2) as SUM2 from PER_SALQUOTADTL1
						where	SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SUM_COUNT_PER = $data2[SUM1];
				$SUM_COUNT_RPER = $data2[SUM2];

			}elseif($DPISDB=="oci8"){
				/*
				$cmd = " select 	ORG_ID from PER_ORG 
								where 	ORG_ID_REF=$DEPARTMENT_ID and OL_CODE='03' and ORG_ACTIVE=1 
								order by 	ORG_SEQ_NO, ORG_CODE ";
				*/
				$cmd = "	select 	sum(SALQD_QTY1) as SUM1, sum(SALQD_QTY2) as SUM2 from PER_SALQUOTADTL1
						where	SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SUM_COUNT_PER = $data2[SUM1];
				$SUM_COUNT_RPER = $data2[SUM2];
			}elseif($DPISDB=="mysql"){
				$cmd = "	select 	sum(SALQD_QTY1) as SUM1, sum(SALQD_QTY2) as SUM2 from PER_SALQUOTADTL1
						where	SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SUM_COUNT_PER = $data2[SUM1];
				$SUM_COUNT_RPER = $data2[SUM2];
			} // if

		}elseif($SALQDTL_TYPE == 2){	//ตามมอบหมายงาน
			$cmd =" select count(ORG_ID) as COUNT_DATA from PER_ORG_ASS where OL_CODE='03' and ORG_ACTIVE=1 ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$COUNT_DATA = $data[COUNT_DATA];
			$total_page = ceil( $COUNT_DATA / $data_per_page );
			$page_link = create_link_page($total_page, $current_page);
			$limit_data = "";			
		
			if($DPISDB=="odbc"){
				/*
				$cmd = "	select 	ORG_ID from PER_ORG_ASS 
						where 	OL_CODE='03' and ORG_ACTIVE=1
								$limit_data  
						order by 	ORG_SEQ_NO, ORG_CODE ";	
				*/
				$cmd = "	select 	sum(SALQD_QTY1) as SUM1, sum(SALQD_QTY2) as SUM2 from PER_SALQUOTADTL1
						where	SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SUM_COUNT_PER = $data2[SUM1];
				$SUM_COUNT_RPER = $data2[SUM2];							
													
			}elseif($DPISDB=="oci8"){
				/*
				$cmd =" 	select ORG_ID from PER_ORG_ASS 
						where OL_CODE='03' and ORG_ACTIVE=1 
						order by ORG_SEQ_NO, ORG_CODE ";	
				*/
				$cmd = "	select 	sum(SALQD_QTY1) as SUM1, sum(SALQD_QTY2) as SUM2 from PER_SALQUOTADTL1
						where	SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SUM_COUNT_PER = $data2[SUM1];
				$SUM_COUNT_RPER = $data2[SUM2];						
			}elseif($DPISDB=="mysql"){
				$cmd = "	select 	sum(SALQD_QTY1) as SUM1, sum(SALQD_QTY2) as SUM2 from PER_SALQUOTADTL1
						where	SALQ_YEAR='$SALQ_YEAR' and SALQ_TYPE=$SALQ_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SUM_COUNT_PER = $data2[SUM1];
				$SUM_COUNT_RPER = $data2[SUM2];	
			} // if 
		} // end if
		$db_dpis->send_cmd($cmd);
	} // end if
	//echo $COUNT_DATA;
?>