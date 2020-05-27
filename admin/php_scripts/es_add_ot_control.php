<? 
	include("php_scripts/session_start.php");
	include("php_scripts/function_share.php");
	$db_dpis2 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	$db_dpis3 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
	switch($CTRL_TYPE){
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			$PROVINCE_CODE = $PROVINCE_CODE;
			$PROVINCE_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			$MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
			$MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			break;
	} // end switch case

	switch($SESS_USERGROUP_LEVEL){								
		case 2 :
			$search_pv_code = $PROVINCE_CODE;
			$search_pv_name = $PROVINCE_NAME;
			$PROVINCE_CODE = $PROVINCE_CODE;
			$PROVINCE_NAME = $PROVINCE_NAME;
			break;
		case 3 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			break;
		case 4 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			$MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
			$MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			break;
		case 5 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			$MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
			$MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			$MAIN_ORG_ID = $ORG_ID;
			$MAIN_ORG_NAME = $ORG_NAME;
			break;
		case 6 :
			$search_ministry_id = $MINISTRY_ID;
			$search_ministry_name = $MINISTRY_NAME;
			$search_department_id = $DEPARTMENT_ID;
			$search_department_name = $DEPARTMENT_NAME;
			$search_org_id_1 = $ORG_ID_1;
			$search_org_name_1 = $ORG_NAME_1;
			$MAIN_MINISTRY_ID = $MINISTRY_ID;
			$MAIN_MINISTRY_NAME = $MINISTRY_NAME;
			$MAIN_DEPARTMENT_ID = $DEPARTMENT_ID;
			$MAIN_DEPARTMENT_NAME = $DEPARTMENT_NAME;
			$MAIN_ORG_ID = $ORG_ID;
			$MAIN_ORG_NAME = $ORG_NAME;
			break;
	} // end switch case
	
	/*ดูสิทธิ์เป็นผู้ตรวจสอบการลาหรือไม่*/
    
    $cmd2 = " select a.PER_AUDIT_FLAG,a.ORG_ID ,
			g.PN_SHORTNAME||a.PER_NAME||' '||a.PER_SURNAME  AS FULLNAME_SHOW
			from PER_PERSONAL  a
			left join PER_PRENAME g on(g.PN_CODE=a.PN_CODE) 
			where a.PER_ID=$SESS_PER_ID"; 
    $db_dpis2->send_cmd($cmd2);
    $data2 = $db_dpis2->get_array();
    $PER_AUDIT_FLAG = $data2[PER_AUDIT_FLAG];
    $PER_ORG_ID = $data2[ORG_ID]; /*หาคนภายใต้หน่วยงานตามหมอบหมายงาน*/
	$CHKALLOW_PER_NAME = $data2[FULLNAME_SHOW];
	$CHKALLOW_PER_ID = $SESS_PER_ID;
    
    //หาว่าอยู่กลุ่ม กจ. กรม หรือไม่--------------------------------
    $cmd4 = "	select	 b.CODE from	user_detail a, user_group b
					where a.group_id=b.id AND a.ID=".$SESS_USERID;
    $db_dpis2->send_cmd($cmd4);
    $data4 = $db_dpis2->get_array();
    if ($data4[CODE]) {
        $NAME_GROUP_HRD = $data4[CODE];
    }else{
        $NAME_GROUP_HRD = "";
    }
    
    //-ถ้าไม่ใช่กลุ่ม admin กับกลุ่ม กจ. กรม จะเอาหน่วยงานตามหมอบหมายงาน
    
     if ($SESS_USERGROUP !=1 && $NAME_GROUP_HRD!='HRD'){
		 
		$cmd2 = " select POS_ID,ORG_ID,POEM_ID,POEMS_ID,POT_ID,ORG_ID_1,PER_OT_FLAG from PER_PERSONAL where PER_ID=$SESS_PER_ID"; 
		$db_dpis2->send_cmd($cmd2);
		$data2 = $db_dpis2->get_array();
		$PER_POS_ID = $data2[POS_ID]; /*หาคนภายใต้หน่วยงานตามกฏหมาย ข้าราชการ*/
		$PER_POEM_ID = $data2[POEM_ID]; /*หาคนภายใต้หน่วยงานตามกฏหมาย ลูกจ้างประจำ*/
		$PER_POEMS_ID = $data2[POEMS_ID]; /*หาคนภายใต้หน่วยงานตามกฏหมาย พนักงานราชการ*/
		$PER_POT_ID = $data2[POT_ID]; /*หาคนภายใต้หน่วยงานตามกฏหมาย ลูกจ้างชั่วคราว*/
		$PER_ORG_ID = $data2[ORG_ID]; /*หาคนภายใต้หน่วยงานตามหมอบหมายงาน*/
		$PER_ORG_ID_1 = $data2[ORG_ID_1]; /*หาคนภายใต้หน่วยงานตามหมอบหมายงาน ต่ำกว่าสำนัก 1 ระดับ*/
		$PER_OT_FLAG = $data2[PER_OT_FLAG]; /*รหัสเจ้าของ OT ตามกฏหมาย*/
		
		if($P_OTTYPE_ORGANIZE ==2){ // มอบหมาย
                $cmd3 = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$PER_ORG_ID"; 
                $db_dpis2->send_cmd($cmd3);
                $data3 = $db_dpis2->get_array();
                $search_org_name = $data3[ORG_NAME];
                $search_org_id = $PER_ORG_ID;
                
                if($PER_OT_FLAG!=$PER_ORG_ID){
                    $cmd3 = " select ORG_NAME from PER_ORG_ASS where ORG_ID=$PER_OT_FLAG"; 
                    $db_dpis2->send_cmd($cmd3);
                    $data3 = $db_dpis2->get_array();
                    $search_org_name_1 = $data3[ORG_NAME];
                    $search_org_id_1 = $PER_OT_FLAG;
                }
         }else{ // กฏหมาย
	
				if($PER_POS_ID){ /*หาคนภายใต้หน่วยงานตามกฏหมาย ข้าราชการ*/
					$fromTable = "PER_POSITION";
					$whereTable = " c.POS_ID=$PER_POS_ID";
				}elseif($PER_POEM_ID){ /*หาคนภายใต้หน่วยงานตามกฏหมาย ลูกจ้างประจำ*/
					$fromTable = "PER_POS_EMP";
					$whereTable = " c.POEM_ID=$PER_POEM_ID";
				}elseif($PER_POEMS_ID){ /*หาคนภายใต้หน่วยงานตามกฏหมาย พนักงานราชการ*/
					$fromTable = "PER_POS_EMPSER";
					$whereTable = " c.POEMS_ID=$PER_POEMS_ID";
				}elseif($PER_POT_ID){ /*หาคนภายใต้หน่วยงานตามกฏหมาย ลูกจ้างชั่วคราว*/
					$fromTable = "PER_POS_TEMP";
					$whereTable = " c.POT_ID=$PER_POT_ID";
				}
				
				$cmd3 = " select g.ORG_NAME ,g.ORG_ID,g1.ORG_NAME as ORG_NAME_1 ,g1.ORG_ID as ORG_ID_1
				   from $fromTable c 
				   LEFT JOIN PER_ORG  g on (g.ORG_ID=c.ORG_ID)
				   LEFT JOIN PER_ORG  g1 on (g1.ORG_ID=c.ORG_ID_1)
				   where $whereTable "; 
				$db_dpis2->send_cmd($cmd3);
				$data3 = $db_dpis2->get_array();
				$search_org_name = $data3[ORG_NAME];
				$search_org_id = $data3[ORG_ID];
				if($PER_OT_FLAG==$data3[ORG_ID_1]){
					$search_org_id_1 = $data3[ORG_ID_1];
				}
		 }
    }
	
	
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	if ($command == "ADD") {
		$date_min = (substr($search_date_min,6,4)-543)."-".substr($search_date_min,3,2)."-".substr($search_date_min,0,2);
        $date_max = (substr($search_date_max,6,4)-543)."-".substr($search_date_max,3,2)."-".substr($search_date_max,0,2);
        
		
			/*บันทึกสรุป*/
		$save_year=$search_year;
		$save_month=$search_month;
		$PAY_DATE =NULL;
		if($search_date){
			$PAY_DATE = (substr($search_date,6,4)-543)."-".substr($search_date,3,2)."-".substr($search_date,0,2);
		}
		

		$date_min = (substr($search_date_min,6,4)-543)."-".substr($search_date_min,3,2)."-".substr($search_date_min,0,2);
        $date_max = (substr($search_date_max,6,4)-543)."-".substr($search_date_max,3,2)."-".substr($search_date_max,0,2);
        
		$save_ALLOW_PER_ID="NULL";
		if($ALLOW_PER_ID){
			$save_ALLOW_PER_ID=$ALLOW_PER_ID;
		}
		
		$save_APPROVE_PER_ID="NULL";
		if($APPROVE_PER_ID){
			$save_APPROVE_PER_ID=$APPROVE_PER_ID;
		}
	
		$save_CONFIRM_FLAG=0;
	
		$con_ORG_ID = " AND ot.DEPARTMENT_ID=-1 ";
		$chk_ORG_ID = -1;
		$con_ORG_ID1 = " AND DEPARTMENT_ID=-1 ";
		
		$con_ORG_LOWER1 = " AND ot.ORG_LOWER1=-1 ";
		$chk_ORG_LOWER1 = -1;
		$con_ORG_LOWER1_1 = " AND ORG_LOWER1=-1 ";
		

		if ($SESS_USERGROUP !=1 && $NAME_GROUP_HRD!='HRD'){
            $con_ORG_ID = " AND ot.DEPARTMENT_ID=$search_org_id ";
			$con_ORG_ID1 = " AND DEPARTMENT_ID=$search_org_id ";
			$chk_ORG_ID = $search_org_id;
			
			if($search_org_id_1){
				
				$con_ORG_LOWER1 = " AND ot.ORG_LOWER1=$search_org_id_1 ";
				$con_ORG_LOWER1_1 = " AND ORG_LOWER1=$search_org_id_1 ";
				$chk_ORG_LOWER1 = $search_org_id_1;
				
			}
   		}
		
		$db_insert1 = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
		$cmd = " select   sum(NVL(ot.AMOUNT,0)) AMOUNT
                         from  TA_PER_OT ot
                        where  
						ot.ORG_ID=$search_department_id 
						$con_ORG_ID $con_ORG_LOWER1
						AND ot.AUDIT_FLAG=1 AND  ot.ALLOW_FLAG =0
                        AND  (ot.OT_DATE between '$date_min' AND '$date_max')
                        ";
		$db_dpis->send_cmd($cmd);
		$data_dup = $db_dpis->get_array();
		if($data_dup[AMOUNT]>0){
			
					
					$cmd = " insert into TA_PER_OT_CONTROL 
								(CONTROL_ID,PER_TYPE,BUDGET_YEAR,PAY_MONTH,PAY_DATE,
								START_DATE,END_DATE,ALLOW_USER,APPROVE_USER,CONFIRM_FLAG,
								CREATE_USER,CREATE_DATE,UPDATE_USER,UPDATE_DATE)
					values	(TA_PER_OT_CONTROL_SEQ.NEXTVAL,1,$save_year,$save_month,'$PAY_DATE',
								'$date_min','$date_max',$save_ALLOW_PER_ID,$save_APPROVE_PER_ID,$save_CONFIRM_FLAG,
								$SESS_USERID,'$UPDATE_DATE',$SESS_USERID,'$UPDATE_DATE'
								)";
					$db_insert1->send_cmd($cmd);
					
					$cmd_SEQ = " SELECT TA_PER_OT_CONTROL_SEQ.currval AS CURID FROM dual ";
					$db_insert1->send_cmd($cmd_SEQ);
					$data_SEQ = $db_insert1->get_array();
					$Save_CONTROL_ID = $data_SEQ[CURID];
					
					insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 >$MENU_TITLE_LV2 > บันทึกปิดบัญชีเบิกจ่าย OT [".$Save_CONTROL_ID." : ".$data[PER_TYPE]." : ".$save_year." : ".$save_month." : ".$date_min." : ".$date_max."]");
					
					
					//Update ตัวบุคคล
					$db_insert = new connect_dpis($dpisdb_host, $dpisdb_name, $dpisdb_user, $dpisdb_pwd);
					$cmd = " select ot.PER_ID,ot.OT_DATE,ot.AUDIT_FLAG
										 from  TA_PER_OT ot
										where 
										ot.ORG_ID=$search_department_id 
										$con_ORG_ID $con_ORG_LOWER1
										AND ot.OT_DATE between '$date_min' AND '$date_max'
										AND ot.AUDIT_FLAG=1 AND ot.ALLOW_FLAG =0 ";
					$count_page_data2 = $db_dpis2->send_cmd($cmd);
					if($count_page_data2){
						while($data2 = $db_dpis2->get_array()){
								$SET_FLAG=1;
								$ALLOW_FLAG=1;

							$cmd = " update TA_PER_OT set 
										AUDIT_USER=$SESS_USERID,AUDIT_DATE = '$UPDATE_DATE',
										SET_FLAG =$SET_FLAG,
										ALLOW_FLAG=$ALLOW_FLAG,ALLOW_USER=$save_ALLOW_PER_ID,ALLOW_DATE='$UPDATE_DATE',
										APPROVE_FLAG=$ALLOW_FLAG,APPROVE_USER=$save_APPROVE_PER_ID,
										APPROVE_DATE='$UPDATE_DATE',CONTROL_ID=$Save_CONTROL_ID,
										UPDATE_USER=$SESS_USERID,UPDATE_DATE = '$UPDATE_DATE'
										where 
										ORG_ID=$search_department_id 
										$con_ORG_ID1 $con_ORG_LOWER1_1
										AND AUDIT_FLAG=1 AND ALLOW_FLAG =0 AND OT_DATE='".$data2[OT_DATE]."' AND PER_ID=".$data2[PER_ID];
							$db_insert->send_cmd($cmd);
							
							
							
						}
					}
					
						/*---------------------------------------------------------------------------*/
			
			echo "<script>window.location='../admin/es_update_ot_control.html?MENU_ID_LV0=$MENU_ID_LV0&MENU_ID_LV1=$MENU_ID_LV1&MENU_ID_LV2=$MENU_ID_LV2&CONTROL_ID=$Save_CONTROL_ID&HIDORG_ID=$search_department_id&HIDDEPARTMENT_ID=$chk_ORG_ID&HIDORG_LOWER1=$chk_ORG_LOWER1&HIDORG_LOWER2=-1&HIDORG_LOWER3=-1&detail=0';</script>";
			

		}else{
			
			echo "<script>alert('ไม่มีข้อมูล');</script>";
			
		}
			
			/*---------------------------------------------------------------------------*/
			
			
		
	}
	
	
  
?>