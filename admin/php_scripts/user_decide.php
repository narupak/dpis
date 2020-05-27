<?
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$cmd = " select code, name_th, group_level, pv_code, org_id from user_group where id=$group_id ";
	$db->send_cmd($cmd);
//	$db->show_error(); 
	$data = $db->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	$group_code = $data[code];
	$group_level = $data[group_level];
	
	switch($group_level){
		case 2 :
			$PROVINCE_CODE = $data[pv_code];
			break;
		case 3 :
			$MINISTRY_ID = $data[org_id];
			break;
		case 4 :
			$DEPARTMENT_ID = $data[org_id];
			break;
		case 5 :
			$ORG_ID = $data[org_id];
			break;
		case 6 :
			$ORG_ID_1 = $data[org_id];
			break;
	} // end switch case

//	echo "$group_level :: $PROVINCE_CODE :: $MINISTRY_ID :: $DEPARTMENT_ID :: $ORG_ID";
		
	if($ORG_ID_1){
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID_1 ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$ORG_NAME_1 = $data[ORG_NAME];
		$ORG_ID = $data[ORG_ID_REF];	
	} // end if

	if($ORG_ID){
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$ORG_NAME = $data[ORG_NAME];
		$DEPARTMENT_ID = $data[ORG_ID_REF];	
	} // end if

	if($DEPARTMENT_ID){
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$DEPARTMENT_NAME = $data[ORG_NAME];
		$MINISTRY_ID = $data[ORG_ID_REF];	
	} // end if

	if($MINISTRY_ID){
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];
	} // end if

	if($PROVINCE_CODE){
		$cmd = " select PV_NAME from PER_PROVINCE where PV_CODE='$PROVINCE_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PROVINCE_NAME = $data[PV_NAME];
	} // end if

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

	$district_id += 0;		$amphur_id += 0;		$province_id += 0;
	if(!$select_group_id) $select_group_id = $group_id;
	$user_link_id += 0;

	if($db_type=="mysql") {
		$update_date = "NOW()";
		$update_by = "'$SESS_USERNAME'";
	} elseif($db_type=="mssql") {
		$update_date = "GETDATE()";
		$update_by = $SESS_USERID;
	} elseif($db_type=="oci8" || $db_type=="odbc") {
		$update_date = date("Y-m-d H:i:s");
		$update_date = "'$update_date'";
		$update_by = $SESS_USERID;
	}
	
	$inherit_group = "";
	if(count($list_inherit_group)) $inherit_group = implode(",", $list_inherit_group);
	
	if($command=='insert_all'){
		$cmd = " select a.PER_CARDNO, a.PER_ID, a.PER_NAME, a.PER_SURNAME, g.ORG_NAME,
                        a.PER_EMAIL, a.PER_OFFICE_TEL, a.PER_FAX, b.PN_NAME, a.PER_BIRTHDATE
								from  PER_PERSONAL a, PER_PRENAME b, PER_POSITION c, PER_POS_EMP d, PER_POS_EMPSER e, PER_POS_TEMP f, PER_ORG g
								where a.PN_CODE=b.PN_CODE 
								and a.POS_ID=c.POS_ID(+) 
								and a.POEM_ID=d.POEM_ID(+) 
								and a.POEMS_ID=e.POEMS_ID(+) 
								and a.POT_ID=f.POT_ID(+) 
								and c.ORG_ID=g.ORG_ID(+)
								and (PER_STATUS in(1,0))
								and a.PER_CARDNO NOT in(select username from user_detail where	group_id=3 and user_flag='Y')
								order by a.DEPARTMENT_ID asc, c.POS_NO_NAME asc , 
								         d.POEM_NO_NAME asc , e.POEMS_NO_NAME asc , 
										 f.POT_NO_NAME asc ,to_number(replace(c.POS_NO,'-',''))asc , 
										 to_number(replace(d.POEM_NO,'-','')) asc, 
										 to_number(replace(e.POEMS_NO,'-','')) asc, 
										 to_number(replace(f.POT_NO,'-','')) asc";
		$cnt_cmd = $db_dpis3->send_cmd($cmd);
	if($cnt_cmd){
		while($data = $db_dpis3->get_array()){
			$PER_CARDNO  = $data[PER_CARDNO];
			$PER_ID 	 = $data[PER_ID];
			$PER_NAME 	 = $data[PER_NAME];
			$PER_SURNAME = $data[PER_SURNAME];
			$full_name 	 = $PER_NAME.' '.$PER_SURNAME;
			$ORG_NAME 	 = $data[ORG_NAME];
			$PER_EMAIL 	 = $data[PER_EMAIL];
			$PER_OFFICE_TEL = $data[PER_OFFICE_TEL];
			$PER_FAX 	 = $data[PER_FAX];
			$PN_NAME 	 = $data[PN_NAME];
			$PER_BIRTHDATE= trim($data[PER_BIRTHDATE]); 
			$new_pssw = explode('-',$PER_BIRTHDATE);
			$pass_year = $new_pssw[0]+543;
			$passw = $new_pssw[2].$new_pssw[1].$pass_year;

			$cmd = " select max(id) as max_id from user_detail ";
			$db->send_cmd($cmd);
			$data = $db->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$max_id = $data[max_id] + 1;
			$passw = md5($passw);
			
			$cmd = " insert into user_detail (id, group_id, username, password, inherit_group, user_link_id, fullname, address, 
											  district_id, amphur_id, province_id, email, tel, fax, titlename, create_date, create_by, 
											  update_date, update_by)
							
							values ($max_id, $select_group_id, '$PER_CARDNO', '$passw', '$inherit_group', '$PER_ID', 
							'$full_name', '$ORG_NAME', $district_id, $amphur_id, $province_id, '$PER_EMAIL', '$PER_OFFICE_TEL', 
							'$PER_FAX', '$PN_NAME', $update_date, $update_by, $update_date, $update_by) ";
			$db->send_cmd($cmd);
		}//end while			
	}//end if	
echo '<script type="text/javascript">parent.refresh_opener("1");</script>';
	}//end insert_all
	
	if( $command=="LOADDPIS" ){
		ini_set("max_execution_time", $max_execution_time);

		//$cmd = " delete from user_detail where group_id = $group_id ";//เดิม
                $cmd = " update user_detail set user_flag='N' where group_id = $group_id ";//Release 5.2.1.21
		$db->send_cmd($cmd);
		
		switch($group_level){
			case 1 :
			case 2 :
				$search_condition = "";
				break;
			case 3 :
				$cmd = " select ORG_ID from PER_ORG where ORG_ID_REF=$MINISTRY_ID ";
				$db_dpis->send_cmd($cmd);
				while($data = $db_dpis->get_array()) $arr_search_org[] = $data[ORG_ID];
				$search_condition = " and (a.DEPARTMENT_ID in (". implode(",", $arr_search_org) .")) ";
				break;
			case 4 :
//				$search_condition = " and (a.DEPARTMENT_ID=$DEPARTMENT_ID) ";
				break;
			case 5 :
				$search_condition = " and (b.ORG_ID=$ORG_ID or c.ORG_ID=$ORG_ID or d.ORG_ID=$ORG_ID) ";
				break;
		} // end switch case

		if ($BKK_FLAG==1) {
			$cmd = " select			a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, b.ORG_ID, a.PER_CARDNO, a.PER_TYPE, a.PER_BIRTHDATE
							 from			PER_PERSONAL a, PER_POSITION b
							 where		a.POS_ID=b.POS_ID and a.PER_CARDNO is not null and a.PER_TYPE=1 and a.PER_STATUS=1 
												$search_condition
							 order by		a.PER_ID ";		
		} else {
			if($DPISDB=="odbc"){
				$cmd = " select			a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, b.ORG_ID, c.ORG_ID as EMP_ORG_ID, d.ORG_ID as EMPSER_ORG_ID,
													a.PER_CARDNO, a.PER_TYPE, a.PER_BIRTHDATE
								 from			(
													(
														PER_PERSONAL a
														left join PER_POSITION b on (a.POS_ID=b.POS_ID)
													) left join PER_POS_EMP c on (a.POEM_ID=c.POEM_ID)
												) left join PER_POS_EMPSER d on (a.POEMS_ID=d.POEMS_ID)
								 where		trim(a.PER_CARDNO)<>'' and a.PER_CARDNO is not null and a.PER_STATUS=1
													$search_condition
								 order by		a.PER_ID ";
			}elseif($DPISDB=="oci8"){
				$cmd = " select			a.PER_ID, a.PN_CODE, a.PER_NAME, a.PER_SURNAME, b.ORG_ID, c.ORG_ID as EMP_ORG_ID,
													a.PER_CARDNO, a.PER_TYPE, a.PER_BIRTHDATE
								 from			PER_PERSONAL a, PER_POSITION b, PER_POS_EMP c, PER_POS_EMPSER d
								 where		a.POS_ID=b.POS_ID(+) and a.POEM_ID=c.POEM_ID(+) and a.POEMS_ID=d.POEMS_ID(+)
													and a.PER_CARDNO is not null and a.PER_STATUS=1 
													$search_condition
								 order by		a.PER_ID ";		
			} // end if
		}
		$count_all = $db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$PER_ID = $data[PER_ID];
			$PN_CODE = trim($data[PN_CODE]);
			$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PN_NAME = trim($data2[PN_NAME]);

			$PER_NAME = $data[PER_NAME];
			$PER_SURNAME = $data[PER_SURNAME];
			$FULLNAME = "$PER_NAME $PER_SURNAME";
			
			$PER_CARDNO = trim($data[PER_CARDNO]);
			$PER_BIRTHDATE = trim($data[PER_BIRTHDATE]);
			if($PER_BIRTHDATE){
				$arr_temp = explode("-", substr($PER_BIRTHDATE, 0, 10));
				$PER_BIRTHDATE = $arr_temp[2] . $arr_temp[1] . ($arr_temp[0] + 543);
			} // end if

			$PER_TYPE = $data[PER_TYPE];
			if($PER_TYPE==1) $ORG_ID = $data[ORG_ID];
			elseif($PER_TYPE==2) $ORG_ID = $data[EMP_ORG_ID];
			elseif($PER_TYPE==3) $ORG_ID = $data[EMPSER_ORG_ID];
			elseif($PER_TYPE==4) $ORG_ID = $data[TEMP_ORG_ID];
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME = trim($data2[ORG_NAME]);
			
			$cmd = " select max(id) as max_id from user_detail ";
			$db->send_cmd($cmd);
			$data = $db->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$max_id = $data[max_id] + 1;
			
			$username = $PER_CARDNO;
			$passwd = $PER_BIRTHDATE?md5($PER_BIRTHDATE):md5($PER_CARDNO);
			$user_link_id = $PER_ID;
			$user_name = $FULLNAME;
			$address = $ORG_NAME;
			$titlename = $PN_NAME;
			
			$cmd = " insert into user_detail (id, group_id, username, password, user_link_id, fullname, 
							address, create_date, create_by, update_date, update_by, titlename)
							 values ($max_id, $group_id, '$username', '$passwd', $user_link_id, '$user_name', 
							'$address', $update_date, $update_by, $update_date, $update_by, '$titlename') ";
			$db->send_cmd($cmd);
//			$db->show_error();
		} // end while
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > นำเข้าข้อมูลจาก DPIS จำนวน $count_all รายการ");
		echo '<script type="text/javascript">parent.refresh_opener("1");</script>';
	}
	
	
			
?>