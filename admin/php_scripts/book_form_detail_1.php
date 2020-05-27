<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");	

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	$data_per_page = 5;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	if(!$SUBPAGE) $SUBPAGE = 1;

	$cmd = " select code, name_th, group_level, pv_code, org_id from user_group where id=$SESS_USERGROUP ";
	$db->send_cmd($cmd);
//	$db->show_error();
	$data = $db->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	$SESS_GROUPCODE = $data[code];
	
	if($SESS_USERGROUP == 1){
		$USER_AUTH = TRUE;
	}elseif((($SESS_GROUPCODE == "BUREAU" || substr($SESS_GROUPCODE, 0, 7) == "BUREAU_" ) || $SESS_USERGROUP==3) && $PER_ID==$SESS_PER_ID){
		$USER_AUTH = TRUE;
	}else{
		$USER_AUTH = FALSE;
	} // end if
	
	$cmd = " select PER_ID, PG_END_DATE from PER_PERFORMANCE_GOODNESS where PG_ID=$PG_ID ";	
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PER_ID = $data[PER_ID];
	$PG_END_DATE = substr($data[PG_END_DATE], 0, 10);
	$PG_YEAR = substr($PG_END_DATE, 0, 4) + 543;
	$BUDGET_YEAR = $PG_YEAR - 543;

	$cmd = " select 	PN_CODE, PER_NAME, PER_SURNAME, PER_TYPE, POS_ID, POEM_ID, POEMS_ID, POT_ID, LEVEL_NO, DEPARTMENT_ID
					 from		PER_PERSONAL
					 where	PER_ID=$PER_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PN_CODE = trim($data[PN_CODE]);
	$PER_NAME = trim($data[PER_NAME]);
	$PER_SURNAME = trim($data[PER_SURNAME]);
	$PER_TYPE = trim($data[PER_TYPE]);
	$POS_ID = trim($data[POS_ID]);
	$POEM_ID = trim($data[POEM_ID]);
	$POEMS_ID = trim($data[POEMS_ID]);
	$POT_ID = trim($data[POT_ID]);
	$LEVEL_NO = trim($data[LEVEL_NO]);
	$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);

	$cmd = " select POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$POSITION_LEVEL = $data[POSITION_LEVEL];
	
	$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$PN_NAME = trim($data[PN_NAME]);
	
	$PER_NAME = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;
		
	if($PER_TYPE==1){
		$cmd = " select 	b.PL_NAME, a.PT_CODE 
						 from 		PER_POSITION a, PER_LINE b
						 where	a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = trim($data[PL_NAME]);
		$PT_CODE = trim($data[PT_CODE]);
		$PT_NAME = trim($data[PT_NAME]);
		//$PL_NAME = trim($PL_NAME)?($PL_NAME ." ". level_no_format($LEVEL_NO) . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"ระดับ ".level_no_format($LEVEL_NO);
		$PL_NAME = trim($PL_NAME)?($PL_NAME ."".$POSITION_LEVEL. (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):"ระดับ ".level_no_format($LEVEL_NO);
	}elseif($PER_TYPE==2){
		$cmd = " select 	b.PN_NAME
						 from 		PER_POS_EMP a, PER_POS_NAME b
						 where	a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE
					  ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = trim($data[PN_NAME]);
	}elseif($PER_TYPE==3){
		$cmd = " select 	b.EP_NAME
						 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b
						 where	a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE
					  ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = trim($data[EP_NAME]);
	}elseif($PER_TYPE==4){
		$cmd = " select 	b.TP_NAME
						 from 		PER_POS_TEMP a, PER_TEMP_POS_NAME b
						 where	a.POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE
					  ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = trim($data[TP_NAME]);
	} // end if
		
	$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$DEPARTMENT_NAME = $data[ORG_NAME];
	$MINISTRY_ID = $data[ORG_ID_REF];

	$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$MINISTRY_NAME = $data[ORG_NAME];	
	
	if( $SUBPAGE==1 ){
		$cmd = " select 		POH_ID, PL_CODE, LEVEL_NO 
						 from 			PER_POSITIONHIS 
						 where 		PER_ID=$PER_ID 
						 order by 	POH_EFFECTIVEDATE desc ";
		$db_dpis->send_cmd($cmd);
		$PL_CODE = $LEVEL_NO = -1;
		$data_count = 0;
		while($data = $db_dpis->get_array()){
			if( $PL_CODE != $data[PL_CODE] || $LEVEL_NO != $data[LEVEL_NO] ){
				$data_count++;
				$ARR_POSITIONHIS[] = $data[POH_ID];
				
				$PL_CODE = $data[PL_CODE];
				$LEVEL_NO = $data[LEVEL_NO];

				$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$ARR_POSITIONHIS_POSITION[$data[POH_ID]] = $data2[PL_NAME];

				$cmd = " select POSITION_LEVEL from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$ARR_POSITIONHIS_LEVEL[$data[POH_ID]] = $data2[POSITION_LEVEL];
			} // end if
			if($data_count >= 5) break;
		} // end while
		
//		echo "<pre>"; print_r($ARR_POSITIONHIS_POSITION); echo "</pre>";
//		echo "<pre>"; print_r($ARR_POSITIONHIS_LEVEL); echo "</pre>";
	}elseif( $SUBPAGE==2 ){
		for($i=$BUDGET_YEAR; $i > ($BUDGET_YEAR - 5); $i--){
			$promote_level = $promote_percent = 0;
			$promote_none = "";
			unset($arr_search_condition);
			if($DPISDB=="odbc"){
				$arr_search_condition[] = "(LEFT(trim(SAH_EFFECTIVEDATE, 10)) >= '".($i - 1)."-10-01')";
				$arr_search_condition[] = "(LEFT(trim(SAH_EFFECTIVEDATE, 10)) < '".$i."-10-01')";
			}elseif($DPISDB=="oci8"){
				$arr_search_condition[] = "(SUBSTR(trim(SAH_EFFECTIVEDATE), 1, 10) >= '".($i - 1)."-10-01')";
				$arr_search_condition[] = "(SUBSTR(trim(SAH_EFFECTIVEDATE), 1, 10) < '".$i."-10-01')";
			} // end if
			$search_condition = " and ". implode(" and ", $arr_search_condition);
			$cmd = " select			SAH_ID, MOV_CODE, SAH_PERCENT_UP
						  	 from			PER_SALARYHIS
							 where		PER_ID=$PER_ID
							 					and MOV_CODE in ('21310', '21320', '21330', '21340', '21370', '21351', '21352', '21353', '21354', '21315', '21325', '21335', '21345', '21375')
												$search_condition
							 order by 	SAH_EFFECTIVEDATE desc ";			
			$db_dpis->send_cmd($cmd);
//			echo "$cmd<br>";
			while($data = $db_dpis->get_array()){
				switch( trim($data[MOV_CODE]) ){
					case	"21310"	:
								$promote_level += 0.5;
								break;
					case	"21315"	:
								$promote_percent += $data[SAH_PERCENT_UP];
								break;
					case	"21351"	:
								$promote_level += 0.5;
								break;
					case	"21320"	:
								$promote_level += 1.0;
								break;
					case	"21325"	:
								$promote_percent += $data[SAH_PERCENT_UP];
								break;
					case	"21352"	:
								$promote_level += 1.0;
								break;
					case	"21330"	:
								$promote_level += 1.5;
								break;
					case	"21335"	:
								$promote_percent += $data[SAH_PERCENT_UP];
								break;
					case	"21353"	:
								$promote_level += 1.5;
								break;
					case	"21340"	:
								$promote_level += 2.0;
								break;
					case	"21345"	:
								$promote_percent += $data[SAH_PERCENT_UP];
								break;
					case	"21354"	:
								$promote_level += 2.0;
								break;
					case	"21370"	:
								$promote_none = "ไม่ได้รับการเลื่อนขั้นเงินเดือน";
								break;
					case	"21375"	:
								$promote_none = "ไม่ได้รับการเลื่อนขั้นเงินเดือน";
								break;
				} // end switch
			} // end while
			
			$ARR_SALARYHIS[] = ($i + 543);
			if($promote_level){
				$ARR_SALARYHIS_PROMOTE[$i] = "ได้เลื่อนขั้นเงินเดือน ".number_format($promote_level, 1)." ขั้น";
			}elseif($promote_percent){
				$ARR_SALARYHIS_PROMOTE[$i] = "ได้เลื่อนเงินเดือน ".number_format($promote_percent, 4)." %";
			}elseif($promote_none){
				$ARR_SALARYHIS_PROMOTE[$i] = $promote_none;
			}else{
				$ARR_SALARYHIS_PROMOTE[$i] = "ไม่มีข้อมูล";
			} // end if
		} // end for
//		echo "<pre>"; print_r($ARR_SALARYHIS_PROMOTE); echo "</pre>";
	}elseif( $SUBPAGE==3 ){
	} // end if
?>