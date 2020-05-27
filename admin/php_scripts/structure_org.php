<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        
        /*Release 5.2.1.2*/
        $cmdModify = "SELECT COLUMN_NAME, DATA_TYPE ,DATA_LENGTH
                     FROM USER_TAB_COLUMNS
                     WHERE TABLE_NAME = 'PER_ORG'
                     AND COLUMN_NAME = 'ORG_NAME'";
         $db_dpis2->send_cmd($cmdModify);
         $dataModify = $db_dpis2->get_array_array();
         $data_length = $dataModify[2];
         if($data_length<=100){
             $cmdModify = "ALTER TABLE PER_ORG MODIFY ORG_NAME VARCHAR2(255)";
             $db_dpis2->send_cmd($cmdModify);
             $db_dpis2->send_cmd("COMMIT");
         }
        /**/
         
	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

//echo "$LIST_OPENED_ORG:$ORG_ID:$search_ol_code<br>";
	if (!$LIST_OPENED_ORG) {
		$LIST_OPENED_ORG = ",$SESS_MINISTRY_ID.0.1,$SESS_DEPARTMENT_ID.0.$SESS_MINISTRY_ID,";
		$LIST_OPENED_ORG = "$LIST_OPENED_ORG".($SESS_ORG_ID ? "$SESS_ORG_ID.0.$SESS_DEPARTMENT_ID," : "");
		$LIST_OPENED_ORG = "$LIST_OPENED_ORG".($SESS_PROVINCE_CODE ? "$SESS_PROVINCE_CODE.0.$SESS_ORG_ID," : "");
	}
	if (!$ORG_ID)  {
		if ($SESS_ORG_ID) { $ORG_ID=$SESS_ORG_ID; $search_ol_code="03"; }
		elseif ($SESS_DEPARTMENT_ID) { $ORG_ID=$SESS_DEPARTMENT_ID; $search_ol_code="02"; }
		elseif ($SESS_MINISTRY_ID) { $ORG_ID=$SESS_MINISTRY_ID; $search_ol_code="01"; }
		else { $ORG_ID=0; $search_ol_code="01"; }
//		echo "not ORG_ID:$ORG_ID:$search_ol_code<br>";
	}
	if (!$SRCH_MINISTRY_ID) {
		$SRCH_MINISTRY_ID = $SESS_MINISTRY_ID;
		$SRCH_MINISTRY_NAME = $SRCH_DEPARTMENT_NAME = "";
		if($SRCH_MINISTRY_ID){
			$cmd = " select ORG_NAME from $ORGTAB where ORG_ID=$SRCH_MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$SRCH_MINISTRY_NAME = $data[ORG_NAME];
		}
		$SRCH_DEPARTMENT_ID = $SESS_DEPARTMENT_ID;
		if($SRCH_DEPARTMENT_ID){
			$cmd = " select ORG_NAME from $ORGTAB where ORG_ID=$SRCH_DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$SRCH_DEPARTMENT_NAME = $data[ORG_NAME];
		}
	}
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	$cmd = " select CTRL_TYPE, PV_CODE, ORG_ID from PER_CONTROL ";
	$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
	$data = $db_dpis->get_array();
	$CTRL_TYPE = $data[CTRL_TYPE];
	if(!$CTRL_TYPE) $CTRL_TYPE = 4;

  	if($CTRL_TYPE==1 || $CTRL_TYPE==2) { 
		unset($AUTH_CHILD_ORG);
/*
		if($CTRL_TYPE==2){
			$AUTH_CHILD_ORG[] = $SESS_DEPARTMENT_ID;
			list_child_org($SESS_DEPARTMENT_ID);
		}elseif($CTRL_TYPE==3 || $SESS_USERGROUP_LEVEL==3){
			$AUTH_CHILD_ORG[] = $SESS_MINISTRY_ID;
			list_child_org($SESS_MINISTRY_ID);
		}elseif($CTRL_TYPE==4 || $SESS_USERGROUP_LEVEL==4){
			$AUTH_CHILD_ORG[] = $SESS_DEPARTMENT_ID;
			list_child_org($SESS_DEPARTMENT_ID);
		}elseif($CTRL_TYPE==5 || $SESS_USERGROUP_LEVEL==5){
			$AUTH_CHILD_ORG[] = $SESS_ORG_ID;
			list_child_org($SESS_ORG_ID);
		} // end if
*/	
		switch($SESS_USERGROUP_LEVEL){
			case 1 :
				$START_ORG_ID = 1;
				break;
			case 2 :
				$START_ORG_ID = 1;
				break;
			case 3 :
				$START_ORG_ID = $SESS_MINISTRY_ID;
				break;
			case 4 :
				$START_ORG_ID = $SESS_DEPARTMENT_ID;
				break;
			case 5 :
				$START_ORG_ID = $SESS_ORG_ID;
				break;
		} // end switch case
	} else {
		$cmd = " select ORG_ID from $ORGTAB where ORG_ID=$ORG_ID ";
		$HAVE_ID_1 = $db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$START_ORG_ID = $data[ORG_ID];
	}

	if ($BKK_FLAG==1 && $START_ORG_ID==1) $START_ORG_ID = 0;
	if(!$START_ORG_ID){
		$cmd = " select ORG_ID from $ORGTAB where ORG_ID=ORG_ID_REF ";
		//echo "$cmd <br>";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$START_ORG_ID = $data[ORG_ID];
	} // end if
	if(!$ORG_ID && !$ORG_ID_REF){
		$ORG_ID = $START_ORG_ID;
		$ORG_ID_REF = $START_ORG_ID;
	} // end if
// echo "$ORG_ID - $DEPARTMENT_ID -  $MINISTRY_ID [+] $SESS_USERGROUP_LEVEL :: $SESS_ORG_ID - $SESS_DEPARTMENT_ID -  $SESS_MINISTRY_ID";		//ไม่มีค่ามาเลย
//	echo "php=( $CTRL_TYPE  / $ORG_ID * $ORG_ID_REF) // $SRCH_DEPARTMENT_ID ~=>".$START_ORG_ID."<br>";

	function list_child_org ($org_parent) {
		global $dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd;
		global $AUTH_CHILD_ORG, $ORGTAB;
		
		$db_dpis = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

		$cmd = " select ORG_ID from $ORGTAB where ORG_ID_REF=$org_parent ";
		$count_data = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		if($count_data){
			while($data = $db_dpis->get_array()){
				$AUTH_CHILD_ORG[] = $data[ORG_ID];
				list_child_org($data[ORG_ID]);
			} // end while
		} // end if
	} // function
?>