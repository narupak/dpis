<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
     
	
/*เพิ่มเติม*/
function get_child_special_skillgrp($db_fnc,$ss_code){
   $cmd ="SELECT SK_SUB.SS_CODE, 
    
                                                SK_MAIN.SS_NAME AS NAME_MAIN,
                                                  SK_MIN.SPMIN_TITLE, 
                                                  SK_SUB.SS_NAME AS NAME_SUB , 

                                                SK_SUB.REF_CODE 
                                                FROM PER_SPECIAL_SKILLGRP SK_SUB 
                                                LEFT JOIN PER_SPECIAL_SKILLGRP SK_MAIN ON(TRIM(SK_MAIN.SS_CODE)=TRIM(SK_SUB.REF_CODE)) 
                                                LEFT JOIN PER_MAPPING_SKILLMIN SK_MAP ON(TRIM(SK_MAP.SS_CODE)=TRIM(SK_SUB.SS_CODE)) 
                                                LEFT JOIN PER_SPECIAL_SKILLMIN SK_MIN ON(TRIM(SK_MIN.SPMIN_CODE)=TRIM(SK_MAP.SPMIN_CODE)) 
                                                WHERE TRIM(SS_CODE) ='".trim($ss_code)."'
                                                AND SK_SUB.REF_CODE IS NOT NULL ";
                                                
                            $db_fnc->send_cmd($cmd);
                           $data_fnc = $db_fnc->get_array();
                           $MAIN    = $data_fnc[NAME_MAIN];
                           $TTLE_SP  = $data_fnc[SPMIN_TITLE];
                           $NAME_SUB  = $data_fnc[NAME_SUB];
                           
                           $name_all = $MAIN."/".$TTLE_SP."/".$NAME_SUB;
                           
                         // echo"tester=>".$cmd;
                           return $name_all;
                           
                           
                           
                           
    /*"SELECT SS_CODE,CASE WHEN LEVEL=1 THEN SS_NAME 
                  ELSE SUBSTR(SYS_CONNECT_BY_PATH(SS_NAME, '|'),2,LENGTH(SYS_CONNECT_BY_PATH(SS_NAME, '|'))) END AS SS_NAME    
            FROM PER_SPECIAL_SKILLGRP
            WHERE  
            START WITH REF_CODE IS NULL
            CONNECT BY NOCYCLE PRIOR TRIM(SS_CODE) = TRIM(REF_CODE)";*/
   
}
/**/
function get_now($db_dpis){
                       $cmd = "SELECT TO_CHAR(SYSDATE,'YYYY-MM-DD HH24:mi:ss') as TIME from DUAL";
                       $db_dpis->send_cmd($cmd);
                       $data = $db_dpis->get_array();
                        $UPDATE_DATE = $data[TIME];
                       return $UPDATE_DATE;
                   }




	/*ตรวจสอบก่อนทำการแทรกคอลัมน์*/
	 $cmdChk ="  SELECT COUNT(COLUMN_NAME) AS CNT
				         FROM USER_TAB_COLS
				         WHERE  TABLE_NAME = 'PER_SPECIAL_SKILL'
				         AND UPPER(COLUMN_NAME) IN('SPS_SEQ_NO')";
	 $db_dpis->send_cmd($cmdChk);
	 $dataChk = $db_dpis->get_array();
	 if($dataChk[CNT]=="0"){
		 $cmdA = "ALTER TABLE PER_SPECIAL_SKILL ADD  SPS_SEQ_NO NUMBER(5)";
		 $db_dpis->send_cmd($cmdA);
		 $cmdA = "COMMIT";
		 $db_dpis->send_cmd($cmdA);
	 }
	 
	 /*ขยาย fld INV_DETAIL เพื่อให้รองรับการคีข้อความยาวๆ จาก 200 เป็น 2000*/
	 $cmdModify = "select column_name, data_type ,data_length
				 from user_tab_columns
				 where table_name = 'PER_SPECIAL_SKILL'
				 and column_name = 'SPS_EMPHASIZE'";
	 $db_dpis2->send_cmd($cmdModify);
	 $dataModify = $db_dpis2->get_array_array();
	 $data_length = $dataModify[2];
	 if($data_length < 2000){
		 $cmdModify = "ALTER TABLE PER_SPECIAL_SKILL modify SPS_EMPHASIZE VARCHAR2(2000)";
		 $db_dpis2->send_cmd($cmdModify);
		 $db_dpis2->send_cmd("COMMIT");
	 }
 	 
	 
         /*ตรวจสอบก่อนทำการแทรกคอลัมน์*/
         $cmdChk ="  SELECT COUNT(COLUMN_NAME) AS CNT
				         FROM USER_TAB_COLS
				         WHERE  TABLE_NAME = 'PER_SPECIAL_SKILLGRP'
				         AND UPPER(COLUMN_NAME) IN('REF_CODE')";
	 $db_dpis->send_cmd($cmdChk);
	 $dataChk = $db_dpis->get_array();
	 if($dataChk[CNT]=="0"){
		 $cmdA = "ALTER TABLE PER_SPECIAL_SKILLGRP ADD REF_CODE CHAR(10)";
		 $db_dpis->send_cmd($cmdA);
		 $cmdA = "COMMIT";
		 $db_dpis->send_cmd($cmdA);
	 }
         
	 ///////////////////////////

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	if($PER_ID){
		if($DPISDB=="odbc"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		}elseif($DPISDB=="oci8"){
			$cmd = " select 	PER_PRENAME.PN_NAME, PER_PERSONAL.PER_NAME, PER_PERSONAL.PER_SURNAME, 
							PER_PERSONAL.PER_TYPE, PER_PERSONAL.PER_CARDNO,PER_PERSONAL.DEPARTMENT_ID
 					from		PER_PERSONAL, PER_PRENAME
					where	PER_PERSONAL.PN_CODE=PER_PRENAME.PN_CODE(+) and 
							PER_PERSONAL.PER_ID=$PER_ID ";
		}elseif($DPISDB=="mysql"){
			$cmd = " select 	PRE.PN_NAME, PER.PER_NAME, PER.PER_SURNAME, PER.PER_TYPE, PER.PER_CARDNO,PER.DEPARTMENT_ID
					from		PER_PERSONAL as PER
							left join PER_PRENAME as PRE on (PER.PN_CODE=PRE.PN_CODE)
					where	PER.PER_ID=$PER_ID ";
		} // end if
		
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_NAME = $data[PN_NAME] . $data[PER_NAME] ." ". $data[PER_SURNAME];
		$PER_CARDNO = trim($data[PER_CARDNO]);
		$PER_ID_DEPARTMENT_ID = trim($data[DEPARTMENT_ID]);
		
		
		
	} // end if

	$UPDATE_DATE = get_now($db_dpis);

	// kittiphat
	
	if($command=="REORDER"){
		if (!$SPS_SEQ_NO) $SPS_SEQ_NO = "NULL";
		foreach($ARR_ORDER as $CODE => $SEQ_NO){
			if($SEQ_NO=="") { 
				$cmd = " update PER_SPECIAL_SKILL set SPS_SEQ_NO='' where SPS_ID=$CODE "; 
				}else {	
				$cmd = " update PER_SPECIAL_SKILL set SPS_SEQ_NO=$SEQ_NO where SPS_ID=$CODE "; 
			}
			//echo $cmd;
			$db_dpis->send_cmd($cmd);
		} // end foreach

		//insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เรียงลำดับประวัติความเชี่ยวชาญพิเศษ [$CODE : $SEQ_NO]");
	} // end if
	
	///////

       
        
        
	if ($command == "SETFLAG") {
		$setflagaudit =  implode(",",$list_audit_id);
//		echo "$setflagaudit";
		$cmd = " update PER_SPECIAL_SKILL set AUDIT_FLAG = 'N' where SPS_ID in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update PER_SPECIAL_SKILL set AUDIT_FLAG = 'Y' where SPS_ID in (".stripslashes($setflagaudit).") ";
		$db_dpis->send_cmd($cmd);
//		echo "$cmd<br>";
//		$db_dpis->show_error();
		
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ตั้งค่าการตรวจสอบข้อมูล");
	}
	
	if($TMPSPS_SEQ_NO){$Save_SPS_SEQ_NO=$TMPSPS_SEQ_NO;}else{$Save_SPS_SEQ_NO="NULL";}

	if($command=="ADD" && $PER_ID){
		$cmd2="select  SS_CODE from PER_SPECIAL_SKILL  where PER_ID=$PER_ID and SS_CODE='$SS_CODE' and SPS_EMPHASIZE='$SPS_EMPHASIZE' ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); 
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจากคุณระบุด้านความเชี่ยวชาญพิเศษและเน้นทางซ้ำ !!!");
				-->   </script>	<? 
		} else {	  	
			$cmd = " select max(SPS_ID) as max_id from PER_SPECIAL_SKILL ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$data = array_change_key_case($data, CASE_LOWER);
			$SPS_ID = $data[max_id] + 1;
                        
			$remove_character = array("\r\n", "\n", "\r", "\t", "  ","<p>", "</p>","<br>", "</br>");
			$Save_SPS_EMPHASIZE= str_replace($remove_character ," ", trim($SPS_EMPHASIZE)); 
                        $Save_SPS_REMARK= str_replace($remove_character ," ", trim($SPS_REMARK)); 
                       
                     /*   $cmd = " SELECT DATA_LENGTH,COLUMN_NAME 
                                FROM user_tab_columns where table_name = 'PER_SPECIAL_SKILL' AND column_name IN('SPS_EMPHASIZE','SPS_REMARK') ";
			$db_dpis->send_cmd($cmd);
			
			
                          while ($data = $db_dpis->get_array()) {
                             if($data[COLUMN_NAME]=="SPS_EMPHASIZE"){
                                $MAX_SPS_EMPHASIZE = $data[DATA_LENGTH];
                            }
                            if($data[COLUMN_NAME]=="SPS_REMARK"){
                                $MAX_SPS_REMARK = $data[DATA_LENGTH];
                            }
                        }*/
                        $MAX_LEN =2000;
                        
			
                        
                        
                        $val_fld_SPS_EMPHASIZE="substr('$Save_SPS_EMPHASIZE',1,".$MAX_LEN.")";
                        $val_fld_SPS_REMARK="substr('$Save_SPS_REMARK',1,".$MAX_LEN.")";
                        
			$cmd = " insert into PER_SPECIAL_SKILL (SPS_ID, PER_ID, SS_CODE, SPS_EMPHASIZE, SPS_REMARK, PER_CARDNO, UPDATE_USER, UPDATE_DATE,AUDIT_FLAG,SPS_SEQ_NO,LEVELSKILL_CODE)
					values ($SPS_ID, $PER_ID, '$SS_CODE', $val_fld_SPS_EMPHASIZE, $val_fld_SPS_REMARK, '$PER_CARDNO', $SESS_USERID, '$UPDATE_DATE','N',$Save_SPS_SEQ_NO,'$LE_CODE') ";
			//  echo $cmd;
                                                       $db_dpis->send_cmd($cmd);
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติความเชี่ยวชาญพิเศษ [$PER_ID : $SPS_ID : $SS_CODE]");
			$ADD_NEXT = 1;
		} // end if
	}

	if($command=="UPDATE" && $PER_ID && $SPS_ID){
                            
		$cmd2="select  SS_CODE from PER_SPECIAL_SKILL  where PER_ID=$PER_ID and SS_CODE='$SS_CODE' and SPS_EMPHASIZE='$SPS_EMPHASIZE' AND SPS_ID!=$SPS_ID ";
		$count=$db_dpis->send_cmd($cmd2);
		//$db_dpis->show_error(); echo "<hr>$cmd2<br>";
		if($count) { ?>  <script> <!--  
			alert("ไม่สามารถแก้ไขข้อมูลได้ เนื่องจากคุณระบุด้านความเชี่ยวชาญพิเศษและเน้นทางซ้ำ !!!");
				-->   </script>	<? 
		} else {	 
			$remove_character = array("\r\n", "\n", "\r", "\t", "  ","<p>", "</p>","<br>", "</br>");
			$Save_SPS_EMPHASIZE= str_replace($remove_character ," ", trim($SPS_EMPHASIZE));
                       $Save_SPS_REMARK= str_replace($remove_character ," ", trim($SPS_REMARK)); 
                        
                        $val_fld_SPS_EMPHASIZE="substr('$Save_SPS_EMPHASIZE',1,(SELECT data_length FROM user_tab_columns where table_name = 'PER_SPECIAL_SKILL' AND column_name='SPS_EMPHASIZE' ))";
                        $val_fld_SPS_REMARK="substr(' $Save_SPS_REMARK',1,(SELECT data_length FROM user_tab_columns where table_name = 'PER_SPECIAL_SKILL' AND column_name='SPS_REMARK' ))";
                  
                                                 $cmd = " UPDATE PER_SPECIAL_SKILL SET
							SS_CODE='$SS_CODE', 
							SPS_EMPHASIZE=$val_fld_SPS_EMPHASIZE, 
							SPS_REMARK=   $val_fld_SPS_REMARK, 
							PER_CARDNO=   '$PER_CARDNO', 
							UPDATE_USER=  $SESS_USERID, 
							UPDATE_DATE=  '$UPDATE_DATE',
							SPS_SEQ_NO=$Save_SPS_SEQ_NO,
                                                        LEVELSKILL_CODE='$LE_CODE'
						WHERE SPS_ID=$SPS_ID ";
			$db_dpis->send_cmd($cmd);
                     // echo"UPDATE=>".$cmd;
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติความเชี่ยวชาญพิเศษ [$PER_ID : $SPS_ID : $SS_CODE]");
		} // end if
	}
	
	if($command=="DELETE" && $PER_ID && $SPS_ID){
		$cmd = " select SS_CODE from PER_SPECIAL_SKILL where SPS_ID=$SPS_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$SS_CODE = $data[SS_CODE];
		
		$cmd = " delete from PER_SPECIAL_SKILL where SPS_ID=$SPS_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติความเชี่ยวชาญพิเศษ [$PER_ID : $SPS_ID : $SS_CODE]");

	} // end if

	if(($UPD && $PER_ID && $SPS_ID) || ($VIEW && $PER_ID && $SPS_ID)){
		   $cmd = " SELECT 	SPS_ID, pa.SS_CODE, pag.SS_NAME, SPS_EMPHASIZE, SPS_REMARK, pa.UPDATE_USER, 
									pa.UPDATE_DATE ,pa.SPS_SEQ_NO,pa.LEVELSKILL_CODE,pag.REF_CODE
						FROM			PER_SPECIAL_SKILL pa
						left join PER_SPECIAL_SKILLGRP pag on(pa.SS_CODE=pag.SS_CODE)
						WHERE		pa.SPS_ID=$SPS_ID  ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$SPS_ID = $data[SPS_ID];
		$SS_CODE = $data[SS_CODE];
                $LE_CODE = $data[LEVELSKILL_CODE];
     		$SS_NAME = get_child_special_skillgrp($db_dpis2,$SS_CODE);//$data[SS_NAME];
                $SS_NAME_MAIN = $data[SS_NAME];
		$SPS_EMPHASIZE = $data[SPS_EMPHASIZE];
		$SPS_REMARK = $data[SPS_REMARK];
		$UPDATE_USER = $data[UPDATE_USER];
                $REF_CODE_V = $data[REF_CODE];
                $SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		$SPS_SEQ_NO = "$data[SPS_SEQ_NO]";
                $cmd = " SELECT LEVELSKILL_NAME FROM PER_LEVELSKILL WHERE LEVELSKILL_CODE = '$LE_CODE'";
                $db_dpis2->send_cmd($cmd);
		$data2 = $db_dpis2->get_array();
                $LE_NAME = $data2[LEVELSKILL_NAME];
              // echo "testet".$REF_CODE_V. $TEM_GET_CHILD.$SS_CODE;
              // echo "Time=>".$SHOW_UPDATE_DATE."=>".$SHOW_UPDATE_USER;
		if($data[SPS_SEQ_NO] > 0){
			$SPS_SEQ_NO = $data[SPS_SEQ_NO];
		}
		
		$cmd ="select TITLENAME, FULLNAME,UPDATE_DATE from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		//$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
      	} // end if
	
	if( !$UPD && !$VIEW ){
		unset($SPS_ID);
		unset($SPS_EMPHASIZE);
		unset($SPS_REMARK);
		unset($SS_CODE);
		unset($SS_NAME);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
                unset($LE_NAME);
                unset($LE_CODE);
	} // end if
	
	
	if(!$SPS_ID){
		$cmd = " select 	NVL(max(SPS_SEQ_NO),0)+1 as MAXNO
				from		PER_SPECIAL_SKILL
				where	PER_ID=$PER_ID ";
		$db_dpis2->send_cmd($cmd);
		$data = $db_dpis2->get_array();
		$SPS_SEQ_NO= substr($data[MAXNO],0,5);
	}
	
?>