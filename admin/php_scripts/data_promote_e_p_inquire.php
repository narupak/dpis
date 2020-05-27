<?
	$alert_save_success = "";
//echo "1..search_from=$search_from, LEVEL_START=$LEVEL_START, LEVEL_END=$LEVEL_END, PL_PN_CODE=$PL_PN_CODE, PER_TYPE=$PER_TYPE, DEPARTMENT_ID=$DEPARTMENT_ID, POS_POEM_ID=$POS_POEM_ID<br>";
/**	
	switch($CTRL_TYPE){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$ORG_ID_M1 = $MINISTRY_ID;
			$ORG_NAME_M1 = $MINISTRY_NAME;
			break;
		case 4 :
			$ORG_ID_M1 = $MINISTRY_ID;
			$ORG_NAME_M1 = $MINISTRY_NAME;
			$ORG_ID_M2 = $DEPARTMENT_ID;
			$ORG_NAME_2 = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$ORG_ID_M1 = $MINISTRY_ID;
			$ORG_NAME_M1 = $MINISTRY_NAME;
			break;
		case 4 :
			$ORG_ID_M1 = $MINISTRY_ID;
			$ORG_NAME_M1 = $MINISTRY_NAME;
			$ORG_ID_M2 = $DEPARTMENT_ID;
			$ORG_NAME_2 = $DEPARTMENT_NAME;
			break;
		case 5 :
			$ORG_ID_M1 = $MINISTRY_ID;
			$ORG_NAME_M1 = $MINISTRY_NAME;
			$ORG_ID_M2 = $DEPARTMENT_ID;
			$ORG_NAME_2 = $DEPARTMENT_NAME;
			$ORG_ID_M3 = $ORG_ID;
			$ORG_NAME_3 = $ORG_NAME;
			break;
		case 6 :
			$ORG_ID_M1 = $MINISTRY_ID;
			$ORG_NAME_M1 = $MINISTRY_NAME;
			$ORG_ID_M2 = $DEPARTMENT_ID;
			$ORG_NAME_2 = $DEPARTMENT_NAME;
			$ORG_ID_M3 = $ORG_ID;
			$ORG_NAME_3 = $ORG_NAME;
			$ORG_ID_M4 = $ORG_ID_1;
			$ORG_NAME_4 = $ORG_NAME_1;
			break;
	} // end switch case
**/

//	echo "$CTRL_TYPE::$SESS_USERGROUP_LEVEL>>1=$ORG_ID_M1:$ORG_NAME_M1, 2=$ORG_ID_M2:$ORG_NAME_2, 3=$ORG_ID_M3:$ORG_NAME_3<br>";

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	//if(trim($POS_POEM_NO) && !$PL_PN_CODE)					$PL_PN_CODE= trim($POS_POEM_NO); 	//กรณีที่เลือกมาแสดงข้อมูล					$PL_PN_CODE			$PL_PN_NAME
	//echo "5555555555555555555555".$PL_PN_CODE." = ".trim($POS_POEM_NO);
	if($SESS_PER_TYPE==0){ $PER_TYPE = (isset($PER_TYPE))?  $PER_TYPE : 1;	}
//	$PER_TYPE = (isset($PER_TYPE))? $PER_TYPE : 1;
	if ($PER_TYPE == 1) {
		$search_pos = "POS_ID"; 
		$search_from = ", PER_POSITION b";
		$search_field = ", POS_CHANGE_DATE, b.PM_CODE, b.PL_CODE, a.LEVEL_NO, POS_NO_NAME, POS_NO, b.PT_CODE";
		if($DPISDB=="oci8")	$search_where = " and b.PL_CODE=d.PL_CODE(+) ";
		else 								$search_where = " and b.PL_CODE=d.PL_CODE ";
		$table_select = "PER_PROMOTE_P";

		$search_position_table = "PER_POSITION b";
		$search_position_condition = "a.POS_ID=b.POS_ID";
		$search_perline_table = "PER_LINE d";
		if($DPISDB=="oci8")	$search_perline_condition = "b.PL_CODE=d.PL_CODE(+)";
		else 								$search_perline_condition = "b.PL_CODE=d.PL_CODE";
	} elseif ($PER_TYPE == 2)	{
		$search_pos = "POEM_ID"; 
		$search_from = ", PER_POS_EMP b , PER_POS_NAME d";				
		$search_field = ", POEM_NO_NAME, POEM_NO, b.PN_CODE, a.LEVEL_NO";
		if($DPISDB=="oci8")	$search_where = " and b.PN_CODE=d.PN_CODE(+) ";			//$search_where = " and b.PN_CODE=c.PN_CODE(+) and b.PN_CODE=d.PN_CODE(+) ";
		else 								$search_where = " and b.PN_CODE=d.PN_CODE ";
		$table_select = "PER_PROMOTE_E";		

		$search_position_table = "PER_POS_EMP b";
		$search_position_condition = "a.POEM_ID=b.POEM_ID";
		$search_perline_table = "PER_POS_NAME d";
		if($DPISDB=="oci8")	$search_perline_condition = "b.PN_CODE=d.PN_CODE(+)";
		else									$search_perline_condition = "b.PN_CODE=d.PN_CODE";
	} elseif ($PER_TYPE == 3)	{
		$search_pos = "POEMS_ID"; 
		$search_from = ", PER_POS_EMPSER b, PER_EMPSER_POS_NAME d";				
		$search_field = ", POEMS_NO_NAME, POEMS_NO, b.EP_CODE, a.LEVEL_NO";
		if($DPISDB=="oci8")	$search_where = " and b.EP_CODE=d.EP_CODE(+) ";			//$search_where = " and b.EP_CODE=c.EP_CODE(+) and b.EP_CODE=d.EP_CODE(+) ";
		else 								$search_where = " and b.EP_CODE=d.EP_CODE ";
		$table_select = "PER_PROMOTE_E";		

		$search_position_table = "PER_POS_EMPSER b";
		$search_position_condition = "a.POEMS_ID=b.POEMS_ID";
		$search_perline_table = "PER_EMPSER_POS_NAME d";
		if($DPISDB=="oci8")	$search_perline_condition = "b.EP_CODE=d.EP_CODE(+)";
		else									$search_perline_condition = "b.EP_CODE=d.EP_CODE";
	}elseif ($PER_TYPE == 4)	{
		$search_pos = "POT_ID"; 
		$search_from = ", PER_POS_TEMP b, PER_TEMP_POS_NAME d";				
		$search_field = ", POT_NO_NAME, POT_NO, b.TP_CODE, a.LEVEL_NO";
		if($DPISDB=="oci8")	$search_where = " and b.TP_CODE=d.TP_CODE(+) ";			//$search_where = " and b.TP_CODE=c.TP_CODE(+) and b.TP_CODE=d.TP_CODE(+) ";
		else 								$search_where = " and b.TP_CODE=d.TP_CODE  ";
		$table_select = "PER_PROMOTE_E";		

		$search_position_table = "PER_POS_TEMP b";
		$search_position_condition = "a.POT_ID=b.POT_ID";
		$search_perline_table = "PER_TEMP_POS_NAME d";
		if($DPISDB=="oci8")	$search_perline_condition = "b.TP_CODE=d.TP_CODE(+)";
		else									$search_perline_condition = "b.TP_CODE=d.TP_CODE";
	}

	if( $ADD_PERSON ) {
		$temp_pro_date =  save_date($PRO_DATE);
		if ($ADD_PER_TYPE==1) {
			if (!$DEPARTMENT_ID) {
				$cmd = " select DEPARTMENT_ID from PER_PERSONAL where POS_ID=$POS_POEM_ID ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
			}

			$cmd = " insert into PER_PROMOTE_P 
					(PRO_DATE, POS_ID, PER_ID, PRO_SUMMARY, UPDATE_USER, UPDATE_DATE, PER_CARDNO, DEPARTMENT_ID) 
							values 
					('$temp_pro_date', $POS_POEM_ID, $ADD_PER_ID, '$PRO_SUMMARY', $SESS_USERID, '$UPDATE_DATE', '$ADD_PER_CARDNO', $DEPARTMENT_ID) ";
		} elseif ($ADD_PER_TYPE==2) {
			if (!$DEPARTMENT_ID) {
				$cmd = " select DEPARTMENT_ID from PER_PERSONAL where POEM_ID=$POS_POEM_ID ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
			}

			$cmd = "	insert into PER_PROMOTE_E 
					(PRO_DATE, POS_ID, PER_ID, PRO_SUMMARY, UPDATE_USER, UPDATE_DATE, PER_CARDNO, DEPARTMENT_ID)
							values 
					('$temp_pro_date', $POS_POEM_ID, $ADD_PER_ID, '$PRO_SUMMARY', $SESS_USERID, '$UPDATE_DATE', '$ADD_PER_CARDNO', $DEPARTMENT_ID)";
		} // if
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	} // if

//echo "2..search_from=$search_from, LEVEL_START=$LEVEL_START, LEVEL_END=$LEVEL_END, PL_PN_CODE=$PL_PN_CODE, PER_TYPE=$PER_TYPE, DEPARTMENT_ID=$DEPARTMENT_ID, POS_POEM_ID=$POS_POEM_ID<br>";

	if( $DEL_PERSON ) {
		if ($DEL_PER_TYPE==1) {
			$cmd = " delete from PER_PROMOTE_P where PER_ID=$DEL_PER_ID";
		} elseif ($DEL_PER_TYPE==2) {
			$cmd = "	delete from PER_PROMOTE_E where PER_ID=$DEL_PER_ID";
		} // if
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
	} // if
	//echo " <br>DEL : $DEL_PERSON // $DEL_PER_TYPE  // $cmd<br> UPD : $UPD <br> table : $table_select";

	if( $UPD ){
		$temp_pro_date =  save_date($PRO_DATE);
		
		include ("data_promote_e_p_inquire_save.php");
		
		$temp_date = explode("-",$temp_pro_date);
		$PRO_DATE = $temp_date[2] ."/". $temp_date[1] ."/". (string)((int)$temp_date[0] + 543);
//		if(!trim($command)){	$command = "SELECT"; }
//		echo "temp_pro_date=$temp_pro_date, PRO_DATE=$PRO_DATE, POS_POEM_ID=$POS_POEM_ID, TMP_PER_ID=$TMP_PER_ID, PRO_SUMMARY=$PRO_SUMMARY, PER_CARDNO=$PER_CARDNO, ORG_ID_M2=$ORG_ID_M2, $SESS_USERID, '$UPDATE_DATE'<br>";
		if ($PER_TYPE == 1) {
			$field1 = "POS_NO as PNO, POS_NO_NAME as PNONAME, PL_NAME as PNAME ";
			$cmd_position = "	select  $field1 from PER_POSITION a, PER_LINE b 
										where a.PL_CODE=b.PL_CODE and POS_ID=$POS_POEM_ID";
		} elseif ($PER_TYPE == 2) {
			$field1 = "POEM_NO as PNO, POEM_NO_NAME as PNONAME, PN_NAME as PNAME ";
			$cmd_position = "	select  $field1 from PER_POS_EMP a, PER_POS_NAME b
										where a.PN_CODE=b.PN_CODE and POEM_ID=$POS_POEM_ID";
		} elseif ($PER_TYPE == 3) {
			$field1 = "POEMS_NO as PNO, POEMS_NO_NAME as PNONAME, EP_NAME as PNAME";
			$cmd_position = "	select $field1 from PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
										where a.EP_CODE=b.EP_CODE and POEM_ID=$POS_POEM_ID";
		} elseif ($PER_TYPE == 4) {
			$field1 = "POT_NO as PNO, POT_NO_NAME as PNONAME, TP_NAME as PNAME";
			$cmd_position = "	select $field1 from PER_POS_TEMP a, PER_TEMP_POS_NAME b
										where a.TP_CODE=b.TP_CODE and POT_ID=$POS_POEM_ID";
		}
		$db_dpis->send_cmd($cmd_position);
//echo "********** $cmd_position<br>";
		$data = $db_dpis->get_array();
		$POS_POEM_NO = $data[PNO];
		$POS_POEM_NO_NAME = $data[PNONAME]; 
		$P_NAME = $data[PNAME];

		$alert_save_success = "alert('บันทึกผลการค้นหาข้อมูลเรียบร้อยแล้ว')"; 
		//parent.refresh_opener('2<::>$POS_POEM_ID<::>$POS_POEM_NO|$POS_POEM_NO_NAME<::>$P_NAME<::>$PRO_DATE<::>$ORG_ID_M2<::>!<::>!<::>!<::>SELECT');";
	}	// endif if ($UPD)

	if( !$UPD && !$DEL && !$VIEW ){
	} // end if		
?>