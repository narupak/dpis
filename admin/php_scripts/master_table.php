<?	

	include("php_scripts/session_start.php");
        
	include("php_scripts/function_share.php");

        
        



	// ==== use for testing phase =====
	if($table=="PER_GROUP_N"){
		$DPISDB = "mysql";
		$db_dpis = $db;
	} // end if
	// ==========================

	if( !$current_page ) $current_page = 1;
	if(!$data_per_page) $data_per_page = 30;
	$start_record = ($current_page - 1) * $data_per_page;
	
	$cmd = " select * from $table ";
	$db_dpis->send_cmd($cmd);
	$field_list = $db_dpis->list_fields($table);
//	echo "<pre>";		print_r($field_list);		echo "</pre>";
	unset($arr_fields);
	if($DPISDB=="odbc" || $DPISDB=="oci8"){
		for($i=1; $i<=count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	}elseif($DPISDB=="mysql"){
		for($i=0; $i<count($field_list); $i++) :
			$arr_fields[] = $field_list[$i]["name"];
		endfor;
	} // end if
//	echo (implode(", ", $arr_fields));
	
	$UPDATE_DATE = date("Y-m-d H:i:s");
	if (!$$arr_fields[5]) $$arr_fields[5] = "NULL";

	if($command=="REORDER"){
		foreach($ARR_ORDER as $CODE => $SEQ_NO){
		if($SEQ_NO=="") { $cmd = " update $table set $arr_fields[5]='' where $arr_fields[0]='$CODE' "; }
		else {	$cmd = " update $table set $arr_fields[5]=$SEQ_NO where $arr_fields[0]='$CODE' "; }
			$db_dpis->send_cmd($cmd);
			//$db_dpis->show_error();
			//echo "<br>".$cmd;
		} // end foreach

		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > �Ѵ�ӴѺ [$CODE : $SEQ_NO]");
	} // end if

	if ($command == "SETFLAG") {
		$setflagshow =  implode(",",$list_show_id);
		//echo "$setflagshow";
		$cmd = " update $table set $arr_fields[2] = 0 where $arr_fields[0] in (".stripslashes($current_list).") ";
		$db_dpis->send_cmd($cmd);
               // echo "flag1.$cmd<br>";
//		$db_dpis->show_error();
		$cmd = " update $table set $arr_fields[2] = 1 where $arr_fields[0] in (".stripslashes($setflagshow).") ";
		$db_dpis->send_cmd($cmd);
//		$db_dpis->show_error();
		//echo "flag2".$cmd;
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ��駤�ҡ����ҹ������");
	}
	
	if($command == "ADD" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[0], $arr_fields[1] from $table where $arr_fields[0]='". trim($$arr_fields[0]) ."' OR $arr_fields[1]='". trim($$arr_fields[1]) ."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		//echo "cmd=$cmd<br>";
		if($count_duplicate <= 0){
                        if($table == "PER_SPECIAL_SKILLGRP"){
                            $cmd = " insert into $table (". implode(", ", $arr_fields) .") values ('".trim($$arr_fields[0])."', '".$$arr_fields[1]."', ".$$arr_fields[2].", 
							$SESS_USERID, '$UPDATE_DATE', ".$$arr_fields[5].", '".$$arr_fields[6]."', '".$$arr_fields[7]."') ";
                            $db_dpis->send_cmd($cmd);
                            
                            }else if($table == "PER_LEVELSKILL"){
                            
                             $cmd = " insert into $table (". implode(", ", $arr_fields) .") values ('".trim($$arr_fields[0])."', '".$$arr_fields[1]."', ".$$arr_fields[2].", 
							$SESS_USERID, '$UPDATE_DATE', ".$$arr_fields[5].")";
                            $db_dpis->send_cmd($cmd);
                             //echo"���͡".$cmd;
                            
                        }else{
                            $cmd = " insert into $table (". implode(", ", $arr_fields) .") values ('".trim($$arr_fields[0])."', '".$$arr_fields[1]."', ".$$arr_fields[2].", 
							$SESS_USERID, '$UPDATE_DATE', ".$$arr_fields[5].", '".$$arr_fields[6]."') ";
			$db_dpis->send_cmd($cmd);
                        }
			//echo $cmd;
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ���������� [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
		}else{
			$data = $db_dpis->get_array();			
			$err_text = "���ʢ����ū�����ͪ��ͫ�� [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]."]";
		} // endif
	}

	if($command == "UPDATE" && trim($$arr_fields[0])){
	
		if($table=="PER_PROJECT_GROUP" || $table=="PER_PROJECT_PAYMENT") {  
			$cmd = " update $table set 
								$arr_fields[1]='".$$arr_fields[1]."', 
								$arr_fields[2]=".$$arr_fields[2].", 
								$arr_fields[6]='".$$arr_fields[6]."', 
								UPDATE_USER=$SESS_USERID, 
								UPDATE_DATE='$UPDATE_DATE' 
								where $arr_fields[0]=".$$arr_fields[0]." ";	 
			
		} else if($table == "PER_LEVELSKILL"){
                    $cmd = " update $table set 
								$arr_fields[1]='".$$arr_fields[1]."', 
								$arr_fields[2]=".$$arr_fields[2].", 
                                                              	UPDATE_USER=$SESS_USERID, 
                                                                UPDATE_DATE='$UPDATE_DATE'
                                                           	where $arr_fields[0]='".$$arr_fields[0]."' ";
                                                               // echo "hook".$cmd;
              }else if($table == "PER_SPECIAL_SKILLGRP"){
                             $cmd = " update $table set 
								$arr_fields[1]='".$$arr_fields[1]."', 
								$arr_fields[2]=".$$arr_fields[2].", 
                                                                $arr_fields[5]=".$$arr_fields[5].",
                                                                $arr_fields[6]='".$$arr_fields[6]."',
								UPDATE_USER=$SESS_USERID, 
                                                                UPDATE_DATE='$UPDATE_DATE',
                                                                $arr_fields[7]='".$$arr_fields[7]."'
                                                               	where $arr_fields[0]='".$$arr_fields[0]."' ";
              
                                                                
                                                                
               }else { 
			$cmd = " update $table set 
								$arr_fields[1]='".$$arr_fields[1]."', 
								$arr_fields[2]=".$$arr_fields[2].", 
								$arr_fields[6]='".$$arr_fields[6]."', 
								UPDATE_USER=$SESS_USERID, UPDATE_DATE='$UPDATE_DATE' 
								where $arr_fields[0]='".$$arr_fields[0]."' ";	
			
		}
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		//echo $cmd;
		insert_log("$MENU_TITLE_LV0 > $MENUT_ITLE_LV1 > ��䢢����� [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
	}
	
	if($command == "DELETE" && trim($$arr_fields[0])){
                $cmd = " select $arr_fields[1] from $table where $arr_fields[0]='".$$arr_fields[0]."' ";
                $db_dpis->send_cmd($cmd);
                $data = $db_dpis->get_array();
                $$arr_fields[1] = $data[$arr_fields[1]];
                //echo $cmd;
                
                
		if($table=="PER_PROJECT_GROUP" || $table=="PER_PROJECT_PAYMENT") 
                {    $cmd = " delete from $table where $arr_fields[0]=".$$arr_fields[0].""; 
                }else if($table == "PER_SPECIAL_SKILLGRP"){
                 $cmd = " delete from $table where $arr_fields[0]='".$$arr_fields[0]."' or $arr_fields[7] ='".$$arr_fields[0]."' "; 
                 //echo "<pre>����١=>".$cmd;
                }else { 
                $cmd = " delete from $table where $arr_fields[0]='".$$arr_fields[0]."' "; 
                }
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > ź������ [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
	}
        if($command == "ADDREF" && trim($$arr_fields[0])){
		$cmd = " select $arr_fields[0], $arr_fields[1] from $table where $arr_fields[0]='". trim($$arr_fields[0]) ."' OR $arr_fields[1]='". trim($$arr_fields[1]) ."' ";
		$count_duplicate = $db_dpis->send_cmd($cmd);
		//echo "cmd=$cmd<br>";
		if($count_duplicate <= 0){
			$cmd = " insert into $table (". implode(", ", $arr_fields) .") values ('".trim($$arr_fields[0])."', '".$$arr_fields[1]."', ".$$arr_fields[2].", 
							$SESS_USERID, '$UPDATE_DATE', ".$$arr_fields[5].", '".$$arr_fields[6]."','".trim($$arr_fields[7])."') ";
			$db_dpis->send_cmd($cmd);
			//echo $cmd;
			//$db_dpis->show_error();
			insert_log("$MENU_TITLE_LV0 > $MENU_TITLE_LV1 > $MENU_TITLE_LV2 > ���������� [".trim($$arr_fields[0])." : ".$$arr_fields[1]."]");
		}else{
                        $NEWSUB=1;
			$data = $db_dpis->get_array();			
			$err_text = "���ʢ����ū�����ͪ��ͫ�� [".$data[$arr_fields[0]]." ".$data[$arr_fields[1]]."]";
		} // endif
	}
	
	if($UPD){
		
		if($table=="PER_PROJECT_GROUP" || $table=="PER_PROJECT_PAYMENT") {  
			$cmd = " select $arr_fields[1], $arr_fields[2], $arr_fields[5], $arr_fields[6], UPDATE_USER, UPDATE_DATE from $table where $arr_fields[0]='".$$arr_fields[0]."'"; 
                        //echo "Gxx";
		}else if($table == "PER_SPECIAL_SKILLGRP") {  
			$cmd = " select $arr_fields[1], $arr_fields[2], $arr_fields[5], $arr_fields[6], UPDATE_USER, UPDATE_DATE, $arr_fields[7] from $table where $arr_fields[0]='".$$arr_fields[0]."' "; 
                        //echo "Gxx".$cmd;
                        
                }else if($table == "PER_LEVELSKILL") {  
			$cmd = " select $arr_fields[1], $arr_fields[2], $arr_fields[5], UPDATE_USER, UPDATE_DATE from $table where $arr_fields[0]='".$$arr_fields[0]."' "; 
                        // echo "test".$cmd;        
                    
		} else { 
			$cmd = " select $arr_fields[1], $arr_fields[2], $arr_fields[5], $arr_fields[6], UPDATE_USER, UPDATE_DATE from $table where $arr_fields[0]='".$$arr_fields[0]."' "; 
                        //echo "XX".$cmd;
                }
                //echo $cmd;
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
		$$arr_fields[1] = $data[$arr_fields[1]];
		$$arr_fields[2] = $data[$arr_fields[2]];
                $$arr_fields[3] = $data[$arr_fields[3]];
                $$arr_fields[5] = $data[$arr_fields[5]];
                $$arr_fields[6] = $data[$arr_fields[6]];
		$UPDATE_USER = $data[UPDATE_USER];
		$cmd ="select TITLENAME, FULLNAME from USER_DETAIL where id =  $UPDATE_USER ";
		$db_dpis->send_cmd($cmd);
		$data2 = $db_dpis->get_array();
		$SHOW_UPDATE_USER = trim($data2[TITLENAME]) . trim($data2[FULLNAME]);
		$SHOW_UPDATE_DATE = show_date_format(trim($data[UPDATE_DATE]), $DATE_DISPLAY);
                if($table == "PER_SPECIAL_SKILLGRP") {
                    $$arr_fields[7] = $data[$arr_fields[7]];
                    $cmd ="select $arr_fields[1] from $table where $arr_fields[0]='".trim($$arr_fields[7])."'";
                    $db_dpis->send_cmd($cmd);
                    $data = $db_dpis->get_array();
                    //echo $cmd;
                    $$arr_fields_REF[1] = $data[$arr_fields[1]];
                    
                }
	} // end if
        if($NEWSUB){
		
		if($table=="PER_PROJECT_GROUP" || $table=="PER_PROJECT_PAYMENT") {  
			$cmd = " select $arr_fields[1], $arr_fields[2], $arr_fields[5], $arr_fields[6], UPDATE_USER, UPDATE_DATE from $table where $arr_fields[0]=".$$arr_fields[0]." "; 
		}else if($table == "PER_SPECIAL_SKILLGRP"){ 
                        $cmd = " select $arr_fields[0], $arr_fields[1] from $table where $arr_fields[0]='".$$arr_fields[0]."' "; 
                        //echo $cmd;
                }else { 
			$cmd = " select $arr_fields[1], $arr_fields[2], $arr_fields[5], $arr_fields[6], UPDATE_USER, UPDATE_DATE from $table where $arr_fields[0]='".$$arr_fields[0]."' ";     
		}
               
		$db_dpis->send_cmd($cmd);
		//$db_dpis->show_error();
		$data = $db_dpis->get_array();
                   
                   $$arr_fields[7] = $data[$arr_fields[0]];
                   $$arr_fields_REF[1] = $data[$arr_fields[1]];
                
                //$$arr_fields[1] = "";
		$$arr_fields[2] = 1;
		$$arr_fields[5] = 0;
		$$arr_fields[6] = "";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
                   
		
	} // end if
	
	if(!$UPD && !$DEL && !$err_text && !$NEWSUB){
		$$arr_fields[0] = "";
		$$arr_fields[1] = "";
		$$arr_fields[2] = 1;
		$$arr_fields[5] = 0;
		$$arr_fields[6] = "";
		unset($SHOW_UPDATE_USER);
		unset($SHOW_UPDATE_DATE);
                //echo "tetsssss";
	} // end if
?>