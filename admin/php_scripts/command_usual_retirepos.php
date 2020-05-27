<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");	

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
			break;
	} // end switch case

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	
	$UPDATE_DATE = date("Y-m-d H:i:s");	
	$REQ_TYPE = 1;

	if(trim($REQ_DATE)){
		$arr_temp = explode("/", $REQ_DATE);
		$REQ_DATE = ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0];
	} // end if
	
	if(trim($REQ_W_DATE)){
		$arr_temp = explode("/", $REQ_W_DATE);
		$REQ_W_DATE = "'". ($arr_temp[2] - 543) ."-". $arr_temp[1] ."-". $arr_temp[0] ."'";
	} // end if
	if(!$WITHDRAW) $REQ_W_DATE = "NULL";
	
	if($command == "ADD" && trim($REQ_YEAR) && trim($REQ_NO)){
		$cmd = " select 	REQ_YEAR, REQ_NO, REQ_TITLE
				   from 		PER_REQ2 
				   where 	trim(REQ_YEAR)='". trim($REQ_YEAR) ."' and trim(REQ_NO)='". trim($REQ_NO) ."' and DEPARTMENT_ID=$DEPARTMENT_ID ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " select max(REQ_ID) as max_id from PER_REQ2 ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$REQ_ID = $data[max_id] + 1;
			
			$cmd = " insert into PER_REQ2 
						(REQ_ID, REQ_YEAR, REQ_NO, REQ_TITLE, REQ_DATE, REQ_TYPE, REQ_W_DATE, REQ_CONFIRM, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE) 
					  values 
						($REQ_ID, '".trim($REQ_YEAR)."', '".$REQ_NO."', '".$REQ_TITLE."', '".$REQ_DATE."', $REQ_TYPE, $REQ_W_DATE, 0, $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".$REQ_ID." : ".trim($REQ_YEAR)." : ".$REQ_NO." : ".$REQ_TITLE." : ".$REQ_DATE."]");

			if($DPISDB=="odbc"){
				$cmd = " select 	a.POS_ID, a.POS_NO
						   from		PER_POSITION a
									left join PER_PERSONAL b on a.POS_ID=b.POS_ID
						   where	a.POS_STATUS = 1 and a.DEPARTMENT_ID=$DEPARTMENT_ID
									and (b.PER_STATUS=1 or b.PER_STATUS=2)
									and LEFT(trim(b.PER_RETIREDATE), 10) >= '".(($REQ_YEAR - 543) - 1)."-10-01' 
									and LEFT(trim(b.PER_RETIREDATE), 10) < '".($REQ_YEAR - 543)."-10-01'
						   order by 	iif(isnull(a.POS_NO),0,CLng(a.POS_NO)) ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select 	a.POS_ID, a.POS_NO
						   from		PER_POSITION a, PER_PERSONAL b
						   where	a.POS_STATUS = 1 and a.DEPARTMENT_ID=$DEPARTMENT_ID and a.POS_ID=b.POS_ID(+)
									and (b.PER_STATUS=1 or b.PER_STATUS=2)
									and SUBSTR(trim(b.PER_RETIREDATE), 1, 10) >= '".(($REQ_YEAR - 543) - 1)."-10-01' 
									and SUBSTR(trim(b.PER_RETIREDATE), 1, 10) < '".($REQ_YEAR - 543)."-10-01'
						   order by 	to_number(replace(a.POS_NO,'-','')) ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select 	a.POS_ID, a.POS_NO
						   from		PER_POSITION a
									left join PER_PERSONAL b on a.POS_ID=b.POS_ID
						   where	a.POS_STATUS = 1 and a.DEPARTMENT_ID=$DEPARTMENT_ID
									and (b.PER_STATUS=1 or b.PER_STATUS=2)
									and LEFT(trim(b.PER_RETIREDATE), 10) >= '".(($REQ_YEAR - 543) - 1)."-10-01' 
									and LEFT(trim(b.PER_RETIREDATE), 10) < '".($REQ_YEAR - 543)."-10-01'
						   order by 	a.POS_NO ";
			} // end if			
			$count_retire_position = $db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()){
				$POS_ID = $data[POS_ID];
				$arr_insert_posno[] = $data[POS_NO];
				
				$cmd = " select max(REQ_SEQ) as MAX_SEQ from PER_REQ2_DTL where REQ_ID=$REQ_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$data2 = array_change_key_case($data2, CASE_LOWER);
				$REQ_SEQ = $data2[max_seq] + 1;

				$cmd = " insert into PER_REQ2_DTL 
								 (REQ_ID, REQ_SEQ, POS_ID_RETIRE, POS_ID_DROP, UPDATE_USER, UPDATE_DATE)
								 values
								 ($REQ_ID, $REQ_SEQ, $POS_ID, $POS_ID, $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
			} // end while
			
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลรายละเอียดจากตำแหน่งที่เกษียณ ปีงบประมาณ $REQ_YEAR จำนวน $count_retire_position รายการ [". implode(",", $arr_insert_posno) ."]");
		}else{
			$data = $db_dpis->get_array();
			$err_text = "รหัสข้อมูลซ้ำ [".$data[REQ_YEAR]." ".$data[REQ_NO]." ".$data[REQ_TITLE]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($REQ_ID) && trim($REQ_YEAR) && trim($REQ_NO)){
		$cmd = " select REQ_YEAR from PER_REQ2 where REQ_ID=$REQ_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PREV_REQ_YEAR = $data[REQ_YEAR];

		$cmd = " update PER_REQ2 set 
							REQ_YEAR='".$REQ_YEAR."', 
							REQ_NO='".$REQ_NO."', 
							REQ_TITLE='".$REQ_TITLE."', 
							REQ_DATE='".$REQ_DATE."', 
							REQ_W_DATE = $REQ_W_DATE,
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE' 
						 where REQ_ID=$REQ_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".$REQ_ID." : ".trim($REQ_YEAR)." : ".$REQ_NO." : ".$REQ_TITLE." : ".$REQ_DATE." : ".$REQ_W_DATE."]");
		
		if($REQ_YEAR != $PREV_REQ_YEAR){
			$cmd = " delete from PER_REQ2_DTL where REQ_ID=$REQ_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลรายละเอียดตำแหน่งที่เกษียณ ปีงบประมาณ $PREV_REQ_YEAR เพื่อเปลี่ยนเป็นข้อมูลจากตำแหน่งที่เกษียณ ปีงบประมาณ $REQ_YEAR ตามบัญชีคำขอเลขที่ $REQ_NO เรื่อง $REQ_TITLE");

			if($DPISDB=="odbc"){
				$cmd = " select 	a.POS_ID, a.POS_NO
						   from		PER_POSITION a
									left join PER_PERSONAL b on a.POS_ID=b.POS_ID
						   where	a.POS_STATUS = 1 and a.DEPARTMENT_ID=$DEPARTMENT_ID
									and (b.PER_STATUS=1 or b.PER_STATUS=2)
									and LEFT(trim(b.PER_RETIREDATE), 10) >= '".(($REQ_YEAR - 543) - 1)."-10-01' 
									and LEFT(trim(b.PER_RETIREDATE), 10) < '".($REQ_YEAR - 543)."-10-01'
						   order by 	iif(isnull(a.POS_NO),0,CLng(a.POS_NO)) ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select 	a.POS_ID, a.POS_NO
						   from		PER_POSITION a, PER_PERSONAL b
						   where	a.POS_STATUS = 1 and a.DEPARTMENT_ID=$DEPARTMENT_ID and a.POS_ID=b.POS_ID(+)
									and (b.PER_STATUS=1 or b.PER_STATUS=2)
									and SUBSTR(trim(b.PER_RETIREDATE), 1, 10) >= '".(($REQ_YEAR - 543) - 1)."-10-01' 
									and SUBSTR(trim(b.PER_RETIREDATE), 1, 10) < '".($REQ_YEAR - 543)."-10-01'
						   order by 	to_number(replace(a.POS_NO,'-','')) ";
			}elseif($DPISDB=="mysql"){
				$cmd = " select 	a.POS_ID, a.POS_NO
						   from		PER_POSITION a
									left join PER_PERSONAL b on a.POS_ID=b.POS_ID
						   where	a.POS_STATUS = 1 and a.DEPARTMENT_ID=$DEPARTMENT_ID
									and (b.PER_STATUS=1 or b.PER_STATUS=2)
									and LEFT(trim(b.PER_RETIREDATE), 10) >= '".(($REQ_YEAR - 543) - 1)."-10-01' 
									and LEFT(trim(b.PER_RETIREDATE), 10) < '".($REQ_YEAR - 543)."-10-01'
						   order by 	a.POS_NO ";
			} // end if			
			$count_retire_position = $db_dpis->send_cmd($cmd);
			while($data = $db_dpis->get_array()){
				$POS_ID = $data[POS_ID];
				$arr_insert_posno[] = $data[POS_NO];
				
				$cmd = " select max(REQ_SEQ) as MAX_SEQ from PER_REQ2_DTL where REQ_ID=$REQ_ID ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$data2 = array_change_key_case($data2, CASE_LOWER);
				$REQ_SEQ = $data2[max_seq] + 1;

				$cmd = " insert into PER_REQ2_DTL 
								 (REQ_ID, REQ_SEQ, POS_ID_RETIRE, POS_ID_DROP, UPDATE_USER, UPDATE_DATE)
								 values
								 ($REQ_ID, $REQ_SEQ, $POS_ID, $POS_ID, $SESS_USERID, '$UPDATE_DATE')
							  ";
				$db_dpis2->send_cmd($cmd);
//				$db_dpis2->show_error();
			} // end while
			
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลรายละเอียดจากตำแหน่งที่เกษียณ ปีงบประมาณ $REQ_YEAR จำนวน $count_retire_position รายการ [". implode(",", $arr_insert_posno) ."]");
		} // end if
	}
	
	if($command == "DELETE" && trim($REQ_ID)){
		$cmd = " select REQ_YEAR, REQ_NO, REQ_TITLE from PER_REQ2 where REQ_ID=$REQ_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REQ_YEAR = $data[REQ_YEAR];
		$REQ_NO = $data[REQ_NO];
		$REQ_TITLE = $data[REQ_TITLE];
		
		$cmd = " delete from PER_REQ2_DTL where REQ_ID=$REQ_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		$cmd = " delete from PER_REQ2 where REQ_ID=$REQ_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [". $REQ_ID ." : ".trim($REQ_YEAR)." : ".$REQ_NO." : ".$REQ_TITLE."]");
	}
	
	if($UPD || $VIEW){
		$cmd = " select 	REQ_YEAR, REQ_NO, REQ_TITLE, REQ_DATE, REQ_W_DATE, DEPARTMENT_ID 
				   from 		PER_REQ2 
				   where 	REQ_ID=$REQ_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$REQ_YEAR = trim($data[REQ_YEAR]);
		$REQ_NO = trim($data[REQ_NO]);
		$REQ_TITLE = trim($data[REQ_TITLE]);
		$REQ_DATE = substr($data[REQ_DATE], 0, 10);
		if($REQ_DATE){
			$arr_temp = explode("-", $REQ_DATE);
			$REQ_DATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
		} // end if
		$WITHDRAW = "";
		$REQ_W_DATE = substr($data[REQ_W_DATE], 0, 10);
		if(trim($REQ_W_DATE)){
			$arr_temp = explode("-", $REQ_W_DATE);
			$REQ_W_DATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
			$WITHDRAW = 1;
		} // end if

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
	
	if( (!$UPD && !$DEL && !$VIEW && !$err_text) ){
		$REQ_ID = "";
		$REQ_YEAR = "";
		$REQ_NO = "";
		$REQ_TITLE = "";
		$REQ_DATE = "";
		$REQ_W_DATE = "";
		$WITHDRAW = "";

		if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){
			$MINISTRY_ID = "";
			$MINISTRY_NAME = "";
		} // end if
		
		if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){
			$DEPARTMENT_ID = "";
			$DEPARTMENT_NAME = "";
		} // end if
	} // end if
?>