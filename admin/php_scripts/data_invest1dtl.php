<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");
	include("php_scripts/load_per_control.php");		

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

        
        /*ขยาย fld INV_DETAIL เพื่อให้รองรับการคีข้อความยาวๆ จาก 200 เป็น 2000*/
        $cmdModify = "select column_name, data_type ,data_length
                    from user_tab_columns
                    where table_name = 'PER_INVEST1'
                    and column_name = 'INV_DETAIL'";
        $db_dpis2->send_cmd($cmdModify);
        $dataModify = $db_dpis2->get_array_array();
        $data_length = $dataModify[2];
        if($data_length==200){
            $cmdModify = "ALTER TABLE PER_INVEST1 MODIFY INV_DETAIL VARCHAR2(2000)";
            $db_dpis2->send_cmd($cmdModify);
            $db_dpis2->send_cmd("COMMIT");
        }
        /**/
        
        
        
        
//==============================================================================
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
//==============================================================================

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$INV_STATUS = (trim($INV_STATUS))? $INV_STATUS : 1;

//==============================================================================
//---Add new invest1
	if( $command == "ADD" && trim(!$INV_ID) ){
		$cmd = " select max(INV_ID) as max_id from PER_INVEST1 ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$INV_ID = $data[max_id] + 1;
		$INV_DETAIL = substr(str_replace("'", "&rsquo;", $INV_DETAIL),0, 2000) ;			
		$INV_DESC = str_replace("'", "&rsquo;", $INV_DESC);

		$tmp_INV_DATE =  save_date($INV_DATE);
		
		$cmd = " insert into PER_INVEST1 
				(INV_ID, INV_NO, INV_DATE, INV_DESC, INV_STATUS, CRD_CODE, INV_DETAIL, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE) 
				VALUES ($INV_ID, '$INV_NO', '$tmp_INV_DATE', '$INV_DESC', $INV_STATUS, '$CRD_CODE', '$INV_DETAIL', $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
	//$db_dpis->show_error();
		
		//-------------------
		$cmd = " select  INV_ID  from PER_INVEST1 where INV_ID=$INV_ID";
		$count_result = $db_dpis->send_cmd($cmd);
		if($count_result==0){
			$INV_ID = ""; 
		}
		//-------------------
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลการสอบข้อเท็จจริง [".trim($INV_ID)." : ".$INV_NO."]");
		if($INV_ID){
			$cmd = " select * from PER_INVEST1DTL where INV_ID = $INV_ID ";
			$count_invdtl = $db_dpis->send_cmd($cmd);
			// เมื่อเพิ่มข้อมูล เรียบร้อย ก็ส่งค่ากลับโปรแกรมหลัก ให้อยู่ใน mode เพิ่มตัวต่อไป
			echo "<script>";
			echo "parent.refresh_opener('2<::>$INV_ID<::><::>$count_invdtl<::>?UPD=1')";
			echo "</script>";
		}
	}

	if( $command == "UPDATE" && trim($INV_ID) ) {
		$tmp_INV_DATE =  save_date($INV_DATE);
		$INV_DETAIL = substr(str_replace("'", "&rsquo;", $INV_DETAIL),0, 2000) ;	
		$INV_DESC = str_replace("'", "&rsquo;", $INV_DESC);

		$cmd = " 	update PER_INVEST1 set  
							INV_NO='$INV_NO', INV_DATE='$tmp_INV_DATE', INV_DESC='$INV_DESC', 
							INV_STATUS=$INV_STATUS, CRD_CODE='$CRD_CODE', INV_DETAIL='$INV_DETAIL', 
							UPDATE_USER=$SESS_USERID, 
							UPDATE_DATE='$UPDATE_DATE' 
						where INV_ID=$INV_ID  ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลการสอบข้อเท็จจริง [".trim($INV_ID)." : ".$INV_NO."]");
	}

//==============================================================================
	
	if($command == "ADD" && trim($PER_ID)){
		$INV_ID = trim($INV_ID);
		$cmd = " select  INV_ID  from PER_INVEST1DTL  where INV_ID=$INV_ID and PER_ID=$PER_ID ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
//echo "A.  $count_duplicate<br>";	
		if($count_duplicate <= 0){
//echo "B.  $count_duplicate<br>";
			$cmd = " insert into PER_INVEST1DTL (INV_ID, PER_ID, UPDATE_USER, UPDATE_DATE) values ($INV_ID, $PER_ID, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis2->send_cmd($cmd);
			//$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลผู้ถูกกล่าวหา [".$INV_ID." : ".$PER_ID."]");

			$cmd = " select * from PER_INVEST1DTL where INV_ID = $INV_ID ";
			$count_invdtl = $db_dpis->send_cmd($cmd);
			// เมื่อเพิ่มข้อมูล เรียบร้อย ก็ส่งค่ากลับโปรแกรมหลัก ให้อยู่ใน mode เพิ่มตัวต่อไป
			echo "<script>";
			echo "parent.refresh_opener('2<::>!<::>$PER_ID<::>$count_invdtl<::>?UPD=1')";
			echo "</script>";
		}else{
//echo "this<br>";
			$data = $db_dpis->get_array();
			$err_text = "รหัสผู้ถูกกล่าวหาซ้ำ [".$INV_ID." : ".$PER_ID."]";
		} // endif
	}
	
	if($command == "DELETE" && trim($PER_ID)){
		$cmd = " delete from PER_INVEST1DTL where INV_ID=$INV_ID and PER_ID=$PER_ID";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลผู้ถูกกล่าวหา [".trim($INV_ID)." : ".$PER_ID."]");

		$cmd = " select * from PER_INVEST1DTL where INV_ID = $INV_ID ";
		$count_invdtl = $db_dpis->send_cmd($cmd);
		// เมื่อเพิ่มข้อมูล เรียบร้อย ก็ส่งค่ากลับโปรแกรมหลัก ให้อยู่ใน mode เพิ่มตัวต่อไป
		if ($count_invdtl == 0) {
			echo "<script>";
			echo "parent.refresh_opener('2<::>!<::><::>$count_invdtl<::>?UPD=1')";
			echo "</script>";
		}
	}
	
//==============================================================================
	if($command == "DELETE_COMMAND" && trim($INV_ID) ){
	
		//---chk invest2 ----------------------------------------------
		$cmd = " select * from PER_INVEST2 where INV_ID_REF=$INV_ID ";
		$count_result_invest2 = $db_dpis->send_cmd($cmd);
		if($count_result_invest2 > 0){
			$error_delete_invest1=1;
		}else{
			$cmd = " delete from PER_INVEST1DTL where INV_ID=$INV_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
				
			$cmd = " delete from PER_INVEST1 where INV_ID=$INV_ID ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
		}
		//-------------------------------------------------------------
		
		
		/*$cmd = " delete from PER_INVEST1DTL where INV_ID=$INV_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
			
		$cmd = " delete from PER_INVEST1 where INV_ID=$INV_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();*/

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลการสอบข้อเท็จจริง [".trim($INV_ID)." : ".$INV_NO."]");
		#############
		$INV_ID = "";
		#############
		echo "<script>";
		echo "parent.refresh_opener('1<::><::><::><::>')";
		echo "</script>";
	}

	if (trim($INV_ID)){
		$cmd = " 	select 	INV_ID, INV_NO, INV_DATE, INV_DESC, INV_STATUS, CRD_CODE, INV_DETAIL, DEPARTMENT_ID
						from 		PER_INVEST1 
						where 	trim(INV_ID)=$INV_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		
		$INV_NO = trim($data[INV_NO]);
		$INV_DESC = trim($data[INV_DESC]);
		$INV_STATUS = trim($data[INV_STATUS]);
		$INV_DETAIL = trim($data[INV_DETAIL]);
		
		$INV_DATE = show_date_format($data[INV_DATE], 1);

		$CRD_CODE = trim($data[CRD_CODE]);
		$cmd = "	select CRD_NAME, CR_NAME from PER_CRIME_DTL a, PER_CRIME b 
						where a.CRD_CODE='$CRD_CODE' and a.CR_CODE=b.CR_CODE ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$CRD_NAME = $data_dpis2[CRD_NAME];		
		$CR_NAME = $data_dpis2[CR_NAME];				

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
	}

	if((!$INV_ID)){
		$INV_ID = "";
		$PER_ID = "";
		$INV_NO = "";
		$INV_DATE = "";
		$INV_DESC = "";
		$INV_STATUS = 1;
		$CRD_CODE = "";
		$CRD_NAME = "";
		$CR_NAME = "";
		$INV_DETAIL = "";

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