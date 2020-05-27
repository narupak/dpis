<?
	include("php_scripts/session_start.php");
	if (!function_exists('create_link_page')) include("php_scripts/function_share.php");		// create_link_page เป็น function ใน function_share.php ใช้ check ว่า function ไม่ซ้ำ
	include("php_scripts/load_per_control.php");

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);	
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
    $db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis4 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
        $debug=0;/*0=close,1=open*/
//	print("<pre> ( $include_add ) // $BKK_FLAG // <br>"); print_r($_POST); print("</pre>");
	if ($SESS_DEPARTMENT_NAME=="กรมชลประทาน") { 
		$NUMBER_DISPLAY = 2; 
		$PRINT_FONT = 3;
	}
	if (($BKK_FLAG==1 || $MFA_FLAG==1) && (trim($DEPARTMENT_ID)=="" || $DEPARTMENT_ID=="NULL" || is_null($DEPARTMENT_ID))) $DEPARTMENT_ID="''";
	if (is_null($ORG_ID) || $ORG_ID=="NULL") $ORG_ID=0;
	if (($COM_SITE=="personal_workflow" && $ORG_ID) || ($SESS_USERGROUP_LEVEL < 5 && $ORG_ID)) {
		$search_org_id = $ORG_ID;
		$search_org_name = $ORG_NAME;
		$ORG_ID_DTL = $ORG_ID;
		$ORG_NAME_DTL = $ORG_NAME;
	} else if ($SESS_USERGROUP_LEVEL < 5){
		$search_org_id = "0";
		$search_org_name = "";
	}	

	if(!$current_page) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	if(!$COM_PER_TYPE) $COM_PER_TYPE = 1;
	if($MFA_FLAG == 1 && $include_confirm == "php_scripts/data_move_req_command_confirm.php" && substr($COM_TYPE,0,3)=="503") $COM_LEVEL_SALP = 1;
	if(!$COM_LEVEL_SALP) $COM_LEVEL_SALP = "NULL";
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	$tmp_COM_DATE =  save_date($COM_DATE);
	
	if (!$ORG_ID_DTL && $_POST[ORG_ID_DTL]) $search_org_id = $_POST[ORG_ID_DTL];
	if (trim($ORG_ID_DTL)=="" || $ORG_ID_DTL=="NULL" || is_null($ORG_ID_DTL))	$ORG_ID_DTL="0";
	
	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) ){
		$cmd = " select max(COM_ID) as max_id from PER_COMMAND ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$data = array_change_key_case($data, CASE_LOWER);
		$COM_ID = $data[max_id] + 1;

		$cmd = " insert into PER_COMMAND (COM_ID, COM_NO, COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, 
						  COM_TYPE, COM_CONFIRM, COM_STATUS, DEPARTMENT_ID, ORG_ID, COM_DOC_TYPE, COM_LEVEL_SALP, 
						  UPDATE_USER, UPDATE_DATE) 
					     VALUES ($COM_ID, '$COM_NO', '$COM_NAME', '$tmp_COM_DATE', '$COM_NOTE', $COM_PER_TYPE, 
						 '$COM_TYPE', 0, '', $DEPARTMENT_ID, $ORG_ID_DTL, '$COM_DOC_TYPE', $COM_LEVEL_SALP, 
						 $SESS_USERID, '$UPDATE_DATE') ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
                //echo '<pre>'.$cmd;

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_ADD_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
		if ($include_add) include ("$include_add");
	}			// 	if( $command == "ADD" && trim(!$COM_ID) && trim($COM_NO) )

	if( $command == "UPDATE" && trim($COM_ID) ) {
		// DEPARTMENT_ID = $DEPARTMENT_ID , ORG_ID=$ORG_ID_DTL, 	//ไม่ควรให้แก้ไข DEPARTMENT_ID / ORG_ID ได้ ควรสร้างมาตั้งแต่ตอนเพิ่มบัญชี **** 
		$cmd = " update PER_COMMAND set  
						COM_NO='$COM_NO', 
						COM_NAME='$COM_NAME', 
						COM_DATE='$tmp_COM_DATE', 
						COM_NOTE='$COM_NOTE', 
						COM_PER_TYPE=$COM_PER_TYPE, 
						COM_TYPE='$COM_TYPE', 
						COM_DOC_TYPE='$COM_DOC_TYPE', 
						COM_LEVEL_SALP=$COM_LEVEL_SALP, 
						UPDATE_USER=$SESS_USERID, 
						UPDATE_DATE='$UPDATE_DATE' 
				where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);
		// echo $cmd;		
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_EDIT_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}			// 	if( $command == "UPDATE" && trim($COM_ID) )

// ============================================================
	// เมื่อมีการยืนยันข้อมูลของปัญชีแนบท้ายคำสั่ง
	if( $command == "COMMAND" && trim($COM_ID) ) {
		$cmd = " select COM_DATE,COM_NAME,COM_PER_TYPE from PER_COMMAND where COM_NO = '$COM_NO' and COM_ID != $COM_ID and COM_CONFIRM=1 ";
		//die($cmd);
		$count_data = $db_dpis1->send_cmd($cmd);
		if (trim($count_data) && empty($chkconfrim)) {	// เลขที่คำสั่งซ้ำ
			$data1 = $db_dpis1->get_array();
			$dup_COM_DATE = show_date_format($data1[COM_DATE], 1);
			$dup_COM_NAME = $data1[COM_NAME];
			$num++;
			//เดิม $error_move_personal.= "<tr><td width='6%'></td><td><font color='#FF0000'>$num.&nbsp;&nbsp;เลขที่คำสั่ง $COM_NO ซ้ำ วันที่ $dup_COM_DATE</font></td></tr>";
			$error_move_personal.= "
                            <tr>
                                <td width='6%'></td>
                                <td><font color='#FF0000'>$num.&nbsp;&nbsp;เลขที่คำสั่ง $COM_NO ซ้ำ กับเรื่อง ".$dup_COM_NAME." ลงวันที่ $dup_COM_DATE</font></td>
                            </tr>"; 
		}  // end if
		
		if ($tmp_COM_DATE > $UPDATE_DATE) {
			$num++;
			$error_move_personal.= "<tr><td width='6%'></td><td><font color='#FF0000'>$num.&nbsp;&nbsp;ไม่สามารถยืนยันคำสั่งลงวันที่ $COM_DATE มากกว่าวันนี้ได้</font></td></tr>";
		}  // end if
		
		if ($include_check) {
			if ($include_check=="php_scripts/data_move_req_command_confirm_check.php") {
				if ($DEPARTMENT_NAME != "กรมการปกครอง" && $MFA_FLAG != 1) include ("$include_check");
			} else include ("$include_check");
			$no=$num;
		}
		/**/
		if($include_check=='P0203'){
			if($COM_PER_TYPE==1){ //ตำแหน่งปัจจุบัน(ข้าราชการ)
				$valposid = 'pos_id'; 
				$tbjoin='per_position';
				$fldno = 'POS_NO';
			}
			if($COM_PER_TYPE==2){ //ตำแหน่งปัจจุบัน(ลูกจ้างประจำ)
				$valposid = 'poem_id'; 
				$tbjoin='per_pos_emp';
				$fldno = 'POEM_NO';
			}
			if($COM_PER_TYPE==3){ //ตำแหน่งปัจจุบัน(พนักงานราชการ)
				$valposid = 'poems_id';
				$tbjoin='per_pos_empser';
				$fldno = 'POEMS_NO';
			}
			if($COM_PER_TYPE==4){ //ตำแหน่งปัจจุบัน(ลูกจ้างชั่วคราว)
				$valposid = 'pot_id';
				$tbjoin='per_pos_temp';
				$fldno = 'POT_NO';
			}
			
			$cmdchk = "	select a.PER_ID, b.PN_NAME, a.PER_NAME, a.PER_SURNAME, a.PER_STATUS ,p.$fldno as POS_NO
						from PER_PERSONAL a, PER_PRENAME b ,$tbjoin p
						where	a.PN_CODE=b.PN_CODE(+) AND a.$valposid=p.$valposid(+)
							and a.$valposid IN(select pos_id from PER_COMDTL where COM_ID=$COM_ID) 
							and a.PER_ID NOT IN(select PER_ID from PER_COMDTL where COM_ID=$COM_ID)
							and (a.PER_STATUS=1 or a.PER_STATUS=0) and a.PER_TYPE=$COM_PER_TYPE ";
			$db_dpis->send_cmd($cmdchk);
                    
			if($DEPARTMENT_NAME == "กรมการปกครอง"){
				$no=0;
			}else{
				$no=0;
				while ($datachk = $db_dpis->get_array()) {
					$no++;
					$db_POS_NO = $datachk[POS_NO];
					$db_PER_ID = $datachk[PER_ID];
					$FULLNAME = $datachk[PN_NAME].$datachk[PER_NAME].' '.$datachk[PER_SURNAME];
					$error_move_personal.= "
                                <tr>
                                    <td width='6%'></td>
                                    <td><font color='#FF0000'>".($no+$num).".&nbsp;&nbsp;เลขที่ตำแหน่ง $db_POS_NO มีคนครองแล้ว ::(PER_ID: $db_PER_ID = $FULLNAME) </font></td>
                                </tr>";
				}
				
				if($no==0){
					$error_move_personal.= "";
				}
			}
		}
		
		//die('>>>>'.$error_move_personal); //$DEPARTMENT_NAME == "กรมการปกครอง"
		if (!trim($error_move_personal) ) {
			include ("$include_confirm");	
			$cmd = " update PER_COMMAND set  
							COM_CONFIRM=1, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' , COM_DATE='$tmp_COM_DATE'
					where COM_ID=$COM_ID  ";
            
			$db_dpis->send_cmd($cmd);		
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_CONFIRM_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
		
		} elseif (trim($error_move_personal)) {
			$cmd = " update PER_COMMAND set  
							COM_CONFIRM=0, UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' , COM_DATE='$tmp_COM_DATE'
					where COM_ID=$COM_ID  ";
			$db_dpis->send_cmd($cmd);		
			//$db_dpis->show_error();
		}
	}// 	if( $command == "COMMAND" && trim($COM_ID) ) 	

// ============================================================
	// เมื่อมีการส่งจากภูมิภาค
	if( $command == "SEND" && trim($COM_ID) && $SESS_USERGROUP_LEVEL > 4 ) {
		$cmd = " update PER_COMMAND set  
						COM_STATUS='$COM_SEND_STATUS', UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
						where COM_ID=$COM_ID  ";
		$db_dpis->send_cmd($cmd);	
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_SEND_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO." : ".$COM_TYPE."]");
	}		// 	if( $command == "SEND" && trim($COM_ID) ) 	
		
	if($command == "DELETE_COMDTL" && trim($COM_ID) && trim($PER_ID)){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID and PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $DEL_PERSON_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$PER_ID."]");
		$PER_ID = "";
	}
	
	if($command == "DELETE_COMMAND" && trim($COM_ID) ){
		$cmd = " delete from PER_COMDTL where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
			
		$cmd = " delete from PER_COMMAND where COM_ID=$COM_ID ";
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $COM_DEL_TITLE$COM_TYPE_NM [".trim($COM_ID)." : ".$COM_NO."]");
		$COM_ID = "";
	}

	if($command == "DELETE_POSITION"){
            $txt_error = '';
		if ($COM_PER_TYPE==1) {
			$cmd = " update PER_PERSONAL set POS_ID = null where (PER_TYPE = 1 and PER_STATUS = 2) or PER_TYPE != 1 ";
			$db_dpis->send_cmd($cmd);
                        $txt_error=$db_dpis->ERROR;

			$cmd = " delete from PER_POS_MOVE where POS_ID in (select POS_ID from PER_POSITION where POS_ID not in 
							(select POS_ID from PER_PERSONAL where PER_TYPE = 1 and (PER_STATUS = 0 or PER_STATUS = 1))) ";
			$db_dpis->send_cmd($cmd);
                        $txt_error=$db_dpis->ERROR;

			$cmd = " delete from PER_ORDER_DTL where POS_ID_OLD in (select POS_ID from PER_POSITION where POS_ID not in 
							(select POS_ID from PER_PERSONAL where PER_TYPE = 1 and (PER_STATUS = 0 or PER_STATUS = 1))) ";
			$db_dpis->send_cmd($cmd);
                        $txt_error=$db_dpis->ERROR;

			$cmd = " delete from PER_REQ3_DTL where POS_ID in (select POS_ID from PER_POSITION where POS_ID not in 
							(select POS_ID from PER_PERSONAL where PER_TYPE = 1 and (PER_STATUS = 0 or PER_STATUS = 1))) ";
			$db_dpis->send_cmd($cmd);
                        $txt_error=$db_dpis->ERROR;

			$cmd = " delete from PER_POSITION where POS_ID not in 
							(select POS_ID from PER_PERSONAL where PER_TYPE = 1 and (PER_STATUS = 0 or PER_STATUS = 1)) ";
			$db_dpis->send_cmd($cmd);
                        $txt_error=$db_dpis->ERROR;
		} elseif ($COM_PER_TYPE==2) {
			$cmd = " update PER_PERSONAL set POEM_ID = null where (PER_TYPE = 2 and PER_STATUS = 2) or PER_TYPE != 2 ";
			$db_dpis->send_cmd($cmd);
                        $txt_error=$db_dpis->ERROR;

			$cmd = " delete from PER_POS_EMP where POEM_ID not in 
							(select POEM_ID from PER_PERSONAL where PER_TYPE = 2 and (PER_STATUS = 0 or PER_STATUS = 1)) ";
			$db_dpis->send_cmd($cmd);
                        $txt_error=$db_dpis->ERROR;
		} elseif ($COM_PER_TYPE==3) {
			$cmd = " update PER_PERSONAL set POEMS_ID = null where (PER_TYPE = 3 and PER_STATUS = 2) or PER_TYPE != 3 ";
			$db_dpis->send_cmd($cmd);
                        $txt_error=$db_dpis->ERROR;

			$cmd = " delete from PER_POS_EMPSER where POEMS_ID not in 
							(select POEMS_ID from PER_PERSONAL where PER_TYPE = 3 and (PER_STATUS = 0 or PER_STATUS = 1) ) ";
			$db_dpis->send_cmd($cmd);
                        $txt_error=$db_dpis->ERROR;
		} elseif ($COM_PER_TYPE==4) {
			$cmd = " update PER_PERSONAL set POT_ID = null where (PER_TYPE = 4 and PER_STATUS = 2) or PER_TYPE != 4 ";
			$db_dpis->send_cmd($cmd);
                        $txt_error=$db_dpis->ERROR;

			$cmd = " delete from PER_POS_TEMP where POT_ID not in 
							(select POT_ID from PER_PERSONAL where PER_TYPE = 4 and (PER_STATUS = 0 or PER_STATUS = 1) ) ";
			$db_dpis->send_cmd($cmd);
                        $txt_error=$db_dpis->ERROR;
		}

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบตำแหน่งว่าง");
		$PER_ID = "";
	}

	if($command =="DELETE_POSITION_EMPTY"){
		//$txt_error = '';
		if ($COM_PER_TYPE==1) {
		$cmd = " update PER_POSITION set POS_REMARK = POS_REMARK||'$COM_NOTE',POS_STATUS = 2  where POS_ID not in 
				(select POS_ID from PER_PERSONAL where PER_TYPE = 1 and (PER_STATUS = 0 or PER_STATUS = 1)) ";
		$db_dpis->send_cmd($cmd);
			//$txt_error=$db_dpis->ERROR;	
			

		} elseif ($COM_PER_TYPE==2) {
			$cmd = " update PER_POS_EMP set POEM_REMARK = POEM_REMARK||'$COM_NOTE',POEM_STATUS = 2  where POEM_ID not in 
									(select POEM_ID from PER_PERSONAL where PER_TYPE = 2 and (PER_STATUS = 0 or PER_STATUS = 1)) ";
							$db_dpis->send_cmd($cmd);
								//$txt_error=$db_dpis->ERROR;	
		
		} elseif ($COM_PER_TYPE==3) {

			$cmd = " update PER_POS_EMPSER set POEMS_REMARK = POEMS_REMARK||'$COM_NOTE',POEM_STATUS = 2  where POEMS_ID not in 
			(select POEMS_ID from PER_PERSONAL where PER_TYPE = 3 and (PER_STATUS = 0 or PER_STATUS = 1)) ";
			$db_dpis->send_cmd($cmd);
				//$txt_error=$db_dpis->ERROR;	

		} elseif ($COM_PER_TYPE==4) {

			$cmd = " update PER_POS_TEMP set POT_REMARK = POT_REMARK||'$COM_NOTE',POT_STATUS = 2  where POT_ID not in 
			(select POT_ID from PER_PERSONAL where PER_TYPE = 4 and (PER_STATUS = 0 or PER_STATUS = 1)) ";
			$db_dpis->send_cmd($cmd);
			//	$txt_error=$db_dpis->ERROR;	
		}

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ยกเลิกตำแหน่งว่าง");
		$PER_ID = "";
	}
	
	if (trim($COM_ID)) {
		$cmd = "	select	COM_NO, a.COM_NAME, COM_DATE, COM_NOTE, COM_PER_TYPE, COM_LEVEL_SALP, COM_DOC_TYPE,
									a.COM_TYPE, COM_CONFIRM, COM_STATUS, b.COM_DESC, a.DEPARTMENT_ID, a.ORG_ID, UPDATE_USER, UPDATE_DATE 
						from		PER_COMMAND a, PER_COMTYPE b
						where	COM_ID=$COM_ID  and a.COM_TYPE=b.COM_TYPE	";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		//echo "<pre>".$cmd;
		$COM_NO = trim($data[COM_NO]);
		$COM_NAME = trim($data[COM_NAME]);
		$COM_DATE = show_date_format($data[COM_DATE], 1);
		$COM_NOTE = trim($data[COM_NOTE]);
		$COM_PER_TYPE = trim($data[COM_PER_TYPE]);
		$COM_LEVEL_SALP = trim($data[COM_LEVEL_SALP]);
		$COM_CONFIRM = trim($data[COM_CONFIRM]);
		$COM_STATUS = trim($data[COM_STATUS]);
		$COM_TYPE = trim($data[COM_TYPE]);
		$COM_TYPE_NAME = trim($data[COM_DESC]);
		$COM_DOC_TYPE = trim($data[COM_DOC_TYPE]);
		$DEPARTMENT_ID = $data[DEPARTMENT_ID];
		$ORG_ID_DTL = $data[ORG_ID];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		
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

		if($ORG_ID_DTL){
			$cmd = " select ORG_NAME from PER_ORG where ORG_ID=$ORG_ID_DTL ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$ORG_NAME_DTL = $data[ORG_NAME];
		}
	}

	if( !$COM_ID ){
		$COM_ID = "";
		$COM_NO = "";
		$COM_NAME = "";
		$COM_DATE = "";
		$COM_NOTE = "";
		$COM_PER_TYPE = "";
		$COM_LEVEL_SALP = "";
		$COM_CONFIRM = 0;
		$COM_STATUS = "";
		$COM_DOC_TYPE = "";
		
		$COM_TYPE = "";
		$COM_TYPE_NAME = "";		
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);

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