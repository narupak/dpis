<?
//		session_start();
		if($SESS_USERID){
			$MENU_ID_LV0 += 0;
			$MENU_ID_LV1 += 0;
			$MENU_ID_LV2 += 0;
			$MENU_ID_LV3 += 0;
                        
                        /**/
                        //echo $SESS_PER_AUDIT_FLAG."<<<";
                        $ACCESSIBLE_GROUP_TA = $ACCESSIBLE_GROUP;  
                        if($SESS_PER_AUDIT_FLAG==1 || (!empty($SESS_USERGROUP_OT))){
                            $cmdAt = "select* from BACKOFFICE_MENU_BAR_LV2 
                                    WHERE (menu_label LIKE 'T%' 
                                    OR menu_label LIKE 'R0702%' 
                                    OR menu_label LIKE 'R12%') AND menu_id=$MENU_ID_LV2 ";
                             $cntAt = $db->send_cmd($cmdAt);
                             if($cntAt>0){
                                 //$ACCESSIBLE_GROUP_TA= $ACCESSIBLE_GROUP.",".$SESS_USERGROUP_HRG;
								 if($SESS_PER_AUDIT_FLAG==1){
								 	$ACCESSIBLE_GROUP_TA= $ACCESSIBLE_GROUP_TA.",".$SESS_USERGROUP_HRG;
								 }
								 
								 // กลุ่มงาน OT
								 if(!empty($SESS_USERGROUP_OT)){
									$ACCESSIBLE_GROUP_TA = $ACCESSIBLE_GROUP_TA .",".$SESS_USERGROUP_OT;
								 }
                             }
                        }
                        /**/
                        //echo "<$ACCESSIBLE_GROUP_TA>";
						
						
                        
                        
                        
			$cmd = " select can_add, can_edit, can_del, can_inq, can_print, can_confirm ,can_audit, can_attach
					 		from 	user_privilege
					 		where 	group_id in ($ACCESSIBLE_GROUP_TA) and page_id=$ACCESS_PAGE and 
										menu_id_lv0=$MENU_ID_LV0 and menu_id_lv1=$MENU_ID_LV1 and 
										menu_id_lv2=$MENU_ID_LV2 and menu_id_lv3=$MENU_ID_LV3 ";
			//echo $cmd."<br><br><br>";
                        $result=$db->send_cmd($cmd);
//			$db->show_error();
			while($data = $db->get_array()){
				$data = array_change_key_case($data, CASE_LOWER);
				$PAGE_AUTH["add"] = ($PAGE_AUTH["add"]=="Y")?"Y":(($data[can_add])?$data[can_add]:"N");
				$PAGE_AUTH["edit"] = ($PAGE_AUTH["edit"]=="Y")?"Y":(($data[can_edit])?$data[can_edit]:"N");
				$PAGE_AUTH["del"] = ($PAGE_AUTH["del"]=="Y")?"Y":(($data[can_del])?$data[can_del]:"N");
				$PAGE_AUTH["inq"] = ($PAGE_AUTH["inq"]=="Y")?"Y":(($data[can_inq])?$data[can_inq]:"N");
				$PAGE_AUTH["print"] = ($PAGE_AUTH["print"]=="Y")?"Y":(($data[can_print])?$data[can_print]:"N");
				$PAGE_AUTH["confirm"] = ($PAGE_AUTH["confirm"]=="Y")?"Y":(($data[can_confirm])?$data[can_confirm]:"N");
				$PAGE_AUTH["audit"] = ($PAGE_AUTH["audit"]=="Y")?"Y":(($data[can_audit])?$data[can_audit]:"N");
				$PAGE_AUTH["attach"] = ($PAGE_AUTH["attach"]=="Y")?"Y":(($data[can_attach])?$data[can_attach]:"N");
			} // end while
//			print_r($PAGE_AUTH);
		}else{
			header("location: ../index.html");
		}
?>