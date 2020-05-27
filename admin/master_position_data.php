<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/load_per_control.php");
	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd , null);

	$UPDATE_DATE = date("Y-m-d H:i:s");
	if (!trim($SG_CODE)) {
		$SG_CODE = "990";
		$SG_NAME = "ยังไม่จัดกลุ่ม";
	}
	if (!trim($SKILL_CODE)) {
		$SKILL_CODE = "683";
		$SKILL_NAME = "ไม่มีสาขาชำนาญการ";
	}
	
	if($SESS_USERGROUP_LEVEL == 5 && $ORG_ID){
		$cmd = " select AP_CODE, PV_CODE, CT_CODE, OT_CODE from PER_ORG where ORG_ID=$ORG_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$AP_CODE = trim($data[AP_CODE]);
		$PV_CODE = trim($data[PV_CODE]);
		$CT_CODE = trim($data[CT_CODE]);
		$ORG_OT_CODE = trim($data[OT_CODE]);

		$cmd = " select AP_NAME from PER_AMPHUR where trim(AP_CODE)='$AP_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$AP_NAME = trim($data[AP_NAME]);

		$cmd = " select PV_NAME from PER_PROVINCE where trim(PV_CODE)='$PV_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PV_NAME = trim($data[PV_NAME]);

		$cmd = " select CT_NAME from PER_COUNTRY where trim(CT_CODE)='$CT_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$CT_NAME = trim($data[CT_NAME]);

		$cmd = " select OT_NAME from PER_ORG_TYPE where trim(OT_CODE)='$ORG_OT_CODE' ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$ORG_OT_NAME = trim($data[OT_NAME]);
	} // end if
	
	if($command=="ADD" || $command=="UPDATE"){
		$POS_DOC_NO = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POS_DOC_NO)));
		$POS_REMARK = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POS_REMARK)));
		$POS_CONDITION = str_replace('"', "&quot;", str_replace("'", "&rsquo;", trim($POS_CONDITION)));
	
		if(!trim($ORG_ID)) $ORG_ID = "NULL";
		if(!trim($ORG_ID_1)) $ORG_ID_1 = "NULL";
		if(!trim($ORG_ID_2)) $ORG_ID_2 = "NULL";
		if(!trim($ORG_ID_3)) $ORG_ID_3 = "NULL";
		if(!trim($ORG_ID_4)) $ORG_ID_4 = "NULL";
		if(!trim($ORG_ID_5)) $ORG_ID_5 = "NULL";
		$PM_CODE = (trim($PM_CODE))? "'".$PM_CODE."'" : "NULL";

		$POS_SALARY += 0;
		$POS_MGTSALARY += 0;

		if($POS_DATE){
			$arr_temp = explode("/", $POS_DATE);
			$POS_DATE = ($arr_temp[2] - 543) ."-". str_pad($arr_temp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($arr_temp[0], 2, "0", STR_PAD_LEFT);
		} // end if
		if($POS_GET_DATE){
			$arr_temp = explode("/", $POS_GET_DATE);
			$POS_GET_DATE = ($arr_temp[2] - 543) ."-". str_pad($arr_temp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($arr_temp[0], 2, "0", STR_PAD_LEFT);
		} // end if
		if($POS_CHANGE_DATE){
			$arr_temp = explode("/", $POS_CHANGE_DATE);
			$POS_CHANGE_DATE = ($arr_temp[2] - 543) ."-". str_pad($arr_temp[1], 2, "0", STR_PAD_LEFT) ."-". str_pad($arr_temp[0], 2, "0", STR_PAD_LEFT);
		} // end if
	} // end if
	
	if($command=="ADD" && $POS_NO && $DEPARTMENT_ID){
		// ========================== This code use before add DEPARTMENT_ID column to PER_POSITION table ==========================
		// $cmd = " select ORG_ID from PER_ORG where OL_CODE='03' and ORG_ID_REF=$DEPARTMENT_ID order by ORG_ID ";
		// $db_dpis->send_cmd($cmd);
		// unset($arr_org);
		// while($data = $db_dpis->get_array()) $arr_org[] = $data[ORG_ID];
		// $cmd = " select POS_ID, POS_NO from PER_POSITION where trim(POS_NO)='". trim($POS_NO) ."' and ORG_ID in (". implode(",", $arr_org) .") ";
		// =================================================================================================================

		// ====================== After add DEPARTMENT_ID column to PER_POSITION table use this code instead ==========================
		$cmd = " select POS_ID, POS_NO from PER_POSITION where trim(POS_NO)='". trim($POS_NO) ."' and DEPARTMENT_ID=$DEPARTMENT_ID ";
		// =================================================================================================================

		$count_duplicate = $db_dpis->send_cmd($cmd);

		if($count_duplicate <= 0 || $DEPARTMENT_NAME=='กรมการปกครอง'){
			$cmd = " select max(POS_ID) as MAX_POS_ID from PER_POSITION ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$POS_ID = $data[MAX_POS_ID] + 1;

			$cmd = "insert into PER_POSITION (POS_ID, ORG_ID, POS_NO, OT_CODE, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, PM_CODE, 
							PL_CODE, CL_NAME, POS_SALARY, POS_MGTSALARY, SKILL_CODE, PT_CODE, PC_CODE, POS_CONDITION, 
							POS_DOC_NO, POS_REMARK, POS_DATE, POS_GET_DATE, POS_CHANGE_DATE, POS_STATUS, 
							UPDATE_USER, UPDATE_DATE, DEPARTMENT_ID, PAY_NO, POS_ORGMGT, LEVEL_NO)
							values ($POS_ID, $ORG_ID, '$POS_NO', '$OT_CODE', $ORG_ID_1, $ORG_ID_2, $ORG_ID_3, $ORG_ID_4, $ORG_ID_5, $PM_CODE, '$PL_CODE', 
							'$CL_NAME', $POS_SALARY, $POS_MGTSALARY,	'$SKILL_CODE', '$PT_CODE', '$PC_CODE', '$POS_CONDITION', 
							'$POS_DOC_NO', '$POS_REMARK', '$POS_DATE', '$POS_GET_DATE', '$POS_CHANGE_DATE', $POS_STATUS, 
							$SESS_USERID, '$UPDATE_DATE', $DEPARTMENT_ID, '$PAY_NO', '$POS_ORGMGT', '$LEVEL_NO') ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
			
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > เพิ่มข้อมูลตำแหน่ง [ $DEPARTMENT_ID : $POS_ID : $POS_NO ]");

			echo "<FONT SIZE='5' COLOR='#FF0066'>เพิ่มข้อมูลเลขที่ตำแหน่ง $POS_NO  เรียบร้อยแล้ว</FONT>";

		}else{
			$data = $db_dpis->get_array();
			$err_text = "เลขที่ตำแหน่งซ้ำ [".$data[POS_NO]."]";
			
//			if($PM_CODE == "NULL") $PM_CODE = "";
//			else $PM_CODE = substr($PM_CODE, 1, -1);

//			if($PC_CODE == "NULL") $PC_CODE = "";
//			else $PC_CODE = substr($PC_CODE, 1, -1);
			
			if($ORG_ID == "NULL") $ORG_ID = "";
			if($ORG_ID_1 == "NULL") $ORG_ID_1 = "";
			if($ORG_ID_2 == "NULL") $ORG_ID_2 = "";
			if($ORG_ID_3 == "NULL") $ORG_ID_3 = "";
			if($ORG_ID_4 == "NULL") $ORG_ID_4 = "";
			if($ORG_ID_5 == "NULL") $ORG_ID_5 = "";
		} // end if
		$POS_ID="";	//เคลียร์ค่าให้เป็นว่าง หลังจาก ADD ไปแล้ว 
		if($POS_DATE){
			$arr_temp = explode("-", $POS_DATE);
			$POS_DATE = ($arr_temp[0] + 543)."-". $arr_temp[1] ."-". $arr_temp[2];
		} // end if
		if($POS_GET_DATE){
			$arr_temp = explode("-", $POS_GET_DATE);
			$POS_GET_DATE = ($arr_temp[0] + 543)."-". $arr_temp[1] ."-".$arr_temp[2] ;
		} // end if
		if($POS_CHANGE_DATE){
			$arr_temp = explode("-", $POS_CHANGE_DATE);
			$POS_CHANGE_DATE = ($arr_temp[0] + 543)."-". $arr_temp[1] ."-".$arr_temp[2] ;
		} // end if 
	} // end if

	if($command=="UPDATE" && $POS_ID && $POS_NO && $DEPARTMENT_ID){	
	
	
		$cmd = " update PER_POSITION  set
							ORG_ID = $ORG_ID, 
							POS_NO = '$POS_NO',
							PAY_NO = '$PAY_NO',
							OT_CODE = '$OT_CODE', 
							ORG_ID_1 = $ORG_ID_1, 
							ORG_ID_2 = $ORG_ID_2, 
							ORG_ID_3 = $ORG_ID_3, 
							ORG_ID_4 = $ORG_ID_4, 
							ORG_ID_5 = $ORG_ID_5, 
							PM_CODE = $PM_CODE, 
							PL_CODE = '$PL_CODE', 
							CL_NAME = '$CL_NAME', 
							POS_SALARY = $POS_SALARY, 
							POS_MGTSALARY = $POS_MGTSALARY, 
							SKILL_CODE = '$SKILL_CODE',
							PT_CODE = '$PT_CODE', 
							PC_CODE = '$PC_CODE', 
							POS_CONDITION = '$POS_CONDITION',
							POS_DOC_NO = '$POS_DOC_NO', 
							POS_REMARK = '$POS_REMARK',
							POS_DATE = '$POS_DATE', 
							POS_GET_DATE = '$POS_GET_DATE', 
							POS_CHANGE_DATE = '$POS_CHANGE_DATE', 
							POS_STATUS = $POS_STATUS, 
							POS_ORGMGT = '$POS_ORGMGT', 
							LEVEL_NO = '$LEVEL_NO', 
							UPDATE_USER = $SESS_USERID, 
							UPDATE_DATE = '$UPDATE_DATE',
							DEPARTMENT_ID = $DEPARTMENT_ID
						 where POS_ID=$POS_ID ";
//		echo "<br>$cmd<br>";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
//		die;

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > แก้ไขข้อมูลตำแหน่ง [ $DEPARTMENT_ID : $POS_ID : $POS_NO ]");
	} // end if
	
	if($command=="DELETE" && $POS_ID){
		$cmd = " select	 POS_NO from PER_POSITION where POS_ID=$POS_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POS_NO = $data[POS_NO];

		$cmd = " delete from PER_POSITION where POS_ID=$POS_ID ";	
		$db_dpis->send_cmd($cmd);
			
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ลบข้อมูลตำแหน่ง [ $DEPARTMENT_ID : $POS_ID : $POS_NO ]");
	} // end if

	if($POS_ID){
		$cmd = "	select	POS_NO, OT_CODE, PL_CODE, PM_CODE, CL_NAME, PT_CODE, SKILL_CODE,
											POS_DATE, POS_GET_DATE, POS_SALARY, POS_MGTSALARY, PC_CODE, POS_CONDITION, POS_STATUS, 
											POS_CHANGE_DATE, DEPARTMENT_ID, ORG_ID, ORG_ID_1, ORG_ID_2, ORG_ID_3, ORG_ID_4, ORG_ID_5, POS_DOC_NO, POS_REMARK, 
											UPDATE_USER, UPDATE_DATE, PAY_NO, POS_ORGMGT, LEVEL_NO
							from		PER_POSITION 
							where	POS_ID=$POS_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$POS_NO = trim($data[POS_NO]);
		$PAY_NO = trim($data[PAY_NO]);
		
		$OT_CODE = trim($data[OT_CODE]);
		$cmd = " select OT_NAME from PER_OFF_TYPE where trim(OT_CODE)='$OT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$OT_NAME = trim($data_dpis2[OT_NAME]);

		$PL_CODE = trim($data[PL_CODE]);
		$cmd = " select PL_NAME from PER_LINE where trim(PL_CODE)='$PL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PL_NAME = trim($data_dpis2[PL_NAME]);

		$PM_CODE = trim($data[PM_CODE]);
		$cmd = " select PM_NAME from PER_MGT where trim(PM_CODE)='$PM_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PM_NAME = trim($data_dpis2[PM_NAME]);
		
		$CL_NAME = trim($data[CL_NAME]);
		$CL_CODE = $CL_NAME;

		$PT_CODE = trim($data[PT_CODE]);
		$cmd = " select PT_NAME from PER_TYPE where trim(PT_CODE)='$PT_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PT_NAME = trim($data_dpis2[PT_NAME]);

		$SKILL_CODE = trim($data[SKILL_CODE]);
		$cmd = " select SKILL_NAME, a.SG_CODE, SG_NAME from PER_SKILL a, PER_SKILL_GROUP b where a.SG_CODE=b.SG_CODE and trim(SKILL_CODE)='$SKILL_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$SKILL_NAME = trim($data_dpis2[SKILL_NAME]);
		$SG_CODE = trim($data_dpis2[SG_CODE]);
		$SG_NAME = trim($data_dpis2[SG_NAME]);
		
		$POS_DATE = trim($data[POS_DATE]);
		if($POS_DATE){
			$arr_temp = explode("-", substr($POS_DATE, 0, 10));
			$POS_DATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
		} // end if

		$POS_GET_DATE = trim($data[POS_GET_DATE]);
		if($POS_GET_DATE){
			$arr_temp = explode("-", substr($POS_GET_DATE, 0, 10));
			$POS_GET_DATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
		} // end if

		$POS_SALARY = trim($data[POS_SALARY]);
		$POS_MGTSALARY = trim($data[POS_MGTSALARY]);

		$PC_CODE = trim($data[PC_CODE]);
		$cmd = " select PC_NAME from PER_CONDITION where trim(PC_CODE)='$PC_CODE' ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$PC_NAME = trim($data_dpis2[PC_NAME]);

		$POS_CONDITION = trim($data[POS_CONDITION]);
		$POS_STATUS = trim($data[POS_STATUS]);

		$POS_CHANGE_DATE = trim($data[POS_CHANGE_DATE]);
		if($POS_CHANGE_DATE){
			$arr_temp = explode("-", substr($POS_CHANGE_DATE, 0, 10));
			$POS_CHANGE_DATE = ($arr_temp[0] + 543) ."-". $arr_temp[1] ."-". $arr_temp[2];
		} // end if

		if($CTRL_TYPE < 4){
			$DEPARTMENT_ID = $data[DEPARTMENT_ID];

			$cmd = " select ORG_ID_REF, ORG_NAME from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
			$db_dpis2->send_cmd($cmd);
			$data_dpis2 = $db_dpis2->get_array();
			$DEPARTMENT_NAME = trim($data_dpis2[ORG_NAME]);
			
			if($CTRL_TYPE < 3){
				$MINISTRY_ID = $data_dpis2[ORG_ID_REF];
				$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$MINISTRY_NAME = trim($data_dpis2[ORG_NAME]);
			} // end if
		} // end if
		
		$ORG_ID = trim($data[ORG_ID]);
		$ORG_ID_1 = trim($data[ORG_ID_1]);
		$ORG_ID_2 = trim($data[ORG_ID_2]); 
		$ORG_ID_3 = trim($data[ORG_ID_3]);
		$ORG_ID_4 = trim($data[ORG_ID_4]); 
		$ORG_ID_5 = trim($data[ORG_ID_5]); 
		if ($ORG_ID_2) $TMP_ORG_ID = $ORG_ID_2;
		elseif ($ORG_ID_1) $TMP_ORG_ID = $ORG_ID_1;
		elseif ($ORG_ID) $TMP_ORG_ID = $ORG_ID;

		if($DPISDB=="odbc"){
			$cmd = " select 	a.CT_CODE, b.CT_NAME, a.PV_CODE, c.PV_NAME, a.AP_CODE, d.AP_NAME, a.OT_CODE, e.OT_NAME 
							 from 		(
							 					(
													( 
														PER_ORG a 
														left join PER_COUNTRY b on a.CT_CODE=b.CT_CODE
													) left join PER_PROVINCE c on a.PV_CODE=c.PV_CODE
												) left join PER_AMPHUR d on a.AP_CODE=d.AP_CODE
											) left join PER_ORG_TYPE e on a.OT_CODE=e.OT_CODE
							 where 	ORG_ID=$TMP_ORG_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	a.CT_CODE, b.CT_NAME, a.PV_CODE, c.PV_NAME, a.AP_CODE, d.AP_NAME, a.OT_CODE, e.OT_NAME 
							 from 		PER_ORG a, PER_COUNTRY b, PER_PROVINCE c, PER_AMPHUR d, PER_ORG_TYPE e
							 where 	a.CT_CODE=b.CT_CODE(+) and a.PV_CODE=c.PV_CODE(+)
											and a.AP_CODE=d.AP_CODE(+) and a.OT_CODE=e.OT_CODE(+)
							 				and ORG_ID=$TMP_ORG_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	a.CT_CODE, b.CT_NAME, a.PV_CODE, c.PV_NAME, a.AP_CODE, d.AP_NAME, a.OT_CODE, e.OT_NAME 
							 from 		(
							 					(
													( 
														PER_ORG a 
														left join PER_COUNTRY b on a.CT_CODE=b.CT_CODE
													) left join PER_PROVINCE c on a.PV_CODE=c.PV_CODE
												) left join PER_AMPHUR d on a.AP_CODE=d.AP_CODE
											) left join PER_ORG_TYPE e on a.OT_CODE=e.OT_CODE
							 where 	ORG_ID=$TMP_ORG_ID ";
		} // end if
		$db_dpis2->send_cmd($cmd);
//		$db_dpis2->show_error();
		$data_dpis2 = $db_dpis2->get_array();
		$CT_CODE = trim($data_dpis2[CT_CODE]);
		$CT_NAME = trim($data_dpis2[CT_NAME]);
		$PV_CODE = trim($data_dpis2[PV_CODE]);
		$PV_NAME = trim($data_dpis2[PV_NAME]);
		$AP_CODE = trim($data_dpis2[AP_CODE]);
		$AP_NAME = trim($data_dpis2[AP_NAME]);
		$ORG_OT_CODE = trim($data_dpis2[OT_CODE]);
		$ORG_OT_NAME = trim($data_dpis2[OT_NAME]);
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_NAME = trim($data_dpis2[ORG_NAME]);

		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_1 ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_NAME_1 = trim($data_dpis2[ORG_NAME]);

		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_2 ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_NAME_2 = trim($data_dpis2[ORG_NAME]);
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_3 ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_NAME_3 = trim($data_dpis2[ORG_NAME]);
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_4 ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_NAME_4 = trim($data_dpis2[ORG_NAME]);
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_5 ";
		$db_dpis2->send_cmd($cmd);
		$data_dpis2 = $db_dpis2->get_array();
		$ORG_NAME_5 = trim($data_dpis2[ORG_NAME]);
		
		$POS_DOC_NO = trim($data[POS_DOC_NO]);
		$POS_REMARK = trim($data[POS_REMARK]);
		$POS_ORGMGT = trim($data[POS_ORGMGT]);
		$LEVEL_NO = trim($data[LEVEL_NO]);

		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);

		$SHOW_UPDATE_DATE = trim($data[UPDATE_DATE]);
		if($SHOW_UPDATE_DATE){
			$arr_temp = explode("-", substr($SHOW_UPDATE_DATE, 0, 10));
			$SHOW_UPDATE_DATE = $arr_temp[2] ."/". $arr_temp[1] ."/". ($arr_temp[0] + 543);
		} // end if

		if($DPISDB=="odbc"){
			$cmd = "	select		b.PER_ID, b.PER_NAME, b.PER_SURNAME, c.PN_NAME
								from		
											(
												PER_POSITION a
												left join PER_PERSONAL b on a.POS_ID=b.PAY_ID
											) left join PER_PRENAME c on b.PN_CODE=c.PN_CODE
								where		a.PAY_NO=$PAY_NO and PER_TYPE = 1 and PER_STATUS = 1 ";
		}elseif($DPISDB=="oci8"){
			$cmd = "	select		b.PER_ID, b.PER_NAME, b.PER_SURNAME, c.PN_NAME
								from		PER_POSITION a, PER_PERSONAL b, PER_PRENAME c
								where		a.POS_ID=b.PAY_ID(+) and a.PAY_NO=$PAY_NO and b.PN_CODE=c.PN_CODE(+) and PER_TYPE = 1 and PER_STATUS = 1 ";
		}elseif($DPISDB=="mysql"){
			$cmd = "	select		b.PER_ID, b.PER_NAME, b.PER_SURNAME, c.PN_NAME
								from		
											(
												PER_POSITION a
												left join PER_PERSONAL b on a.POS_ID=b.PAY_ID
											) left join PER_PRENAME c on b.PN_CODE=c.PN_CODE
								where		a.PAY_NO=$PAY_NO and PER_TYPE = 1 and PER_STATUS = 1 ";
		} // end if
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_ID_PAY = trim($data[PER_ID]);
		$PAYNAME = (trim($data[PN_NAME])?($data[PN_NAME]):"") . $data[PER_NAME] ." ". $data[PER_SURNAME];

		$cmd = " select SAH_EFFECTIVEDATE
						from   PER_SALARYHIS
						where PER_ID=$PER_ID_PAY and SAH_PAY_NO='$PAY_NO'
						order by SAH_EFFECTIVEDATE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$SAH_EFFECTIVEDATE = trim($data[SAH_EFFECTIVEDATE]);
		if ($SAH_EFFECTIVEDATE) {	
			$tmp_date = explode("-", substr(trim($data[SAH_EFFECTIVEDATE]), 0, 10));
			$PAY_DATE = $tmp_date[2] ."/". $tmp_date[1] ."/". ($tmp_date[0] + 543);
		}

		$cmd = "	select PER_ID, PER_STATUS from	PER_PERSONAL where POS_ID=$POS_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while($data = $db_dpis->get_array()){
			$cnt++;
			$PER_ID[$cnt] = trim($data[PER_ID]);
			$PER_STATUS = $data[PER_STATUS];

			if($PER_ID[$cnt] && $PER_STATUS != 2){
				$cmd = " select 	b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.LEVEL_NO, a.PER_STATUS
								 from 		PER_PERSONAL a, PER_PRENAME b 
								 where 	a.PN_CODE=b.PN_CODE and PER_ID=$PER_ID[$cnt] ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$FULLNAME[$cnt] = (trim($data_dpis2[PN_NAME])?($data_dpis2[PN_NAME]):"") . $data_dpis2[PER_NAME] ." ". $data_dpis2[PER_SURNAME];
				$FULLNAME[$cnt] .= (($data_dpis2[PER_STATUS]==0)?" (รอบรรจุ)":"");
//				$LEVEL_NO = trim($data_dpis2[LEVEL_NO]);
				$POS_SITUATION = 3;
/*			
				if (!$RPT_N) {
					$cmd = " select MS_SALARY from PER_MGTSALARY where trim(PT_CODE)='$PT_CODE' and trim(LEVEL_NO)='$LEVEL_NO' ";
					$count_mgtsalary = $db_dpis2->send_cmd($cmd);
					$data_dpis2 = $db_dpis2->get_array();
					$POS_MGTSALARY = $data_dpis2[MS_SALARY];
				}
*/
				$cmd = " select POH_EFFECTIVEDATE
								from   PER_POSITIONHIS
								where PER_ID=$PER_ID[$cnt] and POH_POS_NO='$POS_NO'
								order by POH_EFFECTIVEDATE ";
				$db_dpis2->send_cmd($cmd);
				$data_dpis2 = $db_dpis2->get_array();
				$POH_EFFECTIVEDATE = trim($data_dpis2[POH_EFFECTIVEDATE]);
				if ($POH_EFFECTIVEDATE) {	
					$tmp_date = explode("-", substr(trim($data_dpis2[POH_EFFECTIVEDATE]), 0, 10));
					$POH_DATE[$cnt] = $tmp_date[2] ."/". $tmp_date[1] ."/". ($tmp_date[0] + 543);
				}
			}else{
				$POS_SITUATION = 1;
				if($POS_GET_DATE) $POS_SITUATION = 2;
			} // end if
		} // end while
	} // end if
?>