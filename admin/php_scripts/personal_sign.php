<?	
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	include("php_scripts/function_list.php");	
	include("php_scripts/load_per_control.php");	

	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);

        
        
        
       
	if(!$search_sign_type)	$search_sign_type = 0;

	switch($CTRL_TYPE){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
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

	$UPDATE_DATE = date("Y-m-d H:i:s");	

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;

	$cmd = " select max(SIGN_SEQ_NO) as max_seq_no from PER_SIGN ";
	$db_dpis->send_cmd($cmd);
	$data = $db_dpis->get_array();
	$data = array_change_key_case($data, CASE_LOWER);
	$SIGN_SEQ_NO = $data[max_seq_no] + 1;
	if(!$SIGN_SEQ_NO) $SIGN_SEQ_NO = 1;

	if($command == "ADD" && trim($PER_ID)){
			$SIGN_STARTDATE_INPUT = $SIGN_STARTDATE;
			$SIGN_ENDDATE_INPUT= $SIGN_ENDDATE;
			$SIGN_STARTDATE =  save_date($SIGN_STARTDATE_INPUT);
			$SIGN_ENDDATE =  save_date($SIGN_ENDDATE_INPUT);
                        $SING_TYPE_INPUT = $SIGN_TYPE;
                        $SIGN_PER_TYPE_INPUT =  $PER_TYPE;
                        
                        if($SIGN_PER_TYPE_INPUT == 1){
                            $SIGN_PER_TYPE = "ข้าราชการ";
                        }else if($SIGN_PER_TYPE_INPUT == 2){
                            $SIGN_PER_TYPE = "ลูกจ้างประจำ";
                        }else if($SIGN_PER_TYPE_INPUT == 3){
                            $SIGN_PER_TYPE = "พนักงานข้าราชการ";
                        } else if($SIGN_PER_TYPE_INPUT == 4){
                            $SIGN_PER_TYPE = "ลูกจ้างชั่วคราว";
                        }
                            
                         
                        if($SING_TYPE_INPUT==1){
                            $SIGN_NAME = "สลิปเงินเดือน";
                        }else if($SING_TYPE_INPUT==2){
                            $SIGN_NAME = " หนังสือแจ้งผลการเลื่อนเงินเดือน";
                        }else if($SING_TYPE_INPUT==3){
                            $SIGN_NAME = " หนังสือรับรอง";
                        }else if($SING_TYPE_INPUT==4){
                            $SIGN_NAME = " หนังสือรับรองการหักภาษี ณ ที่จ่าย";
                        }
                     
                            

			// หาช่วงเวลาที่ซ้ำกัน
			$cmd = " select SIGN_ID ,SIGN_TYPE,SIGN_PER_TYPE  from PER_SIGN 
							where SIGN_TYPE ='$SIGN_TYPE' and PER_ID=$PER_ID and
							SIGN_STARTDATE>='$SIGN_STARTDATE' and SIGN_PER_TYPE = $PER_TYPE ";   //and SIGN_ENDDATE<='$SIGN_ENDDATE'
			$count_data=$db_dpis->send_cmd($cmd);
//echo "-> $cmd"; 
                        $data = $db_dpis->get_array();
                        $SING_DATA = $data[SIGN_TYPE];
                        $SIGN_TYPE_T = $data[SIGN_PER_TYPE];
                        

			if(!$count_data){
				$cmd = " select max(SIGN_ID) as max_id from PER_SIGN ";
				$db_dpis->send_cmd($cmd);
				$data = $db_dpis->get_array();
				$data = array_change_key_case($data, CASE_LOWER);
				$SIGN_ID = $data[max_id] + 1;
                                
                                /*
                                    if(empty($SIGN_STATUS)){$SIGN_STATUS='0';}
                                    if($SIGN_STATUS!='0'){
                                        if($SIGN_STATUS=="1"){$SIGN_POS=$SIGN_POS1;}
                                        if($SIGN_STATUS=="2"){$SIGN_POS=$SIGN_POS2;}
                                        if($SIGN_STATUS=="3"){$SIGN_POS=$SIGN_POS3;}
                                        $SIGN_POS_Save="'".htmlspecialchars($SIGN_POS)."'"; 
                                    }else{
                                        $SIGN_POS_Save="NULL";
                                    }
                                */
                                //echo $SIGN_STATUS."=>".$SIGN_POS;
                                
				$cmd = " insert into PER_SIGN (SIGN_ID,SIGN_PER_TYPE,PER_ID,DEPARTMENT_ID, SIGN_ACTING, SIGN_STARTDATE,SIGN_ENDDATE,SIGN_NAME,SIGN_POSITION,SIGN_REMARK,SIGN_TYPE,SIGN_SEQ_NO,UPDATE_USER,UPDATE_DATE) 
								values ($SIGN_ID, $PER_TYPE,$PER_ID,$DEPARTMENT_ID, '$SIGN_ACTING','$SIGN_STARTDATE','$SIGN_ENDDATE','$PER_NAME','$PL_NAME','$SIGN_REMARK','$SIGN_TYPE','$SIGN_SEQ_NO', $SESS_USERID, '$UPDATE_DATE') ";
				$db_dpis->send_cmd($cmd);
                                
	//$db_dpis->show_error();
	//echo "ADD - > ".$cmd."<br>";
	
				insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > เพิ่มข้อมูล [".$SIGN_ID." : ".trim($SIGN_NAME)." : ".$SIGN_TYPE);
			}else{  // end if(!$count_data)
                        if($SING_TYPE_INPUT==$SING_DATA){
                          
?>			
				<script type="text/javascript">
					var SING_TYPE_INPUT = '<?=$SIGN_NAME; ?>';
                                        var SIGN_PER_TYPE_INPUT ='<?=$SIGN_PER_TYPE;?>';
					alert('ไม่สามารถเพิ่มข้อมูลได้ เพราะมีประเภทผู้ลงนาม '+SING_TYPE_INPUT+ ' ของ ' +SIGN_PER_TYPE_INPUT+ ' แล้ว');
				</script>
<?	}else{ ?>
                                 <script type="text/javascript">
					var SIGN_STARTDATE_INPUT = '<?=$SIGN_STARTDATE_INPUT; ?>';
					var SIGN_ENDDATE_INPUT = '<?=$SIGN_ENDDATE_INPUT; ?>';
					alert('ไม่สามารถเพิ่มข้อมูลได้ เพราะมีช่วงวันที่ '+SIGN_STARTDATE_INPUT+' - '+SIGN_ENDDATE_INPUT+'แล้ว');
				</script>

<?
                }          
			}
	} // end if

	if($command == "UPDATE" && trim($SIGN_ID) && trim($PER_ID)){
			$SIGN_STARTDATE =  save_date($SIGN_STARTDATE);
			$SIGN_ENDDATE =  save_date($SIGN_ENDDATE);
	
                        
			$cmd = " update PER_SIGN set 
								SIGN_PER_TYPE =$PER_TYPE,
								PER_ID = $PER_ID,
								DEPARTMENT_ID = $DEPARTMENT_ID,
								SIGN_STARTDATE = '$SIGN_STARTDATE',
								SIGN_ENDDATE = '$SIGN_ENDDATE',
								SIGN_NAME = '$PER_NAME',
								SIGN_POSITION ='$PL_NAME' ,
								SIGN_REMARK ='$SIGN_REMARK' ,
								SIGN_TYPE ='$SIGN_TYPE' ,
								SIGN_ACTING='$SIGN_ACTING',
								SIGN_SEQ_NO ='$SIGN_SEQ_NO' ,
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE'
							 where SIGN_ID=$SIGN_ID ";
			$db_dpis->send_cmd($cmd);
//			$db_dpis->show_error();
//			echo "UPD - > ".$cmd;
//	
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > แก้ไขข้อมูล [".$SIGN_ID." : ".trim($SIGN_NAME)." : ".$SIGN_TYPE);
	}
	
	if($command == "DELETE" && trim($SIGN_ID)){
		$cmd = " select PER_ID,SIGN_TYPE from PER_SIGN ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PER_ID = $data[PER_ID];
		$SIGN_TYPE = $data[SIGN_TYPE];
	
		$cmd = " delete from PER_SIGN where SIGN_ID=$SIGN_ID ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();

			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ลบข้อมูล [".$SIGN_ID." : ".trim($PER_ID)." : ".$SIGN_TYPE);
	}

	if($UPD || $VIEW){	// ดู / แก้ไข
		$cmd = " select 	SIGN_PER_TYPE,PER_ID, SIGN_ACTING, DEPARTMENT_ID,SIGN_STARTDATE,SIGN_ENDDATE,SIGN_NAME,SIGN_POSITION,SIGN_REMARK,SIGN_TYPE,SIGN_SEQ_NO
				  		 from 		PER_SIGN
						 where 	SIGN_ID=$SIGN_ID ";
		//echo "<br>>> $cmd<br>";	
		$count = $db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();

		$SIGN_PER_TYPE = $data[SIGN_PER_TYPE];
		$PER_ID = $data[PER_ID];
		$DEPARTMENT_ID = $data[DEPARTMENT_ID];

		$SIGN_STARTDATE =  show_date_format($data[SIGN_STARTDATE],1);			
		$SIGN_ENDDATE =  show_date_format($data[SIGN_ENDDATE],1);
		$PER_NAME = $data[SIGN_NAME];
		$SIGN_REMARK = $data[SIGN_REMARK];
		$SIGN_TYPE = $data[SIGN_TYPE];
                $PER_TYPE  = $data[SIGN_PER_TYPE];
     		$SIGN_SEQ_NO = $data[SIGN_SEQ_NO];
                $SIGN_POSITION = $data[SIGN_POSITION];
		$SIGN_ACTING = $data[SIGN_ACTING];
     
               

		$cmd = " select ORG_ID, ORG_NAME from PER_ORG 	where ORG_ID = $DEPARTMENT_ID";
		$db_dpis->send_cmd($cmd);		
		$data = $db_dpis->get_array();
		$DEPARTMENT_NAME = trim($data[ORG_NAME]);

		  $cmd = " select 	PN_CODE,PER_CARDNO, PER_NAME, PER_SURNAME, LEVEL_NO, PER_SALARY, PER_TYPE, POS_ID, POEM_ID, POEMS_ID
						 from		PER_PERSONAL
						 where	PER_ID=$PER_ID ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_CODE = trim($data[PN_CODE]);
		if(!trim($PER_CARDNO)) $PER_CARDNO = trim($data[PER_CARDNO]);
		/*$PER_NAME = trim($data[PER_NAME]);
		$PER_SURNAME = trim($data[PER_SURNAME]);*/
		$LEVEL_NO = trim($data[LEVEL_NO]);
		$PER_SALARY = trim($data[PER_SALARY]);
		$PER_TYPE_MAN = trim($data[PER_TYPE]);
               // echo "POWER".$PER_TYPE;
		$POS_ID = trim($data[POS_ID]);
		$POEM_ID = trim($data[POEM_ID]);
		$POEMS_ID = trim($data[POEMS_ID]);

/*		
		$cmd = " select PN_NAME from PER_PRENAME where trim(PN_CODE)='$PN_CODE' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$PN_NAME = trim($data[PN_NAME]);

		$PER_NAME = $PN_NAME . $PER_NAME . " " . $PER_SURNAME;
*/			
             $cmd = " select LEVEL_NAME, POSITION_LEVEL from PER_LEVEL where trim(LEVEL_NO)='$LEVEL_NO' ";
		$db_dpis->send_cmd($cmd);
		$data = $db_dpis->get_array();
		$LEVEL_NAME = trim($data[LEVEL_NAME]);
		$POSITION_LEVEL = $data[POSITION_LEVEL];
		if (!$POSITION_LEVEL) $POSITION_LEVEL = $LEVEL_NAME;

		if($PER_TYPE_MAN==1){
		$cmd = " select 	b.PL_NAME, c.ORG_NAME, a.PT_CODE
							 from 		PER_POSITION a, PER_LINE b, PER_ORG c
							 where	a.POS_ID=$POS_ID and a.PL_CODE=b.PL_CODE and a.ORG_ID=c.ORG_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PL_NAME = trim($data[PL_NAME]);
			$ORG_NAME = trim($data[ORG_NAME]);
			$PT_CODE = trim($data[PT_CODE]);
			$cmd = "	select PT_NAME from PER_TYPE where PT_CODE='$PT_CODE'";
			$db_dpis2->send_cmd($cmd);
			$data2 = $db_dpis2->get_array();
			$PT_NAME = trim($data2[PT_NAME]);
			$PL_NAME = trim($PL_NAME)?($PL_NAME . $POSITION_LEVEL . (($PT_NAME != "ทั่วไป" && $LEVEL_NO >= 6)?"$PT_NAME":"")):" ".$LEVEL_NAME;
		}elseif($PER_TYPE_MAN==2){
			$cmd = " select 	b.PN_NAME, c.ORG_NAME 
							 from 		PER_POS_EMP a, PER_POS_NAME b, PER_ORG c
							 where	a.POEM_ID=$POEM_ID and a.PN_CODE=b.PN_CODE and a.ORG_ID=c.ORG_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PL_NAME = trim($data[PN_NAME]);
			$ORG_NAME = trim($data[ORG_NAME]);
		}elseif($PER_TYPE_MAN==3){
			$cmd = " select 	b.EP_NAME, c.ORG_NAME 
							 from 		PER_POS_EMPSER a, PER_EMPSER_POS_NAME b, PER_ORG c
							 where	a.POEMS_ID=$POEMS_ID and a.EP_CODE=b.EP_CODE and a.ORG_ID=c.ORG_ID ";
			$db_dpis->send_cmd($cmd);
			$data = $db_dpis->get_array();
			$PL_NAME = trim($data[EP_NAME]);
			$ORG_NAME = trim($data[ORG_NAME]);
		} // end if
	} // end if
	
	if( (!$UPD && !$VIEW) ){
		unset($SIGN_ID);
		$SIGN_PER_TYPE = 1;
               
		unset($PER_ID);
		unset($SIGN_STARTDATE);
		unset($SIGN_ENDDATE);

		unset($PER_NAME);
		unset($PL_NAME);
		unset($LEVEL_NAME);
		unset($ORG_NAME);
		unset($PER_SALARY);

		unset($PL_NAME);
		unset($SIGN_REMARK);
	} // end if
?>