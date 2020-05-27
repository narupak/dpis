<?
	$UPDATE_DATE = date("Y-m-d H:i:s");
	if(!$MV_ID && $_GET[MV_ID])	$MV_ID = $_GET[MV_ID];

	if( $command == "ADD" && trim($PER_ID) && trim(!$MV_ID) ){
		$cmd = " select max(MV_ID) as max_id from PER_MOVE_REQ ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$MV_ID = $data[max_id] + 1;
		$MV_DATE =  save_date($MV_DATE);
		
		$PL_CODE_1 = $PL_CODE_2 = $PL_CODE_3 = $PN_CODE_1 = $PN_CODE_2 = $PN_CODE_3 = "";		
		if ($PER_TYPE == 1) { 
			$PL_CODE_1 = $PL_PN_CODE_1;
			$PL_CODE_2 = $PL_PN_CODE_2;
			$PL_CODE_3 = $PL_PN_CODE_3;						
		} elseif ($PER_TYPE == 2) { 
			$PN_CODE_1 = $PL_PN_CODE_1;
			$PN_CODE_2 = $PL_PN_CODE_2;
			$PN_CODE_3 = $PL_PN_CODE_3;			
		}

		$ORG_ID_10 = (trim($ORG_ID_10))?	$ORG_ID_10 : 'NULL';
		$ORG_ID_20 = (trim($ORG_ID_20))?	$ORG_ID_20 : 'NULL';
		$ORG_ID_30 = (trim($ORG_ID_30))?	$ORG_ID_30 : 'NULL';

		$ORG_ID_11 = (trim($ORG_ID_11))?	$ORG_ID_11 : 'NULL';
		$ORG_ID_21 = (trim($ORG_ID_21))?	$ORG_ID_21 : 'NULL';
		$ORG_ID_31 = (trim($ORG_ID_31))?	$ORG_ID_31 : 'NULL';	

		$ORG_ID_12 = (trim($ORG_ID_12))?	$ORG_ID_12 : 'NULL';
		$ORG_ID_22 = (trim($ORG_ID_22))?	$ORG_ID_22 : 'NULL';
		$ORG_ID_32 = (trim($ORG_ID_32))?	$ORG_ID_32 : 'NULL';	
		
		$PL_CODE_1 = (trim($PL_CODE_1))? "'$PL_CODE_1'":"NULL";
		$PL_CODE_2 = (trim($PL_CODE_2))? "'$PL_CODE_2'":"NULL";
		$PL_CODE_3 = (trim($PL_CODE_3))? "'$PL_CODE_3'":"NULL";

		$PN_CODE_1 = (trim($PN_CODE_1))? "'$PN_CODE_1'":"NULL";
		$PN_CODE_2 = (trim($PN_CODE_2))? "'$PN_CODE_2'":"NULL";
		$PN_CODE_3 = (trim($PN_CODE_3))? "'$PN_CODE_3'":"NULL";

		$search_pos_no1 = (trim($search_pos_no1))? $search_pos_no1 : 'NULL';
		$search_pos_no_name1 = (trim($search_pos_no_name1))? "'$search_pos_no_name1'":"NULL";
		
		$search_pos_no2 = (trim($search_pos_no2))? $search_pos_no2 : 'NULL';
		$search_pos_no_name2 = (trim($search_pos_no_name2))? "'$search_pos_no_name2'":"NULL";
		
		$search_pos_no3 = (trim($search_pos_no3))? $search_pos_no3 : 'NULL';
		$search_pos_no_name3 = (trim($search_pos_no_name3))? "'$search_pos_no_name3'":"NULL";

		$cmd = " insert into PER_MOVE_REQ (MV_ID, PER_ID, MV_DATE, PL_CODE_1, PN_CODE_1, 
						ORG_ID_1, PL_CODE_2, PN_CODE_2, ORG_ID_2, PL_CODE_3, PN_CODE_3, ORG_ID_3, 
						MV_REASON, MV_REMARK, DEPARTMENT_ID, ORG_ID_REF_1, ORG_ID_REF_2, 
						ORG_ID_REF_3, DEPARTMENT_ID_1, DEPARTMENT_ID_2 , DEPARTMENT_ID_3 , POS_NO_NAME_1 , POS_NO_1, POS_NO_NAME_2 , POS_NO_2, POS_NO_NAME_3 , POS_NO_3, UPDATE_USER, UPDATE_DATE) 
						VALUES ($MV_ID, $PER_ID, '$MV_DATE', $PL_CODE_1, $PN_CODE_1, $ORG_ID_12, 
						$PL_CODE_2, $PN_CODE_2, $ORG_ID_22, $PL_CODE_3, $PN_CODE_3, $ORG_ID_32, 
						'$MV_REASON', '$MV_REMARK', $DEPARTMENT_ID, $ORG_ID_11, $ORG_ID_21, 
						$ORG_ID_31,$ORG_ID_10, $ORG_ID_20 , $ORG_ID_30 , $search_pos_no_name1 , $search_pos_no1, $search_pos_no_name2 , $search_pos_no2, $search_pos_no_name3 , $search_pos_no3, $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//echo "$cmd<br>";
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูลประวัติการขอย้าย [ $DEPARTMENT_ID : ".trim($MV_ID)." : ".$PER_ID." ]");
	
/*		
		// เมื่อเพิ่มข้อมูล เรียบร้อย ก็ส่งค่ากลับโปรแกรมหลัก ให้อยู่ใน mode เพิ่มตัวต่อไป
		echo "<script>";
		echo "parent.refresh_opener('2<::>!<::>!<::>!<::><::>')";
		echo "</script>";				
*/	

		$UPD = 1;		//ให้ค้างค่าเดิม และสามารถแก้ไขได้
	}

	if($command == "UPDATE" && trim($PER_ID) && trim($MV_ID) ) {
		$MV_DATE =  save_date($MV_DATE);
		$PL_CODE_1 = $PL_CODE_2 = $PL_CODE_3 = $PN_CODE_1 = $PN_CODE_2 = $PN_CODE_3 = "";		
		if ($PER_TYPE == 1) { 
			$PL_CODE_1 = $PL_PN_CODE_1;
			$PL_CODE_2 = $PL_PN_CODE_2;
			$PL_CODE_3 = $PL_PN_CODE_3;						
		} elseif ($PER_TYPE == 2) { 
			$PN_CODE_1 = $PL_PN_CODE_1;
			$PN_CODE_2 = $PL_PN_CODE_2;
			$PN_CODE_3 = $PL_PN_CODE_3;			
		}	

		$ORG_ID_10 = (trim($ORG_ID_10))?	$ORG_ID_10 : 'NULL';
		$ORG_ID_20 = (trim($ORG_ID_20))?	$ORG_ID_20 : 'NULL';
		$ORG_ID_30 = (trim($ORG_ID_30))?	$ORG_ID_30 : 'NULL';

		$ORG_ID_11 = (trim($ORG_ID_11))?	$ORG_ID_11 : 'NULL';
		$ORG_ID_21 = (trim($ORG_ID_21))?	$ORG_ID_21 : 'NULL';
		$ORG_ID_31 = (trim($ORG_ID_31))?	$ORG_ID_31 : 'NULL';	

		$ORG_ID_12 = (trim($ORG_ID_12))?	$ORG_ID_12 : 'NULL';
		$ORG_ID_22 = (trim($ORG_ID_22))?	$ORG_ID_22 : 'NULL';
		$ORG_ID_32 = (trim($ORG_ID_32))?	$ORG_ID_32 : 'NULL';

		$PL_CODE_1 = (trim($PL_CODE_1))? "'$PL_CODE_1'":"NULL";
		$PL_CODE_2 = (trim($PL_CODE_2))? "'$PL_CODE_2'":"NULL";
		$PL_CODE_3 = (trim($PL_CODE_3))? "'$PL_CODE_3'":"NULL";

		$PN_CODE_1 = (trim($PN_CODE_1))? "'$PN_CODE_1'":"NULL";
		$PN_CODE_2 = (trim($PN_CODE_2))? "'$PN_CODE_2'":"NULL";
		$PN_CODE_3 = (trim($PN_CODE_3))? "'$PN_CODE_3'":"NULL";
		
		$search_pos_no1 = (trim($search_pos_no1))? $search_pos_no1 : 'NULL';
		$search_pos_no_name1 = (trim($search_pos_no_name1))? "'$search_pos_no_name1'":"NULL";
		
		$search_pos_no2 = (trim($search_pos_no2))? $search_pos_no2 : 'NULL';
		$search_pos_no_name2 = (trim($search_pos_no_name2))? "'$search_pos_no_name2'":"NULL";
		
		$search_pos_no3 = (trim($search_pos_no3))? $search_pos_no3 : 'NULL';
		$search_pos_no_name3 = (trim($search_pos_no_name3))? "'$search_pos_no_name3'":"NULL";

		$cmd = " update PER_MOVE_REQ set  
						MV_DATE = '$MV_DATE', 
						PL_CODE_1 = $PL_CODE_1, 
						PN_CODE_1 = $PN_CODE_1, 
						ORG_ID_1 = $ORG_ID_12, 
						PL_CODE_2 = $PL_CODE_2, 
						PN_CODE_2 = $PN_CODE_2, 
						ORG_ID_2 = $ORG_ID_22, 
						PL_CODE_3 = $PL_CODE_3, 
						PN_CODE_3 = $PN_CODE_3, 
						ORG_ID_3 = $ORG_ID_32,  
						MV_REASON = '$MV_REASON', 
						MV_REMARK = '$MV_REMARK', 
						DEPARTMENT_ID = $DEPARTMENT_ID,
						ORG_ID_REF_1 = $ORG_ID_11, 
						ORG_ID_REF_2 = $ORG_ID_21, 
						ORG_ID_REF_3 = $ORG_ID_31, 
						DEPARTMENT_ID_1 = $ORG_ID_10,
						DEPARTMENT_ID_2 = $ORG_ID_20,
						DEPARTMENT_ID_3 = $ORG_ID_30,
						POS_NO_NAME_1 ='$search_pos_no_name1', 
						POS_NO_1 ='$search_pos_no1', 
						POS_NO_NAME_2 ='$search_pos_no_name2', 
						POS_NO_2 ='$search_pos_no2', 
						POS_NO_NAME_3 ='$search_pos_no_name3', 
						POS_NO_3 ='$search_pos_no3', 
						UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
					where MV_ID=$MV_ID ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูลประวัติการขอย้าย [ $DEPARTMENT_ID : ".trim($MV_ID)." : ".$PER_ID." ]");
		$command="SEARCH";	$UPD=1;
	}
	
	if($command == "DELETE" && trim($MV_ID) ){	
		$cmd = " delete from PER_MOVE_REQ where MV_ID=$MV_ID and DEPARTMENT_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูลประวัติการขอย้าย [".trim($MV_ID)." : ".$PER_ID."]");
	}

	if( $PER_ID ){
		// select PER_PERSONAL
		if ($PER_TYPE == 1) {
			$cmd = "	select	b.PL_CODE, c.PL_NAME, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2  , a.PER_NAME , a.PER_SURNAME , PER_CARDNO , b.DEPARTMENT_ID
							from		PER_PERSONAL a, PER_POSITION b, PER_LINE c 
							where	PER_ID=$PER_ID and a.POS_ID=b.POS_ID and b.PL_CODE=c.PL_CODE ";
		} elseif ($PER_TYPE == 2) {
			$cmd = "	select	b.PN_CODE, c.PN_NAME, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2   , a.PER_NAME , a.PER_SURNAME , PER_CARDNO, b.DEPARTMENT_ID
							from		PER_PERSONAL a, PER_POS_EMP b, PER_POS_NAME c 
							where	PER_ID=$PER_ID and a.POEM_ID=b.POEM_ID and b.PN_CODE=c.PN_CODE "; 
		} elseif ($PER_TYPE == 3) {
			$cmd = "	select	b.EP_CODE, c.EP_NAME, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2   , a.PER_NAME , a.PER_SURNAME , PER_CARDNO, b.DEPARTMENT_ID
							from		PER_PERSONAL a, PER_POS_EMPSER b, PER_EMPSER_POS_NAME c 
							where	PER_ID=$PER_ID and a.POEMS_ID=b.POEMS_ID and b.EP_CODE=c.EP_CODE "; 
		}elseif ($PER_TYPE == 4) {
			$cmd = "	select	b.TP_CODE, c.TP_NAME, b.ORG_ID, b.ORG_ID_1, b.ORG_ID_2   , a.PER_NAME , a.PER_SURNAME , PER_CARDNO, b.DEPARTMENT_ID
							from		PER_PERSONAL a, PER_POS_TEMP b, PER_TEMP_POS_NAME c 
							where	PER_ID=$PER_ID and a.POT_ID=b.POT_ID and b.TP_CODE=c.TP_CODE "; 
		}
		$db_dpis->send_cmd($cmd);
		//echo "$MV_ID ==> $cmd";
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$PER_NAME = trim($data[PER_NAME])." ".trim($data[PER_SURNAME]);
		$PER_CARDNO = $data[PER_CARDNO];
		$POSITION = ($PER_TYPE == 1)? trim($data[PL_NAME]) : $POSITION;
		$POSITION = ($PER_TYPE == 2)? trim($data[PN_NAME]) : $POSITION;	
		$POSITION = ($PER_TYPE == 3)? trim($data[EP_NAME]) : $POSITION;	
		$POSITION = ($PER_TYPE == 4)? trim($data[TP_NAME]) : $POSITION;	
		$ORG_ID = trim($data[ORG_ID]) + 0;
		$ORG_ID_1 = trim($data[ORG_ID_1]) + 0;
		$ORG_ID_2 = trim($data[ORG_ID_2]) + 0;
		
		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
	
		$PER_ORG1 = $PER_ORG2 = $PER_ORG3 = "";
		$cmd = "select ORG_ID, ORG_NAME from PER_ORG where ORG_ID in  ($ORG_ID, $ORG_ID_1, $ORG_ID_2)";
		$db_dpis->send_cmd($cmd);
		while ( $data = $db_dpis->get_array() ) {
			$code_tmp = trim($data[ORG_ID]);
			$name_tmp = trim($data[ORG_NAME]);
			if ( $ORG_ID == $code_tmp )			$PER_ORG1 = $name_tmp;
			if ( $ORG_ID_1 == $code_tmp )		$PER_ORG2 = $name_tmp;
			if ( $ORG_ID_2 == $code_tmp )		$PER_ORG3 = $name_tmp;
		}
		
		$cmd = " select ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID=$DEPARTMENT_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$DEPARTMENT_NAME = $data[ORG_NAME];
		$MINISTRY_ID = $data[ORG_ID_REF];
										
		$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$MINISTRY_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$MINISTRY_NAME = $data[ORG_NAME];

		// select PER_EDUCATE
		$cmd = "	select 	EN_NAME
						from		PER_EDUCATE a, PER_EDUCNAME b
						where	PER_ID=$PER_ID and EDU_TYPE like '%2%' and a.EN_CODE=b.EN_CODE ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$EN_NAME = trim($data[EN_NAME]);

if ($UPD || $VIEW) {		
			// select PER_MOVE_REQ 
			$cmd = " 	select 	MV_ID, PER_ID, MV_DATE, 
											PL_CODE_1, PN_CODE_1, ORG_ID_1, ORG_ID_REF_1,
											PL_CODE_2, PN_CODE_2, ORG_ID_2, ORG_ID_REF_2, 
											PL_CODE_3, PN_CODE_3, ORG_ID_3, ORG_ID_REF_3,  
											MV_REASON, MV_REMARK ,
											DEPARTMENT_ID_1, DEPARTMENT_ID_2 , DEPARTMENT_ID_3 , POS_NO_NAME_1 , POS_NO_1, POS_NO_NAME_2 , POS_NO_2, POS_NO_NAME_3 , POS_NO_3
								from 	PER_MOVE_REQ 
								where MV_ID=$MV_ID "; 
//echo "-> $cmd";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$MV_REASON = trim($data[MV_REASON]);
			$MV_REMARK = trim($data[MV_REMARK]);
			
			$MV_DATE = show_date_format($data[MV_DATE], 1);

			$search_pos_no1 = trim($data[POS_NO_1]);
			$search_pos_no_name1 = trim($data[POS_NO_NAME_1]);
			
			$search_pos_no2 = trim($data[POS_NO_2]);
			$search_pos_no_name2 = trim($data[POS_NO_NAME_2]);
			
			$search_pos_no3 = trim($data[POS_NO_3]);
			$search_pos_no_name3 = trim($data[POS_NO_NAME_3]);

			$PL_PN_NAME_1 = $PL_PN_NAME_2 = $PL_PN_NAME_3 = "";				
			if ($PER_TYPE == 1) { 				// ข้าราชการ
				$PL_PN_CODE_1 = trim($data[PL_CODE_1]);
				$PL_PN_CODE_2 = trim($data[PL_CODE_2]);
				$PL_PN_CODE_3 = trim($data[PL_CODE_3]);
				
				$cmd = "select PL_CODE, PL_NAME from PER_LINE where PL_CODE in ('$PL_PN_CODE_1', '$PL_PN_CODE_2', '$PL_PN_CODE_3')";
				$db_dpis2->send_cmd($cmd);
				while ( $data2 = $db_dpis2->get_array() ) {
					$code_tmp = trim($data2[PL_CODE]);
					$name_tmp = trim($data2[PL_NAME]);
					if ( $PL_PN_CODE_1 == $code_tmp )		$PL_PN_NAME_1 = $name_tmp;
					if ( $PL_PN_CODE_2 == $code_tmp )		$PL_PN_NAME_2 = $name_tmp;
					if ( $PL_PN_CODE_3 == $code_tmp )		$PL_PN_NAME_3 = $name_tmp;
				}				
			} elseif ($PER_TYPE == 2) { 		// ลูกจ้างประจำ
				$PL_PN_CODE_1 = trim($data[PN_CODE_1]);
				$PL_PN_CODE_2 = trim($data[PN_CODE_2]);
				$PL_PN_CODE_3 = trim($data[PN_CODE_3]);
				
				$cmd = "select PN_CODE, PN_NAME from PER_POS_NAME where PN_CODE in ('$PL_PN_CODE_1', '$PL_PN_CODE_2', '$PL_PN_CODE_3')";
				$db_dpis2->send_cmd($cmd);
				while ( $data2 = $db_dpis2->get_array() ) {
					$code_tmp = trim($data2[PN_CODE]);
					$name_tmp = trim($data2[PN_NAME]);
					if ( $PL_PN_CODE_1 == $code_tmp )		$PL_PN_NAME_1 = $name_tmp;
					if ( $PL_PN_CODE_2 == $code_tmp )		$PL_PN_NAME_2 = $name_tmp;
					if ( $PL_PN_CODE_3 == $code_tmp )		$PL_PN_NAME_3 = $name_tmp;
				}				
			}	 elseif ($PER_TYPE == 3) { 		// พนักงานราชการ
				$PL_PN_CODE_1 = trim($data[PN_CODE_1]);
				$PL_PN_CODE_2 = trim($data[PN_CODE_2]);
				$PL_PN_CODE_3 = trim($data[PN_CODE_3]);
				
				$cmd = "select EP_CODE, EP_NAME from PER_POS_NAME where EP_CODE in ('$PL_PN_CODE_1', '$PL_PN_CODE_2', '$PL_PN_CODE_3')";
				$db_dpis2->send_cmd($cmd);
				while ( $data2 = $db_dpis2->get_array() ) {
					$code_tmp = trim($data2[EP_CODE]);
					$name_tmp = trim($data2[EP_NAME]);
					if ( $PL_PN_CODE_1 == $code_tmp )		$PL_PN_NAME_1 = $name_tmp;
					if ( $PL_PN_CODE_2 == $code_tmp )		$PL_PN_NAME_2 = $name_tmp;
					if ( $PL_PN_CODE_3 == $code_tmp )		$PL_PN_NAME_3 = $name_tmp;
				}				
			}	elseif ($PER_TYPE == 4) { 		// ลูกจ้างชั่วคราว
				$PL_PN_CODE_1 = trim($data[PN_CODE_1]);
				$PL_PN_CODE_2 = trim($data[PN_CODE_2]);
				$PL_PN_CODE_3 = trim($data[PN_CODE_3]);
				
				$cmd = "select TP_CODE, TP_NAME from PER_TEMP_POS_NAME where TP_CODE in ('$PL_PN_CODE_1', '$PL_PN_CODE_2', '$PL_PN_CODE_3')";
				$db_dpis2->send_cmd($cmd);
				//$db_dpis2->show_error();
				while ( $data2 = $db_dpis2->get_array() ) {
					$code_tmp = trim($data2[TP_CODE]);
					$name_tmp = trim($data2[TP_NAME]);
					if ( $PL_PN_CODE_1 == $code_tmp )		$PL_PN_NAME_1 = $name_tmp;
					if ( $PL_PN_CODE_2 == $code_tmp )		$PL_PN_NAME_2 = $name_tmp;
					if ( $PL_PN_CODE_3 == $code_tmp )		$PL_PN_NAME_3 = $name_tmp;
				}				
			}			
			
			$ORG_NAME_10 = $ORG_NAME_20 = $ORG_NAME_30 = $$ORG_NAME_11 = $ORG_NAME_21 = $ORG_NAME_31 = $ORG_NAME_12 = $ORG_NAME_22 = $ORG_NAME_32 = "";
			$ORG_ID_10 = (trim($data[DEPARTMENT_ID_1]))? trim($data[DEPARTMENT_ID_1]) : 0;
			$ORG_ID_20 = (trim($data[DEPARTMENT_ID_2]))? trim($data[DEPARTMENT_ID_2]) : 0;
			$ORG_ID_30 = (trim($data[DEPARTMENT_ID_3]))? trim($data[DEPARTMENT_ID_3]) : 0;
			$cmd = "  select ORG_ID, ORG_NAME from PER_ORG where ORG_ID in ($ORG_ID_10, $ORG_ID_20, $ORG_ID_30)  ";
			$db_dpis2->send_cmd($cmd);
			while ( $data2 = $db_dpis2->get_array() ) {
				$code_tmp = trim($data2[ORG_ID]);
				$name_tmp = trim($data2[ORG_NAME]);
			
				if ( $ORG_ID_10 == $code_tmp ) {	$ORG_NAME_10 = $name_tmp; }
				if ( $ORG_ID_20 == $code_tmp ) {	$ORG_NAME_20 = $name_tmp; }
				if ( $ORG_ID_30 == $code_tmp ) {	$ORG_NAME_30 = $name_tmp; }
			}
			
			$ORG_ID_12 = (trim($data[ORG_ID_1]))? trim($data[ORG_ID_1]) : 0;
			$ORG_ID_22 = (trim($data[ORG_ID_2]))? trim($data[ORG_ID_2]) : 0;
			$ORG_ID_32 = (trim($data[ORG_ID_3]))? trim($data[ORG_ID_3]) : 0;	
//			$cmd = "	select ORG_ID, ORG_NAME, ORG_ID_REF from PER_ORG where ORG_ID in ($ORG_ID_12, $ORG_ID_22, $ORG_ID_32)";
			$cmd = "	select ORG_ID, ORG_NAME from PER_ORG where ORG_ID in ($ORG_ID_12, $ORG_ID_22, $ORG_ID_32)";
			$db_dpis2->send_cmd($cmd);
			while ( $data2 = $db_dpis2->get_array() ) {
				$code_tmp = trim($data2[ORG_ID]);
				$name_tmp = trim($data2[ORG_NAME]);
/*
				if ( $ORG_ID_12 == $code_tmp ) {	$ORG_NAME_12 = $name_tmp;		$arr_ORG_ID_REF[0] = trim($data2[ORG_ID_REF]); }
				if ( $ORG_ID_22 == $code_tmp ) {	$ORG_NAME_22 = $name_tmp;		$arr_ORG_ID_REF[1] = trim($data2[ORG_ID_REF]); }
				if ( $ORG_ID_32 == $code_tmp ) {	$ORG_NAME_32 = $name_tmp;		$arr_ORG_ID_REF[2] = trim($data2[ORG_ID_REF]); }
*/

				if ( $ORG_ID_12 == $code_tmp ) {	$ORG_NAME_12 = $name_tmp; }
				if ( $ORG_ID_22 == $code_tmp ) {	$ORG_NAME_22 = $name_tmp; }
				if ( $ORG_ID_32 == $code_tmp ) {	$ORG_NAME_32 = $name_tmp; }
			}
			
//			$search_arr = implode(", ", $arr_ORG_ID_REF); 
//			$cmd = "  select ORG_ID, ORG_NAME from PER_ORG where ORG_ID in ($search_arr)  ";
			$ORG_ID_11 = (trim($data[ORG_ID_REF_1]))? trim($data[ORG_ID_REF_1]) : 0;
			$ORG_ID_21 = (trim($data[ORG_ID_REF_2]))? trim($data[ORG_ID_REF_2]) : 0;
			$ORG_ID_31 = (trim($data[ORG_ID_REF_3]))? trim($data[ORG_ID_REF_3]) : 0;	
			$cmd = "  select ORG_ID, ORG_NAME from PER_ORG where ORG_ID in ($ORG_ID_11, $ORG_ID_21, $ORG_ID_31)  ";
			$db_dpis2->send_cmd($cmd);
			while ( $data2 = $db_dpis2->get_array() ) {
				$code_tmp = trim($data2[ORG_ID]);
				$name_tmp = trim($data2[ORG_NAME]);
/*
				if ( $arr_ORG_ID_REF[0] == $code_tmp ) {
						$ORG_ID_11 = $code_tmp;
						$ORG_NAME_11 = $name_tmp;
				}
				if ( $arr_ORG_ID_REF[1] == $code_tmp ) {
						$ORG_ID_21 = $code_tmp;
						$ORG_NAME_21 = $name_tmp;
				}	
				if ( $arr_ORG_ID_REF[2] == $code_tmp ) {
						$ORG_ID_31 = $code_tmp;				
						$ORG_NAME_31 = $name_tmp;	
				}
*/
				if ( $ORG_ID_11 == $code_tmp ) {	$ORG_NAME_11 = $name_tmp; }
				if ( $ORG_ID_21 == $code_tmp ) {	$ORG_NAME_21 = $name_tmp; }
				if ( $ORG_ID_31 == $code_tmp ) {	$ORG_NAME_31 = $name_tmp; }
			}

		} 	// end if($UPD || $VIEW)
	}		// end if($PER_ID)

	if( !$UPD && !$DEL && !$VIEW ){
		if( !$PER_ID ) {
			$PER_ID_ = "";
			$PER_NAME = "";
			$PER_CARDNO = "";
			$POSITION = "";
			$PER_ORG1 = "";
			$PER_ORG2 = "";
			$PER_ORG3 = "";
			$EN_NAME = "";		

			if($CTRL_TYPE < 3 && $SESS_USERGROUP_LEVEL < 3){ 
				$MINISTRY_ID = "";
				$MINISTRY_NAME = "";
			} // end if
			if($CTRL_TYPE < 4 && $SESS_USERGROUP_LEVEL < 4){ 
				$DEPARTMENT_ID = "";
				$DEPARTMENT_NAME = "";
			} // end if
		}
		$MV_ID = "";
		$MV_DATE = "";
		$MV_REASON = "";
		$MV_REMARK = "";
		
		$PL_PN_NAME_1 = "";
		$PL_PN_CODE_1 = "";
		$PL_PN_NAME_2 = "";
		$PL_PN_CODE_2 = "";
		$PL_PN_NAME_3 = "";
		$PL_PN_CODE_3 = "";
		
		$ORG_ID_10 = "";	
		$ORG_ID_11 = "";
		$ORG_ID_12 = "";		
		$ORG_ID_20 = "";
		$ORG_ID_21 = "";
		$ORG_ID_22 = "";	
		$ORG_ID_30 = "";
		$ORG_ID_31 = "";
		$ORG_ID_32 = "";
		$ORG_NAME_10 = "";
		$ORG_NAME_11 = "";
		$ORG_NAME_12 = "";		
		$ORG_NAME_20 = "";
		$ORG_NAME_21 = "";
		$ORG_NAME_22 = "";		
		$ORG_NAME_30 = "";
		$ORG_NAME_31 = "";
		$ORG_NAME_32 = "";
		$search_pos_no1 = "";
		$search_pos_no_name1 = "";
		$search_pos_no2 = "";
		$search_pos_no_name2 = "";
		$search_pos_no3 = "";
		$search_pos_no_name3 = "";
	} // end if	
	//echo "$PER_ORG1 - $PER_ORG2 - $PER_ORG3 <br>";
?>