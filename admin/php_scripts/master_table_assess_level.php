 <?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");	

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
//	echo "2.. command=$command :::  $AL_YEAR - $AL_CYCLE :: (UPD=$UPD)<br>";

	if($SESS_PER_TYPE==0){ 
			$PER_TYPE = (isset($PER_TYPE))?  $PER_TYPE : 1;	
			$search_per_type = (isset($search_per_type))?  $search_per_type : 1;	
	}
//	if($search_per_type!="" && $search_per_type!=0){	$PER_TYPE = $search_per_type; } 	// บรรทัดนี้ ทำให้ PER_TYPE มีค่าเท่ากับ search_per_type ตอนเลือกใน search 
	$PER_TYPE = (trim($PER_TYPE))? $PER_TYPE : 1;	
	if(!$search_al_year)	$search_al_year = $KPI_BUDGET_YEAR;		
	if(!$search_al_cycle)	$search_al_cycle = $KPI_CYCLE; 
	
	if($command=="SEARCH"){//ค้นหา เงื่อนไข
		if($_POST[search_al_year])	$search_al_year = $_POST[search_al_year];		
		if($_POST[search_al_cycle])	$search_al_cycle = $_POST[search_al_cycle]; 
		if($_POST[search_al_year]=="")	$search_al_year = "";		
		if(!$_POST[search_al_cycle] || $_POST[search_al_cycle]=="")	$search_al_cycle = ""; 
	}
	if($command=="SEARCH_ALL"){//ค้นหา แสดงทั้งหมด
		$search_al_year ="";	$search_al_cycle = "";
	}
	//echo " $command :::  $_POST[search_al_year] - $search_al_year  / $_POST[search_al_cycle] -  $search_al_cycle :: ";

//---เพิ่มใหม่
//	if (!$AL_YEAR) $AL_YEAR = $KPI_BUDGET_YEAR;
//	if (!$AL_CYCLE) $AL_CYCLE = $KPI_CYCLE;
	
//	if($_GET[AL_YEAR] || $_GET[AL_YEAR]=="")				$AL_YEAR = $_GET[AL_YEAR];
//	if($_GET[AL_CYCLE] || $_GET[AL_CYCLE]=="")		$AL_CYCLE = $_GET[AL_CYCLE];

	function list_per_assess_main ($name, $val) {
		global $db_list, $DPISDB, $PER_TYPE, $RPT_N;
		global $PAGE_AUTH,$UPD,$CH_ADD,$PER_ID, $AL_YEAR , $AL_CYCLE;
		$search_cond = "";
		if($AL_YEAR)		$search_cond = " and AM_YEAR = '$AL_YEAR'"; 
		if($AL_CYCLE)	$search_cond .= " and  AM_CYCLE = $AL_CYCLE "; 
		$cmd = "	select AM_CODE, AM_NAME, AM_YEAR, AM_CYCLE from PER_ASSESS_MAIN 
							WHERE (PER_TYPE=$PER_TYPE and AM_ACTIVE=1 $search_cond) 
							ORDER BY AM_YEAR, AM_CYCLE, AM_CODE ";
		$count_list=$db_list->send_cmd($cmd);
		//$db_list->show_error();
//echo "MAIN : $count_list - $cmd <br>";
		if($count_list > 0){
			echo "<select name=\"$name\" class=\"selectbox\">
			<option value=''>== เลือกระดับผลการประเมินหลัก ==</option>";
			while ($data_list = $db_list->get_array()) {
				//$data_list = array_change_key_case($data_list, CASE_LOWER);
				$tmp_dat = trim($data_list[AM_CODE]);
				$tmp_name = trim($data_list[AM_YEAR])." รอบที่ ".trim($data_list[AM_CYCLE])." ".trim($data_list[AM_NAME]);
				$qm_arr[$tmp_dat] = $tmp_dat;
				$sel = (($tmp_dat) == trim($val))? "selected" : "";
				echo "<option value='$tmp_dat' $sel>". $tmp_name ."</option>";
			}
			echo "</select>";
		}else{
			echo "<span class=\"label_alert\">ต้องสร้าง \"ระดับผลการประเมินหลัก\""; 
			if($AL_YEAR)		echo " ของปีงบประมาณ $AL_YEAR";
			if($AL_CYCLE)	echo " และรอบการประเมินครั้งที่ $AL_CYCLE ที่เมนู \"M1103\" ก่อน</span>";
		}
		return $val;
		//echo "<pre>";		
		//print_r($data_list);
		//echo "</pre>";	
	}	

	switch($CTRL_TYPE){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_ministry_id2 = $MINISTRY_ID;
			$search_ministry_name2 = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
		
			$search_ministry_id2 = $MINISTRY_ID;
			$search_ministry_name2 = $MINISTRY_NAME;
			$search_department_id2 = $DEPARTMENT_ID;
			$search_department_name2 = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_ministry_id2 = $MINISTRY_ID;
			$search_ministry_name2 = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			
			$search_ministry_id2 = $MINISTRY_ID;
			$search_ministry_name2 = $MINISTRY_NAME;
			$search_department_id2 = $DEPARTMENT_ID;
			$search_department_name2 = $DEPARTMENT_NAME;
			break;
		case 5 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;

			$search_ministry_id2 = $MINISTRY_ID;
			$search_ministry_name2 = $MINISTRY_NAME;
			$search_department_id2 = $DEPARTMENT_ID;
			$search_department_name2 = $DEPARTMENT_NAME;
			$search_org_id2 = $ORG_ID;
			$search_org_name2 = $ORG_NAME;
			break;
		case 6 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id = $ORG_ID;
			$search_org_name = $ORG_NAME;
			
			$search_ministry_id2 = $MINISTRY_ID;
			$search_ministry_name2 = $MINISTRY_NAME;
			$search_department_id2 = $DEPARTMENT_ID;
			$search_department_name2 = $DEPARTMENT_NAME;
			$search_org_id2 = $ORG_ID;
			$search_org_name2 = $ORG_NAME;
			$search_org_id_1 = $ORG_ID_1;
			$search_org_name_1 = $ORG_NAME_1;
			break;
	} // end switch case

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	if (!$search_department_id) $search_department_id = "NULL";
	if (!$search_org_id) $search_org_id = "NULL";
	if (!$AL_PERCENT) $AL_PERCENT = 0;

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
//		echo "$setflagshow";
		$cmd = " update PER_ASSESS_LEVEL set AL_ACTIVE = 0 where AL_CODE in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$cmd = " update PER_ASSESS_LEVEL set AL_ACTIVE = 1 where AL_CODE in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการใช้งานข้อมูล");
	}
	
	if($command == "ADD" && trim($AL_NAME)){
		$cmd = " select AL_NAME from PER_ASSESS_LEVEL where AL_YEAR='$AL_YEAR' and AL_CYCLE=$AL_CYCLE and AL_CODE='$AL_CODE' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			if ($PER_TYPE==3)
				$AL_CYCLE = (trim($AL_CYCLE))? $AL_CYCLE : 2;			//เพิ่ม
			else
				$AL_CYCLE = (trim($AL_CYCLE))? $AL_CYCLE : 1;			//เพิ่ม
			$zero = "00000";
			$cmd = " select count(AL_CODE) as MAX_ID from PER_ASSESS_LEVEL ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$cnt_chk = 1;
			$inc = 1;
			while($cnt_chk > 0) {
				$max = (string)((int)$data[MAX_ID]+$inc);
				$maxlen = strlen($max);
				$NEW_AL_CODE = substr($zero,0,5-$maxlen).$max;
//				echo "gen max-$cmd (".$data[MAX_ID].", $NEW_AL_CODE)<br>";
				$cmd = " select * from PER_ASSESS_LEVEL where AL_CODE='$NEW_AL_CODE' ";
				$cnt_chk = $db_dpis->send_cmd($cmd);
				$inc++;
			}			
			if (!$search_department_id) $search_department_id = "NULL";
			$cmd = " insert into PER_ASSESS_LEVEL (AL_YEAR , AL_CYCLE, AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, ORG_ID, DEPARTMENT_ID, 
							AM_CODE, AL_PERCENT, AL_ACTIVE, UPDATE_USER, UPDATE_DATE, PER_TYPE) 
							values ($AL_YEAR , $AL_CYCLE, '$NEW_AL_CODE', '$AL_NAME', $AL_POINT_MIN, $AL_POINT_MAX, $search_org_id, $search_department_id, 
							'$AM_CODE', $AL_PERCENT, 1, $SESS_USERID, '$UPDATE_DATE', $PER_TYPE) ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			//echo "ADD-$cmd<br>";
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".trim($AL_CODE)." : ".$AL_NAME."]");
				//			$AL_YEAR  = "";
//			$AL_CYCLE = 1;
				$AL_CODE = "";
				$AL_NAME = "";
				$AL_POINT_MIN = "";
				$AL_POINT_MAX = "";
				$AL_PERCENT = "";
				$AM_CODE = "";
//			$PER_TYPE = 1;
//		 if($SESS_USERGROUP_LEVEL < 5){ 	$search_org_id="";	$search_org_name="";	}
				$command = "";
				unset($SHOW_UPDATE_USER);
				unset($SHOW_UPDATE_DATE);
				$err_text = "";
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสข้อมูลซ้ำ [".$AL_CODE." ".$AL_NAME."]";
		} // endif
	}

	if($command == "UPDATE" && trim($AL_CODE)){
		if (!$search_department_id) $search_department_id = "NULL";
		$cmd = " update PER_ASSESS_LEVEL set 
									AL_NAME = '$AL_NAME', 
									AL_POINT_MIN = $AL_POINT_MIN, 
									AL_POINT_MAX = $AL_POINT_MAX, 
									ORG_ID = $search_org_id, 
									DEPARTMENT_ID = $search_department_id, 
									AM_CODE = '$AM_CODE', 
									AL_PERCENT = $AL_PERCENT, 
									PER_TYPE = $PER_TYPE, 
									UPDATE_USER = $SESS_USERID, 
									UPDATE_DATE = '$UPDATE_DATE'  
									where AL_YEAR='$AL_YEAR' and AL_CYCLE=$AL_CYCLE and AL_CODE='$AL_CODE' ";
		$db_dpis->send_cmd($cmd);
		//echo "$cmd";
		//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".trim($AL_CODE)." : ".$AL_NAME."]");
			$UPD = "";		
			$UPD_Y_C = "";
			$AL_CODE = "";
			$AL_NAME = "";
			$AL_POINT_MIN = "";
			$AL_POINT_MAX = "";
			$AM_CODE = "";
			$AL_PERCENT = ""; 
			$AL_YEAR = "";
			$AL_CYCLE = "";
			if($SESS_USERGROUP_LEVEL < 5){ 	$search_org_id="";	$search_org_name="";	}
			$command = "";
			unset($SHOW_UPDATE_USER);
			unset($SHOW_UPDATE_DATE);
		//เคลียร์ค่าเพื่อเพิ่มใหม่
 	}
	
	if($command == "DELETE" && trim($AL_CODE)){
		//เพื่อให้อันที่ไม่มี ปีงบประมาณ และครั้งที่ สามารถลบทิ้งได้ ถ้าไม่มีลบทิ้งไม่ได้
		$cmd = " select AL_NAME from PER_ASSESS_LEVEL where AL_YEAR='$AL_YEAR' and AL_CYCLE=$AL_CYCLE and AL_CODE='$AL_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$AL_NAME = $data[AL_NAME];

		$cmd = " delete from PER_ASSESS_LEVEL where AL_YEAR='$AL_YEAR' and AL_CYCLE=$AL_CYCLE and AL_CODE='$AL_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($AL_YEAR)." : ".trim($AL_CYCLE)." : ".trim($AL_CODE)." : ".$AL_NAME."]");
		$AL_CODE = "";
		$AL_NAME = "";
		$AL_YEAR  = "";
		$AL_POINT_MIN = "";
		$AL_POINT_MAX = "";
		$AL_PERCENT = "";
		$AM_CODE = "";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
		$command = "";
		
	}
	
	if($command == "COPY" && trim($AL_COPY_FROMYEAR)){
		$where = "";
		if ($search_department_id) $where = " and DEPARTMENT_ID = $search_department_id";
		if ($search_org_id) 
			if ($search_org_id=="NULL") $where .= " and ORG_ID is NULL";
			else $where .= " and ORG_ID = $search_org_id";

		$cmd = " delete from PER_ASSESS_LEVEL 
						where AL_YEAR='$AL_COPY_TOYEAR' and AL_CYCLE=$AL_COPY_TOCYCLE and PER_TYPE = $PER_TYPE $where ";
		$db_dpis->send_cmd($cmd);

		$cmd = " select AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, ORG_ID, DEPARTMENT_ID, AM_CODE, AL_ACTIVE, UPDATE_USER, UPDATE_DATE, AL_PERCENT  
						from PER_ASSESS_LEVEL 
						where AL_YEAR='$AL_COPY_FROMYEAR' and AL_CYCLE=$AL_COPY_FROMCYCLE and PER_TYPE = $PER_TYPE $where ";
		$db_dpis->send_cmd($cmd);
		while ($data = $db_dpis->get_array()) {
			$TMP_AL_CODE = trim($data[AL_CODE]);
			$TMP_AL_NAME = trim($data[AL_NAME]);
			$TMP_AL_POINT_MIN = $data[AL_POINT_MIN];
			$TMP_AL_POINT_MAX = $data[AL_POINT_MAX];
			$TMP_ORG_ID = $data[ORG_ID];
			$TMP_DEPARTMENT_ID = $data[DEPARTMENT_ID];
			$TMP_AM_CODE = trim($data[AM_CODE]);
			$TMP_AL_ACTIVE = $data[AL_ACTIVE];
			$TMP_UPDATE_USER = $data[UPDATE_USER];
			$TMP_UPDATE_DATE = trim($data[UPDATE_DATE]);
			$TMP_AL_PERCENT = $data[AL_PERCENT];
			if (!$TMP_AL_POINT_MIN) $TMP_AL_POINT_MIN = 0;
			if (!$TMP_AL_POINT_MAX) $TMP_AL_POINT_MAX = 0;
			if (!$TMP_ORG_ID) $TMP_ORG_ID = "NULL";
			if (!$TMP_DEPARTMENT_ID) $TMP_DEPARTMENT_ID = "NULL";
			if (!$TMP_AL_PERCENT) $TMP_AL_PERCENT = "NULL";
			
			$cmd = " insert into PER_ASSESS_LEVEL (AL_YEAR, AL_CYCLE, AL_CODE, AL_NAME, AL_POINT_MIN, AL_POINT_MAX, ORG_ID, DEPARTMENT_ID, 
							AM_CODE, AL_ACTIVE, UPDATE_USER, UPDATE_DATE, PER_TYPE, AL_PERCENT) 
							values ('$AL_COPY_TOYEAR', $AL_COPY_TOCYCLE, '$TMP_AL_CODE', '$TMP_AL_NAME', $TMP_AL_POINT_MIN, $TMP_AL_POINT_MAX, 
							$TMP_ORG_ID, $TMP_DEPARTMENT_ID, '$TMP_AM_CODE', $TMP_AL_ACTIVE, $TMP_UPDATE_USER, '$TMP_UPDATE_DATE', $PER_TYPE, $TMP_AL_PERCENT) ";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".trim($AM_YEAR)." : ".trim($AM_CYCLE)." : ".trim($AM_CODE)." : ".$AM_NAME."]");
		}
	}
/*	
	if(!$UPD && $_GET[UPD])		$UPD = $_GET[UPD];	
	if($command == "UPDATE"){
		if (!$AL_CODE && $_POST[AL_CODE]) $AL_CODE = $_POST[AL_CODE];
		if (!$AL_YEAR && $_POST[AL_YEAR]) $AL_YEAR = $_POST[AL_YEAR];
		if (!$AL_CYCLE && $_POST[AL_CYCLE]) $AL_CYCLE = $_POST[AL_CYCLE];
	} */
	if($UPD){
		$cmd = " select AL_NAME, AL_POINT_MIN, AL_POINT_MAX, ORG_ID, DEPARTMENT_ID, AM_CODE, AL_PERCENT, PER_TYPE, UPDATE_USER, UPDATE_DATE
						from PER_ASSESS_LEVEL 
						where AL_YEAR='$AL_YEAR' and AL_CYCLE=$AL_CYCLE and AL_CODE='$AL_CODE'  ";
		$db_dpis->send_cmd($cmd);
//		echo "---> cmd=$cmd<br>";
		$data = $db_dpis->get_array();
		$AL_NAME = $data[AL_NAME];
		$AL_POINT_MIN = $data[AL_POINT_MIN];
		$AL_POINT_MAX = $data[AL_POINT_MAX];
		$search_org_id = $data[ORG_ID];
		$search_department_id = $data[DEPARTMENT_ID];
		$AM_CODE = $data[AM_CODE];
		$AL_PERCENT = $data[AL_PERCENT];
		$PER_TYPE = $data[PER_TYPE];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		
		if(trim($search_department_id)){
			$cmd = " select ORG_NAME,ORG_ID_REF from PER_ORG where ORG_ID=$search_department_id ";
			if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$search_department_name = $data[ORG_NAME];
			$search_ministry_id= $data[ORG_ID_REF];
		}
		
		if(trim($search_ministry_id)){					
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$search_ministry_id ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$search_ministry_name = $data[ORG_NAME];
		}
		
		if(trim($search_org_id)){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$search_org_id ";
			if($SESS_ORG_STRUCTURE==1){	$cmd = str_replace("PER_ORG","PER_ORG_ASS",$cmd); 	}
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$search_org_name = $data[ORG_NAME];
		}
	} // end if
	
	if( (!$UPD  && $UPD_Y_C && !$DEL && !$err_text) ){
		if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){			 // จังหวัด			 && !$VIEW
			$search_ministry_id = "";
			$search_ministry_name= "";
			$search_department_id= "";
			$search_department_name= "";
			$search_org_id= ""; 
			$search_org_name= "";
		}
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
		//ถ้าใส่ไว้ เวลาเลือก ประเภทบุคลากร เพื่อให้ไปดึงค่า ระดับผลการประเมินหลัก ของข้าราชการ/ลูกจ้าง/พนง ค่าที่กรอกไว้ก่อนหน้าจะถูกเคลียร์ค่าไปหมด
		/*$AL_CODE = "";
		$AL_NAME = "";
		$AL_POINT_MIN = "";
		$AL_POINT_MAX = "";
		$AM_CODE = "";
		$AL_PERCENT = ""; 
		$AL_YEAR = "";
		$AL_CYCLE = "";
		 if($SESS_USERGROUP_LEVEL < 5){ 	$search_org_id="";	$search_org_name="";	}
		*/
	} // end if
?>