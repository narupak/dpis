<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis_org = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	$BONUS_TYPE = (trim($BONUS_TYPE))? $BONUS_TYPE : 1;
	$BONUSDTL_TYPE = (trim($BONUSDTL_TYPE))? $BONUSDTL_TYPE : 1;	
	$BONUS_QUOTA = (trim($BONUS_QUOTA))? number_format(str_replace(",","",$BONUS_QUOTA),2,'.',',') : "";

	if ($BONUSDTL_TYPE == 1) {					// โครงสร้างตามกฎหมาย
		$cmd_org =" select ORG_ID from PER_ORG where ORG_ID_REF=$DEPARTMENT_ID and OL_CODE='03' and ORG_ACTIVE=1 order by ORG_NAME ";
	} elseif ($BONUSDTL_TYPE == 2) {			// โครงสร้างตามมอบหมายงาน 
		$cmd_org =" select ORG_ID from PER_ORG_ASS where OL_CODE='03' and ORG_ACTIVE=1 order by ORG_NAME ";
	}	
	$count_data = $db_dpis_org->send_cmd($cmd_org);	
	//$db_dpis_org->show_error();	
	$total_page = ceil( $count_data / $data_per_page );
	$page_link = create_link_page($total_page, $current_page);
	$limit_data = "";

	// ถ้าเป็นการ PROCESS  ให้ insert ข้อมูลลง ...DTL2  ก่อน  ส่วน ...DTL1 แสดงพร้อม insert
	if ($command == "PROCESS" && trim($BONUS_YEAR) && trim($BONUS_TYPE)) { 	
		$TMP_BONUS_QUOTA = str_replace(",", "", $BONUS_QUOTA);
		$cmd = " insert into PER_BONUSQUOTA 
					(BONUS_YEAR, BONUS_TYPE, BONUS_QUOTA, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE) 
					values
					('$BONUS_YEAR', $BONUS_TYPE, $TMP_BONUS_QUOTA, $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE')";
		$db_dpis2->send_cmd($cmd);
		$cmd = " update PER_BONUSQUOTA set 
					BONUS_QUOTA=$TMP_BONUS_QUOTA, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
					where BONUS_YEAR='$BONUS_YEAR' and BONUS_TYPE=$BONUS_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID ";
		$db_dpis2->send_cmd($cmd);

		$cmd_org =" select ORG_ID from PER_ORG_ASS where OL_CODE='03' and ORG_ACTIVE=1 order by ORG_NAME ";
		$db_dpis_org->send_cmd($cmd_org);
		while ( $data_org = $db_dpis_org->get_array() ) {
			$TMP_ORG_ID = $data_org[ORG_ID];
			$cmd = " insert into PER_BONUSQUOTADTL2 
				   	(BONUS_YEAR, BONUS_TYPE, ORG_ID, BONUSQ_QTY, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE)
					values
					('$BONUS_YEAR', $BONUS_TYPE, $TMP_ORG_ID, 0, $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis2->send_cmd($cmd);
		}
		
	} // end if
	
	
	if( $command == "VIEW" && trim($BONUS_YEAR) && trim($BONUS_TYPE1) && trim($BONUS_TYPE2) && $DEPARTMENT_ID){
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

		if($BONUSDTL_TYPE == 1){
			$cmd =" select ORG_ID from PER_ORG where ORG_ID_REF=$DEPARTMENT_ID and OL_CODE='03' and ORG_ACTIVE=1 order by ORG_SEQ_NO, ORG_CODE ";
		}elseif($BONUSDTL_TYPE == 2){
//			$cmd =" select ORG_ID from PER_ORG_ASS where ORG_ID_REF=$DEPARTMENT_ID and OL_CODE='03' and ORG_ACTIVE=1 order by ORG_NAME ";
			$cmd =" select ORG_ID from PER_ORG_ASS where OL_CODE='03' and ORG_ACTIVE=1 order by ORG_SEQ_NO, ORG_CODE ";
		} // end if
		$count_data = $db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$total_page = ceil( $count_data / $data_per_page );
		$page_link = create_link_page($total_page, $current_page);
		$limit_data = "";		
	} // end if
	

	if ( $command == "DELETE" && trim($BONUS_YEAR) && trim($BONUS_TYPE)) {
		$cmd = " delete from PER_BONUSQUOTADTL1 where BONUS_YEAR='$BONUS_YEAR' and BONUS_TYPE=$BONUS_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID ";
		$db_dpis2->send_cmd($cmd);

		$cmd = " delete from PER_BONUSQUOTADTL2 where BONUS_YEAR='$BONUS_YEAR' and BONUS_TYPE=$BONUS_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID ";
		$db_dpis2->send_cmd($cmd);		

		$cmd = " delete from PER_BONUSQUOTA where BONUS_YEAR='$BONUS_YEAR' and BONUS_TYPE=$BONUS_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID ";
		$db_dpis2->send_cmd($cmd);
	}	
	
	// ปรับปรุงข้อมูล
	if ($UPD) {
		$TMP_BONUS_QUOTA = str_replace(",", "", $BONUS_QUOTA);	
		$cmd = " update PER_BONUSQUOTA set 
					BONUS_QUOTA=$TMP_BONUS_QUOTA, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
					where BONUS_YEAR='$BONUS_YEAR' and BONUS_TYPE=$BONUS_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID ";
		$db_dpis2->send_cmd($cmd);
	
		if ($BONUSDTL_TYPE == 1)	{				// โครงสร้างตามกฎหมาย
			for ($i=0; $i<count($COF_ORG_ID); $i++) { 
				$tmp_bonusq_qty = str_replace(",", "", ${"COUNT_MONEY$COF_ORG_ID[$i]"});	
				$cmd = " update PER_BONUSQUOTADTL1 set BONUSQ_QTY=". $tmp_bonusq_qty ." 
							  where BONUS_YEAR='$BONUS_YEAR' and BONUS_TYPE=$BONUS_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID and ORG_ID=$COF_ORG_ID[$i] ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();				
			}	// for
		} elseif ($BONUSDTL_TYPE == 2) {		// โครงสร้างตามมอบหมายงาน
			for ($i=0; $i<count($COF_ORG_ID); $i++) { 
				$tmp_bonusq_qty = str_replace(",", "", ${"COUNT_MONEY$COF_ORG_ID[$i]"});	
				$cmd = " update PER_BONUSQUOTADTL2 set BONUSQ_QTY=". $tmp_bonusq_qty ." 
							  where BONUS_YEAR='$BONUS_YEAR' and BONUS_TYPE=$BONUS_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID and ORG_ID=$COF_ORG_ID[$i] ";
				$db_dpis2->send_cmd($cmd);
			}	// for	
		}		// if ($BONUSDTL_TYPE == 1)
	}

	if( $command == "CANCEL" || $command == "DELETE" ){
		$BONUS_YEAR = "";
		$BONUS_QUOTA = "";
		$BONUS_TYPE = 1;		
		$BONUSDTL_TYPE = 1;
	} // end if		
?>