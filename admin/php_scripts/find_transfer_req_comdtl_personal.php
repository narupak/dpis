<?
//	include("../../php_scripts/connect_database.php");
//	include("php_scripts/function_share.php");	
//	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	
	function load_default_en_code($per_id) {
		global $db_dpis;
		
		$cmd = " select b.EN_CODE, b.EDU_TYPE from PER_EDUCNAME a, PER_EDUCATE b  
						where 	EN_ACTIVE=1 and a.EN_CODE=b.EN_CODE and PER_ID=$per_id
						order by	b.EDU_SEQ ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data_count = 0;
		$ret_value = "";
		while($data = $db_dpis->get_array()) {
			$data_count++;
			$TMP_EN_CODE = trim($data[EN_CODE]);
			if (strpos($data[EDU_TYPE],"2") !== false) { // มีค่า 2 ใน EDU_TYPE
				$ret_value = "$ret_value,$TMP_EN_CODE";
			}
		} // end loop while $data
		$ret_value = substr($ret_value,1); // ตัด comma ตัวแรกออก
		
		return $ret_value;
	}

//	echo "find_transsfer_req_comdtl_personal:1.PER_ID=$PER_ID<br />";
	if( trim($PER_ID) ) {
		$cmd = "	select 	PER_CARDNO, PER_BIRTHDATE, PER_TYPE, MOV_CODE, 
						POS_ID, POEM_ID, POEMS_ID, LEVEL_NO, PER_SALARY, PER_OCCUPYDATE   
				from 	PER_PERSONAL 
				where 	PER_ID=$PER_ID  ";
		$db_dpis->send_cmd($cmd);		
		$data = $db_dpis->get_array();
		$PER_CARDNO = trim($data[PER_CARDNO]);
        if ($PER_CARDNO=="NULL") $PER_CARDNO = "";
		$PER_TYPE = trim($data[PER_TYPE]);		
		$PER_BIRTHDATE = show_date_format($data[PER_BIRTHDATE], 1);

		$POS_ID = trim($data[POS_ID]);
		$POEM_ID = trim($data[POEM_ID]);
		$POEMS_ID = trim($data[POEMS_ID]);
		$MOV_CODE = trim($data[MOV_CODE]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
	  	$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '$LEVEL_NO' ";
		$db_dpis1->send_cmd($cmd);
		$data1 = $db_dpis1->get_array();					
		$LEVEL_NAME = trim($data1[LEVEL_NAME]);		

		$PER_SALARY = trim($data[PER_SALARY]);
		$PER_OCCUPYDATE = show_date_format($data[PER_OCCUPYDATE], 1);

		if ($MOV_CODE) {
			$cmd = "	select MOV_NAME from PER_MOVMENT where MOV_CODE='$MOV_CODE' ";
			$db_dpis1->send_cmd($cmd);	
			$data1 = $db_dpis1->get_array();
			$MOV_NAME = trim($data1[MOV_NAME]);
		}

		if ($POS_ID) {
			$POS_POEM_ID = $POS_ID;
			$cmd = "	select	POS_NO,POS_NO_NAME, PL_NAME, ORG_ID, ORG_ID_1, ORG_ID_2  ,LEVEL_NO, a.PL_CODE, PM_CODE, DEPARTMENT_ID
					from		PER_POSITION a, PER_LINE b 
					where	POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$POS_POEM_NO = $data1[POS_NO];	
			$POS_POEM_NO_NAME = $data1[POS_NO_NAME];
			$POS_POEM_NAME = $data1[PL_NAME];
			$PL_CODE = trim($data1[PL_CODE]);
			$PM_CODE = trim($data1[PM_CODE]);
			$DEPARTMENT_ID = trim($data1[DEPARTMENT_ID]);
			$POS_POEM_ORG3_ID = $tmp_org_id[] = trim($data1[ORG_ID]);
			$POS_POEM_ORG4_ID = $tmp_org_id[] = trim($data1[ORG_ID_1]);
			$POS_POEM_ORG5_ID = $tmp_org_id[] = trim($data1[ORG_ID_2]);
			$LEVEL_NO_POS = trim($data[LEVEL_NO]);
		  	$cmd = " select LEVEL_NAME from PER_LEVEL where LEVEL_NO = '$LEVEL_NO_POS' ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();					
			$LEVEL_NAME_POS = trim($data1[LEVEL_NAME]);		
			
			$arr_null_org_id = array("", "NULL");
			$result = array_diff($tmp_org_id, $arr_null_org_id);
			$search_org_id = implode(", ", $result);			

			$cmd = " select OT_CODE from PER_ORG where ORG_ID=$POS_POEM_ORG3_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$OT_CODE = trim($data[OT_CODE]);
		
			// --เพื่อไปกำหนดค่าตรวจสอบอัตราเงินเดือนให้ hidden
			if(trim($LEVEL_NO)){
					$cmd = " select 				LAYER_NO,LAYER_SALARY, LAYER_ACTIVE, LAYER_SALARY_MIN, 
																LAYER_SALARY_MAX, LAYER_SALARY_MIDPOINT, 
																LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, LAYER_SALARY_FULL
											  from 			PER_LAYER a, PER_LEVEL b
											  where		a.LEVEL_NO=b.LEVEL_NO and LAYER_NO=0 and b.LEVEL_NO='$LEVEL_NO' AND a.LAYER_ACTIVE=1
											  order by 	b.PER_TYPE, b.LEVEL_SEQ_NO, a.LEVEL_NO, a.LAYER_TYPE, a.LAYER_NO ";
					$db_dpis1->send_cmd($cmd);
					$data1 = $db_dpis1->get_array();
					$LAYER_SALARY_MIN = trim($data1[LAYER_SALARY_MIN]);
					$LAYER_SALARY_MAX = trim($data1[LAYER_SALARY_MAX]);
			}  // end if
		} elseif ($POEM_ID) {
			$POS_POEM_ID = $POEM_ID;
			$cmd = "	select	POEM_NO,POEM_NO_NAME, PG_CODE
					from		PER_POS_EMP a, PER_POS_NAME b 
					where	POEM_ID=$POS_POEM_ID and a.PN_CODE=b.PN_CODE ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$POS_POEM_NO = $data1[POEM_NO];	
			$POS_POEM_NO_NAME = $data1[POEM_NO_NAME];	
			$PG_CODE = $data1[PG_CODE];
		} elseif ($POEMS_ID) {
			$POS_POEM_ID = $POEMS_ID;	
			$cmd = "	select	POEMS_NO,POEMS_NO_NAME, EP_NAME, ORG_ID, ORG_ID_1, ORG_ID_2  ,LEVEL_NO
					from		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b 
					where	POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE ";
			$db_dpis1->send_cmd($cmd);
			$data1 = $db_dpis1->get_array();
			$POS_POEM_NO = $data1[POEMS_NO];	
			$POS_POEM_NO_NAME = $data1[POEMS_NO_NAME];	
			$POS_POEM_NAME = $data1[EP_NAME];
			$POS_POEM_ORG3_ID = $tmp_org_id[] = trim($data1[ORG_ID]);
			$POS_POEM_ORG4_ID = $tmp_org_id[] = trim($data1[ORG_ID_1]);
			$POS_POEM_ORG5_ID = $tmp_org_id[] = trim($data1[ORG_ID_2]);
			$LEVEL_NO_POS = trim($data[LEVEL_NO]);
			$arr_null_org_id = array("", "NULL");
			$result = array_diff($tmp_org_id, $arr_null_org_id);
			$search_org_id = implode(", ", $result);			

			// --เพื่อไปกำหนดค่าตรวจสอบอัตราเงินเดือนให้ hidden
			if(trim($LEVEL_NO)){
					$cmd = " select 				LAYER_NO,LAYER_SALARY, LAYER_ACTIVE, LAYER_SALARY_MIN, 
																LAYER_SALARY_MAX, LAYER_SALARY_MIDPOINT, 
																LAYER_SALARY_MIDPOINT1, LAYER_SALARY_MIDPOINT2, LAYER_SALARY_FULL
											  from 			PER_LAYER a, PER_LEVEL b
											  where		a.LEVEL_NO=b.LEVEL_NO and LAYER_NO=0 and b.LEVEL_NO='$LEVEL_NO' AND a.LAYER_ACTIVE=1
											  order by 	b.PER_TYPE, b.LEVEL_SEQ_NO, a.LEVEL_NO, a.LAYER_TYPE, a.LAYER_NO ";
					$db_dpis1->send_cmd($cmd);
					$data1 = $db_dpis1->get_array();
					$LAYER_SALARY_MIN = trim($data1[LAYER_SALARY_MIN]);
					$LAYER_SALARY_MAX = trim($data1[LAYER_SALARY_MAX]);
			}  // end if
		}
		
		$EN_CODE = load_default_en_code($PER_ID);
		if (!$LAYER_SALARY_MIN) $LAYER_SALARY_MIN = 0;
		if (!$LAYER_SALARY_MAX) $LAYER_SALARY_MAX = 0;
				
		// ===== select ชื่อหน่วยงานของตำแหน่งที่สังกัด ===== 
		$POS_POEM_ORG1 = $POS_POEM_ORG2 = $POS_POEM_ORG3 = $POS_POEM_ORG4 = $POS_POEM_ORG5 = "";
		$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID IN ($search_org_id) ";
		$db_dpis1->send_cmd($cmd);
		while ( $data1 = $db_dpis1->get_array() ) {
			$POS_POEM_ORG3 = ($POS_POEM_ORG3_ID == trim($data1[ORG_ID]))? 	trim($data1[ORG_NAME]) : "$POS_POEM_ORG3";
			$POS_POEM_ORG4 = ($POS_POEM_ORG4_ID == trim($data1[ORG_ID]))? 	trim($data1[ORG_NAME]) : "$POS_POEM_ORG4";
			$POS_POEM_ORG5 = ($POS_POEM_ORG5_ID == trim($data1[ORG_ID]))? 	trim($data1[ORG_NAME]) : "$POS_POEM_ORG5";
		}	// while
		
		$cmd = " select b.ORG_ID, b.ORG_NAME, b.ORG_ID_REF from PER_ORG a, PER_ORG b
				where a.ORG_ID=$POS_POEM_ORG3_ID and a.ORG_ID_REF=b.ORG_ID";
		$db_dpis->send_cmd($cmd);
		$data1 = $db_dpis->get_array();
		$POS_POEM_ORG2 = $data1[ORG_NAME];
		$POS_POEM_ORG1_ID = $data1[ORG_ID_REF]; 
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$POS_POEM_ORG1_ID ";
		$db_dpis->send_cmd($cmd);
		$data1 = $db_dpis->get_array();
		$POS_POEM_ORG1 = $data1[ORG_NAME];
	
	// === แสดงเงินเดือนในระดับ  find_retire_comdtl_personal.html
	if(trim($LEVEL_NO) && (!$RPT_N || $PER_TYPE==2)) {
			$arr = 1;		$selectfield="";
			if($PER_TYPE==2){
				$selectfield="LAYERE_SALARY";
				$cmd = " select $selectfield FROM PER_LAYEREMP where (PG_CODE='$PG_CODE' AND LAYERE_ACTIVE=1) order by $selectfield ";
			}else if($PER_TYPE==3){
				$selectfield="LAYER_SALARY";
				$cmd = " select $selectfield FROM PER_LAYER where (LEVEL_NO like '$LEVEL_NO%' AND LAYER_ACTIVE=1) order by $selectfield ";
			}
			$count_layer_salary = $db_dpis->send_cmd($cmd);
			if ($count_layer_salary)  $cmd_salary_list .= "parent.document.form1.CMD_SALARY_SELECT.options[0] = new Option('== เลือกอัตราเงินเดือน ==','');";
				while ( $data = $db_dpis->get_array() ) {
					$layer_salary_value = $data[$selectfield];
					$layer_salary_show = number_format($data[$selectfield], 2, '.', ',');
					$cmd_salary_list .= " parent.document.form1.CMD_SALARY_SELECT.options[$arr] = new Option('$layer_salary_show','$layer_salary_value'); ";
					if ($CMD_OLD_SALARY==$layer_salary_value) $cmd_salary_list .= " parent.document.form1.CMD_SALARY_SELECT.selectedIndex = $arr; ";
					$arr++;				
				}		// end while
		}			// end if 
	}
	
		$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_ID = $data[ORG_ID_REF];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];
		
		$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = $data[PL_NAME];
		
		$cmd = " select PM_NAME from PER_MGT where PM_CODE='$PM_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PM_NAME = $data[PM_NAME];
		
		$cmd = " select POSITION_LEVEL from PER_PERSONAL a, PER_LEVEL b where a.POS_ID=$POS_ID and a.LEVEL_NO=b.LEVEL_NO ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POSITION_LEVEL = $data[POSITION_LEVEL];
		
		if ($PM_NAME) $PL_NAME_WORK = $PM_NAME." (".$PL_NAME.$POSITION_LEVEL.")";
		else $PL_NAME_WORK = $PL_NAME.$POSITION_LEVEL;

		if ($POS_POEM_ORG3=="-") $POS_POEM_ORG3 = "";		
		if ($POS_POEM_ORG4=="-") $POS_POEM_ORG4 = "";		
		if ($POS_POEM_ORG5=="-") $POS_POEM_ORG5 = "";		
		if ($OT_CODE == "03") 
			if (!$POS_POEM_ORG5 && !$POS_POEM_ORG4 && $POS_POEM_ORG2=="กรมการปกครอง") 
				$ORG_NAME_WORK = "ที่ทำการปกครอง".$POS_POEM_ORG3." ".$POS_POEM_ORG3;
			else 
				$ORG_NAME_WORK = trim($POS_POEM_ORG5." ".$POS_POEM_ORG4." ".$POS_POEM_ORG3);
		elseif ($OT_CODE == "01") $ORG_NAME_WORK = trim($POS_POEM_ORG4." ".$POS_POEM_ORG3." ".$POS_POEM_ORG2);
		else $ORG_NAME_WORK =  trim($POS_POEM_ORG4." ".$POS_POEM_ORG3);
		
		$PL_NAME_WORK = $MOV_NAME . " และแต่งตั้งให้ดำรงตำแหน่ง " . $PL_NAME_WORK;
			 
//	echo $PER_TYPE ." : ". $POS_POEM_ID ." : ". $RPT_N.":::".$LEVEL_NO;
//		$PER_CARDNO = $PER_CARDNO;
//		$PER_BIRTHDATE = $PER_BIRTHDATE;
//		$PER_TYPE = $PER_TYPE;
		$CMD_DATE = $PER_OCCUPYDATE;
	
//		$EN_CODE = $EN_CODE;

//		$POS_POEM_ID = $POS_POEM_ID;	
//		$POS_POEM_NO = $POS_POEM_NO;
//		if($POS_POEM_NO_NAME)	$POS_POEM_NO_NAME = $POS_POEM_NO_NAME;
//		$POS_POEM_NAME = $POS_POEM_NAME;
		$POS_PM_CODE = $PM_CODE;
		$POS_PM_NAME = $PM_NAME;
		$CMD_LEVEL = $LEVEL_NO;
		$CMD_LEVEL2 = $LEVEL_NAME;
		$CMD_LEVEL1 = $LEVEL_NO_POS;
		$CMD_LEVEL3 = $LEVEL_NAME_POS;
//		$LEVEL_NO = $LEVEL_NO;
	
//		if ($PER_TYPE==1) {
//			$LAYER_SALARY_MIN = $LAYER_SALARY_MIN;
//			$LAYER_SALARY_MAX = $LAYER_SALARY_MAX;
//		}
		
//		$MOV_CODE = $MOV_CODE;
//		$MOV_NAME = $MOV_NAME;
	
//		$POS_POEM_ORG5 = $POS_POEM_ORG5;
//		$POS_POEM_ORG4 = $POS_POEM_ORG4;
//		$POS_POEM_ORG3 = $POS_POEM_ORG3;
//		$POS_POEM_ORG2 = $POS_POEM_ORG2;
		$POS_POEM_ORG1 = $POS_POEM_ORG1;
//		$PL_NAME_WORK = $PL_NAME_WORK;	
//		$ORG_NAME_WORK = $ORG_NAME_WORK;	
//		$PL_CODE = $PL_CODE;		
//		echo "find transfer-->PL_CODE=".$PL_CODE."<br>";

		if($cmd_salary_list){
			if($CMD_SALARY_SELECT && document.form1.CMD_SALARY_SELECT.options){
				$cmd_salary_list;
			}
		}

		$CMD_SALARY = $PER_SALARY;
		$CMD_OLD_SALARY = "0.00";	
		if (!$RPT_N) { ?>
        <script>
			parent.change_salary_bylevel('<?=$LEVEL_NO?>', '<?=$PER_SALARY?>');
		</script>
<?		}	?>
<script>
		parent.select_salary_inputtype('<?=$RPT_N?>', '<?=$PER_TYPE?>', '<?=$LEVEL_NO?>');
</script>
