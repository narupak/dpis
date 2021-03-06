<?	
	$UPDATE_DATE = date("Y-m-d H:i:s");

	$table="PER_MAIN_JOB";

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);

		if($DPISDB=="odbc"){    
			$cmd = " update PER_MAIN_JOB set LC_ACTIVE = 0 WHERE (PL_CODE&'||')&MJT_CODE in (".stripslashes($current_list).") AND ORG_ID=".$ORG_ID."";  }
		elseif($DPISDB=="oci8"){     
			$cmd = " update PER_MAIN_JOB set LC_ACTIVE = 0 WHERE concat(concat(PL_CODE, '||'), MJT_CODE) in (".stripslashes($current_list).") AND ORG_ID=".$ORG_ID."";  }
		elseif($DPISDB=="mysql"){    
			$cmd = " update PER_MAIN_JOB set LC_ACTIVE = 0 WHERE (PL_CODE&'||')&MJT_CODE in (".stripslashes($current_list).") AND ORG_ID=".$ORG_ID."";  }
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		if($DPISDB=="odbc"){    
			$cmd = " update PER_MAIN_JOB set LC_ACTIVE = 1 WHERE  (PL_CODE&'||')&MJT_CODE in (".stripslashes($setflagshow).") AND ORG_ID=".$ORG_ID."";  }
		elseif($DPISDB=="oci8"){    
			$cmd = " update PER_MAIN_JOB set LC_ACTIVE = 1 WHERE concat(concat(PL_CODE, '||'), MJT_CODE) in (".stripslashes($setflagshow).") AND ORG_ID=".$ORG_ID."";  }
		elseif($DPISDB=="mysql"){    
			$cmd = " update PER_MAIN_JOB set LC_ACTIVE = 1 WHERE  (PL_CODE&'||')&MJT_CODE in (".stripslashes($setflagshow).") AND ORG_ID=".$ORG_ID."";  }
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��駤�ҡ����ҹ������");
	}
	
	if($command == "ADD" && trim($PL_CODE) && trim($MJT_CODE)){
		$cmd = " 	SELECT PL_CODE, ORG_ID, MJT_CODE FROM PER_MAIN_JOB 
							WHERE PL_CODE='$PL_CODE' AND ORG_ID='$ORG_ID' AND MJT_CODE='$MJT_CODE' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " SELECT ORG_ID_REF FROM	PER_ORG WHERE ORG_ID = $ORG_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			$data = $db_dpis->get_array();
			$ORG_ID_REF = $data[ORG_ID_REF];

			$cmd = " insert into PER_MAIN_JOB (PL_CODE, ORG_ID, MJT_CODE, LC_ACTIVE, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID) 
							  values ('$PL_CODE', $ORG_ID, '$MJT_CODE', $LC_ACTIVE, $SESS_USERID, '$UPDATE_DATE', $ORG_ID_REF) ";
			$db_dpis->send_cmd($cmd);
//			echo "---> $cmd";
//			$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ���������� [$PL_CODE : $MJT_CODE]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "���ʢ����ū�� [$data[PL_CODE] $data[ORG_ID] $data[MJT_CODE]]";
		} // endif
	}//if($command == "ADD" && trim($PL_CODE)){

	if($command == "UPDATE" && trim($PL_CODE) && trim($MJT_CODE)){
		$cmd = " 	SELECT PL_CODE, ORG_ID, MJT_CODE FROM PER_MAIN_JOB 
							WHERE PL_CODE='$PL_CODE' AND ORG_ID=$ORG_ID AND MJT_CODE='$MJT_CODE' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		//echo $cmd;
		if($count_duplicate <= 0){
			$cmd = " 	update PER_MAIN_JOB set 
									PL_CODE='$PL_CODE', 
									ORG_ID='$ORG_ID', 
									MJT_CODE='$MJT_CODE', 
									LC_ACTIVE=$LC_ACTIVE, 	
									UPDATE_USER=$SESS_USERID, 
									UPDATE_DATE='$UPDATE_DATE' 
								WHERE PL_CODE='$upd_pl_code' AND ORG_ID='$upd_org_id' AND MJT_CODE='$upd_mjt_code' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��䢢����� [�ҡ->$PL_CODE : $ORG_ID : $MJT_CODE ��->$upd_pl_code : $upd_org_id : $upd_mjt_code]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "���ʢ����ū�� [$data[PL_CODE] $data[ORG_ID] $data[MJT_CODE]]";
			$UPD = 1;
		} // endif
	}//if($command == "UPDATE" && trim($PL_CODE)){
	
	if($command == "DELETE" && trim($PL_CODE) && trim($MJT_CODE)){
		$cmd = " delete FROM PER_MAIN_JOB WHERE PL_CODE='$PL_CODE' AND ORG_ID=$ORG_ID AND MJT_CODE='$MJT_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź������ [$PL_CODE : $ORG_ID : $MJT_CODE]");
	}//if($command == "DELETE" && trim($PL_CODE)){
	
	if($UPD){
		$cmd = " SELECT 	a.PL_CODE, a.ORG_ID, a.MJT_CODE, LC_ACTIVE, b.PL_NAME, MJT_NAME, ORG_NAME
						  FROM 		PER_MAIN_JOB a, PER_LINE b, PER_MAIN_JOB_TYPE c, PER_ORG d 
						  WHERE 	a.PL_CODE = '$PL_CODE' AND a.ORG_ID = $ORG_ID  AND a.MJT_CODE = '$MJT_CODE' AND 
					  							a.PL_CODE=trim(b.PL_CODE) AND a.ORG_ID=d.ORG_ID AND a.MJT_CODE=trim(c.MJT_CODE) ";
		$db_dpis->send_cmd($cmd);
//		echo "-> $cmd";
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PL_CODE = $data[PL_CODE];
		$PL_NAME = $data[PL_NAME];
		$ORG_ID = $data[ORG_ID];
		$MJT_CODE = $data[MJT_CODE];
		$LC_ACTIVE = $data[LC_ACTIVE];
		$ORG_NAME = $data[ORG_NAME];
		$MJT_NAME = $data[MJT_NAME];
		$upd_org_id =  $data[ORG_ID];
		$upd_pl_code = $data[PL_CODE];
		$upd_mjt_code = $data[MJT_CODE];		
		
		if($PL_CODE && !$PL_NAME){
			$cmd = " select * from PER_LINE where PL_CODE='$PL_CODE' ";
			$db_dpis->send_cmd($cmd);
	//			$db_dpis->show_error();
			$data = $db_dpis->get_array();			
			$PL_NAME = $data[PL_NAME];
		}
	} // end if
	
	if( (!$UPD && !$err_text) ){
		if ($PL_CODE) {
			$cmd = " select * from PER_LINE where PL_CODE='$PL_CODE' ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			$data = $db_dpis->get_array();			
			$PL_NAME = $data[PL_NAME];
			//echo "$cmd : $PL_NAME<br>";

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
			$db_dpis->send_cmd($cmd);
			$data_dpis = $db_dpis->get_array();
			$ORG_NAME = $data_dpis[ORG_NAME];
		} else {
			$PL_CODE = "";
			$PL_NAME = "";
			$ORG_ID = "";
			$ORG_NAME = "";
		}
		$MJT_CODE = "";
		$LC_ACTIVE = 1;
		
		$MJT_NAME = "";
	} // end if
?>