<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$command = (trim($command))? $command : "SELECT"; 
	$TMP_ORG_ID = (trim($ORG_ID))? $ORG_ID : $ORG_ID_ASS; 

	if ($BONUS_TYPE == 1) {					// ข้าราชการ
		$table = ", PER_POSITION d ";
		if($SESS_ORG_STRUCTURE==1){	
			$where = " and b.ORG_ID=$TMP_ORG_ID and b.POS_ID=d.POS_ID ";
		}else{	
			$where = " and d.ORG_ID=$TMP_ORG_ID and b.POS_ID=d.POS_ID ";
		}
	} elseif ($BONUS_TYPE == 2) {			// ลูกจ้างประจำ
		$table = ", PER_POS_EMP d ";
		if($SESS_ORG_STRUCTURE==1){	
			$where = " and b.ORG_ID=$TMP_ORG_ID and b.POEM_ID=d.POEM_ID ";
		}else{
			$where = " and d.ORG_ID=$TMP_ORG_ID and b.POEM_ID=d.POEM_ID ";
		}
	} elseif ($BONUS_TYPE == 3) {			// พนักงานราชการ
		$table = ", PER_POS_EMPSER d ";	
		if($SESS_ORG_STRUCTURE==1){	
			$where = " and b.ORG_ID=$TMP_ORG_ID and b.POEMS_ID=d.POEM_ID ";
		}else{
			$where = " and d.ORG_ID=$TMP_ORG_ID and b.POEMS_ID=d.POEM_ID ";
		}
	}
	
	switch($CTRL_TYPE){
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

	if($SESS_USERGROUP_LEVEL == 5 && $ORG_ID){
		$cmd = " select 	BONUSQ_QTY
				   from 		PER_BONUSQUOTADTL1
				   where 	BONUS_YEAR='$BONUS_YEAR' and DEPARTMENT_ID=$DEPARTMENT_ID and ORG_ID=$ORG_ID
				";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$BONUSQ_QTY = number_format($data[BONUSQ_QTY], 2, '.', ',');
		$command = "SELECT";
	} 

	$BONUS_TYPE = (trim($BONUS_TYPE))? $BONUS_TYPE : 1;
	
	if( $command == "ADD" && trim($BONUS_YEAR) && trim($DEPARTMENT_ID) ){

		$cmd2 = "select  PER_ID from PER_BONUSPROMOTE where PER_ID=$PER_ID";
		$db_dpis->send_cmd($cmd2);
		$data =$db_dpis->get_array();
		$data2=$data[PER_ID];
		//$db_dpis->show_error();
		if($data2) {  ?>    <script> <!--  
		alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจากข้อมูลซ้ำ !!!");
				-->   </script>		
		<? }
		
		$cmd = " insert into PER_BONUSPROMOTE 
				(BONUS_YEAR, BONUS_TYPE, PER_ID, BONUS_PERCENT, BONUS_QTY, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE) 
				VALUES 
				('$BONUS_YEAR', $BONUS_TYPE, $PER_ID, $BONUS_PERCENT, $BONUS_QTY, $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//echo "cmd=$cmd<br>";
		//$db_dpis->show_error(); echo "<br><hr>";
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลผู้ได้รับเงินรางวัลประจำปี [".trim($BONUS_YEAR)." : ".$BONUS_TYPE." : ".$PER_ID."]");
		$command = "SELECT";
	}

	if( $command == "UPDATE" && trim($BONUS_YEAR) && trim($DEPARTMENT_ID) ) {

		$cmd = " 	update PER_BONUSPROMOTE set  
					BONUS_PERCENT=$BONUS_PERCENT, BONUS_QTY=$BONUS_QTY,
					UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
				where BONUS_YEAR='$BONUS_YEAR' and BONUS_TYPE=$BONUS_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID and PER_ID=$PER_ID   ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลผู้ได้รับเงินรางวัลประจำปี [".trim($BONUS_YEAR)." : ".$BONUS_TYPE." : ".$PER_ID."]");
		$command = "SELECT";
	}
	
	if($command == "DELETE" && trim($BONUS_YEAR) && trim($DEPARTMENT_ID) ){
		$cmd = " delete from PER_BONUSPROMOTE where BONUS_YEAR='$BONUS_YEAR' and BONUS_TYPE=$BONUS_TYPE and DEPARTMENT_ID=$DEPARTMENT_ID and PER_ID=$PER_ID";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลผู้ได้รับเงินรางวัลประจำปี [".trim($BONUS_YEAR)." : ".$BONUS_TYPE." : ".$PER_ID."]");
		$command = "SELECT";
	}

	if ($command == "PROCESS" && trim($DEPARTMENT_ID)) {
		if ($ORG_ID) {
			$cmd = " delete from PER_BONUSPROMOTE where PER_ID in (
								select 	a.PER_ID 
								from 	PER_BONUSPROMOTE a, PER_PERSONAL b  $table 
								where 	BONUS_YEAR='$BONUS_YEAR' and BONUS_TYPE=$BONUS_TYPE and a.DEPARTMENT_ID=$DEPARTMENT_ID and 
										a.PER_ID=b.PER_ID 
										$where  ) ";
			$db_dpis->send_cmd($cmd);					
			$cmd = "	select		b.PER_ID, b.PER_CARDNO, b.PER_SALARY 
					from			PER_PERSONAL b $table 
					where		b.PER_TYPE=$BONUS_TYPE and b.PER_STATUS=1 $where ";						
										
		} elseif ($ORG_ID_ASS) {
			$cmd = " delete from PER_BONUSPROMOTE where PER_ID in (
								select 	a.PER_ID 
								from 	PER_BONUSPROMOTE a, PER_PERSONAL b 
								where 	BONUS_YEAR='$BONUS_YEAR' and BONUS_TYPE=$BONUS_TYPE and a.DEPARTMENT_ID=$DEPARTMENT_ID and 
										a.PER_ID=b.PER_ID and b.ORG_ID=$TMP_ORG_ID ) ";		
			$db_dpis->send_cmd($cmd);					
			$cmd = "	select		b.PER_ID, b.PER_CARDNO, b.PER_SALARY 
					from			PER_PERSONAL b 
					where		b.PER_TYPE=$BONUS_TYPE and b.PER_STATUS=1 and ORG_ID=$TMP_ORG_ID ";
		}										
		$db_dpis->send_cmd($cmd);
		while ($data = $db_dpis->get_array()) {
			$PER_ID = $data[PER_ID];
			$PER_CARDNO = $data[PER_CARDNO];
			$PER_SALARY = $data[PER_SALARY];
			$BONUS_QTY = ($PER_SALARY * $BONUS_PERCENT_ALL) / 100;
			$cmd = " insert into PER_BONUSPROMOTE 
							(BONUS_YEAR, BONUS_TYPE, PER_ID, BONUS_PERCENT, BONUS_QTY, PER_CARDNO, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE)
							values 
							('$BONUS_YEAR', $BONUS_TYPE, $PER_ID, $BONUS_PERCENT_ALL, $BONUS_QTY, '$PER_CARDNO', $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis1->send_cmd($cmd);
		}	// end while 
		
		$command = "SELECT";
	} 	// if ($command == "PROCESS")

	if($UPD || $VIEW){
		$cmd = " 	select 		a.PER_ID, BONUS_PERCENT, BONUS_QTY, PN_CODE, PER_NAME, PER_SURNAME 
				from 		PER_BONUSPROMOTE a, PER_PERSONAL b 
				where 		BONUS_YEAR='$BONUS_YEAR' and BONUS_TYPE=$BONUS_TYPE 
							and a.DEPARTMENT_ID=$DEPARTMENT_ID and a.PER_ID=$PER_ID and 
							a.PER_ID=b.PER_ID "; 
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();

		$PER_ID = $data[PER_ID];
		$PN_CODE = $data[PN_CODE];
		$BONUS_PERCENT = $data[BONUS_PERCENT];
		$BONUS_QTY = $data[BONUS_QTY];

		$PER_NAME = "";				
		$cmd = "select PN_NAME from PER_PRENAME where PN_CODE='$PN_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
		$PER_NAME = $data2[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];		
	} // end if

//	echo "command=$command || UPD=$UPD || DEL=$DEL || VIEW=$VIEW<br>";
	if( !$UPD && !$DEL && !$VIEW ){
		$PER_NAME = "";
		$PER_ID = "";
		$PER_SALARY = "";
		$BONUS_PERCENT = "";		
		$BONUS_QTY = "";		
	} // end if		
	
	if ( $command != "PROCESS" && $command != "SELECT" ) {
		$BONUS_YEAR = "";
		$BONUS_TYPE = 1;

		$ORG_ID = "";
		$ORG_NAME = "";
		$ORG_ID_ASS = "";
		$ORG_NAME_ASS = "";
		$SUM_BONUSQ_QTY = "";
		$REST_BONUSQ_QTY = "";	
		$BONUSQ_QTY = "";	
	}	
?>