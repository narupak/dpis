<?
	$UPDATE_DATE = date("Y-m-d H:i:s");
	if (($BKK_FLAG==1 || $ISCS_FLAG==1) && (trim($DEPARTMENT_ID)=="" || $DEPARTMENT_ID=="NULL" || is_null($DEPARTMENT_ID))) $DEPARTMENT_ID="''";

	//=======================================================================================
	//--Add coursedtl
	if( $command == "ADD" && trim(!$CO_ID) ){
		$cmd = " select max(CO_ID) as max_id from PER_COURSE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$CO_ID = $data[max_id] + 1;

		if($_POST[DEPARTMENT_ID])	$DEPARTMENT_ID = $_POST[DEPARTMENT_ID];
		if(trim($CO_STARTDATE))	$CO_STARTDATE = save_date($CO_STARTDATE);
		if(trim($CO_ENDDATE))	$CO_ENDDATE = save_date($CO_ENDDATE);
		if(trim($CO_BOOK_DATE)) $CO_BOOK_DATE = save_date($CO_BOOK_DATE);
		$CT_CODE_FUND = (trim($CT_CODE_FUND) && $CT_CODE_FUND!="NULL")? "'$CT_CODE_FUND'" : "NULL";
		$cmd = " insert into PER_COURSE (CO_ID, TR_CODE, CO_NO, CO_STARTDATE, CO_ENDDATE, CO_PLACE, CT_CODE, 
				CO_ORG, CO_FUND, CT_CODE_FUND, CO_TYPE, CO_CONFIRM, DEPARTMENT_ID, UPDATE_USER, UPDATE_DATE, 
				CO_DAY, CO_REMARK, CO_PROJECT_NAME, CO_COURSE_NAME, CO_DEGREE_RECEIVE, CO_BOOK_NO, CO_BOOK_DATE) 
				VALUES ($CO_ID, '$TR_CODE', '$CO_NO', '$CO_STARTDATE', '$CO_ENDDATE', '$CO_PLACE', '$CT_CODE', '$CO_ORG', 
				'$CO_FUND', $CT_CODE_FUND, $CO_TYPE, 0, $DEPARTMENT_ID, $SESS_USERID, '$UPDATE_DATE', $CO_DAY, 
				'$CO_REMARK','$CO_PROJECT_NAME', '$CO_COURSE_NAME', '$CO_DEGREE_RECEIVE', '$CO_BOOK_NO', '$CO_BOOK_DATE') ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error();
//		echo "<br>".$cmd;

		//-------------------
		$cmd = " select  CO_ID  from PER_COURSE where CO_ID=$CO_ID";
		$count_result = $db_dpis->send_cmd($cmd);
		if($count_result==0){
			$CO_ID =""; 
		}
		//-------------------

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลการอบรม [".trim($CO_ID)." : ".$TR_CODE."]");
	}
	
	if( $command == "UPDATE" && trim($CO_ID) ) {
		if(trim($CO_STARTDATE))	$CO_STARTDATE = save_date($CO_STARTDATE);
		if(trim($CO_ENDDATE))	$CO_ENDDATE = save_date($CO_ENDDATE);
		if(trim($CO_BOOK_DATE)) $CO_BOOK_DATE = save_date($CO_BOOK_DATE);
		$CT_CODE_FUND = (trim($CT_CODE_FUND) && $CT_CODE_FUND!="NULL")? "'$CT_CODE_FUND'" : "NULL";
		$cmd = " update PER_COURSE set  
					TR_CODE='$TR_CODE', 
					CO_NO='$CO_NO', 
					CO_STARTDATE='$CO_STARTDATE', 
					CO_ENDDATE='$CO_ENDDATE', 
					CO_PLACE='$CO_PLACE', 
					CT_CODE='$CT_CODE', 
					CO_ORG='$CO_ORG', 
					CO_FUND='$CO_FUND', 
					CT_CODE_FUND=$CT_CODE_FUND, 
					CO_TYPE=$CO_TYPE, 
					CO_DAY=$CO_DAY, 
					CO_REMARK='$CO_REMARK', 
					CO_PROJECT_NAME = '$CO_PROJECT_NAME',
					CO_COURSE_NAME = '$CO_COURSE_NAME',
					CO_DEGREE_RECEIVE = '$CO_DEGREE_RECEIVE',
					CO_BOOK_NO = '$CO_BOOK_NO',
					CO_BOOK_DATE = '$CO_BOOK_DATE',
					UPDATE_USER=$SESS_USERID, 
					UPDATE_DATE='$UPDATE_DATE'
				where CO_ID=$CO_ID  ";				
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		//echo "<hr>".$cmd;

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลการอบรม [".trim($CO_ID)." : ".$TR_CODE."]");
	}			// 	if( $command == "UPDATE" && trim($COM_ID) )

// ============================================================
	// เมื่อยืนยันข้อมูลการฝึกอบรม/ดูงาน/สัมมนา โดยเลือกเฉพาะคนที่ได้รับคัดเลือกเท่านั้น
	if( $command == "COMMAND" && trim($CO_ID) ) {
		if(trim($CO_STARTDATE))	$CO_STARTDATE = save_date($CO_STARTDATE);
		if(trim($CO_ENDDATE))	$CO_ENDDATE = save_date($CO_ENDDATE);
		if(trim($CO_BOOK_DATE)) $CO_BOOK_DATE = save_date($CO_BOOK_DATE);
		$CT_CODE_FUND = (trim($CT_CODE_FUND) && $CT_CODE_FUND!="NULL")? "'$CT_CODE_FUND'" : "NULL";
		$cmd = " update PER_COURSE set  
							CO_CONFIRM=1, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
						 where CO_ID=$CO_ID  ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		$cmd = " select PER_ID, PER_CARDNO, COD_PASS  from PER_COURSEDTL a
						  where CO_ID=$CO_ID and COD_RESULT=1 "; 
		$db_dpis->send_cmd($cmd);
		while ($data = $db_dpis->get_array()) {
			$TMP_PER_ID = $data[PER_ID];
			$TMP_PER_CARDNO = $data[PER_CARDNO];
			$TMP_COD_PASS = $data[COD_PASS];
			$cmd = " select max(TRN_ID) as max_id from PER_TRAINING ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$data1 = array_change_key_case($data1, CASE_LOWER);
			$TRN_ID = $data1[max_id] + 1;	
			
			$cmd = " insert into PER_TRAINING (TRN_ID, PER_ID, TRN_TYPE, TR_CODE, TRN_NO, TRN_STARTDATE, TRN_ENDDATE, TRN_ORG, 
							TRN_PLACE, CT_CODE, TRN_FUND, CT_CODE_FUND, UPDATE_USER, UPDATE_DATE, PER_CARDNO, TRN_DAY, TRN_REMARK, 
							TRN_PASS, TRN_PROJECT_NAME,  TRN_COURSE_NAME, TRN_DEGREE_RECEIVE, TRN_BOOK_NO, TRN_BOOK_DATE,TRN_POINT)
							VALUES ($TRN_ID, $TMP_PER_ID, 1, '$TR_CODE', '$CO_NO', '$CO_STARTDATE', '$CO_ENDDATE', '$CO_ORG', '$CO_PLACE', 
							'$CT_CODE', '$CO_FUND', $CT_CODE_FUND, $SESS_USERID, '$UPDATE_DATE', '$TMP_PER_CARDNO', $CO_DAY, '$CO_REMARK', 
							$TMP_COD_PASS, '$CO_PROJECT_NAME', '$CO_COURSE_NAME', '$CO_DEGREE_RECEIVE', '$CO_BOOK_NO', '$CO_BOOK_DATE','$TRN_POINT')";
			$db_dpis1->send_cmd($cmd);
			//$db_dpis1->show_error(); echo "<hr>";
			//echo "<hr>".$cmd;
		}	// end while
	}
	
	if($command == "DELETE_COMMAND" && trim($CO_ID) ){
		$cmd = " delete from PER_COURSEDTL where CO_ID=$CO_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
			
		$cmd = " delete from PER_COURSE where CO_ID=$CO_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลการอบรม [".trim($CO_ID)." : ".$TR_CODE."]");
		#############
		$CO_ID = "";
		#############
	}
	//=======================================================================================
	
	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
//echo "setflagshow = $setflagshow<br>";
		$cmd = " update PER_COURSEDTL set COD_RESULT = 0 where CO_ID=$CO_ID and PER_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error(); echo "<br>";
		$cmd = " update PER_COURSEDTL set COD_RESULT = 1 where CO_ID=$CO_ID and PER_ID in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error(); echo "<br>";
		
		$setflagpass =  implode(",",$list_pass_id);
//echo "setflagshow = $setflagshow<br>";
		$cmd = " update PER_COURSEDTL set COD_PASS = 0 where CO_ID=$CO_ID and PER_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error(); echo "<br>";
		$cmd = " update PER_COURSEDTL set COD_PASS = 1 where CO_ID=$CO_ID and PER_ID in (".stripslashes($setflagpass).") ";
		$db_dpis->send_cmd($cmd);
//$db_dpis->show_error(); echo "<br>";
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลผู้เข้าฝึกอบรม [".trim($CO_ID)." : ".$setflagshow." : ".$setflagpass."]");
	}	

	//---Add person		สามารถเพิ่มผู้เข้าฝึกอบรมได้ครั้งละหลายๆคน
//	echo "command=$command, SELECTED_PER_ID=".trim($SELECTED_PER_ID)." CO_ID=".trim($CO_ID)."<br>";
	if($command == "ADD" && trim($SELECTED_PER_ID)){		//if($command == "ADD" && trim($PER_ID)){
		$CO_ID = trim($CO_ID);
		if(trim($SELECTED_PER_ID)){
			$ARR_SELECTED_PER_ID = explode(",",$SELECTED_PER_ID);
//			$cmd = " select CO_ID, PER_ID  from PER_COURSEDTL  where CO_ID=$CO_ID and PER_ID in ($SELECTED_PER_ID) ";
			$cmd = " select CO_ID, PER_ID  from PER_COURSEDTL  where CO_ID=$CO_ID ";
			$count_duplicate = $db_dpis->send_cmd($cmd);
//			echo "$cmd ($count_duplicate)<br>";
			if($count_duplicate > 0){
				while($data = $db_dpis->get_array()){
					//เปรียบเทียบค่า PER_ID ที่มีอยู่ใน PER_COURSEDTL กับ PER_ID ที่เลือกมา			
//					echo ">> per_id:".$data[PER_ID]." in ".$SELECTED_PER_ID."<br>";
					if(in_array($data[PER_ID],$ARR_SELECTED_PER_ID)){ 	
						$ARR_EXIST_PER_ID[] = $data[PER_ID];
					} else {
						$cmd1 = " delete from PER_COURSEDTL where CO_ID=$CO_ID and PER_ID=".$data[PER_ID]." ";
						$db_dpis1->send_cmd($cmd1);
//						echo "not select : $cmd1 <br>";
					}
				} //end while
				$ARR_UNEXIST_PER_ID = array_diff($ARR_SELECTED_PER_ID,$ARR_EXIST_PER_ID);
			}else{
				$ARR_UNEXIST_PER_ID = $ARR_SELECTED_PER_ID;
			}

			//เพิ่มเฉพาะ PER_ID ที่ยังไม่มี
			if(is_array($ARR_UNEXIST_PER_ID)){
				foreach($ARR_UNEXIST_PER_ID as $key=>$value){	//for($i=0; $i < count($ARR_UNEXIST_PER_ID); $i++){	
					$cmd = " insert into PER_COURSEDTL (CO_ID, PER_ID, COD_RESULT, UPDATE_USER, UPDATE_DATE, COD_PASS) 
									  values ($CO_ID, $value, 1, $SESS_USERID, '$UPDATE_DATE', 1) ";
					$db_dpis->send_cmd($cmd);
					//echo "<br>$cmd<br>";
					//$db_dpis->show_error();
					$RESULT_PER_ID .=  $value.",";	//$ARR_UNEXIST_PER_ID[$i]
				}
			}			
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลผู้เข้าฝึกอบรม [".$CO_ID." : ".substr($RESULT_PER_ID,0,-1)."]");
		
			if(is_array($ARR_EXIST_PER_ID)){	
				$err_text = "รหัสผู้เข้าฝึกอบรมซ้ำ [".$CO_ID." : ".implode(",", $ARR_EXIST_PER_ID)."]";	
			}
		}
//		$SELECTED_PER_ID = "";	//เคลียร์ค่าเดิมที่เลือกไว้ให้เป็นว่าง

		/**************** $cmd = " select CO_ID, PER_ID  from PER_COURSEDTL  where CO_ID=$CO_ID and PER_ID=$PER_ID ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		if($count_duplicate <= 0){
			$cmd = " insert into PER_COURSEDTL (CO_ID, PER_ID, COD_RESULT, UPDATE_USER, UPDATE_DATE) values ($CO_ID, $PER_ID, 0, $SESS_USERID, '$UPDATE_DATE') ";
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลผู้เข้าฝึกอบรม [".$CO_ID." : ".$PER_ID."]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "รหัสผู้เข้าฝึกอบรมซ้ำ [".$CO_ID." : ".$PER_ID."]";
		} // endif   *************/
	}

	if($command == "DELETE" && trim($PER_ID)){
		$cmd = " delete from PER_COURSEDTL where CO_ID=$CO_ID and PER_ID=$PER_ID";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลผู้เข้าฝึกอบรม [".trim($CO_ID)." : ".$PER_ID."]");
	}
	
	if ( trim($CO_ID) ) {
		$cmd = " select 	CO_NO, CO_STARTDATE, CO_ENDDATE, CO_PLACE, CT_CODE, CO_ORG, 
										CO_FUND, CT_CODE_FUND, CO_TYPE, CO_CONFIRM, DEPARTMENT_ID, CO_DAY, CO_REMARK, 
										CO_PROJECT_NAME, CO_COURSE_NAME, CO_DEGREE_RECEIVE, CO_BOOK_NO, CO_BOOK_DATE, TR_CODE
						 from 		PER_COURSE 
						 where 	trim(CO_ID)=$CO_ID ";
		$cnt = $db_dpis->send_cmd($cmd);
//		echo "CO $cmd ($cnt)<br>";
		//$db_dpis->show_error();	
		$data = $db_dpis->get_array();
		$CO_NO = trim($data[CO_NO]);
		$CO_PLACE = trim($data[CO_PLACE]);
		$CO_ORG = trim($data[CO_ORG]);
		$CO_FUND = trim($data[CO_FUND]);
		$CO_TYPE = trim($data[CO_TYPE]);
		$CO_CONFIRM = trim($data[CO_CONFIRM]);
		$CO_DAY = trim($data[CO_DAY]);
		$CO_REMARK = trim($data[CO_REMARK]);
		$CO_PROJECT_NAME = trim($data[CO_PROJECT_NAME]);
		$CO_COURSE_NAME = trim($data[CO_COURSE_NAME]);
		$CO_DEGREE_RECEIVE = trim($data[CO_DEGREE_RECEIVE]);
		$CO_BOOK_NO = trim($data[CO_BOOK_NO]);

		$CO_STARTDATE = show_date_format($data[CO_STARTDATE], 1);
		$CO_ENDDATE = show_date_format($data[CO_ENDDATE], 1);
		$CO_BOOK_DATE = show_date_format($data[CO_BOOK_DATE], 1);

//		echo "in if ( trim($CO_ID) )  : SELECTED_PER_ID=$SELECTED_PER_ID<br>";
		$ARR_SELECTED_PER_ID = (array) null;
		$cmd = " select CO_ID, PER_ID  from PER_COURSEDTL  where CO_ID=$CO_ID ";
		$count_per = $db_dpis1->send_cmd($cmd);
		if($count_per > 0){
			while($data1 = $db_dpis1->get_array()){
				//เปรียบเทียบค่า PER_ID ที่มีอยู่ใน PER_COURSEDTL กับ PER_ID ที่เลือกมา
				$ARR_SELECTED_PER_ID[] = $data1[PER_ID];
			} //end while
		}
		$SELECTED_PER_ID = implode(",",$ARR_SELECTED_PER_ID);

		$CT_CODE = trim($data[CT_CODE]);
		$cmd = "select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$CT_NAME = $data_dpis2[CT_NAME];
		
		$CT_CODE_FUND = trim($data[CT_CODE_FUND]);
		$cmd = "select CT_NAME from PER_COUNTRY where CT_CODE='$CT_CODE_FUND' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$CT_NAME_FUND = $data_dpis2[CT_NAME];

		$TR_CODE = trim($data[TR_CODE]);
		$cmd = "select TR_NAME from PER_TRAIN where TR_CODE='$TR_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$TR_NAME = $data_dpis2[TR_NAME];

		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
		if($DEPARTMENT_ID){	//กรณีที่เพิ่มของ กทม แบบไม่มี DEPARTMENT_ID ก็จะเอาค่า MINISTRY_NAME จาก session load_per_control
			$cmd = " select ORG_NAME,ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$DEPARTMENT_NAME = $data[ORG_NAME];
			$MINISTRY_ID = $data[ORG_ID_REF];
	
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$MINISTRY_NAME = $data[ORG_NAME];
		}

	}	
	
	if( (!$CO_ID) ){
		$PER_ID = "";
		$SELECTED_PER_ID = "";
		$TR_NAME = "";
		$CO_NO = "";
//		$CO_STARTDATE = "";
//		$CO_ENDDATE = ""
		$CO_STARTDATE = show_date_format($UPDATE_DATE,1);
		$CO_ENDDATE = show_date_format($UPDATE_DATE,1);
		$CO_PLACE = "";
		$CT_NAME = "";
		$CO_ORG = "";
		$CO_FUND = "";
		$CT_NAME_FUND = "";
		$CO_TYPE = "";
		$CO_CONFIRM = 0;
		$CO_DAY = "";
		$CO_REMARK = "";
		$CO_PROJECT_NAME="";
		$CO_COURSE_NAME="";
		$CO_DEGREE_RECEIVE="";
		$CO_BOOK_NO="";
		$CO_BOOK_DATE = "";

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