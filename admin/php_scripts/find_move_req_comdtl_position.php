<?
//	include("../../php_scripts/connect_database.php");
//	include("session_start.php");
//	echo "POS_ID=$POS_ID, PROGRAM_NAME=$PROGRAM_NAME, MOV_NAME=$MOV_NAME<br>";
	$db_dpis_n = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	// ==== use for testing phase =====
//	$db_dpis_n = new connect_db($db_host, $db_name, $db_user, $db_pwd);
	// ==========================
/*  มอบหมายงาน ยังผิดอยู่
	if( trim($PER_ID) ) {
		$cmd = " select 	POS_ID from PER_PERSONAL 	where PER_ID=$PER_ID  ";
		$db_dpis->send_cmd($cmd);	
		$data = $db_dpis->get_array();
		$POS_ID = trim($data[POS_ID]);
	}
*/

	if( trim($POS_ID) ){
		$cmd = " 	select 	ORG_ID, ORG_ID_1, ORG_ID_2,ORG_ID_3, ORG_ID_4, ORG_ID_5, PL_CODE, PM_CODE, DEPARTMENT_ID, LEVEL_NO, CL_NAME, POS_NO_NAME, POS_NO 
							from  	PER_POSITION  
							where POS_ID=$POS_ID ";
		
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		$data = $db_dpis->get_array();
		
		$DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
		$ORG_ID = trim($data[ORG_ID]);
		$ORG_ID_1 = trim($data[ORG_ID_1]);
		$ORG_ID_2 = trim($data[ORG_ID_2]);
		$ORG_ID_3 = trim($data[ORG_ID_3]);
		$ORG_ID_4 = trim($data[ORG_ID_4]);
		$ORG_ID_5 = trim($data[ORG_ID_5]);
		$PL_CODE = trim($data[PL_CODE]);
		$PM_CODE = trim($data[PM_CODE]);
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$CL_NAME = trim($data[CL_NAME]);
		$POS_NO_NAME = trim($data[POS_NO_NAME]);
		$POS_NO = trim($data[POS_NO]);

		$cmd = " select OT_CODE from PER_ORG where ORG_ID=$ORG_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$OT_CODE = trim($data[OT_CODE]);
		
		if ($DEPARTMENT_ID) $tmp_ORG_ID[] =  $DEPARTMENT_ID;
		if ($ORG_ID)			$tmp_ORG_ID[] =  $ORG_ID;
		if ($ORG_ID_1)		$tmp_ORG_ID[] =  $ORG_ID_1;
		if ($ORG_ID_2)		$tmp_ORG_ID[] =  $ORG_ID_2;
		if ($ORG_ID_3)		$tmp_ORG_ID[] =  $ORG_ID_3;
		if ($ORG_ID_4)		$tmp_ORG_ID[] =  $ORG_ID_4;
		if ($ORG_ID_5)		$tmp_ORG_ID[] =  $ORG_ID_5;
		$search_org_id = implode(", ", $tmp_ORG_ID);
		
		$DEPARTMENT_NAME = $ORG_NAME = $ORG_NAME_1 = $ORG_NAME_2 = $ORG_NAME_3 = $ORG_NAME_4 = $ORG_NAME_5 = "";
		$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID IN ($search_org_id) ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		while ( $data1 = $db_dpis->get_array() ) {
			$DEPARTMENT_NAME = ($DEPARTMENT_ID == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$DEPARTMENT_NAME";
			$ORG_NAME = 		($ORG_ID == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$ORG_NAME";
			$ORG_NAME_1 = 	($ORG_ID_1 == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$ORG_NAME_1";
			$ORG_NAME_2 = 	($ORG_ID_2 == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$ORG_NAME_2";
			$ORG_NAME_3= 		($ORG_ID_3 == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$ORG_NAME_3";
			$ORG_NAME_4 = 	($ORG_ID_4 == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$ORG_NAME_4";
			$ORG_NAME_5 = 	($ORG_ID_5 == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$ORG_NAME_5";
		}	// while		
		
		$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_ID = $data[ORG_ID_REF];
		$MINISTRY_NAME = "";
		if($MINISTRY_ID){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$MINISTRY_NAME = $data[ORG_NAME];
		}
		$cmd = " select PL_NAME from PER_LINE where PL_CODE='$PL_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PL_NAME = $data[PL_NAME];
		
		$cmd = " select PM_NAME from PER_MGT where PM_CODE='$PM_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PM_NAME = $data[PM_NAME];
		
		$cmd = " select POSITION_LEVEL, LEVEL_NAME from PER_LEVEL where LEVEL_NO='$LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$POSITION_LEVEL = $data[POSITION_LEVEL];
//		if ($CMD_LEVEL) $POSITION_LEVEL = $CMD_LEVEL;
		$LEVEL_NAME = $data[LEVEL_NAME];
		$arr_temp = explode(" ", trim($LEVEL_NAME));
		if ($PROGRAM_NAME=="data_promote_e_p") $LEVEL_NAME = $CL_NAME;
		$ES_CODE = "02";
		$ES_NAME = "ตรงตามตำแหน่ง";
		
		if ($PM_NAME) $PL_NAME_WORK = $PM_NAME." (".$PL_NAME.$POSITION_LEVEL.")";
		else $PL_NAME_WORK = $PL_NAME.$POSITION_LEVEL;

//		echo "PL_CODE=$PL_CODE<br>";

		if ($ORG_NAME=="-") $ORG_NAME = "";		
		if ($ORG_NAME_1=="-") $ORG_NAME_1 = "";		
		if ($ORG_NAME_2=="-") $ORG_NAME_2 = "";		
		if ($OT_CODE == "03") 
			if (!$ORG_NAME_2 && !$ORG_NAME_1 && $DEPARTMENT_NAME=="กรมการปกครอง") 
				$ORG_NAME_WORK = "ที่ทำการปกครอง".$ORG_NAME." ".$ORG_NAME;
			else 
				$ORG_NAME_WORK = trim($ORG_NAME_2." ".$ORG_NAME_1." ".$ORG_NAME);
		elseif ($OT_CODE == "01") $ORG_NAME_WORK = trim($ORG_NAME_1." ".$ORG_NAME." ".$DEPARTMENT_NAME);
		else $ORG_NAME_WORK = trim($ORG_NAME_1." ".$ORG_NAME);

		if ($PROGRAM_NAME=="data_promote_e_p")			 
			$PL_NAME_WORK = "เลื่อนขึ้นเป็น$PERSON_TYPE[$PER_TYPE] " . $arr_temp[1] . " และแต่งตั้งให้ดำรงตำแหน่ง " . $PL_NAME_WORK;
		elseif ($PROGRAM_NAME=="data_transfer_req")			 
			$PL_NAME_WORK = $MOV_NAME . " และแต่งตั้งให้ดำรงตำแหน่ง " . $PL_NAME_WORK;
	} 		// if 
	if(!$formName)	$formName="form1";

	$POS_POEM_ORG1 = $MINISTRY_NAME;
	$POS_POEM_ORG2 = $DEPARTMENT_NAME;
	$POS_POEM_ORG3 = $ORG_NAME;
	$POS_POEM_ORG4 = $ORG_NAME_1;
	$POS_POEM_ORG5 = $ORG_NAME_2;
	if($POS_POEM_ORG6)	$POS_POEM_ORG6 = $ORG_NAME_3;
	if($POS_POEM_ORG7)	$POS_POEM_ORG7 = $ORG_NAME_4;
	if($POS_POEM_ORG8)	$POS_POEM_ORG8 = $ORG_NAME_5;		
	$POS_POEM_NAME = $PL_NAME;
	$POS_PM_CODE = $PM_CODE;
	$POS_PM_NAME = $PM_NAME;
	if ($POSITION_NO_CHAR=="Y") { 
		$POS_POEM_NO_NAME = $POS_NO_NAME;
	}
	$POS_POEM_NO = $POS_NO;
	
	/*กำหนดหาหน่วยงานตามมอบหมาย Release 5.2.1.8 Begin*/
	/*โครงสร้างตามมอบหมายงาน สำนัก/กอง*/
	$ORG_NAME_ASS='';
	$ORG_ID_ASS3='';
	if($ORG_NAME){
		$cmd_ass = " SELECT ORG_ID,ORG_CODE,ORG_NAME FROM PER_ORG_ASS WHERE TRIM(ORG_NAME) ='".trim($ORG_NAME)."' ";
		$db_dpis->send_cmd($cmd_ass);
		$data_ass = $db_dpis->get_array();
		if($data_ass){
			$ORG_NAME_ASS= $data_ass[ORG_NAME];
			$ORG_ID_ASS3= $data_ass[ORG_ID];
		}
	}
	/*โครงสร้างตามมอบหมายงาน ต่ำกว่าสำนัก/กอง 1 ระดับ*/
	$ORG_NAME_1_ASS='';
	$ORG_ID_1_ASS4='';
	if($ORG_NAME_1){
		$cmd_ass = " SELECT ORG_ID,ORG_CODE,ORG_NAME FROM PER_ORG_ASS WHERE TRIM(ORG_NAME) ='".trim($ORG_NAME_1)."' ";
		$db_dpis->send_cmd($cmd_ass);
		$data_ass = $db_dpis->get_array();
		if($data_ass){
			$ORG_NAME_1_ASS= $data_ass[ORG_NAME];
			$ORG_ID_1_ASS4= $data_ass[ORG_ID];
		}
	}
	/*โครงสร้างตามมอบหมายงาน ต่ำกว่าสำนัก/กอง 2 ระดับ*/
	$ORG_NAME_2_ASS='';
	$ORG_ID_2_ASS5='';
	if($ORG_NAME_2){
		$cmd_ass = " SELECT ORG_ID,ORG_CODE,ORG_NAME FROM PER_ORG_ASS WHERE TRIM(ORG_NAME) ='".trim($ORG_NAME_2)."' ";
		$db_dpis->send_cmd($cmd_ass);
		$data_ass = $db_dpis->get_array();
		if($data_ass){
			$ORG_NAME_2_ASS= $data_ass[ORG_NAME];
			$ORG_ID_2_ASS5= $data_ass[ORG_ID];
		}
	}
	/*Release 5.2.1.8  End*/
	
	
/*	if ($PROGRAM_NAME!="data_transfer_req")	{
		$LEVEL_NO = $LEVEL_NO;
		$ES_CODE = $ES_CODE;
		$ES_NAME = $ES_NAME;
		if ($PROGRAM_NAME!="data_promote_e_p")	{
			$LEVEL_NAME = $LEVEL_NAME;
		}
	}
	$PL_NAME_WORK = $PL_NAME_WORK;		
	$ORG_NAME_WORK = $ORG_NAME_WORK;		
*/
/*
	$POS_POEM_ORG1 = $MINISTRY_NAME;
	$POS_POEM_ORG2 = $DEPARTMENT_NAME;
	$POS_POEM_ORG3 = $ORG_NAME;
	$POS_POEM_ORG4 = $ORG_NAME_1;
	$POS_POEM_ORG5 = $ORG_NAME_2;		
	$POS_POEM_NAME = $PL_NAME;
	$POS_PM_CODE = $PM_CODE;		
	$POS_PM_NAME = $PM_NAME;
*/
/*	if ($PROGRAM_NAME!="data_transfer_req")	{ 
		$LEVEL_NO = $LEVEL_NO;
		$ES_CODE = $ES_CODE;
		$ES_NAME = $ES_NAME;
		if ($PROGRAM_NAME!="data_promote_e_p")	{
			$LEVEL_NAME = $LEVEL_NAME;
		}
	}
	$PL_NAME_WORK = $PL_NAME_WORK;	
	$ORG_NAME_WORK = $ORG_NAME_WORK;
*/
?>
