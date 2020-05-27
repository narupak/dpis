<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");

	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
     
        /*เพิ่มเติม*/
        function get_now($db_dpis){
                       $cmd = "SELECT TO_CHAR(SYSDATE,'YYYY-MM-DD HH24:mi:ss') as TIME from DUAL";
                       $db_dpis->send_cmd($cmd);
                       $data = $db_dpis->get_array();
                        $UPDATE_DATE = $data[TIME];
                       return $UPDATE_DATE;
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

	if($command=="ADD" && $PER_ID){
              //ทำไว้รอเพื่อมีการให้เช็คค่าซ้ำ 
            $BEH_DATE_A = explode("/",$BEH_DATE);
            $BEH_DATE_YEAR = $BEH_DATE_A[2]-543;
            $BEH_DATE =  $BEH_DATE_YEAR.$BEH_DATE_A[1].$BEH_DATE_A[0];
            /* $cmd2="select  BEH_ID from PER_BEHAVIOR  where PER_ID=$PER_ID and BEH_TITLE='$BEH_TITLE' and BEH_DATE='$BEH_DATE' ";
            $count=$db_dpis->send_cmd($cmd2);
            //$db_dpis->show_error(); 
            if($count) { ?>  <script> <!--  
                    alert("ไม่สามารถเพิ่มข้อมูลได้ เนื่องจากคุณระบุด้านพฤติกรรมและวันที่แสดงพฤติกรรมซ้ำ !!!");
                            -->   </script>	<? 
            } else {
             */
                    $cmd = " select max(BEH_ID) as max_id from PER_BEHAVIOR ";
                    $db_dpis->send_cmd($cmd);
                    $data = $db_dpis->get_array();
                    $data = array_change_key_case($data, CASE_LOWER);
                    $BEH_ID = $data[max_id] + 1;

                    $remove_character = array("\r\n", "\n", "\r", "\t", "  ","<p>", "</p>","<br>", "</br>","'");
                    $BEH_TITLE= str_replace($remove_character ,"", trim($BEH_TITLE)); 
                    $Save_BEH_DESC= str_replace($remove_character ,"", trim($BEH_DESC)); 
                    $Save_REMARK= str_replace($remove_character ,"", trim($REMARK)); 

                    $MAX_LEN =2000;
                    $MAX_LEN2 =255;

                    $val_fld_BEH_DESC="substr('$Save_BEH_DESC',1,".$MAX_LEN.")";
                    $val_fld_REMARK="substr('$Save_REMARK',1,".$MAX_LEN2.")";

                    $cmd = " insert into PER_BEHAVIOR (BEH_ID, PER_ID, PER_CARDNO, BEH_DATE, BEH_TITLE, BEH_DESC, REMARK , CREATE_DATE, CREATE_BY, UPDATE_DATE, UPDATE_USER)
                                    values ($BEH_ID, $PER_ID, '$PER_CARDNO', '$BEH_DATE', '$BEH_TITLE', $val_fld_BEH_DESC ,$val_fld_REMARK , '$UPDATE_DATE', $SESS_USERID ,'$UPDATE_DATE', $SESS_USERID) ";
                   //echo '<pre>'.$cmd;
                                                   $db_dpis->send_cmd($cmd);
                    insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มประวัติด้านพฤติกรรม [$PER_ID : $BEH_ID]");
                    $ADD_NEXT = 1;
          /*  } // end if*/
	}

	if($command=="UPDATE" && $PER_ID && $BEH_ID){
              
                $BEH_DATE_A = explode("/",$BEH_DATE);
                $BEH_DATE_YEAR = $BEH_DATE_A[2]-543;
                $BEH_DATE =  $BEH_DATE_YEAR.$BEH_DATE_A[1].$BEH_DATE_A[0];   
                $remove_character = array("\r\n", "\n", "\r", "\t", "  ","<p>", "</p>","<br>", "</br>","'");
                $Save_BEH_DESC= str_replace($remove_character ,"", trim($BEH_DESC));
                $Save_REMARK= str_replace($remove_character ,"", trim($REMARK));
                
                $BEH_TITLE= str_replace($remove_character ,"", trim($BEH_TITLE)); 
                $val_fld_BEH_DESC="substr('$Save_BEH_DESC',1,(SELECT data_length FROM user_tab_columns where table_name = 'PER_BEHAVIOR' AND column_name='BEH_DESC' ))";
                $val_fld_REMARK="substr('$Save_REMARK',1,(SELECT data_length FROM user_tab_columns where table_name = 'PER_BEHAVIOR' AND column_name='REMARK' ))";

                                         $cmd = " UPDATE PER_BEHAVIOR SET
                                                BEH_DATE='$BEH_DATE', 
                                                BEH_TITLE= '$BEH_TITLE', 
                                                BEH_DESC=   $val_fld_BEH_DESC,  
                                                REMARK  = $val_fld_REMARK,  
                                                UPDATE_USER=  $SESS_USERID, 
                                                UPDATE_DATE=  '$UPDATE_DATE'
                                        WHERE BEH_ID=$BEH_ID ";
                $db_dpis->send_cmd($cmd);
              //echo"UPDATE=><pre>".$cmd;
                insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขประวัติด้านพฤติกรรม [$PER_ID : $BEH_ID]");
		
	}
	
	if($command=="DELETE" && $PER_ID && $BEH_ID){
		$cmd = " delete from PER_BEHAVIOR where PER_ID=$PER_ID and BEH_ID=$BEH_ID ";
		$db_dpis->send_cmd($cmd);
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบประวัติด้านพฤติกรรม [$PER_ID : $BEH_ID]");

	} // end if

	if(($UPD && $PER_ID && $BEH_ID) || ($VIEW && $PER_ID && $BEH_ID)){
		   $cmd = " SELECT 	BEH_ID,PER_ID,PER_CARDNO,BEH_DATE,BEH_TITLE,BEH_DESC,REMARK, UPDATE_DATE,UPDATE_USER
                                                from 	PER_BEHAVIOR
                                                where 	PER_ID=$PER_ID and BEH_ID = $BEH_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$BEH_ID = $data[BEH_ID];
                $YEAR_BEH_DATE = substr($data[BEH_DATE],0,4)+543;
		$BEH_DATE = substr($data[BEH_DATE],6,2).'/'.substr($data[BEH_DATE],4,2).'/'.$YEAR_BEH_DATE;
		$BEH_TITLE = $data[BEH_TITLE];
                
		$BEH_DESC = (trim($data[BEH_DESC]))? $data[BEH_DESC] : "-" ;
                $REMARK = $data[REMARK];
		$UPDATE_USER = $data[UPDATE_USER];
                
                $SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
		$cmd ="select TITLENAME, FULLNAME,UPDATE_DATE from USER_DETAIL where id =  $UPDATE_USER ";
		$db->send_cmd($cmd);
		$data2 = $db->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
      	} // end if
	
	if( !$UPD && !$VIEW ){
                unset($BEH_ID);
		unset($BEH_TITLE);
		unset($BEH_DATE);
		unset($BEH_DESC);
                unset($REMARK);
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
	} // end if
        
?>