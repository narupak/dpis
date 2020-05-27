<?
//	include("../../php_scripts/connect_database.php");
//	include("session_start.php");
	
//	$db_dpis_n = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	// ==== use for testing phase =====
	$db_dpis_n = new connect_db($db_host, $db_name, $db_user, $db_pwd);
	// ==========================

	/*if( trim($PER_ID) ) {
		$cmd = " select 	POT_ID from PER_PERSONAL 	where PER_ID=$PER_ID  ";
		$db_dpis->send_cmd($cmd);	
		$data = $db_dpis->get_array();
		$POT_ID = trim($data[POT_ID]);
	}*/

	if(trim($POT_ID)){
		$cmd = "	select 	ORG_ID, ORG_ID_1, ORG_ID_2,ORG_ID_3, ORG_ID_4, ORG_ID_5,  TP_CODE, DEPARTMENT_ID 
							from  	PER_POS_TEMP  
							where POT_ID=$POT_ID ";
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
		$TP_CODE = trim($data[TP_CODE]);

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
		
		$DEPARTMENT_NAME = $ORG_NAME = $ORG_NAME_1 = $ORG_NAME_2 =  $ORG_NAME_3 = $ORG_NAME_4 = $ORG_NAME_5 = "";
		$cmd = " select ORG_ID, ORG_NAME from PER_ORG where ORG_ID IN ($search_org_id) ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		while ( $data1 = $db_dpis->get_array() ) {
			$DEPARTMENT_NAME = ($DEPARTMENT_ID == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$DEPARTMENT_NAME";
			$ORG_NAME = 		($ORG_ID == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$ORG_NAME";
			$ORG_NAME_1 = 	($ORG_ID_1 == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$ORG_NAME_1";
			$ORG_NAME_2 = 	($ORG_ID_2 == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$ORG_NAME_2";
			$ORG_NAME_3 = 	($ORG_ID_3 == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$ORG_NAME_3";
			$ORG_NAME_4 = 	($ORG_ID_4 == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$ORG_NAME_4";
			$ORG_NAME_5 = 	($ORG_ID_5 == trim($data1[ORG_ID]))? 		trim($data1[ORG_NAME]) : "$ORG_NAME_5";
		}	// while		
		
		$cmd = " select ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_ID = $data[ORG_ID_REF];
		
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];

		$cmd = " select TP_NAME from PER_TEMP_POS_NAME where TP_CODE='$TP_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$TP_NAME = $data[TP_NAME];
		
		$cmd = " select a.LEVEL_NO,LEVEL_NAME, POSITION_LEVEL 
						from PER_PERSONAL a, PER_LEVEL b where a.POT_ID=$POT_ID and a.LEVEL_NO=b.LEVEL_NO ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$LEVEL_NAME = $data[LEVEL_NAME];
		$POSITION_LEVEL = $data[POSITION_LEVEL];
		if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;
		$LEVEL_NO = $data[LEVEL_NO];
		$ES_CODE = "02";
		$ES_NAME = "ตรงตามตำแหน่ง";
		
		$PL_NAME_WORK = $TP_NAME." ".$POSITION_LEVEL;

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
			$PL_NAME_WORK = "เลื่อนและแต่งตั้งให้ดำรงตำแหน่ง ". $PL_NAME_WORK;
		elseif ($PROGRAM_NAME=="data_transfer_req")			 
			$PL_NAME_WORK = $MOV_NAME . " และแต่งตั้งให้ดำรงตำแหน่ง " . $PL_NAME_WORK;
	} 		// if 
	if(!$formName)	$formName="form1";

	$POS_POEM_ORG1 = $MINISTRY_NAME;
	$POS_POEM_ORG2 = $DEPARTMENT_NAME;
	$POS_POEM_ORG3 = $ORG_NAME;
	$POS_POEM_ORG4 = $ORG_NAME_1;
	$POS_POEM_ORG5 = $ORG_NAME_2;
	if($POS_POEM_ORG6) $POS_POEM_ORG6 = $ORG_NAME_3;
	if($POS_POEM_ORG7) $POS_POEM_ORG7 = $ORG_NAME_4;
	if($POS_POEM_ORG8) $POS_POEM_ORG8 = $ORG_NAME_5;
	$POS_POEM_NAME = $TP_NAME;
        
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
        
        
/*
	<? if ($PROGRAM_NAME!="data_transfer_req")	{ ?>		 
	parent.document.<?=$formName; ?>.LEVEL_NO.value = '<?=$LEVEL_NO?>';
	parent.document.<?=$formName; ?>.ES_CODE.value = '<?=$ES_CODE?>';
	parent.document.<?=$formName; ?>.ES_NAME.value = '<?=$ES_NAME?>';
		<? if ($PROGRAM_NAME!="data_promote_e_p")	{ ?>		 
		parent.document.<?=$formName; ?>.LEVEL_NAME.value = '<?=$LEVEL_NAME?>';
		<? } ?>
	<? } ?>
	parent.document.<?=$formName; ?>.PL_NAME_WORK.value = '<?=$PL_NAME_WORK?>';		
	parent.document.<?=$formName; ?>.ORG_NAME_WORK.value = '<?=$ORG_NAME_WORK?>';
*/
?>
