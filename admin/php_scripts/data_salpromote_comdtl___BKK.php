<? 
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");	
	include("php_scripts/load_per_control.php");	
	
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

	//----------------------------------------------------------------------------------
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

	$COM_SEND_STATUS = "";
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
			$COM_SEND_STATUS = "S";
			break;
	} // end switch case
//----------------------------------------------------------------------------------

	if(!$SALQ_YEAR&&$_POST[SALQ_YEAR])	$SALQ_YEAR = $_POST[SALQ_YEAR];	
	$COM_PER_TYPE = (trim($COM_PER_TYPE))? $COM_PER_TYPE : 2;
	$SALQ_TIME = (trim($SALQ_TIME))? $SALQ_TIME : 1;	

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	if (!$search_org_id) $search_org_id = "NULL";
	//---------------------------------------------------------------------------------------------------------------------------------------------------
	if ($COM_SITE=="personal_workflow" && $ORG_ID) {
		$search_org_id = $ORG_ID;
		$search_org_name = $ORG_NAME;
	} else if ($SESS_USERGROUP_LEVEL < 5){
	//		$search_org_id = "0";
	//		$search_org_name = "";
	}
	if($ORG_ID=="" || $ORG_ID==0 && $search_org_id)	$ORG_ID=$search_org_id;
	if (is_null($ORG_ID) || trim($ORG_ID)=="" || $ORG_ID=="NULL") 	$ORG_ID=0;
	//	if (($COM_SITE=="personal_workflow" && $ORG_ID) || ($SESS_USERGROUP_LEVEL < 5 && $ORG_ID)) {
	if (is_null($search_org_id) || trim($search_org_id)=="" || $search_org_id=="NULL")		$search_org_id="0";
	//---------------------------------------------------------------------------------------------------------------------------------------------------
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$COM_PER_TYPE = (trim($COM_PER_TYPE))? $COM_PER_TYPE : 1;	
	$list_type = (trim($list_type))? $list_type : 4;	
	if ($COM_PER_TYPE==1)	$COM_LEVEL_SALP = (trim($COM_LEVEL_SALP))? $COM_LEVEL_SALP : 1; 
	else $COM_LEVEL_SALP = (trim($COM_LEVEL_SALP))? $COM_LEVEL_SALP : 7; 
	
	if($COM_PER_TYPE == 1){
		$position_table = "PER_POSITION";
		$position_join = "b.POS_ID=c.POS_ID";
	}elseif($COM_PER_TYPE == 2){
		$position_table = "PER_POS_EMP";
		$position_join = "b.POEM_ID=c.POEM_ID";
	}elseif($COM_PER_TYPE == 3){
		$position_table = "PER_POS_EMPSER";
		$position_join = "b.POEMS_ID=c.POEMS_ID";
	}elseif($COM_PER_TYPE == 4){
		$position_table = "PER_POS_TEMP";
		$position_join = "b.POT_ID=c.POT_ID";
	} // end if

	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) ){
		$cmd = " select max(COM_ID) as max_id from PER_COMMAND ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$COM_ID = $data[max_id] + 1;

		$COM_DATE =  save_date($COM_DATE);

		// SALQ_YEAR เอาไปใส่ตรงไหน ????   !!!!!!!!!!!!ยังไม่มีฟิล์ดที่จะเอา SALQ_YEAR ไปเก็บใน table
		$cmd = " insert into PER_COMMAND (COM_ID, COM_NO, COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
						COM_TYPE, COM_CONFIRM, COM_STATUS, DEPARTMENT_ID, ORG_ID, UPDATE_USER, UPDATE_DATE, COM_LEVEL_SALP) 
						VALUES ($COM_ID, '$COM_NO', '$COM_NAME', '$COM_DATE', '$COM_NOTE', $COM_PER_TYPE, 
						'$COM_TYPE', 0, '', $DEPARTMENT_ID, $search_org_id, $SESS_USERID, '$UPDATE_DATE', $COM_LEVEL_SALP) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		//echo "<br>$cmd<br>=======================<br>";

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลบัญชีแนบท้ายคำสั่งเลื่อนขั้นเงินเดือน [$DEPARTMENT_ID : ".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	
//		echo "$search_org_id // $RPT_N // $SALQ_YEAR //   $ORG_ID --- $ORG_ID_ASS ";

		// ===== เริ่มต้น insert ข้อมูลจากผู้สมควรได้เลื่อนขั้นเงินเดือน เข้าสู่ข้อมูลบัญชีแนบท้ายคำสั่ง (table PER_COMDTL) =====
		if ($RPT_N) {
			if($_POST[CT_CODE]) $CT_CODE=$_POST[CT_CODE];	 
			if($_POST[PV_CODE]) $PV_CODE=$_POST[PV_CODE];

			if ($list_type==1) $where = " and d.OT_CODE = '01' ";		
			elseif ($list_type==2) $where = " and d.PV_CODE = '$PV_CODE' ";		
			elseif ($list_type==3) $where = " and d.CT_CODE = '$CT_CODE' ";		

			if ($COM_LEVEL_SALP == 1)					// 1=ระดับปฏิบัติงาน ชำนาญงาน อาวุโส ปฏิบัติการ ชำนาญการ ชำนาญการพิเศษ อำนวยการระดับต้น
				$where_com_level_salp = " and b.PER_STATUS=1 and SALP_YN=1 and b.LEVEL_NO in ('O1','O2','O3','K1','K2','K3','D1') ";		
			elseif ($COM_LEVEL_SALP == 2)					// 2=ระดับทักษะพิเศษ เชี่ยวชาญ ทรงคุณวุฒิ อำนวยการระดับสูง บริหารระดับต้น บริหารระดับสูง
				$where_com_level_salp = " and b.PER_STATUS=1 and SALP_YN=1 and b.LEVEL_NO in ('O4','K4','K5','D2','M1','M2') ";
			elseif ($COM_LEVEL_SALP == 3) 				// 3=เงินเพิ่มพิเศษ ระดับปฏิบัติงาน ชำนาญงาน อาวุโส ปฏิบัติการ ชำนาญการ ชำนาญการพิเศษ อำนวยการระดับต้น
				$where_com_level_salp = " and b.PER_STATUS=1 and SALP_PERCENT>0 and b.LEVEL_NO in ('O1','O2','O3','K1','K2','K3','D1') ";	
			elseif ($COM_LEVEL_SALP == 4)					// 4=เงินเพิ่มพิเศษ ระดับทักษะพิเศษ เชี่ยวชาญ ทรงคุณวุฒิ อำนวยการระดับสูง บริหารระดับต้น บริหารระดับสูง
				$where_com_level_salp = " and b.PER_STATUS=1 and SALP_PERCENT>0 and b.LEVEL_NO in ('O4','K4','K5','D2','M1','M2') ";	
			elseif ($COM_LEVEL_SALP == 5)					// 5=เกษียณ
				$where_com_level_salp = " and b.PER_STATUS=0 ";					
			elseif ($COM_LEVEL_SALP == 6)					// 6=ข้าราชการทั้งหมด
				$where_com_level_salp = "";		
			elseif ($COM_LEVEL_SALP == 7)					// 7=ปกติ
				$where_com_level_salp = " and b.PER_STATUS=1 and SALP_YN=1 ";
			elseif ($COM_LEVEL_SALP == 8)					// 8=เงินเพิ่มพิเศษ
				$where_com_level_salp = " and b.PER_STATUS=1 and SALP_PERCENT>0 ";
			elseif ($COM_LEVEL_SALP == 9)					// 9=เกษียณ
				$where_com_level_salp = " and b.PER_STATUS=0 ";
			elseif ($COM_LEVEL_SALP == 10)				// 10=ลูกจ้างประจำหรือพนักงานราชการทั้งหมด
				$where_com_level_salp = "";
		} else {
			if ($COM_LEVEL_SALP == 1)					// 1=ระดับ 8 ลงมา
				$where_com_level_salp = " and b.PER_STATUS=1 and SALP_YN=1 and b.LEVEL_NO_SALARY <= '08' ";		
			elseif ($COM_LEVEL_SALP == 2)					// 2=ระดับ 9 ขึ้นไป
				$where_com_level_salp = " and b.PER_STATUS=1 and SALP_YN=1 and b.LEVEL_NO_SALARY >= '09' ";
			elseif ($COM_LEVEL_SALP == 3) 				// 3=เงินเพิ่มพิเศษ ระดับ 8 ลงมา
				$where_com_level_salp = " and b.PER_STATUS=1 and SALP_PERCENT>0 and b.LEVEL_NO_SALARY <= '08' ";	
			elseif ($COM_LEVEL_SALP == 4)					// 4=เงินเพิ่มพิเศษ ระดับ 9 ขึ้นไป
				$where_com_level_salp = " and b.PER_STATUS=1 and SALP_PERCENT>0 and b.LEVEL_NO_SALARY >= '09' ";	
			elseif ($COM_LEVEL_SALP == 5)					// 5=เกษียณ
				$where_com_level_salp = " and b.PER_STATUS=0 ";					
			elseif ($COM_LEVEL_SALP == 6)					// 6=ข้าราชการทั้งหมด
				$where_com_level_salp = "";		
			elseif ($COM_LEVEL_SALP == 7)					// 7=ปกติ
				$where_com_level_salp = " and b.PER_STATUS=1 and SALP_YN=1 ";
			elseif ($COM_LEVEL_SALP == 8)					// 8=เงินเพิ่มพิเศษ
				$where_com_level_salp = " and b.PER_STATUS=1 and SALP_PERCENT>0 ";
			elseif ($COM_LEVEL_SALP == 9)					// 9=เกษียณ
				$where_com_level_salp = " and b.PER_STATUS=0 ";
			elseif ($COM_LEVEL_SALP == 10)				// 10=ลูกจ้างประจำหรือพนักงานราชการทั้งหมด
				$where_com_level_salp = "";
		}		
		/*
		if ($COM_LEVEL_SALP == 5 || $COM_LEVEL_SALP == 9) {		// 5, 9=เกษียณ
			$where_com_level_salp = "";		
			$ORG_ID = $ORG_ID_ASS = "";
		}
		*/

		if ($ORG_ID || $ORG_ID==0) {					
			$cmd1 = " select	a.PER_ID, SALP_LEVEL, SALP_DATE, SALP_SALARY_OLD, SALP_SALARY_NEW, SALP_SPSALARY, 
							SALP_PERCENT, SALP_REMARK, b.LEVEL_NO, b.LEVEL_NO_SALARY, b.POS_ID, b.POEM_ID, b.POEMS_ID, b.POT_ID
					   from 	PER_SALPROMOTE a, PER_PERSONAL b, $position_table c , PER_ORG d
					   where 	a.SALQ_YEAR='$SALQ_YEAR' and a.SALQ_TYPE=$SALQ_TYPE and a.PER_ID=b.PER_ID and  
									$position_join and c.ORG_ID = d.ORG_ID and b.DEPARTMENT_ID = $DEPARTMENT_ID
						$where_com_level_salp $where ";
		} elseif ($ORG_ID_ASS) {			
			$cmd1 = " select	a.PER_ID, SALP_LEVEL, SALP_DATE, SALP_SALARY_OLD, SALP_SALARY_NEW, SALP_SPSALARY, 
							SALP_PERCENT, SALP_REMARK, b.LEVEL_NO, b.LEVEL_NO_SALARY, b.POS_ID, b.POEM_ID, b.POEMS_ID, b.POT_ID
					   from 	PER_SALPROMOTE a, PER_PERSONAL b, PER_ORG_ASS d 
					   where 	a.SALQ_YEAR='$SALQ_YEAR' and a.SALQ_TYPE=$SALQ_TYPE and a.PER_ID=b.PER_ID and 
							b.ORG_ID = d.ORG_ID and b.DEPARTMENT_ID = $DEPARTMENT_ID
							$where_com_level_salp $where ";		
		}
		$count_temp = $db_dpis->send_cmd($cmd1);
//			$db_dpis->show_error();
//		echo "==========>         $cmd1<br>";

		$cmd_seq = 0;
		while ($data = $db_dpis->get_array()) {
			$cmd_seq++;
			$TMP_PER_ID = $data[PER_ID] + 0;		
			$CMD_OLD_SALARY = $data[SALP_SALARY_OLD] + 0;
			$CMD_SALARY = $data[SALP_SALARY_NEW] + 0;
			$CMD_SPSALARY = $data[SALP_SPSALARY] + 0;
			$TMP_SALP_LEVEL = $data[SALP_LEVEL] + 0;
			if ($TMP_SALP_LEVEL == 0.5) 		$MOV_CODE = "21310";
			elseif ($TMP_SALP_LEVEL == 1)		$MOV_CODE = "21320";
			elseif ($TMP_SALP_LEVEL == 1.5) 	$MOV_CODE = "21330";
			elseif ($TMP_SALP_LEVEL == 2) 		$MOV_CODE = "21340";
			$TMP_SALP_PERCENT = $data[SALP_PERCENT] + 0;				
			if ($TMP_SALP_PERCENT == 2)		$MOV_CODE = "21420";
			elseif ($TMP_SALP_PERCENT == 4)	$MOV_CODE = "21430";
			//$tmp_date = explode("-", substr(trim($data[SALP_DATE]), 0, 10));
			$CMD_DATE = trim($data[SALP_DATE]);
			$CMD_NOTE1 = trim($data[SALP_REMARK]);
			
			$CMD_LEVEL = $LEVEL_NO = trim($data[LEVEL_NO])? "'".$data[LEVEL_NO]."'" : "NULL";
			$LEVEL_NO_SALARY = trim($data[LEVEL_NO_SALARY])? "'".$data[LEVEL_NO_SALARY]."'" : "NULL";
			$POS_ID = (trim($data[POS_ID]))? $data[POS_ID] : "NULL";
			$POEM_ID = (trim($data[POEM_ID]))? $data[POEM_ID] : "NULL";
			$POEMS_ID = (trim($data[POEMS_ID]))? $data[POEMS_ID] : "NULL";
			$POT_ID = (trim($data[POT_ID]))? $data[POT_ID] : "NULL";
			$EN_CODE = $PL_CODE_ASSIGN = $PN_CODE_ASSIGN = $EP_CODE_ASSIGN = "NULL";
			if ($COM_PER_TYPE==1) {
				$cmd = " 	select POS_NO, PL_NAME, PM_CODE, ORG_ID, ORG_ID_1, ORG_ID_2, POS_NO_NAME
									from PER_POSITION a, PER_LINE b 
									where POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POS_NO = trim($data2[POS_NO]);
				$POS_NO_NAME =  trim($data2[POS_NO_NAME]);
				$PL_NAME = trim($data2[PL_NAME]);
				$PM_CODE = trim($data2[PM_CODE]);
				if ($PL_NAME)				$CMD_POSITION = "$PL_NAME";
				if ($POS_NO)					$CMD_POS_NO = $POS_NO;
				if ($POS_NO_NAME)	$CMD_POS_NO_NAME = $POS_NO_NAME;

				$cmd = " select PM_NAME from PER_MGT where PM_CODE='$PM_CODE' ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$PM_NAME = $data1[PM_NAME];
	
				$cmd = " select POSITION_LEVEL from PER_PERSONAL a, PER_LEVEL b where a.POS_ID=$POS_ID and a.LEVEL_NO=b.LEVEL_NO ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$POSITION_LEVEL = $data1[POSITION_LEVEL];
	
				if ($PM_NAME) $PL_NAME_WORK = $PM_NAME." (".$PL_NAME.$POSITION_LEVEL.")";
				else $PL_NAME_WORK = $PL_NAME.$POSITION_LEVEL;
			} elseif ($COM_PER_TYPE==2) {
				$cmd = " 	select POEM_NO, PN_NAME, ORG_ID, ORG_ID_1, ORG_ID_2 , POEM_NO_NAME
									from PER_POS_EMP a, PER_POS_NAME b 
									where POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POEM_NO = trim($data2[POEM_NO]);
				$POEM_NO_NAME = trim($data2[POEM_NO_NAME]);
				$PN_NAME = trim($data2[PN_NAME]);
				if ($PN_NAME)				$CMD_POSITION = "$PN_NAME";
				if ($POEM_NO)				$CMD_POS_NO = $POEM_NO;
				if ($POEM_NO_NAME)	$CMD_POS_NO_NAME = $POEM_NO_NAME;

				$cmd = " select POSITION_LEVEL from PER_PERSONAL a, PER_LEVEL b where a.POEM_ID=$POEM_ID and a.LEVEL_NO=b.LEVEL_NO ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$POSITION_LEVEL = $data1[POSITION_LEVEL];
		
				$PL_NAME_WORK = $PN_NAME.' '.$POSITION_LEVEL;
			} elseif ($COM_PER_TYPE==3) {
				$cmd = " 	select POEMS_NO, EP_NAME, ORG_ID, ORG_ID_1, ORG_ID_2 , POEMS_NO_NAME
									from PER_POS_EMPSER a, PER_EMPSER_POS_NAME b 
									where POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POEMS_NO = trim($data2[POEMS_NO]);
				$POEMS_NO_NAME = trim($data2[POEMS_NO_NAME]);
				$EP_NAME = trim($data2[EP_NAME]);
				if ($EP_NAME)					$CMD_POSITION = "$EP_NAME";
				if ($POEMS_NO)				$CMD_POS_NO = $POEMS_NO;
				if ($POEMS_NO_NAME)	$CMD_POS_NO_NAME = $POEMS_NO_NAME;

				$cmd = " select POSITION_LEVEL from PER_PERSONAL a, PER_LEVEL b where a.POEMS_ID=$POEMS_ID and a.LEVEL_NO=b.LEVEL_NO ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$POSITION_LEVEL = $data1[POSITION_LEVEL];
		
				$PL_NAME_WORK = $EP_NAME.' '.$POSITION_LEVEL;
			} elseif ($COM_PER_TYPE==4) {
				$cmd = " 	select POT_NO, TP_NAME, ORG_ID, ORG_ID_1, ORG_ID_2 , POT_NO_NAME
									from PER_POS_TEMP a, PER_TEMP_POS_NAME b 
									where POT_ID=$POT_ID and a.TP_CODE=b.TP_CODE ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$POT_NO = trim($data2[POT_NO]);		
				$POT_NO_NAME = trim($data2[POT_NO_NAME]);			
				$TP_NAME = trim($data2[TP_NAME]);
				if ($TP_NAME)				$CMD_POSITION = "$TP_NAME";
				if ($POT_NO)					$CMD_POS_NO = $POT_NO;
				if ($POT_NO_NAME)	$CMD_POS_NO_NAME = $POT_NO_NAME;

				$cmd = " select POSITION_LEVEL from PER_PERSONAL a, PER_LEVEL b where a.POT_ID=$POT_ID and a.LEVEL_NO=b.LEVEL_NO ";
				$db_dpis1->send_cmd($cmd);
				$data1 = $db_dpis1->get_array();
				$POSITION_LEVEL = $data1[POSITION_LEVEL];
		
				$PL_NAME_WORK = $TP_NAME.' '.$POSITION_LEVEL;
			}
			$ORG_ID_1 = (trim($data2[ORG_ID]))? trim($data2[ORG_ID]) : 0;
			$ORG_ID_2 = (trim($data2[ORG_ID_1]))? trim($data2[ORG_ID_1]) : 0;
			$ORG_ID_3 = (trim($data2[ORG_ID_2]))? trim($data2[ORG_ID_2]) : 0;
			$CMD_ORG3 = $CMD_ORG4 = $CMD_ORG5 = "";
			$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID IN ($ORG_ID_1, $ORG_ID_2, $ORG_ID_3) ";
			$db_dpis2->send_cmd($cmd);
			while ( $data2 = $db_dpis2->get_array() ) {
				$temp_id = trim($data2[ORG_ID]);
				$CMD_ORG3 = ($temp_id == $ORG_ID_1)?  trim($data2[ORG_NAME]) : $CMD_ORG3;
				$CMD_ORG4 = ($temp_id == $ORG_ID_2)?  trim($data2[ORG_NAME]) : $CMD_ORG4;
				$CMD_ORG5 = ($temp_id == $ORG_ID_3)?  trim($data2[ORG_NAME]) : $CMD_ORG5;						
			}
				
			$cmd = " select OT_CODE from PER_ORG where ORG_ID=$ORG_ID_1 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$OT_CODE = trim($data2[OT_CODE]);
	
			if ($CMD_ORG3=="-") $CMD_ORG3 = "";
			if ($CMD_ORG4=="-") $CMD_ORG4 = "";
			if ($CMD_ORG5=="-") $CMD_ORG5 = "";
			if ($OT_CODE == "03") 
				if (!$CMD_ORG5 && !$CMD_ORG4 && $search_department_name=="กรมการปกครอง") 
					$ORG_NAME_WORK = "ที่ทำการปกครอง".$CMD_ORG3." ".$CMD_ORG3;
				else 
					$ORG_NAME_WORK = trim($CMD_ORG5." ".$CMD_ORG4." ".$CMD_ORG3);
			elseif ($OT_CODE == "01") $ORG_NAME_WORK = trim($CMD_ORG4." ".$CMD_ORG3." ".$search_department_name);
			else $ORG_NAME_WORK = trim($CMD_ORG4." ".$CMD_ORG3);

//echo "<br>$search_org_id : $ORG_ID_1 == $COM_LEVEL_SALP - $CMD_SALARY > $CMD_OLD_SALARY<br>";

			if (!$search_org_id || $search_org_id == $ORG_ID_1) { 
				if ((($COM_LEVEL_SALP==3 || $COM_LEVEL_SALP==4 || $COM_LEVEL_SALP==8) && $CMD_SPSALARY > 0) || 
					($COM_LEVEL_SALP!=3 && $COM_LEVEL_SALP!=4 && $COM_LEVEL_SALP!=8 && $CMD_SALARY > $CMD_OLD_SALARY)) {
					$cmd = " insert into PER_COMDTL (COM_ID, CMD_SEQ, PER_ID, EN_CODE, CMD_DATE, CMD_POSITION, 
									CMD_LEVEL, CMD_ORG1, CMD_ORG2, CMD_ORG3, CMD_ORG4, CMD_ORG5, CMD_OLD_SALARY,  
									POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, CMD_SALARY, CMD_SPSALARY, PL_CODE_ASSIGN, 
									PN_CODE_ASSIGN, EP_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2, MOV_CODE, CMD_SAL_CONFIRM,   
									UPDATE_USER, UPDATE_DATE, LEVEL_NO_SALARY, PL_NAME_WORK, ORG_NAME_WORK ,CMD_POS_NO_NAME, CMD_POS_NO)
									values ($COM_ID, $cmd_seq, $TMP_PER_ID, $EN_CODE, '$CMD_DATE', '$CMD_POSITION', 
									$CMD_LEVEL, '$search_ministry_name', '$search_department_name', '$CMD_ORG3', '$CMD_ORG4', '$CMD_ORG5', 
									$CMD_OLD_SALARY, $POS_ID, $POEM_ID, $POEMS_ID, $LEVEL_NO, $CMD_SALARY, $CMD_SPSALARY, 
									$PL_CODE_ASSIGN, $PN_CODE_ASSIGN, $EP_CODE_ASSIGN, '$CMD_NOTE1', '$CMD_NOTE2', '$MOV_CODE', 0, 
									$SESS_USERID, '$UPDATE_DATE', $LEVEL_NO_SALARY, '$PL_NAME_WORK', '$ORG_NAME_WORK','$CMD_POS_NO_NAME', '$CMD_POS_NO') ";
					$db_dpis1->send_cmd($cmd);				
					//$db_dpis1->show_error();
//	echo "<br>$cmd<br>=======================<br>";
				}
			}
		}	// end while
		if ($count_temp)
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลรายละเอียดบัญชีแนบท้ายคำสั่งเลื่อนขั้นเงินเดือน [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");		
		// ===== สิ้นสุด insert ข้อมูลจากผู้สมควรได้เลื่อนขั้นเงินเดือน เข้าสู่ข้อมูลบัญชีแนบท้ายคำสั่ง (table PER_COMDTL) ===== 

		// ===== สิ้นสุด insert ข้อมูลจากผู้สมควรได้เลื่อนขั้นเงินเดือน เข้าสู่ข้อมูลบัญชีแนบท้ายคำสั่ง (table PER_COMDTL) ===== 
		$cmd = " select * from PER_COMDTL where COM_ID = $COM_ID ";
		$count_comdtl = $db_dpis->send_cmd($cmd);
		// เมื่อเพิ่มข้อมูล เรียบร้อย ก็ส่งค่ากลับโปรแกรมหลัก ให้อยู่ใน mode เพิ่มตัวต่อไป
		echo "<script>";
		echo "parent.refresh_opener('2<::>$COM_ID<::>$COM_NAME<::>$search_org_id<::>$COM_PER_TYPE<::><::>$count_comdtl<::><::>?UPD=1<::>')";
		echo "</script>";
	}			// 	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) )


	if( $command == "UPDATE" && trim($COM_ID) ) {
		$COM_DATE =  save_date($COM_DATE);

		$cmd = " update PER_COMMAND set  
						COM_NO='$COM_NO', COM_NAME='$COM_NAME', COM_DATE='$COM_DATE', COM_NOTE='$COM_NOTE', 
						COM_PER_TYPE=$COM_PER_TYPE, COM_TYPE='$COM_TYPE', 
						UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
				where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลบัญชีแนบท้ายคำสั่งเลื่อนขั้นเงินเดือน [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}			// 	if( $command == "UPDATE" && trim($COM_ID) )
	

// ============================================================
	// เมื่อมีการยืนยันข้อมูลของปัญชีแนบท้ายคำสั่ง
	if( $command == "COMMAND" && trim($COM_ID) ) {
		$COM_DATE =  save_date($COM_DATE);

		// ให้ insert ข้อมูลจาก per_comdtl ไป per_personal และ per_positionhis
		$cmd = "  	select 	PER_ID, CMD_DATE, POS_ID, POEM_ID, POEMS_ID, POT_ID, LEVEL_NO, CMD_OLD_SALARY, CMD_SALARY, 
							CMD_SPSALARY, MOV_CODE, PL_CODE_ASSIGN, PN_CODE_ASSIGN, 
							EP_CODE_ASSIGN, CMD_NOTE1, CMD_NOTE2, LEVEL_NO_SALARY, CMD_PERCENT, 
							CMD_MIDPOINT, PL_NAME_WORK, ORG_NAME_WORK, CMD_SEQ_NO, CMD_SEQ, PER_CARDNO,CMD_POS_NO_NAME, CMD_POS_NO
					from		PER_COMDTL 
					where	COM_ID=$COM_ID";
		$db_dpis->send_cmd($cmd);
		while ($data = $db_dpis->get_array()) {
			$tmp_PER_ID = $data[PER_ID];
			$tmp_CMD_DATE = trim($data[CMD_DATE]);
			$tmp_POS_ID = $data[POS_ID];
			$tmp_POEM_ID = $data[POEM_ID];
			$tmp_POEMS_ID = $data[POEMS_ID];
			$tmp_POT_ID = $data[POT_ID];
			$tmp_LEVEL_NO = trim($data[LEVEL_NO]);
			$tmp_LEVEL_NO_SALARY = trim($data[LEVEL_NO_SALARY]);
			$tmp_CMD_OLD_SALARY = $data[CMD_OLD_SALARY];
			$tmp_CMD_SALARY = $data[CMD_SALARY];
			$tmp_CMD_SPSALARY = $data[CMD_SPSALARY];
			$tmp_MOV_CODE = trim($data[MOV_CODE]);
			$tmp_PER_CARDNO = trim($data[PER_CARDNO]);
			$tmp_PL_CODE_ASSIGN = trim($data[PL_CODE_ASSIGN]);
			$tmp_PN_CODE_ASSIGN = trim($data[PN_CODE_ASSIGN]);
			$tmp_EP_CODE_ASSIGN = trim($data[EP_CODE_ASSIGN]);		
			$tmp_CMD_NOTE1 = (trim($data[CMD_NOTE1]))? str_replace("'", "&rsquo;", trim($data[CMD_NOTE1])) : "";
			$tmp_CMD_NOTE2 = (trim($data[CMD_NOTE2]))? str_replace("'", "&rsquo;", trim($data[CMD_NOTE2])) : "";
			$tmp_CMD_PERCENT = $data[CMD_PERCENT];
			$tmp_CMD_SALARY_UP = $tmp_CMD_SALARY - $tmp_CMD_OLD_SALARY;
			$tmp_CMD_MIDPOINT = $data[CMD_MIDPOINT] + 0;
			$PL_NAME_WORK = trim($data[PL_NAME_WORK]);
			$ORG_NAME_WORK = trim($data[ORG_NAME_WORK]);
			$tmp_CMD_SEQ_NO = $data[CMD_SEQ_NO] + 0;
			$tmp_CMD_SEQ = $data[CMD_SEQ] + 0;
			if ($tmp_CMD_SEQ_NO==0) $tmp_CMD_SEQ_NO = $tmp_CMD_SEQ;
			$tmp_CMD_POSPOEM_NO_NAME = trim($data[CMD_POS_NO_NAME]); 
			$tmp_CMD_POSPOEM_NO = trim($data[CMD_POS_NO]); 
						
			$tmp_date = explode("-", $tmp_CMD_DATE);
			// 86400 วินาที = 1 วัน
			$before_cmd_date = (mktime(0, 0, 0, $tmp_date[1], substr($tmp_date[2],0,2), $tmp_date[0]) - 86400);
			$before_cmd_date = date("Y-m-d", $before_cmd_date);

			$PM_CODE = $PL_CODE = $LEVEL_NO = $PN_CODE = $EP_CODE = "";
			if (trim($tmp_POS_ID)) {									// ตำแหน่งข้าราชการ
				$cmd = "  select POS_NO,POS_NO_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, PM_CODE, PL_CODE 
							   from PER_POSITION where POS_ID=$tmp_POS_ID  ";
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SAH_POS_NO = trim($data2[POS_NO]);
				$SAH_POS_NO_NAME = trim($data2[POS_NO_NAME]);
				$PM_CODE = trim($data2[PM_CODE]);
				$PL_CODE = trim($data2[PL_CODE]);
			} elseif (trim($tmp_POEM_ID)) {						// ตำแหน่งลูกจ้างประจำ
				$cmd = "  select POEM_NO, POEM_NO_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, PN_CODE 
							   from PER_POS_EMP where POEM_ID=$tmp_POEM_ID  ";				
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SAH_POS_NO = trim($data2[POEM_NO]);
				$SAH_POS_NO_NAME = trim($data2[POEM_NO_NAME]);		
				$PN_CODE = trim($data2[PN_CODE]);	
			} elseif (trim($tmp_POEMS_ID)) {						// ตำแหน่งพนักงานราชการ
				$cmd = "  select POEMS_NO, POEMS_NO_NAME , ORG_ID, ORG_ID_1, ORG_ID_2, EP_CODE 
							   from PER_POS_EMPSER where POEMS_ID=$tmp_POEMS_ID  ";	
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SAH_POS_NO = trim($data2[POEMS_NO]);
				$SAH_POS_NO_NAME = trim($data2[POEMS_NO_NAME]);				
				$EP_CODE = trim($data2[EP_CODE]);							
			} elseif (trim($tmp_POT_ID)) {						// ตำแหน่งลูกจ้างชั่วคราว
				$cmd = "  select POT_NO, POT_NO_NAME, ORG_ID, ORG_ID_1, ORG_ID_2, TP_CODE 
							   from PER_POS_TEMP where POT_ID=$tmp_POT_ID  ";	
				$db_dpis2->send_cmd($cmd);
				$data2 = $db_dpis2->get_array();
				$SAH_POS_NO = trim($data2[POT_NO]);
				$SAH_POS_NO_NAME = trim($data2[POT_NO_NAME]);
				$TP_CODE = trim($data2[TP_CODE]);				   
			}
			$ORG_ID_3 = trim($data2[ORG_ID]);		
			$ORG_ID_4 = trim($data2[ORG_ID_1]);
			$ORG_ID_5 = trim($data2[ORG_ID_2]);

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_4 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_4 = $data2[ORG_NAME];

			$cmd = " select ORG_NAME from PER_ORG where ORG_ID = $ORG_ID_5 ";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_NAME_5 = $data2[ORG_NAME];				

			$cmd = "  select ORG_ID_REF, CT_CODE, PV_CODE, AP_CODE from PER_ORG where ORG_ID=$ORG_ID_3 and OL_CODE='03'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_ID_2 = trim($data2[ORG_ID_REF]);
			$CT_CODE = trim($data2[CT_CODE]);
			$PV_CODE = trim($data2[PV_CODE]);
			$AP_CODE = trim($data2[AP_CODE]);
			$cmd = "  select ORG_ID_REF from PER_ORG where ORG_ID=$ORG_ID_2 and OL_CODE='02'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$ORG_ID_1 = trim($data2[ORG_ID_REF]);
			
			// update status of PER_PERSONAL 
			$cmd = "update PER_PERSONAL set MOV_CODE='$tmp_MOV_CODE', PER_SALARY=$tmp_CMD_SALARY, 
						LEVEL_NO_SALARY='$tmp_LEVEL_NO_SALARY' 
					where PER_ID=$tmp_PER_ID ";
			$db_dpis1->send_cmd($cmd);	
//$db_dpis1->show_error();	
//echo "<br>";
			// update into PER_POSITION 
			$cmd = "	update PER_POSITION set POS_SALARY=$tmp_CMD_SALARY where POS_ID=$tmp_POS_ID ";
			$db_dpis1->send_cmd($cmd);
//$db_dpis1->show_error();	
//echo "<br>";				
				
			// update and insert into PER_SALARYHIS 
			if($DPISDB=="oci8")
				$cmd = " select SAH_ID, SAH_EFFECTIVEDATE from PER_SALARYHIS where PER_ID=$tmp_PER_ID order by substr(SAH_EFFECTIVEDATE,1,10) desc, SAH_SALARY desc, SAH_DOCNO desc ";
			else
				$cmd = " select SAH_ID, SAH_EFFECTIVEDATE from PER_SALARYHIS where PER_ID=$tmp_PER_ID order by left(SAH_EFFECTIVEDATE,10) desc, SAH_SALARY desc, SAH_DOCNO desc ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$tmp_SAH_ID = trim($data1[SAH_ID]);
			$tmp_SAH_EFFECTIVEDATE = trim($data1[SAH_EFFECTIVEDATE]);
			$tmp_SAH_ENDDATE = $tmp_CMD_DATE - 1;
			if (substr($before_cmd_date,0,10) < substr($tmp_SAH_EFFECTIVEDATE,0,10)) $before_cmd_date = $tmp_SAH_EFFECTIVEDATE;
			$cmd = " update PER_SALARYHIS set SAH_ENDDATE='$before_cmd_date', 
						UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE'			
					where SAH_ID=$tmp_SAH_ID";
			$db_dpis1->send_cmd($cmd);	
//$db_dpis1->show_error();							
//echo "<br>";

			$cmd = " UPDATE PER_SALARYHIS SET SAH_LAST_SALARY='N' WHERE PER_ID=$tmp_PER_ID ";
			$db_dpis1->send_cmd($cmd);

			if (!$tmp_CMD_PERCENT) $tmp_CMD_PERCENT = "NULL";
			if (!$tmp_CMD_SALARY_UP) $tmp_CMD_SALARY_UP = "NULL";
			if (!$tmp_CMD_SPSALARY) $tmp_CMD_SPSALARY = "NULL";
			$cmd = " select max(SAH_ID) as max_id from PER_SALARYHIS ";
			$db_dpis1->send_cmd($cmd);
			$data = $db_dpis1->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$SAH_ID = $data[max_id] + 1; 			 

			$SAH_SEQ_NO = 1;
			$SAH_REMARK = "NULL";
			$EX_CODE = "024";
			if (!$search_kf_year) $search_kf_year = "NULL";
			if (!$search_kf_cycle) $search_kf_cycle = "NULL";
			if (!$tmp_TOTAL_SCORE) $tmp_TOTAL_SCORE = "NULL";
			$cmd = "	insert into PER_SALARYHIS (SAH_ID, PER_ID, SAH_EFFECTIVEDATE, MOV_CODE, SAH_SALARY, SAH_DOCNO, 
							SAH_DOCDATE, SAH_ENDDATE, UPDATE_USER, UPDATE_DATE, SAH_PERCENT_UP, SAH_SALARY_UP, 
							SAH_SALARY_EXTRA, SAH_SEQ_NO, SAH_REMARK, LEVEL_NO, SAH_POS_NO, SAH_POSITION, SAH_ORG, 
							EX_CODE, SAH_PAY_NO, SAH_SALARY_MIDPOINT, SAH_KF_YEAR, SAH_KF_CYCLE, SAH_TOTAL_SCORE,
							SAH_LAST_SALARY, SM_CODE, SAH_CMD_SEQ, SAH_ORG_DOPA_CODE, SAH_OLD_SALARY, SAH_POS_NO_NAME) 
							values ($SAH_ID, $tmp_PER_ID, '$tmp_CMD_DATE', '$tmp_MOV_CODE', $tmp_CMD_SALARY, '$COM_NO',  
							'$COM_DATE', '', $SESS_USERID, '$UPDATE_DATE', $tmp_CMD_PERCENT, $tmp_CMD_SALARY_UP, 
							$tmp_CMD_SPSALARY, $SAH_SEQ_NO, $SAH_REMARK, '$tmp_LEVEL_NO', '$SAH_POS_NO', 
							'$PL_NAME_WORK', '$ORG_NAME_WORK', '$EX_CODE', '$SAH_POS_NO', $tmp_CMD_MIDPOINT, 
							$search_kf_year, $search_kf_cycle, $tmp_TOTAL_SCORE, 'Y', '$SM_CODE', $tmp_CMD_SEQ_NO, 
							'$SAH_ORG_DOPA_CODE', $tmp_CMD_OLD_SALARY, '$SAH_POS_NO_NAME') ";
			$db_dpis1->send_cmd($cmd);
//$db_dpis1->show_error();	
//echo "<br>";		
		}	// 		while ($data = $db_dpis->get_array())		
		
		$cmd = " update PER_COMMAND set  
						COM_CONFIRM=1, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
				where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);		

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลการดำรงตำแหน่ง และเพิ่มประวัติการดำรงตำแหน่งเมื่อยืนยันข้อมูลบัญชีแนบท้ายคำสั่งเลื่อนขั้นเงินเดือน [$COM_ID : $PER_ID : $POH_ID : $SAH_ID]");
	
		$cmd = " select * from PER_COMDTL where COM_ID = $COM_ID ";
		$count_comdtl = $db_dpis->send_cmd($cmd);
		// เมื่อเพิ่มข้อมูล เรียบร้อย ก็ส่งค่ากลับโปรแกรมหลัก ให้อยู่ใน mode เพิ่มตัวต่อไป

		echo "<script>";
		echo "parent.refresh_opener('2<::>!<::>!<::>!<::>!<::><::>!<::>1<::>?UPD=1<::>')";
		echo "</script>";
	}		// 	if ($COM_CONFIRM == 1 && ($command=="ADD" || $command=="UPDATE")) 	
// ============================================================
	// เมื่อมีการส่งจากภูมิภาค
	if( $command == "SEND" && trim($COM_ID) && $SESS_USERGROUP_LEVEL > 4 ) {
		$cmd = " update PER_COMMAND set  
						COM_STATUS='$COM_SEND_STATUS', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
						where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);	
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ส่งข้อมูลบัญชีแนบท้ายคำสั่งย้าย [$DEPARTMENT_ID : ".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}		// 	if( $command == "SEND" && trim($COM_ID) ) 	
	
	if($command == "DELETE_COMDTL" && trim($COM_ID) && trim($PER_ID) ){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID and PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลข้าราชการ/ลูกจ้างแนบท้ายบัญชีคำสั่งเลื่อนขั้นเงินเดือน [".trim($COM_ID)." : ".$PER_ID."]");
	
		$cmd = " select * from PER_COMDTL where COM_ID = $COM_ID ";
		$count_comdtl = $db_dpis->send_cmd($cmd);
		// เมื่อเพิ่มข้อมูล เรียบร้อย ก็ส่งค่ากลับโปรแกรมหลัก ให้อยู่ใน mode เพิ่มตัวต่อไป
		echo "<script>";
		if ($count_comdtl > 0)
			echo "parent.refresh_opener('2<::>!<::>!<::>!<::>!<::><::>$count_comdtl<::><::>!<::>')";
		else
			echo "parent.refresh_opener('3<::>!<::>!<::>!<::>!<::><::>$count_comdtl<::><::>!<::>')";
		echo "</script>";
	}
	
	if($command == "DELETE_COMMAND" && trim($COM_ID) ){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
			
		$cmd = " delete from PER_COMMAND where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลบัญชีและข้าราชการ/ลูกจ้างแนบท้ายบัญชีคำสั่งเลื่อนขั้นเงินเดือน [$DEPARTMENT_ID : ".trim($COM_ID)." : ".$PER_ID."]");
		$COM_ID = "";

		echo "<script>";
		echo "parent.refresh_opener('1<::><::><::><::><::><::><::><::><::>')";
		echo "</script>";
	}

	if (trim($COM_ID)) {
		$cmd = "	select	COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
						a.COM_TYPE, COM_CONFIRM, COM_STATUS, b.COM_DESC, a.DEPARTMENT_ID , a.ORG_ID
				from		PER_COMMAND a, PER_COMTYPE b
				where	COM_ID=$COM_ID  and a.COM_TYPE=b.COM_TYPE	";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		
		$COM_NO = trim($data[COM_NO]);
		$COM_NAME = trim($data[COM_NAME]);
		$COM_DATE = show_date_format($data[COM_DATE], 1);
		$COM_NOTE = trim($data[COM_NOTE]);
		$COM_PER_TYPE = trim($data[COM_PER_TYPE]);
		$COM_CONFIRM = trim($data[COM_CONFIRM]);
		$COM_STATUS = trim($data[COM_STATUS]);
		
		$COM_TYPE = trim($data[COM_TYPE]);
		$COM_TYPE_NAME = trim($data[COM_DESC]);
		
		$search_org_id = $data[ORG_ID];
		$DEPARTMENT_ID = $data[DEPARTMENT_ID];

		$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$search_org_id ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$search_org_name = $data[ORG_NAME];
		
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


	if( !$COM_ID ){
		$COM_ID = "";
		$COM_NO = "";
		$COM_NAME = "";
		$COM_DATE = "";
		$COM_NOTE = "";
		$COM_CONFIRM = 0;
		$COM_STATUS = "";
		
		$COM_TYPE = "";
		$COM_TYPE_NAME = "";
		$search_per_name = "";
		$search_per_surname = "";		
		$list_type = "4";		

		if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){ 
			$MINISTRY_ID = "";
			$MINISTRY_NAME = "";
		} // end if
		if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){ 
			$DEPARTMENT_ID = "";
			$DEPARTMENT_NAME = "";
		} // end if
		if($SESS_USERGROUP_LEVEL < 5){ 
			$search_org_id = "";
			$search_org_name = "";
		} // end if
	} // end if		
?>