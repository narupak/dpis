<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");
	ini_set("max_execution_time", 0);
        $debug=0;/*0=close,1=open*/
	switch($CTRL_TYPE){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$PV_CODE = $PROVINCE_CODE;
			$PV_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			break;
		case 5 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			$search_org_id1 = $ORG_ID;
			$search_org_name1 = $ORG_NAME;
			break;
		case 6 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			$search_org_id1 = $ORG_ID;
			$search_org_name1 = $ORG_NAME;
			$search_org_id_1 = $ORG_ID_1;
			$search_org_name_1 = $ORG_NAME_1;
			break;
	} // end switch case

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$PER_ID_DEPARTMENT_ID = $DEPARTMENT_ID;
	$PER_TYPE = 1;

	if($command == "ADD" && trim($POS_ID)){
		$cmd = " select a.POS_ID, b.POS_NO  from PER_POSITION_COMPETENCE a, PER_POSITION b 
						where a.POS_ID=b.POS_ID and a.DEPARTMENT_ID=$DEPARTMENT_ID and a.POS_ID=$POS_ID and a.PER_TYPE = $PER_TYPE ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		//echo $cmd.'<br><br>';
                //echo 'count_duplicate:'.$count_duplicate.'<br>';
                if($debug==1){echo __LINE__.'<pre>'.$cmd.'=>count_duplicate:'.$count_duplicate.'<br>';}
                if($count_duplicate <= 0){
			$cmd = " select LEVEL_NO from PER_PERSONAL where POS_ID=$POS_ID and PER_TYPE=1 and PER_STATUS=1 ";
                        //echo '1:'.$cmd.'<br><br>';
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$LEVEL_NO = trim($data[LEVEL_NO]);

			$cmd = " select PL_CODE, LEVEL_NO, ORG_ID  from PER_POSITION where POS_ID=$POS_ID ";
                        //echo '2:'.$cmd.'<br><br>';
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PL_CODE = trim($data[PL_CODE]);
			if (!$LEVEL_NO ) $LEVEL_NO = trim($data[LEVEL_NO]);
			$TMP_ORG_ID = $data[ORG_ID];
			$cmd = " select	distinct CP_CODE from	PER_LINE_COMPETENCE 
								where PL_CODE='$PL_CODE' and ORG_ID = $TMP_ORG_ID and LC_ACTIVE=1 order by CP_CODE ";
                        //echo '3:'.$cmd.'<br><br>';
			$count_data = $db_dpis->send_cmd($cmd);
//				$db_dpis->show_error();
			if (!$count_data) echo 'กรุณาตรวจสอบข้อมูลที่ K15 สมรรถนะของแต่ละสายงาน<br>';//echo "$cmd<br>";
			while($data = $db_dpis->get_array()){
				$CP_CODE = $data[CP_CODE];
			
				$cmd = " select TARGET_LEVEL from PER_STANDARD_COMPETENCE 
								where LEVEL_NO = '$LEVEL_NO' and CP_CODE = '$CP_CODE' and DEPARTMENT_ID = $DEPARTMENT_ID "; 
				//echo '4:'.$cmd.'<br><br>';
                                $count_data = $db_dpis2->send_cmd($cmd);
                                if($debug==1){echo __LINE__.'<pre>'.$cmd.'=>count_data:'.$count_data.'<br>';}
				if (!$count_data) echo "$cmd<br>";
				else {
					$data2 = $db_dpis2->get_array();
					$TARGET_LEVEL = $data2[TARGET_LEVEL] + 0;

					$cmd = " insert into PER_POSITION_COMPETENCE (POS_ID, CP_CODE, PC_TARGET_LEVEL, DEPARTMENT_ID, 
									  UPDATE_USER, UPDATE_DATE, PER_TYPE) 
									  values ($POS_ID, '$CP_CODE', $TARGET_LEVEL, $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE', $PER_TYPE) ";
					$db_dpis2->send_cmd($cmd);
                                        if($debug==1){echo __LINE__.'<pre>'.$cmd.'<br>';}
	//				$db_dpis2->show_error();
		
					insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [$POS_ID, $CP_CODE], $PER_TYPE");
				}
			} // end while
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "เลขที่ตำแหน่งซ้ำ [".$data[POS_NO]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($POS_ID)){
		$cmd = " select a.POS_ID, b.POS_NO  from PER_POSITION_COMPETENCE a, PER_POSITION b 
						where a.POS_ID=b.POS_ID and a.DEPARTMENT_ID=$DEPARTMENT_ID and a.POS_ID=$POS_ID and a.PER_TYPE = $PER_TYPE ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " 	update PER_POSITION_COMPETENCE set 
									POS_ID=$POS_ID, 
									PER_TYPE=$PER_TYPE, 
									UPDATE_USER=$SESS_USERID, 
									UPDATE_DATE='$UPDATE_DATE' 
								where POS_ID=$OLD_POS_ID and PER_TYPE = $PER_TYPE ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
	
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [$OLD_POS_ID -> $POS_ID, $PER_TYPE]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "เลขที่ตำแหน่งซ้ำ [".$data[POS_NO]."]";
			$UPD = 1;
		} // end if
	}
	
	if($command == "DELETE" && trim($POS_ID)){
		$cmd = " delete from PER_POSITION_COMPETENCE where POS_ID=$POS_ID and PER_TYPE = $PER_TYPE ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [$POS_ID, $PER_TYPE]");
	}
	
	if($command == "GENDATA"){
		$cmd = " SELECT COUNT(PL_CODE) AS COUNT_DATA FROM PER_LINE_COMPETENCE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$COUNT_DATA = $data[COUNT_DATA] + 0;
		if ($COUNT_DATA==0) {
			$cmd = " SELECT DISTINCT PL_CODE, ORG_ID, DEPARTMENT_ID FROM PER_POSITION WHERE DEPARTMENT_ID > 0 AND POS_STATUS = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$PL_CODE = trim($data[PL_CODE]);
				$TMP_ORG_ID = $data[ORG_ID];
				$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];

				$cmd = " SELECT PL_CP_CODE FROM PER_LINE WHERE PL_CODE = '$PL_CODE' ";
				$db_dpis1->send_cmd($cmd);
				//$db_dpis1->show_error();
				$data1 = $db_dpis1->get_array();
				$PL_CP_CODE = trim($data1[PL_CP_CODE]);
				if ($PL_CP_CODE) {
					$arr_temp = explode("|", $PL_CP_CODE);
					for ($count=0; $count<count($arr_temp); $count++) {
						$CP_CODE = $arr_temp[$count];
						$cmd = " SELECT CP_CODE FROM PER_TYPE_COMPETENCE 
										WHERE PER_TYPE = 1 AND DEPARTMENT_ID = $TMP_DEPARTMENT_ID AND CP_CODE = '$CP_CODE' ";
						$db_dpis1->send_cmd($cmd);
						//$db_dpis1->show_error();
						$data1 = $db_dpis1->get_array();
						$CP_CODE = trim($data1[CP_CODE]);
						if ($CP_CODE) {
							$cmd = " INSERT INTO PER_LINE_COMPETENCE (PL_CODE, ORG_ID, CP_CODE, LC_ACTIVE, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID) 
											VALUES('$PL_CODE', $TMP_ORG_ID, '$CP_CODE', 1, $SESS_USERID, '$UPDATE_DATE', $TMP_DEPARTMENT_ID) ";
							$db_dpis2->send_cmd($cmd);
							//$db_dpis2->show_error();
						}
					}
				}

				$cmd = " SELECT a.CP_CODE FROM PER_TYPE_COMPETENCE a, PER_COMPETENCE b 
								WHERE PER_TYPE = 1 AND a.DEPARTMENT_ID = $TMP_DEPARTMENT_ID AND a.CP_CODE = b.CP_CODE AND CP_MODEL  = 1 ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
				while($data2 = $db_dpis2->get_array()){
					$CP_CODE = trim($data2[CP_CODE]);
					$cmd = " INSERT INTO PER_LINE_COMPETENCE (PL_CODE, ORG_ID, CP_CODE, LC_ACTIVE, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID) 
									VALUES('$PL_CODE', $TMP_ORG_ID, '$CP_CODE', 1, $SESS_USERID, '$UPDATE_DATE', $TMP_DEPARTMENT_ID) ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while						
			} // end while						

			$cmd = " SELECT DISTINCT PL_CODE, ORG_ID, DEPARTMENT_ID FROM PER_POSITION 
							  WHERE DEPARTMENT_ID > 0 AND LEVEL_NO IN ('D1', 'D2', 'M1', 'M2') AND POS_STATUS = 1 ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			while($data = $db_dpis->get_array()){
				$PL_CODE = trim($data[PL_CODE]);
				$TMP_ORG_ID = $data[ORG_ID];
				$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];

				$cmd = " SELECT a.CP_CODE FROM PER_TYPE_COMPETENCE a, PER_COMPETENCE b 
								WHERE PER_TYPE = 1 AND a.DEPARTMENT_ID = $TMP_DEPARTMENT_ID AND a.CP_CODE = b.CP_CODE AND CP_MODEL  = 2 ";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
				while($data2 = $db_dpis2->get_array()){
					$CP_CODE = trim($data2[CP_CODE]);
					$cmd = " INSERT INTO PER_LINE_COMPETENCE (PL_CODE, ORG_ID, CP_CODE, LC_ACTIVE, UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID) 
									VALUES('$PL_CODE', $TMP_ORG_ID, '$CP_CODE', 1, $SESS_USERID, '$UPDATE_DATE', $TMP_DEPARTMENT_ID) ";
					$db_dpis1->send_cmd($cmd);
					//$db_dpis1->show_error();
				} // end while						
			} // end while						
		} // end if 

		if ($DEPARTMENT_ID) 
			$cmd = " delete from PER_POSITION_COMPETENCE 
							where PER_TYPE = $PER_TYPE and (DEPARTMENT_ID = $DEPARTMENT_ID or DEPARTMENT_ID = 0) ";
		else
			$cmd = " delete from PER_POSITION_COMPETENCE where PER_TYPE = $PER_TYPE ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

//		if($SCOMPETENCY[$PER_ID_DEPARTMENT_ID]=="Y") {
		if ($DEPARTMENT_ID) 
				$cmd = " select	POS_ID, PL_CODE, LEVEL_NO, DEPARTMENT_ID, ORG_ID from PER_POSITION 
								where DEPARTMENT_ID = $DEPARTMENT_ID and POS_STATUS=1 order by POS_NO_NAME, POS_NO ";		
			else
				$cmd = " select	POS_ID, PL_CODE, LEVEL_NO, DEPARTMENT_ID, ORG_ID from PER_POSITION 
								where DEPARTMENT_ID > 0 AND POS_STATUS=1 order by POS_NO_NAME, POS_NO ";		
		$db_dpis1->send_cmd($cmd);
//			$db_dpis1->show_error();
		while($data1 = $db_dpis1->get_array()){
			$POS_ID = $data1[POS_ID];
			$PL_CODE = trim($data1[PL_CODE]);

			$cmd = " select 	LEVEL_NO from PER_PERSONAL where 	POS_ID=$POS_ID and PER_STATUS=1 and PER_TYPE = 1 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$LEVEL_NO = trim($data2[LEVEL_NO]);
			if (!$LEVEL_NO) $LEVEL_NO = trim($data1[LEVEL_NO]);

			$TMP_DEPARTMENT_ID = $data1[DEPARTMENT_ID];
			$TMP_ORG_ID = $data1[ORG_ID];

			if ($BKK_FLAG==1) {
				$code = array(	"101", "102", "103", "104", "105" );
				for ( $i=0; $i<count($code); $i++ ) { 
					$cmd = " select CP_MODEL from PER_COMPETENCE where CP_CODE = '$code[$i]' and DEPARTMENT_ID = $TMP_DEPARTMENT_ID "; 
					$count_data = $db_dpis2->send_cmd($cmd);
	//					$db_dpis2->show_error();
					//if (!$count_data) echo "$cmd<br>";
					$data2 = $db_dpis2->get_array();
					$CP_MODEL = $data2[CP_MODEL];

					if ($LEVEL_NO > "11" && ($CP_MODEL==1 || ($CP_MODEL==2 && ($LEVEL_NO=="D1" || $LEVEL_NO=="D2" || $LEVEL_NO=="M1" || $LEVEL_NO=="M2")) || $CP_MODEL==3)) {  
						$cmd = " select TARGET_LEVEL from PER_STANDARD_COMPETENCE 
										where LEVEL_NO = '$LEVEL_NO' and CP_CODE = '$code[$i]' and DEPARTMENT_ID = $TMP_DEPARTMENT_ID "; 
						$count_data = $db_dpis2->send_cmd($cmd);
	//					$db_dpis2->show_error();
						if (!$count_data && $LEVEL_NO) 
                                                    echo '<font color=red>1.ไม่สามารถเพิ่ม POS_ID:'.$POS_ID.' ได้ กรุณาตรวจสอบระดับ '.$LEVEL_NO.'ที่เมนู K12 มาตรฐานสมรรถนะของระดับตำแหน่ง</font><br>';//echo "$cmd<br>";//echo "$cmd<br>";
						else {
							$data2 = $db_dpis2->get_array();
							$TARGET_LEVEL = $data2[TARGET_LEVEL] + 0;
							
							$cmd = " insert into PER_POSITION_COMPETENCE (POS_ID, CP_CODE, PC_TARGET_LEVEL, DEPARTMENT_ID, 
											  UPDATE_USER, UPDATE_DATE, PER_TYPE) 
											  values ($POS_ID, '$code[$i]', $TARGET_LEVEL, $TMP_DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE', $PER_TYPE) "; 
							$db_dpis2->send_cmd($cmd);
		//					$db_dpis2->show_error();
						}
					} // end if
				}
			} else {
				$cmd = " select	distinct CP_CODE from	PER_LINE_COMPETENCE 
									where PL_CODE='$PL_CODE' and ORG_ID = $TMP_ORG_ID and LC_ACTIVE=1 order by CP_CODE ";
				$count_data = $db_dpis->send_cmd($cmd);
	//				$db_dpis->show_error();
				if (!$count_data) echo '<font color=red>1.ไม่สามารถเพิ่ม POS_ID:'.$POS_ID.' ได้ กรุณาตรวจ PL_CODE: '.$PL_CODE.'ที่เมนู K15 สมรรถนะของแต่ละสายงาน ว่า ACTIVE หรือไม่	</font><br>';//echo "$cmd<br>";//echo "$cmd<br>";
				while($data = $db_dpis->get_array()){
					$CP_CODE = $data[CP_CODE];
				
					$cmd = " select CP_MODEL from PER_COMPETENCE where CP_CODE = '$CP_CODE' and DEPARTMENT_ID = $TMP_DEPARTMENT_ID "; 
					$count_data = $db_dpis2->send_cmd($cmd);
	//					$db_dpis2->show_error();
					if (!$count_data) echo '<font color=red>1.ไม่สามารถเพิ่ม POS_ID:'.$POS_ID.' ได้ กรุณาตรวจ CP_CODE: '.$CP_CODE.'ที่เมนู M1101 สมรรถนะ ว่า ACTIVE หรือไม่	</font><br>';//echo "$cmd<br>";//echo "$cmd<br>";
					$data2 = $db_dpis2->get_array();
					$CP_MODEL = $data2[CP_MODEL];

					if ($LEVEL_NO > "11" && ($CP_MODEL==1 || ($CP_MODEL==2 && ($LEVEL_NO=="D1" || $LEVEL_NO=="D2" || $LEVEL_NO=="M1" || $LEVEL_NO=="M2")) || $CP_MODEL==3)) {  
						$cmd = " select TARGET_LEVEL from PER_STANDARD_COMPETENCE 
										where LEVEL_NO = '$LEVEL_NO' and CP_CODE = '$CP_CODE' and DEPARTMENT_ID = $TMP_DEPARTMENT_ID "; 
						$count_data = $db_dpis2->send_cmd($cmd);
	//					$db_dpis2->show_error();
						if (!$count_data && $LEVEL_NO) 
                                                    echo '<font color=red>2.ไม่สามารถเพิ่ม POS_ID:'.$POS_ID.' ได้ กรุณาตรวจสอบระดับ '.$LEVEL_NO.'ที่เมนู K12 มาตรฐานสมรรถนะของระดับตำแหน่ง</font><br>';//echo "$cmd<br>";
						else {
							$data2 = $db_dpis2->get_array();
							$TARGET_LEVEL = $data2[TARGET_LEVEL] + 0;
							
							$cmd = " insert into PER_POSITION_COMPETENCE (POS_ID, CP_CODE, PC_TARGET_LEVEL, DEPARTMENT_ID, 
											  UPDATE_USER, UPDATE_DATE, PER_TYPE) 
											  values ($POS_ID, '$CP_CODE', $TARGET_LEVEL, $TMP_DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE', $PER_TYPE) "; 
							$db_dpis2->send_cmd($cmd);
		//					$db_dpis2->show_error();
						}
					} // end if
				} // end while
	
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [$POS_ID, $CP_CODE], $PER_TYPE");
			} // end if
		} // end while 
	}

	if($UPD && !$err_text){
		$cmd = "  select 	distinct a.POS_ID, b.POS_NO, c.PL_NAME, a.DEPARTMENT_ID
				    from 		PER_POSITION_COMPETENCE a, PER_POSITION b, PER_LINE c
				    where 	a.POS_ID=b.POS_ID and b.PL_CODE=c.PL_CODE and a.POS_ID=$POS_ID and PER_TYPE = $PER_TYPE ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$POS_ID = $data[POS_ID];
		$POS_NO = $data[POS_NO];
		$PL_NAME = $data[PL_NAME];
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
	
	if( (!$UPD && !$DEL && !$err_text) ){
		$POS_ID = "";
		$OLD_POS_ID = "";
		$POS_NO = "";
		$PL_NAME = "";
	} // end if
?>