<?
/*
	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	if($UPD || $VIEW){
		$cmd = " select 	TR_CODE, CO_NO, CO_STARTDATE, CO_ENDDATE, CO_PLACE, CT_CODE, 
						CO_ORG, CO_FUND, CT_CODE_FUND, CO_TYPE, CO_CONFIRM, CO_DAY, CO_REMARK
				from 	PER_COURSE 
				where 	CO_ID=$CO_ID  "; 
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$CO_NO = trim($data[CO_NO]);
		$CO_PLACE = trim($data[CO_PLACE]);
		$CO_ORG = trim($data[CO_ORG]);
		$CO_FUND = trim($data[CO_FUND]);
		$CO_TYPE = trim($data[CO_TYPE]);
		$CO_CONFIRM = trim($data[CO_CONFIRM]);
		$CO_DAY = trim($data[CO_DAY]);
		$CO_REMARK = trim($data[CO_REMARK]);

		$CO_STARTDATE =  substr(trim($data[CO_STARTDATE]), 8, 2) ."/". substr(trim($data[CO_STARTDATE]), 5, 2) ."/". (substr(trim($data[CO_STARTDATE]), 0, 4) + 543);
		$CO_ENDDATE =  substr(trim($data[CO_ENDDATE]), 8, 2) ."/". substr(trim($data[CO_ENDDATE]), 5, 2) ."/". (substr(trim($data[CO_ENDDATE]), 0, 4) + 543);

		$TR_NAME = $CT_NAME = $CT_NAME_FUND = "";
		$TR_CODE = trim($data[TR_CODE]);
		$cmd = "select TR_NAME from PER_TRAIN where TR_CODE='$TR_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$TR_NAME = $data_dpis2[TR_NAME];
		
		$CT_CODE = trim($data[CT_CODE]);
		$CT_CODE_FUND = trim($data[CT_CODE_FUND]);				
		$cmd = "select CT_CODE, CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE' or CT_CODE='$CT_CODE_FUND' ";
		$db_dpis2->send_cmd($cmd);
		while ( $data_dpis2 = $db_dpis2->get_array() ) {
			if ( $CT_CODE == $data_dpis2[CT_CODE] )			$CT_NAME = $data_dpis2[CT_NAME];
			if ( $CT_CODE_FUND == $data_dpis2[CT_CODE] )		$CT_NAME_FUND = $data_dpis2[CT_NAME];			
		}

		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
		$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEPARTMENT_NAME = $data[ORG_NAME];
		$MINISTRY_ID = $data[ORG_ID_REF];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];
	} // end if
	
	if( !$UPD && !$DEL && !$VIEW ){
		$CO_ID = "";
		$TR_CODE = "";
		$TR_NAME = "";
		$CO_NO = "";
		$CO_STARTDATE = "";
		$CO_ENDDATE = "";
		$CO_PLACE = "";
		$CT_CODE = "";
		$CT_NAME = "";
		$CO_ORG = "";
		$CO_FUND = "";
		$CT_CODE_FUND = "";
		$CT_NAME_FUND = "";
		$CO_TYPE = "";
		$CO_CONFIRM = 0;
		$CO_DAY = "";
		$CO_REMARK = "";
	} // end if		*/
	
	if($command == "DELETE" && trim($CO_ID) ){
	$cmd = " select PER_ID from PER_COURSEDTL a
						  where CO_ID=$CO_ID "; 
		$count_result = $db_dpis->send_cmd($cmd);
		//echo $count_result."<hr>";
		$data = $db_dpis->get_array();
			$TMP_PER_ID = $data[PER_ID];
		//echo "PER_ID = $TMP_PER_ID<hr>";	
		if($TMP_PER_ID!="") { echo "<br><cencer>ไม่สามารถลบช้อมูลได้ เนื่องจากมีคนเข้าอบรมหลักสูตรนี้อยู่</center>"; }
					
		$cmd = " delete from PER_COURSE where CO_ID=$CO_ID ";
	$db_dpis->send_cmd($cmd);
	

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลการอบรม [".trim($CO_ID)." : ".$TR_CODE."]");
		#############
		$CO_ID = "";
		#############
	}
?>